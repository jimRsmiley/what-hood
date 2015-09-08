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

    public function __construct(array $data) {
        parent::__construct($data);
    }

    public static function build(array $data) {
        $job = new NeighborhoodBorderBuilderJob($data);

        if (empty($job->m()))
            throw new \InvalidArgumentException("must define mapperBuilder");

        if (empty($job->getGridResolution()))
            throw new \InvalidArgumentException("gridResolution may not be empty");

        if (empty($job->getHeatmapGridResolution()))
            throw new \InvalidArgumentException("heatmapGridResolution may not be empty");
        return $job;
    }

    public function execute() {
        $api_timer = Timer::start('api');
        $this->infoLog("job ".$this->getName()." started");
        $this->infoLog("grid-resolution: ".rtrim(sprintf("%.8F",$this->getGridResolution()),"0"));

        $neighborhood = $this->getNeighborhood();

        if (!$neighborhood)
            throw new \Whathood\Exception("must include neighborhood in job content");

        $userPolygons = $this->m()->userPolygonMapper()->byNeighborhood($neighborhood);

        if (empty($userPolygons)) {
            throw new \Whathood\Exception(
                sprintf($this->getName().": no user polygons found for neighborhood %s(%s)",
                    $neighborhood->getName(), $neighborhood->getId() ));
        }

        $this->infoLog(
            sprintf("neighborhood %s(%s) num_user_polygons=%s",
                $neighborhood->getName(), $neighborhood->getId(), count($userPolygons)));

        try {
            /* build the border */
            $timer = Timer::start('generate_border');
            $electionCollection = $this->m()->pointElectionMapper()->getCollection(
                $userPolygons,
                $neighborhood->getId(),
                $this->getGridResolution()
            );

            $this->buildAndSaveNeighborhoodPolygon($electionCollection, $neighborhood, $userPolygons);
            $this->infoLog(
                sprintf("\t\tsaved neighborhood polygon elapsed=%s", $timer->elapsedReadableString()));

            #$this->buildAndSaveHeatmapPoints($userPolygons, $neighborhood, $this->getHeatmapGridResolution());
            $timer->stop();
        }
        catch(\Exception $e) {
            $this->logger()->err($e->getMessage());
            $this->logger()->err($e->getTraceAsString());
            throw $e;
        }
        $this->infoLog("job finished");
    }

    public function buildAndSaveNeighborhoodPolygon(PointElectionCollection $electionCollection, Neighborhood $n,$ups) {
        if (empty($electionCollection->getPointElections()))
            throw new \InvalidArgumentException("electionCollection may not be empty");

        $polygon = $this->m()->pointElectionMapper()->generateBorderPolygon(
            $electionCollection, $n
        );

        if (!$polygon) {
            $this->logger()->warn("Could not construct a neighborhood border for ".$n->getName());
            return;
        }

        $neighborhoodPolygon = NeighborhoodPolygon::build( array(
            'geom' => $polygon,
            'neighborhood' => $n,
            'user_polygons' => $ups,
            'grid_resolution' => $this->getGridResolution()
        ));
        $this->m()->neighborhoodPolygonMapper()->save($neighborhoodPolygon);
        $this->m()->neighborhoodPolygonMapper()->detach($neighborhoodPolygon);
        return $neighborhoodPolygon;
    }

    /**
     * take the user polygons, run a point election, and save the points in the db
     **/
    public function buildAndSaveHeatmapPoints($user_polygons, Neighborhood $n, $grid_resolution) {

        $timer = Timer::start("heatmap_builder");
        $electionCollection = $this->m()->pointElectionMapper()
            ->getCollection(
                $user_polygons,
                $n->getId(),
                $grid_resolution
            );

        $heatmap_points = $electionCollection->heatMapPointsByNeighborhood($n);

        if (!empty($heatmap_points)) {
            $this->m()->heatMapPoint()->deleteByNeighborhood($n);
            $this->m()->heatMapPoint()->savePoints($heatmap_points);
            $this->m()->heatMapPoint()->detach($heatmap_points);
            $this->logger()->info(
                sprintf("saved %s heatmap points from %s points elapsed=%s",
                    count($heatmap_points), count($electionCollection->getPointElections()), $timer->elapsedReadableString()
                )
            );
        }
        else
            $this->logger()->info("\t\tno heatmap_points generated to save");
        return $heatmap_points;
    }

    public function setHeatmapGridResolution($gridRes) {
        $this->_heatmapGridResolution = $gridRes;
    }

    public function getHeatmapGridResolution() {
        return $this->_heatmapGridResolution;
    }

    public function setGridResolution($gridResolution) {
        $this->_gridResolution = $gridResolution;
    }

    public function getGridResolution() {
        return $this->_gridResolution;
    }

    public function getNeighborhood() {
        return $this->getContent()['neighborhood'];
    }

}
