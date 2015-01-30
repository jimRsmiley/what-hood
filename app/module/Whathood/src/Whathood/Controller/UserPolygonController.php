<?php
namespace Whathood\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Whathood\Entity\Neighborhood;
use Whathood\Entity\UserPolygon;
use Whathood\Entity\Region;
use Whathood\Entity\WhathoodUser;
use Whathood\Model\EmailMessageBuilder;
use Whathood\Spatial\PHP\Types\Geometry\FeatureCollection;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Whathood\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Whathood\View\Model\ErrorViewModel;
use Whathood\Collection;
/**
 * Description of NeighborhoodController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class UserPolygonController extends BaseController
{
    /**
     * we want to page through the neighborhoods
     * @return \Zend\View\Model\ViewModel
     * @throws \InvalidArgumentException
     */
    public function pageAction() {
        
        $center  = $this->getUriParameter('center');        
        $pageNum = $this->getUriParameter('page');
		$neighborhood_id = $this->getUriParameter('neighborhood_id');

		$y = null; $x = null;

		$uriParams = array();
		if (!empty($center)) {
			list($y,$x) = explode(',',$center);
			$uriParams['center'] = $center;
		}

		if (!empty($neighborhood_id)) {
			$uriParams['neighborhood_id'] = $neighborhood_id;
		}

		$query = $this->userPolygonMapper()->getPaginationQuery(
			array(
				'x' => $x, 
				'y' => $y,
				'neighborhood_id' => $neighborhood_id
			)
		);

        if( empty($pageNum) )
            $pageNum = 1;

        $paginator = new \Whathood\Model\UserPolygonPaginator(
                        new \Whathood\Model\UserPolygonPaginatorAdapter($query)
                );
        $paginator->setDefaultItemCountPerPage(1);
		$paginator->setBaseUrl('/whathood/user-polygon');
        $paginator->setCurrentPageNumber($pageNum);
        $paginator->setUriParams($uriParams);
        
        $viewModel = $this->getViewModel( array(
            'paginator' => $paginator,
            'center' => $center
        ));
        $viewModel->setTemplate('/whathood/user-polygon/user_polygon_page.phtml');
        return $viewModel;
    }
	
	/**
     * we want to page through the neighborhoods
     * @return \Zend\View\Model\ViewModel
     * @throws \InvalidArgumentException
     */
    public function pageCenterAction() {
        
        $center  = $this->getUriParameter('center');        
        $pageNum = $this->getUriParameter('page');
        
        list($lat,$lng) = explode(',',$center);
        
		$query = $this->userPolygonMapper()->getPaginationQuery(
			array(
				$x => $lng, 
				$y => $lat
			)
		);

        $uriParams = array(
            'center' => $center
        );
        
        if( empty($pageNum) )
            $pageNum = 1;

        $paginator = new \Whathood\Model\UserPolygonPaginator(
                        new \Whathood\Model\UserPolygonPaginatorAdapter($query)
                );
        $paginator->setDefaultItemCountPerPage(1);
		$paginator->setBaseUrl('/n');
        $paginator->setCurrentPageNumber($pageNum);
        $paginator->setUriParams($uriParams);
        
        $viewModel = $this->getViewModel( array(
            'paginator' => $paginator,
            'center' => $center
        ));
        $viewModel->setTemplate('/whathood/user-polygon/user_polygon_page.phtml');
        return $viewModel;
    }
    
    /**
     * we want to page through the neighborhoods
     * @return \Zend\View\Model\ViewModel
     * @throws \InvalidArgumentException
     */
    public function pageListAction() {
		$itemCountPerPage = 10;
	   	
        $pageNum = $this->getUriParameter('page');
        
        $query = $this->userPolygonMapper()->getPaginationQuery();

        if(empty($pageNum))
            $pageNum = 1;

        $paginator = new \Whathood\Model\UserPolygonPaginator(
                        new \Whathood\Model\UserPolygonPaginatorAdapter($query)
                );
		$paginator->setBaseUrl('/whathood/user-polygon/page-list');
        $paginator->setDefaultItemCountPerPage($itemCountPerPage);
        $paginator->setCurrentPageNumber($pageNum);
        
        $viewModel = $this->getViewModel( array(
			'paginator' => $paginator,
        ));
        return $viewModel;
	}

    public function byNeighborhoodNameAction() {
        $regionName         = $this->getUriParameter('region');
        $neighborhoodName   = $this->getUriParameter('neighborhood');

        if( empty( $regionName ) )
            return new ErrorViewModel( array( 'message' => 'region must be defined' ) );
        
        if( empty( $neighborhoodName ) )
            return new ErrorViewModel( array( 'message' => 'neighborhood must be defined' ) );
        
        $neighborhoods = $this->neighborhoodMapper()
                ->getNeighborhoodByName( $neighborhoodName, $regionName );
        
        $viewModel = new ViewModel( array( 
            'neighborhoods' => $neighborhoods,
            'neighborhoodName' => $neighborhoodName,
            'regionName' => $regionName ) );
        
        $viewModel->setTemplate('whathood/user-polygon/show-many.phtml');
        
        return $viewModel;
    }
    
    public function byUserIdAction() {
        $userId = $this->getUriParameter('i');
        $format = $this->getUriParameter('format');
        
        if( empty($userId) ) {
            return new ErrorViewModel( "user_id must be defined" );
        }
        
        if( 'json' === $format ) {
            $userPolygonJson = $this->userPolygonMapper()->userPolygonsByUserIdAsGeoJson( $userId );
        }
        else {
            return new ViewModel();
        }
    }
    public function addAction() {
        $regionName = $this->params()->fromQuery('region_name');
        
        /*
         * we need a region name, NEED IT, redirect region-chose to get it
         */
        if( empty( $regionName ) ) {
            $viewModel = new ViewModel( array( 
                    'regionNames' => $this->regionMapper()->fetchDistinctRegionNames(),
                    'uriString' => $this->url()->fromRoute('user_polygon_add')
                ));
            $viewModel->setTemplate('whathood/region/region_choose.phtml');
            return $viewModel;
        }

        /*
         * we know the region name
         */
        else {
            $region = $this->regionMapper()->getRegionByName( $regionName );
            $userPolygon = new UserPolygon();
            $userPolygon->setRegion( $region );

            $form = new \Whathood\Form\NeighborhoodPolygonForm();
            $form->bind($userPolygon);
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
                        'whathood/user-polygon/add-edit.phtml');
                return $viewModel;
            }

            /*
             * esle, we've been here before, so process the form data
             */
            else {

                $form->setData( $this->getRequest()->getPost() );

                if( $form->isValid() ) {
                    $saveMessage = 'It takes a lot to regenerate the ' 
                            . $userPolygon->getNeighborhood()->getName() 
                            . ' heatmap.  Your neighborhood should be added to it'
                            . ' within the hour';

                    $polygonGeoJsonString = $form->get('polygonGeoJson')
                                                                    ->getValue();

                    if( empty( $polygonGeoJsonString ) ) 
                        throw new \InvalidArgumentException( 
                                                "polygonGeoJson must be defined" );

                    $this->createPolygon($userPolygon, 
                                                            $polygonGeoJsonString );

                    $userPolygon->getNeighborhood()->setRegion( new Region(
                            array( 'name' => $regionName ) ) );

                    /*
                     * save the user with the neighborhood
                     */
                    $whathoodUser = $this->getAuthenticationService()
                                                        ->getWhathoodUser();

                    $userPolygon->setWhathoodUser( $whathoodUser );

                    
                    $userPolygon->getPolygon()->setSRID(4326);
                    $this->userPolygonMapper()
                            ->save( $userPolygon );
                    
                    
                    
                    
                    $logger = $this->getServiceLocator()->get('logger');
                    
                    $logger->info( "neighborhood has been added" );

                    $form->bind( $userPolygon );
                    
                    $viewModel = new ViewModel( array( 
                        'form' => $form,
                        'saveMessage' =>  $saveMessage
                    ));

                    $viewModel->setTemplate(
                            '/whathood/user-polygon/view.phtml');
                    return $viewModel;

                } // end if the form is valid
                
                /*
                    nope the form wasn't valid
                */
                else {
                    $neighborhoods = $this->neighborhoodMapper()
                            ->byRegionName($regionName);
                }

                $viewModel = new ViewModel( array(
                    'form' => $form,
                    'currentNeighborhoods' => $neighborhoods) );
                $viewModel->setTemplate(
                        'whathood/user-polygon/add-edit.phtml');
                return $viewModel;
            }
        } // end else we know what region we're using
    }
    
    public function byIdAction() {

        $format = $this->getUriParameter('format');
        $id     = $this->getUriParameter('user_polygon_id');
        
        if( empty($id) ) {
            throw new \InvalidArgumentException('id may not be empty');
        }
        
        try {
            $userPolygon = $this->userPolygonMapper()->byId($id);
        } catch( \Doctrine\ORM\NoResultException $e ) {
            $viewModel = new ViewModel( array(
                'message'   => 'No Neighborhood exists with id ' . $id
            ));
            $viewModel->setTemplate('whathood/neighborhood/error_no_neighborhood.phtml');
            return $viewModel;
        }
        
        /*
         * return JSON format
         */
        if( $format === 'json' ) {
            return new JsonModel( $userPolygon->toArray() );
        }
        
        /*
         * return HTML
         */
        else {
            $form = new \Whathood\Form\NeighborhoodPolygonForm($noedit = true);
            $form->bind($userPolygon);
            $viewModel = $this->getViewModel( array(
                        'form' => $form,
                        'editable'  => false 
                    ));
            $viewModel->setTemplate(
                    '/whathood/user-polygon/view.phtml');
            return $viewModel;
        }
    }
    
    public function byUserNameAction() {
        
        $userName = $this->getUriParameter('whathood_user_name');
        
        if( empty( $userName ) )
            throw new \InvalidArgumentException('user id may not be null');

        $neighborhoods = $this->userPolygonMapper()->byUserName($userName);
        
        if( empty( $neighborhoods ) )
            return new ErrorViewModel( array(
                'message' => 'You do not have any neighborhoods'
            ));

        $viewModel = new ViewModel( array('neighborhoods' => $neighborhoods ) );
        $viewModel->setTemplate('whathood/neighborhood/list.phtml');
        
        return $viewModel;
    }
    
    /*
    public function byRegionAction() {
        
        $regionId = $this->params()->fromQuery('region_id');
        $format = $this->getUriParameter('format');
        
        $mapper = $this->getServiceLocator()
                    ->get('Whathood\Mapper\Neighborhood');
        
        if( !empty( $regionId ) ) {
            $neighborhoods = $mapper->getByRegionId( $regionId );
        }
        
        $featureCollection = new FeatureCollection();
        foreach( $neighborhoods as $n ) {
            $featureCollection->addFeature( $n );
        }
        
        if( $format == 'json' ) {
            return new JsonModel( $featureCollection->toArray() );
        }
        $viewModel = new ViewModel( array( 'neighborhoods' => $neighborhoods ) );
        $viewModel->setTemplate( 'whathood/neighborhood/show-many.phtml');
        return $viewModel;
    }*/
    
    
    /*
     * from the latLngJson, we need to create a polygon object
     */
    public function createPolygon(UserPolygon $neighborhood, $polygonGeoJson) {

        $polygonArray = \Zend\Json\Json::decode($polygonGeoJson,\Zend\Json\Json::TYPE_ARRAY);

        //\Zend\Debug\Debug::dump( $polygonArray );
        $lineStringArray = $polygonArray['geometry']['coordinates'];
        //\Zend\Debug\Debug::dump( $lineStringArray );
        //exit;
        $ring = array();
        
        foreach( $lineStringArray as $lineString ) {
            foreach( $lineString as $point ) {
                $ring[] = new Point( $point[0], $point[1] );
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
            $viewModel->setTemplate('whathood/neighborhood/neighborhood_confirm_delete.phtml');
            return $viewModel;
        }
        /*
         * make sure this neighborhood belongs to the user
         */
        $neighborhood = $this->neighborhoodMapper()->byId($neighborhoodId);
        $neighborhood->setDeleted(true);
        $this->neighborhoodMapper()->update( $neighborhood );
    }

    public function consoledefaultAction() {

        $collection = new Collection($this->userPolygonMapper()->fetchAll());
        print $collection->__toString();
    }
}

?>
