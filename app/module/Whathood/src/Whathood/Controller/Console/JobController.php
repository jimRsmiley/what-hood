<?php

namespace Whathood\Controller\Console;

use Whathood\Controller\BaseController;

class JobController extends BaseController {

    public function rebuildBordersAction() {

        $neighborhoods = $this->m()->neighborhoodMapper()->fetchAll();

        foreach ($neighborhoods as $neighborhood) {
            $this->logger()->info(sprintf("creating job for %s(%s)",
                $neighborhood->getName(), $neighborhood->getId() ));

            $job = $this->messageQueue()->push(
                'Whathood\Job\NeighborhoodBorderBuilderJob',
                array(
                    'neighborhood_id' => $neighborhood->getId()
                )
            );
        }
    }
}
