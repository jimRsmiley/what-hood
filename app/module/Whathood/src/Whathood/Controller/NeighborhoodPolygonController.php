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
use Whathood\Entity\NeighborhoodPolygon;
use Whathood\Model\EmailMessageBuilder;
use Whathood\Spatial\PHP\Types\Geometry\FeatureCollection;
use Whathood\Spatial\PHP\Types\Geometry\LineString;
use Whathood\Doctrine\ORM\Query\NeighborhoodPolygonQueryBuilder;

/**
 * Description of NeighborhoodController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodPolygonController extends BaseController
{
    /**
     * show neighborhood polygons by region
     *
     */
    public function showRegionAction() {
        $format     = $this->getUriParameter('format');
        $regionName = $this->getUriParameter('region_name');

        if( $format == 'json' ) {
            try {

                $region = $this->regionMapper()->getRegionByName( $regionName );
                $json = $this->neighborhoodPolygonMapper()
                        ->getNeighborhoodPolygonsAsGeoJsonByRegion( $region );

                $array = \Zend\Json\Json::decode( $json, \Zend\Json\Json::TYPE_ARRAY );
            }
            catch(\Exception $e) {
                $this->getResponse()->setStatusCode(400);
		$this->logger()->warn($e->getMessage());
                $array = array( 'msg' => 'an error occurred executing the request' );
            }
            return new JsonModel( $array );
        }
        else {
            $viewModel = new ViewModel( array(
                'region' => $region
            ));

            return $viewModel;
        }
    }

    public function byIdAction() {

        $format = $this->getUriParameter('format');
        $neighborhoodPolygonId     = $this->getUriParameter('id');

        if( empty($neighborhoodPolygonId) )
            return new ErrorViewModel('id may not be empty');

        if( $format === 'json' ) {

            try {
                $neighborhoodPolygon = $this->neighborhoodPolygonMapper()->getNpById($neighborhoodPolygonId);
            } catch( \Doctrine\ORM\NoResultException $e ) {
                return new ErrorViewModel( array(
                    'message'   => 'No Neighborhood exists with id ' . $neighborhoodPolygonId
                ));
            }
            return new JsonModel( $neighborhoodPolygon->toArray() );
        }

        /*
         * return HTML
         */
        else {
            return new ViewModel(array('neighborhoodPolygonId' => $neighborhoodPolygonId ) );
        }
    }

    public function byUserNameAction() {

        $userName = $this->getUriParameter('whathood_user_name');

        if( empty( $userName ) )
            return new ErrorViewModel( array( 'message' => 'user id may not be null' ) );

        $neighborhoods = $this->userPolygonMapper()->byUserName($userName);

        if( empty( $neighborhoods ) )
            return new ViewModel( array(
                'message' => 'You do not have any neighborhoods'
            ));

        $viewModel = new ViewModel( array('neighborhoods' => $neighborhoods ) );
        $viewModel->setTemplate('whathood/neighborhood/list.phtml');

        return $viewModel;
    }

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
        $myLineString = new LineString( $ring );
        $myLineString->close();

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
}

?>
