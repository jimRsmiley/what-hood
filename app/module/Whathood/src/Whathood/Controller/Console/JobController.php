<?php

namespace Whathood\Controller\Console;

use Zend\View\Model\ConsoleModel;

use Whathood\Controller\BaseController;
use Whathood\Entity\DefaultJob;

class JobController extends BaseController {

    public function clearQueueAction() {
        $numJobs = $this->m()->queueMapper()->removeAll();
        return "cleared $numJobs queue\n";
    }


    public function infoAction() {
        $detailed = $this->getRequest()->getParam('verbose');
        $jobs = $this->m()->queueMapper()->fetchAll();

        $status_counts = array();

        $str = '';

        foreach($jobs as $job) {
            if ($detailed) {
                $executed = $job->getExecuted();
                if ($executed)
                    $executed = $executed->format('c');
                $str .= sprintf("id=%s date=%s status-string=%s status=%s %s\n",
                    $job->getId(), $executed, $job->getStatusString(), $job->getStatus(), $job->getMessage() );
            }
            $status_counts[$job->getStatus()]++;
        }

        foreach ($status_counts as $status => $count) {
            $str .= sprintf("status=%s(%s) count=%s\n",
                DefaultJob::statusToString($status), $status, $count );
        }

        $consoleModel = new ConsoleModel();
        $consoleModel->setResult($str);
        return $consoleModel;
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
