<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
/**
 * Description of RegionController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class IndexController extends BaseController {
    
    public function indexAction() {
        $regions = $this->regionMapper()->fetchDistinctRegionNames();
       
        return new ViewModel( array( 'regionNames' => $regions ) );
    }
    
    public function neighborhoodMapper() {
        return $this->getServiceLocator()
                        ->get('Application\Mapper\Neighborhood');
    }
    
    public function testExceptionLoggingAction() {
        throw new \Exception(
                    'this is a test in IndexController\testExceptionLogging');
    }
}

?>
