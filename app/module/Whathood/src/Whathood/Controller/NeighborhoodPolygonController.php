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
use Whathood\Doctrine\ORM\Query\NeighborhoodPolygonQueryBuilder;
/**
 * Description of NeighborhoodController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodPolygonController extends BaseController
{
    public function showRegionAction() {

        $regionName = $this->getUriParameter('region_name');
        $createEventId  = $this->getUriParameter('create_event_id');
        $format     = $this->getUriParameter('format');

        if( empty( $createEventId ) ) {
            $createEventId = $this->neighborhoodPolygonMapper()->getLastCreateEventId();
        }

        $region = $this->regionMapper()->getRegionByName( $regionName );
        $json = $this->neighborhoodPolygonMapper()
                ->getNeighborhoodPolygonsAsGeoJsonByRegion( $region, $createEventId );

        if( $format == 'json' ) {
            $array = \Zend\Json\Json::decode( $json, \Zend\Json\Json::TYPE_ARRAY );
            return new JsonModel( $array );
        }
        else {
            $viewModel = new ViewModel( array(
                'json' => $json,
                'region' => $region
            ));

            return $viewModel;
        }
    }

    public function byCreateEventAction() {

        $createEventId = $this->getUriParameter('create_event_id');

        if( empty($neighborhoodPolygonId) )
            return new ErrorViewModel('id may not be empty');

        if( $format === 'json' ) {

            try {
                $neighborhoodPolygons = $this->neighborhoodPolygonMapper()->getNpByCreateEventId($createEventId);
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
