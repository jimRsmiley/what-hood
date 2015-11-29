<?php

namespace Whathood\Job;

use SlmQueue\Queue\QueueAwareInterface;
use SlmQueue\Queue\QueueAwareTrait;
use SlmQueue\Queue\QueueInterface;
use Whathood\Timer;
use Whathood\Election\PointElectionCollection;
use Whathood\Entity\Neighborhood;
use Whathood\Entity\NeighborhoodBoundary;

class NeighborhoodBorderBuilderJob extends \Whathood\Job\AbstractJob implements QueueAwareInterface
{
    use QueueAwareTrait;

    protected $_gridResolution;

    protected $_build_heatmap = false;

    protected $_boundary_builder;

    public static function build(array $data) {
        $job = new static($data);
        if (empty($job->m()))
            throw new \InvalidArgumentException("must define mapperBuilder");
        if (empty($job->getGridResolution()))
            throw new \InvalidArgumentException("gridResolution may not be empty");
        if (empty($job->getQueue()))
            throw new \InvalidArgumentException("queue may not be empty");
        if (empty($job->getBoundaryBuilder()))
            throw new \InvalidArgumentException("boundary_builder may not be empty");
        return $job;
    }

    public function setBoundaryBuilder($builder) {
        $this->_boundary_builder = $builder;
    }

    public function getBoundaryBuilder() {
        return $this->_boundary_builder;
    }

    public function setContent($content) {
        parent::setContent($content);

        if (!array_key_exists('neighborhood_id', $content))
            throw new \InvalidArgumentException("neighborhood_id may not be empty");

        if (empty($content['neighborhood_id']))
            throw new \InvalidArgumentException("must define a neighborhood id");
    }

    public function execute() {
        $this->infoLog("job ".$this->getName()." started");
        $this->infoLog("grid-resolution: ".rtrim(sprintf("%.8F",$this->getGridResolution()),"0"));

        $neighborhood = $this->m()->neighborhoodMapper()
            ->byId($this->getNeighborhoodId());

        $this->processNeighborhood($neighborhood);

        if ($this->buildHeatmap())
            $this->triggerHeatmapJob();

        $this->infoLog("job finished");
    }

    /**
     *  trigger a heatmap job
     **/
    public function triggerHeatmapJob() {
        $this->infoLog("triggering heatmap job");
        $queue   = $this->getQueue();
        $job     = $queue->getJobPluginManager()
            ->get('Whathood\Job\HeatmapBuilderJob');
        $job->setContent(array(
            'neighborhood_id' => $this->getNeighborhoodId()
        ));
        $queue->push($job);
    }

    /**
     * do the heavy lifting of building and saving the neighborhood border if there is any
     *
     **/
    public function processNeighborhood(Neighborhood $neighborhood) {
        if (!$neighborhood) {
            $str = "must include neighborhood in job content";
            $this->infoLog($str);
            throw new \Whathood\Exception($str);
        }
        $userPolygons = $this->m()->userPolygonMapper()->byNeighborhood($neighborhood);

        $this->infoLog(
            sprintf("neighborhood %s(%s) num_user_polygons=%s",
                $neighborhood->getName(), $neighborhood->getId(), count($userPolygons)));

        if (empty($userPolygons)) {
            $str = sprintf("%s: no user polygons found for neighborhood %s(%s)",
                $this->getName(), $neighborhood->getName(), $neighborhood->getId());
            $this->infoLog($str);
            throw new \Whathood\Exception($str);
        }
        try {
            /* build the border */
            $electionCollection = $this->m()->pointElectionMapper()->getCollection(
                $userPolygons,
                $neighborhood->getId(),
                $this->getGridResolution()
            );

            $polygon = $this->buildNeighborhoodBoundary($electionCollection, $neighborhood, $userPolygons);

            if ($polygon) {
                $this->saveNeighborhoodBoundary($neighborhood, $polygon, $userPolygons);
                $this->infoLog(sprintf("saved neighborhood polygon"));
            }
            else {
                $this->infoLog(sprintf("NOTE: no neighborhoodPolygon was built, possibly not a dominant neighborhood"));
            }
        }
        catch(\Exception $e) {
            $this->infoLog("There was an error building the neighborhood");
            $this->infoLog($e->getMessage());
            $this->infoLog($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     *  build the neighborhood polygon
     *
     **/
    public function buildNeighborhoodBoundary(PointElectionCollection $electionCollection, Neighborhood $neighborhood,$ups) {
        if (empty($electionCollection->getPointElections()))
            throw new \InvalidArgumentException("electionCollection may not be empty");
        $this->infoLog(sprintf("working with %s election points",
            count($electionCollection->getPointElections()) ));

        try {
            return $this->getBoundaryBuilder()
                ->build($electionCollection, $neighborhood);
        }
        catch(\Exception $e) {
            $this->infoLog("failed to generate border");
            $this->infoLog($e);
        }
    }

    /**
     *  save the neighborhood polygon
     *
     **/
    public function saveNeighborhoodBoundary($neighborhood, $polygon, $ups) {
        $neighborhoodPolygon = NeighborhoodBoundary::build( array(
            'geom'              => $polygon,
            'neighborhood'      => $neighborhood,
            'user_polygons'     => $ups,
            'grid_resolution'   => $this->getGridResolution()
        ));
        $this->m()->neighborhoodPolygonMapper()->save($neighborhoodPolygon);
        return $neighborhoodPolygon;
    }

    public function buildHeatmap() {
        return $this->getBuildHeatmap();
    }

    public function getBuildHeatmap() {
        return $this->_build_heatmap;
    }

    public function setBuildHeatmap($buildHeatmap) {
        $this->_build_heatmap = $buildHeatmap;
    }

    public function setGridResolution($gridResolution) {
        $this->_gridResolution = $gridResolution;
    }

    public function getGridResolution() {
        return $this->_gridResolution;
    }

    public function getNeighborhoodId() {
        $neighborhood_id = $this->getContent()['neighborhood_id'];

        if (empty($neighborhood_id))
            throw new \Exception("neighborhood_id may not be empty");
        return $neighborhood_id;
    }

    public function __construct(array $data) {
        parent::__construct($data);
    }
}
