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
     * we want to page through the user polygons based on a center point
     *
     * @return \Zend\View\Model\ViewModel
     * @throws \InvalidArgumentException
     */
    public function pageCenterAction() {
        $base_url = '/user-neighborhood/page-center';
        $item_count_per_page = 1;

        $x  = $this->getUriParameter('x');
        $y  = $this->getUriParameter('y');
        $pageNum = $this->getUriParameter('page');

        if (empty($x) or empty($y))
            throw new \InvalidArgumentException("must specify x and y");


		$query = $this->m()->userPolygonMapper()->getPaginationQuery(
			array(
				'x' => $x,
				'y' => $y
			)
		);

        $uriParams = array(
            'x' => $x,
            'y' => $y
        );

        if( empty($pageNum) )
            $pageNum = 1;

        $paginator = new \Whathood\View\Paginator\UserPolygonPaginator(
                        new \Whathood\View\Paginator\UserPolygonPaginatorAdapter($query)
                );
        $paginator->setDefaultItemCountPerPage($item_count_per_page);
		$paginator->setBaseUrl($base_url);
        $paginator->setCurrentPageNumber($pageNum);
        $paginator->setUriParams($uriParams);

        $viewModel = $this->getViewModel( array('paginator' => $paginator,'x'=>$x,'y'=>$y));
        $viewModel->setTemplate('/whathood/user-polygon/page-id.phtml');
        return $viewModel;
    }

	/**
     * we want to page through the user polygons based on a center point
     *
     * @return \Zend\View\Model\ViewModel
     * @throws \InvalidArgumentException
     */
    public function pageNeighborhoodAction() {
        $base_url = '/user-neighborhood';
        $item_count_per_page = 1;

        $neighborhood_name  = $this->getUriParameter('neighborhood');
        $region_name        = $this->getUriParameter('region');
        $pageNum            = $this->getUriParameter('page');

        $this->logger()->info(
            sprintf("Controller/UserPolygon/page-neighborhood neighborhood_name=%s region_name=%s",
                $neighborhood_name,$region_name ));
        if (empty($neighborhood_name) or empty($region_name))
            throw new \InvalidArgumentException("must specify x and y");


        try {
            $neighborhood = $this->m()->neighborhoodMapper()->byName($neighborhood_name,$region_name);
        }
        catch(\Doctrine\ORM\NoResultException $e) {
            throw new \Exception("no neighborhood found with name $neighborhood_name, region $region_name");
        }

        $query = $this->m()->userPolygonMapper()->createByNeighborhoodQuery($neighborhood);

        $uriParams = array(
            'region' => $region_name,
            'neighborhood' => $neighborhood_name
        );

        if( empty($pageNum) )
            $pageNum = 1;

        // prep the paginator
        $paginator = new \Whathood\View\Paginator\UserPolygonPaginator(
                new \Whathood\View\Paginator\UserPolygonPaginatorAdapter($query)
        );
        $paginator->setDefaultItemCountPerPage($item_count_per_page);
		$paginator->setBaseUrl($base_url);
        $paginator->setCurrentPageNumber($pageNum);
        $paginator->setUriParams($uriParams);

        if (0 == $paginator->getTotalItemCount())
            throw new \Exception("no user polygons returned by paginator query");

        $viewModel = $this->getViewModel(
            array('paginator' => $paginator)
        );
        $viewModel->setTemplate('/whathood/user-polygon/page-id.phtml');
        return $viewModel;
    }
    /**
     * we want to page through all user polygons 10 at a time
     *
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
		$paginator->setBaseUrl('/user-neighborhood/page-list');
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
        return new ViewModel();
    }

    public function addPostAction() {

        if (!$this->getRequest()->isPost())
            throw new \Exception("addPostAction expects a POST");

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

        $this->m()->userPolygonMapper()->save( $userPolygon );

        $this->logger()->info(
            sprintf("saved user-polygon id=%s neighborhood=%s region=%s ip-address=%s",
                $userPolygon->getId(),
                $neighborhood->getName(),
                $region->getName(),
                $whathoodUser->getIpAddress()
            )
        );


        $this->pushEmailJob(
            \Whathood\View\MailMessageBuilder::buildNewUserPolygon($userPolygon)
        );

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
        $viewModel = $this->getViewModel( array(
                'user_polygon_id' => $id ));
        $viewModel->setTemplate(
                '/whathood/user-polygon/view.phtml');
        return $viewModel;
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

    public function listAction() {}

    public function consoledefaultAction() {

        $collection = new Collection($this->userPolygonMapper()->fetchAll());
        print $collection->__toString();
    }
}

?>
