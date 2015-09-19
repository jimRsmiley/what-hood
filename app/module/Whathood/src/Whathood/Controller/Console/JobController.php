<?php

namespace Whathood\Controller\Console;

use Whathood\Controller\BaseController;

class JobController extends BaseController {

    public function infoAction() {
        foreach($this->m()->queueMapper()->fetchAll() as $job) {
            print sprintf("id=%s status=%s %s\n",
                $job->getId(), $job->getStatus(), $job->getMessage() );
        }
    }

    /**
     * rebuild all neighborhood borders,
     *
     * starts with neighborhoods:
     *   * that have no heatmaps defined
     **/
    public function rebuildBordersAction() {
        $arr = array();
        # get neighborhoods with no heatmaps first
        $neighborhoods = $this->m()->heatMapPoint()->neighborhoodsWithNoHeatmapPoints();

        printf("adding %s neighborhoods with no heatmaps\n", count($neighborhoods));
        $this->array_add_neighborhoods($arr, $neighborhoods);

        # then get the rest
        $neighborhoods = $this->m()->neighborhoodMapper()->fetchAll();
        $neighborhoods = $this->m()->neighborhoodMapper()
            ->sortByOldestBorder($neighborhoods);
        $this->array_add_neighborhoods($arr, $neighborhoods);

        foreach ($arr as $neighborhood) {
            $this->logger()->info(sprintf("creating job for %s(%s)",
                $neighborhood->getName(), $neighborhood->getId() ));

            $this->pushBorderJob($neighborhood);
            $this->pushHeatmapJob($neighborhood);
        }
    }

    public function array_add_neighborhoods(array &$arr, array $neighborhoods) {

        foreach ($neighborhoods as $n) {
            $found = 0;
            foreach ($arr as $test) {
                # $n already in $arr
                if ($test->getId() == $n->getId())
                    $found = true;
            }
            if (!$found)
                array_push($arr, $n);
        }
        return $arr;
    }

    public function pushBorderJob($neighborhood) {
        $this->messageQueue()->push(
            'Whathood\Job\NeighborhoodBorderBuilderJob',
            array(
                'neighborhood_id' => $neighborhood->getId()
            )
        );
    }

    public function pushHeatmapJob($neighborhood) {
        $this->messageQueue()->push('Whathood\Job\HeatmapBuilderJob',
            array(
                'neighborhood_id' => $neighborhood->getId()
            )
        );
    }
}
