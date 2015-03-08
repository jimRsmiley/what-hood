<?php
namespace Whathood\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Whathood\Entity\Neighborhood;
use Whathood\Entity\UserPolygon;
use Whathood\Entity\Region;
use Whathood\Entity\WhathoodUser;
use Whathood\Model\EmailMessageBuilder;
use Whathood\Spatial\PHP\Types\Geometry\FeatureCollection;
use Whathood\Spatial\PHP\Types\Geometry\LineString;
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

        if (empty($center))
            throw new \InvalidArgumentException("must specify center");

        list($lat,$lng) = explode(',',$center);

		$query = $this->userPolygonMapper()->getPaginationQuery(
			array(
				'x' => $lng,
				'y' => $lat
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
        $viewModel->setTemplate('/whathood/user-polygon/page-id.phtml');
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

        if (!$this->getRequest()->isPost())
            return new ViewModel();

        $neighborhood_name  = $this->getRequest()->getPost('neighborhood_name');
        $region_name        = $this->getRequest()->getPost('region_name');
        $polygon_array       = $this->getRequest()->getPost('polygon_json');

        if (empty($polygon_array))
            throw new \InvalidArgumentException("polygon_json may not be empty");
        if (empty($neighborhood_name))
            throw new \InvalidArgumentException("neighborhood_name may not be empty");

        $neighborhood = new Neighborhood(array(
            'name' => $neighborhood_name ));
        $region = new Region( array(
            'name' => $region_name ));
        $polygon = \Whathood\Polygon::buildPolygonFromGeoJsonArray($polygon_array,$srid=4326);

        $whathoodUser = $this->getWhathoodUser();

        $userPolygon = new UserPolygon( array(
            'neighborhood' => $neighborhood,
            'polygon' => $polygon,
            'region' => $region,
            'whathoodUser' => $whathoodUser  ));

        $this->userPolygonMapper()->save( $userPolygon );

        $this->logger()->info( "user polygon added id(".$userPolygon->getId().")" );

        return new JsonModel( array(
            'status' => 'success',
            'user_polygon_id' => $userPolygon->getId() ));
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
