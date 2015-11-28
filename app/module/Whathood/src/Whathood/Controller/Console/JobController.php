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
     * Without route parameters:
     *   starts with neighborhoods:
     *     * that have no heatmaps defined
     *     * then push the neighborhoods that have borders, older first
     *
     * @param neighborhood [String] the name of the neighborhood to build
     *
     **/
    public function rebuildBordersAction() {
        $neighborhoodName   = $this->params()->fromRoute('neighborhood');
        $regionName         = $this->params()->fromRoute('region');

        $neighborhoodBuilder = $this->getServiceLocator()
            ->get('Whathood\Spatial\Neighborhood\NeighborhoodBuilder');

        $neighborhoods = array();
        if ($neighborhoodName and $regionName) {
            $neighborhoods[] = $neighborhoodBuilder
                ->byName($neighborhoodName, $regionName);
        }
        else {
            $neighborhoods = $neighborhoodBuilder->allByHeatmapPriority();
        }

        foreach ($neighborhoods as $neighborhood) {
            $this->logger()->info(sprintf("creating job for %s(%s)",
                $neighborhood->getName(), $neighborhood->getId() ));

            $this->pushBorderJob($neighborhood);
            $this->pushHeatmapJob($neighborhood);
        }
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
