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

    public function rebuildBordersAction() {

        $neighborhoods = $this->m()->neighborhoodMapper()->fetchAll();
        $neighborhoods = $this->m()->neighborhoodMapper()
            ->sortByOldestBorder($neighborhoods);

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
