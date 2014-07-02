<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Entity\Neighborhood;
use Application\Entity\NeighborhoodPolygon;
use Application\Entity\Region;
use Application\Entity\WhathoodUser;
use Application\Model\EmailMessageBuilder;
use Application\Spatial\PHP\Types\Geometry\FeatureCollection;
use Application\Spatial\PHP\Types\Geometry\GeometryCollection;
use Application\Spatial\PHP\Types\Geometry\Point;
use Application\Spatial\PHP\Types\Geometry\LineString;
use Application\Spatial\PHP\Types\Geometry\Polygon;
use Application\Doctrine\ORM\Query\NeighborhoodPolygonQueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
/**
 * Description of NeighborhoodController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodPolygonController extends BaseController
{
    /**
     * we want to page through the neighborhoods
     * @return \Zend\View\Model\ViewModel
     * @throws \InvalidArgumentException
     */
    public function pageAction() {
        
        $center  = $this->getUriParameter('center');        
        $pageNum = $this->getUriParameter('page');
        
        list($lat,$lng) = explode(',',$center);

        $qb = new NeighborhoodPolygonQueryBuilder( 
                $this->getServiceLocator()->get('mydoctrineentitymanager')
                    ->createQueryBuilder() 
            );
        $qb->setLatLng( $lat, $lng );

        $query = $qb->getQuery();

        $uriParams = array(
            'center' => $center
        );
        
        if( empty($pageNum) )
            $pageNum = 1;

        $paginator = new \Application\Model\NeighborhoodPaginator(
                        new \Application\Model\NeighborhoodPaginatorAdapter($query)
                );
        $paginator->setDefaultItemCountPerPage(1);
        $paginator->setCurrentPageNumber($pageNum);
        $paginator->setUriParams($uriParams);
        
        $viewModel = $this->getViewModel( array(
            'paginator' => $paginator,
            'center' => $center
        ));
        $viewModel->setTemplate('/application/neighborhood-polygon/neighborhood_polygon_page.phtml');
        return $viewModel;
    }
    
    public function showAction() {
        $regionName = $this->getEvent()->getRouteMatch()->getParam('region');
        $neighborhoodName = $this->getEvent()->getRouteMatch()->getParam('neighborhood');

        $neighborhoods = $this->neighborhoodMapper()
                ->byNeighborhoodName( $neighborhoodName, $regionName );
        
        $viewModel = new ViewModel( array( 
            'neighborhoods' => $neighborhoods,
            'neighborhoodName' => $neighborhoodName,
            'regionName' => $regionName ) );
        
        $viewModel->setTemplate('application/neighborhood/show-many.phtml');
        
        return $viewModel;
    }
    
    public function addAction() {
        $regionName = $this->params()->fromQuery('region_name');
        
        /*
         * we need a region name, NEED IT, redirect region-chose to get it
         */
        if( empty( $regionName ) ) {
            $viewModel = new ViewModel( array( 
                    'regionNames' => $this->regionMapper()->fetchDistinctRegionNames(),
                    'uriString' => $this->url()->fromRoute('neighborhood_add')
                ));
            $viewModel->setTemplate('application/region/region_choose.phtml');
            return $viewModel;
        }
        /*
         * we know the region name
         */
        else {
            $region = $this->regionMapper()->byName( $regionName );
            $neighborhoodPolygon = new NeighborhoodPolygon();
            $neighborhoodPolygon->setRegion( $region );

            $form = new \Application\Form\NeighborhoodPolygonForm();
            $form->bind($neighborhoodPolygon);
            $form->get('submit')->setAttribute('onclick', 'return submitAddForm();' );

            /*
             * first time through the page, set up the initial form
             */
            if( !$this->getRequest()->isPost() ) {
                /*
                 * get a list of unique neighborhood names for the region
                 */
                $neighborhoods = $this->neighborhoodMapper()
                            ->byRegionName($regionName);

                $viewModel = new ViewModel( array(
                    'form'          => $form, 
                    'editable'      => true,
                    'currentNeighborhoods' => $neighborhoods,
                    'region'        => $region
                ));
                $viewModel->setTemplate(
                        'application/neighborhood-polygon/add-edit.phtml');
                return $viewModel;
            }

            /*
             * process form data
             */
            else {

                $form->setData( $this->getRequest()->getPost() );

                if( $form->isValid() ) {
                    $saveMessage = 'It takes a lot to regenerate the ' 
                            . $neighborhoodPolygon->getNeighborhood()->getName() 
                            . ' heatmap.  Your neighborhood should be added to it'
                            . ' within the hour';

                    $polygonGeoJsonString = $form->get('polygonGeoJson')
                                                                    ->getValue();

                    if( empty( $polygonGeoJsonString ) ) 
                        throw new \InvalidArgumentException( 
                                                "polygonGeoJson must be defined" );

                    $this->createPolygon($neighborhoodPolygon, 
                                                            $polygonGeoJsonString );

                    $neighborhoodPolygon->getNeighborhood()->setRegion( new Region(
                            array( 'name' => $regionName ) ) );

                    /*
                     * save the user with the neighborhood
                     */
                    $whathoodUser = $this->getAuthenticationService()
                                                        ->getWhathoodUser();

                    $neighborhoodPolygon->setWhathoodUser( $whathoodUser );

                    $this->neighborhoodPolygonMapper()
                            ->save( $neighborhoodPolygon );

                    $logger = $this->getServiceLocator()->get('logger');
                    $logger->info( "neighborhood has been added" );

                    $emailer = $this->getServiceLocator()->get('emailer');
                    $emailer->send(
                        $subject = $neighborhoodPolygon->getNeighborhood()->getName() 
                            . ' polygon added by ' . $whathoodUser->getUserName(),
                        EmailMessageBuilder::neighborhoodAdd($neighborhoodPolygon)
                    );
                    $form->bind( $neighborhoodPolygon );
                    //\Zend\Debug\Debug::dump( $form->get('neighborhoodPolygon')->get('neighborhood')->get('region') );
                    //exit;
                    $viewModel = new ViewModel( array( 
                        'form' => $form,
                        'saveMessage' =>  $saveMessage
                    ));

                    $viewModel->setTemplate(
                            '/application/neighborhood-polygon/view.phtml');
                    return $viewModel;
                } // end if the form is valid
                else {
                    $neighborhoods = $this->neighborhoodMapper()
                            ->byRegionName($regionName);
                }

                $viewModel = new ViewModel( array(
                    'form' => $form,
                    'currentNeighborhoods' => $neighborhoods) );
                $viewModel->setTemplate(
                        'application/neighborhood-polygon/add-edit.phtml');
                return $viewModel;
            }
        } // end else we know what region we're using
    }
    
    public function byIdAction() {

        $format = $this->getUriParameter('format');
        $id     = $this->getUriParameter('neighborhood_id');
        
        if( empty($id) ) {
            throw new \InvalidArgumentException('id may not be empty');
        }
        
        try {
            $neighborhoodPolygon = $this->neighborhoodPolygonMapper()->byId($id);
        } catch( \Doctrine\ORM\NoResultException $e ) {
            $viewModel = new ViewModel( array(
                'message'   => 'No Neighborhood exists with id ' . $id
            ));
            $viewModel->setTemplate('application/neighborhood/error_no_neighborhood.phtml');
            return $viewModel;
        }
        
        /*
         * return JSON format
         */
        if( $format === 'json' ) {
            return new JsonModel( $neighborhoodPolygon->toGeoJsonArray() );
        }
        
        /*
         * return HTML
         */
        else {
            $form = new \Application\Form\NeighborhoodPolygonForm($noedit = true);
            $form->bind($neighborhoodPolygon);
            $viewModel = $this->getViewModel( array(
                        'form' => $form,
                        'editable'  => false 
                    ));
            $viewModel->setTemplate(
                    '/application/neighborhood-polygon/view.phtml');
            return $viewModel;
        }
    }
    
    public function byUserNameAction() {
        
        $userName = $this->getUriParameter('whathood_user_name');
        
        if( empty( $userName ) )
            throw new \InvalidArgumentException('user id may not be null');

        $neighborhoods = $this->neighborhoodPolygonMapper()->byUserName($userName);
        
        if( empty( $neighborhoods ) ) {
            $viewModel = new ViewModel( array(
                'message' => 'You do not have any neighborhoods'
            ));
            $viewModel->setTemplate('application/show-message.phtml');
            return $viewModel;
        }
        $viewModel = new ViewModel( array('neighborhoods' => $neighborhoods ) );
        $viewModel->setTemplate('application/neighborhood/list.phtml');
        
        return $viewModel;
    }
    
    public function byRegionAction() {
        
        $regionId = $this->params()->fromQuery('region_id');
        $format = $this->getUriParameter('format');
        
        $mapper = $this->getServiceLocator()
                    ->get('Application\Mapper\Neighborhood');
        
        if( !empty( $regionId ) ) {
            $neighborhoods = $mapper->getByRegionId( $regionId );
        }
        
        $featureCollection = new FeatureCollection();
        foreach( $neighborhoods as $n ) {
            $featureCollection->addFeature( $n );
        }
        
        if( $format == 'json' ) {
            return new JsonModel( $featureCollection->toGeoJsonArray() );
        }
        $viewModel = new ViewModel( array( 'neighborhoods' => $neighborhoods ) );
        $viewModel->setTemplate( 'application/neighborhood/show-many.phtml');
        return $viewModel;
    }
    
    
    /*
     * from the latLngJson, we need to create a polygon object
     */
    public function createPolygon(NeighborhoodPolygon $neighborhood, $polygonGeoJson) {

        $polygonArray = \Zend\Json\Json::decode($polygonGeoJson,\Zend\Json\Json::TYPE_ARRAY);

        //\Zend\Debug\Debug::dump( $polygonArray );
        $lineStringArray = $polygonArray['geometry']['coordinates'];
        //\Zend\Debug\Debug::dump( $lineStringArray );
        //exit;
        $ring = array();
        
        foreach( $lineStringArray as $lineString ) {
            foreach( $lineString as $point ) {
                $ring[] = new Point( $point[1], $point[0] );
            }
        }
//die( \Zend\Debug\Debug::dump( $ring ) );
        $myLineString = new LineString( $ring );
        $myLineString->close();
        
        //\Zend\Debug\Debug::dump( $myLineString );
        //exit;
        $neighborhood->setPolygon(
                new Polygon( $rings = array($myLineString))
        );
    }
    
    public function deleteAction() {
        
        $user = $this->getLoggedInWhathoodUser();
        $neighborhoodId = $this->getUriParameter('id');
        $confirmedDelete = $this->getUriParameter('confirmed');
        /*
         * have user confirm delete
         */
        if( empty($confirmedDelete) || $confirmedDelete !== 'yes' ) {
            $viewModel = new ViewModel( array( 
                    'neighborhoodId' => $neighborhoodId
                ));
            $viewModel->setTemplate('application/neighborhood/neighborhood_confirm_delete.phtml');
            return $viewModel;
        }
        /*
         * make sure this neighborhood belongs to the user
         */
        $neighborhood = $this->neighborhoodMapper()->byId($neighborhoodId);
        $neighborhood->setDeleted(true);
        $this->neighborhoodMapper()->update( $neighborhood );
    }
}

?>
