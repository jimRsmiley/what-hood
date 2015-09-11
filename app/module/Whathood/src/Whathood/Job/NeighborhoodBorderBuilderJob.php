<?php

namespace Whathood\Job;


use Whathood\Entity\NeighborhoodPolygon;
use Whathood\Timer;
use Whathood\Election\PointElectionCollection;
use Whathood\Entity\Neighborhood;

class NeighborhoodBorderBuilderJob extends \Whathood\Job\AbstractJob
{

    protected $_gridResolution;
    protected $_heatmapGridResolution;

    public static function build(array $data) {
        $job = parent::build($data);

        if (empty($job->m()))
            throw new \InvalidArgumentException("must define mapperBuilder");

        if (empty($job->getGridResolution()))
            throw new \InvalidArgumentException("gridResolution may not be empty");

        return $job;
    }

    public function execute() {
        $this->infoLog("job ".$this->getName()." started");
        $this->infoLog("grid-resolution: ".rtrim(sprintf("%.8F",$this->getGridResolution()),"0"));

        $neighborhood = $this->m()->neighborhoodMapper()
            ->byId($this->getNeighborhoodId());

        $this->processNeighborhood($neighborhood);

        $this->infoLog("job finished");
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

            $this->buildAndSaveNeighborhoodPolygon($electionCollection, $neighborhood, $userPolygons);

            $this->infoLog(sprintf("saved neighborhood polygon elapsed="));
        }
        catch(\Exception $e) {
            $this->infoLog($e->getMessage());
            $this->infoLog($e->getTraceAsString());
            throw $e;
        }
    }

    public function buildAndSaveNeighborhoodPolygon(PointElectionCollection $electionCollection, Neighborhood $neighborhood,$ups) {
        if (empty($electionCollection->getPointElections()))
            throw new \InvalidArgumentException("electionCollection may not be empty");

        $this->infoLog(sprintf("building neighborhood polygon with %s points",
            count($electionCollection->getPointElections()) ));
        $polygon = $this->m()->pointElectionMapper()
            ->generateBorderPolygon($electionCollection, $neighborhood);

        if (!$polygon)
            $this->throwException("No polygon created for neighborhood; possibly not dominant for any point ".$neighborhood->getName());

        $neighborhoodPolygon = NeighborhoodPolygon::build( array(
            'geom' => $polygon,
            'neighborhood' => $neighborhood,
            'user_polygons' => $ups,
            'grid_resolution' => $this->getGridResolution()
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
