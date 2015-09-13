<?php

namespace Whathood\Job;

use SlmQueue\Queue\QueueAwareInterface;
use SlmQueue\Queue\QueueAwareTrait;
use SlmQueue\Queue\QueueInterface;
use Whathood\Timer;
use Whathood\Election\PointElectionCollection;
use Whathood\Entity\Neighborhood;
use Whathood\Entity\NeighborhoodPolygon;

class NeighborhoodBorderBuilderJob extends \Whathood\Job\AbstractJob implements QueueAwareInterface
{
    use QueueAwareTrait;

    protected $_gridResolution;

    public static function build(array $data) {
        $job = new static($data);
        if (empty($job->m()))
            throw new \InvalidArgumentException("must define mapperBuilder");
        if (empty($job->getGridResolution()))
            throw new \InvalidArgumentException("gridResolution may not be empty");
        if (empty($job->getQueue()))
            throw new \InvalidArgumentException("queue may not be empty");
        return $job;
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

        $this->triggerHeatmapJob();

        $this->infoLog("job finished");
    }

    /**
     *  trigger a heatmap job
     **/
    public function triggerHeatmapJob() {
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

            $polygon = $this->buildNeighborhoodPolygon($electionCollection, $neighborhood, $userPolygons);

            if ($polygon) {
                $this->saveNeighborhoodPolygon($neighborhood, $polygon, $userPolygons);
                $this->infoLog(sprintf("saved neighborhood polygon"));
            }
            else {
                $this->infoLog(sprintf("NOTE: no neighborhoodPolygon was built, possibly not a dominant neighborhood"));
            }
        }
        catch(\Exception $e) {
            $this->infoLog($e->getMessage());
            $this->infoLog($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     *  build the neighborhood polygon
     *
     **/
    public function buildNeighborhoodPolygon(PointElectionCollection $electionCollection, Neighborhood $neighborhood,$ups) {
        if (empty($electionCollection->getPointElections()))
            throw new \InvalidArgumentException("electionCollection may not be empty");
        $this->infoLog(sprintf("working with %s election points",
            count($electionCollection->getPointElections()) ));
        return $this->m()->pointElectionMapper()
            ->generateBorderPolygon($electionCollection, $neighborhood);
    }

    /**
     *  save the neighborhood polygon
     *
     **/
    public function saveNeighborhoodPolygon($neighborhood, $polygon, $ups) {
        $neighborhoodPolygon = NeighborhoodPolygon::build( array(
            'geom'              => $polygon,
            'neighborhood'      => $neighborhood,
            'user_polygons'     => $ups,
            'grid_resolution'   => $this->getGridResolution()
        ));
        $this->m()->neighborhoodPolygonMapper()->save($neighborhoodPolygon);
        return $neighborhoodPolygon;
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
