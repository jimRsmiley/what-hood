<?php

namespace Whathood\Entity;

// need this even though Netbeans says you don't
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodHydrator;
use Whathood\Spatial\PHP\Types\Geometry\Polygon as WhathoodPolygon;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
/**
 * @ORM\Entity
 * @ORM\Table(name="neighborhood_polygon")
 */
class NeighborhoodPolygon extends \ArrayObject {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="Neighborhood",
     *      inversedBy="neighborhoodPolygons")
     * @ORM\JoinColumn(name="neighborhood_id", referencedColumnName="id",
     *      nullable=false)
     */
    protected $neighborhood = null;

    /**
     * @ORM\Column(name="polygon",type="geometry",nullable=false)
     */
    protected $geometry = null;

    /**
     * @ORM\Column(name="created_at",type="datetimetz",nullable=false)
     */
    protected $created_at = null;

    /**
     * @ORM\Column
     */
    protected $grid_resolution = null;

    /**
     * @ORM\Column
     */
    protected $target_precision = null;

    /**
     * @ORM\ManyToMany(targetEntity="UserPolygon",mappedBy="neighborhood_polygons",cascade={"persist"})
     */
    protected $user_polygons = null;

    public function getId() {
        return $this->id;
    }

    public function setId( $id ) {
        $this->id = $id;
    }

    public function getNeighborhood() {
        return $this->neighborhood;
    }

    public function setNeighborhood( $data ) {
        if( is_array( $data ) )
            $this->neighborhood = new Neighborhood( $data );
        else if( $data instanceof \Whathood\Entity\Neighborhood )
            $this->neighborhood = $data;
        else
            throw new \InvalidArgumentException(
                                'data must be array or Neighborhood object');
    }

    public function getUserPolygons() {
        return $this->user_polygons;
    }

    public function setUserPolygons($user_polygons) {
        $this->user_polygons = $user_polygons;
    }

    public function getGeometry() {
        return $this->geometry;
    }
    //
    // sugar
    public function setGeom($geom) {
        $this->setGeometry($geom);
    }

    public function setGeometry(  $data ) {
        if( $data instanceof Polygon )
            $this->geometry = $data;
        else if( is_array( $data ) ) {
            $ring = array();
            foreach( $data['points'] as $point ) {
                $ring[] = new Point( $point['x'],$point['y'] );
            }
            // close the ring
            $ring[] = $ring[0];

            $this->geometry = new Polygon(array($ring));
        }
        else {
            throw new \InvalidArgumentException("did not get expected value for geometry");
        }
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    public function __construct(array $array = null ) {
        if( $array !== null ) {
            $hydrator = new ClassMethodHydrator();
            $hydrator->hydrate( $array, $this );
        }
    }

    public static function build(array $data) {
        $neighborhood_polygon = new static($data);
        if (null == $neighborhood_polygon->getCreatedAt())
            $neighborhood_polygon->setCreatedAt(new \DateTime());
        return $neighborhood_polygon;
    }

    public function userPolygonCount() {
        return count($this->getUserPolygons());
    }

    public function setGeoJson($geojson) {
        $polygon = \Whathood\Polygon::buildPolygonFromGeoJsonString($geojson,4326);
        $this->setGeometry($polygon);
    }

    public function getUserPolygonIds() {
        $ids = array();
        foreach($this->getUserPolygons() as $up) {
            $ids[] = $up->getId();
        }
        return $ids;
    }

    public function toArray(array $opts = null) {
        if ($opts == null)
            $opts = array();

        // for geojson, we want to merge the polygon
        $np_arr = $this->polygonToGeoJsonArray( $this->geometry );

        if( $this->getNeighborhood() != null )
            $np_arr['neighborhood'] = $this->getNeighborhood()->toArray();

        $np_arr['id'] = $this->getId();
        return $np_arr;
    }

    public static function polygonToGeoJsonArray( $polygon ) {

        if (empty($polygon))
            throw new \InvalidArgumentException("polygon may not be empty");
        $coordinates = array();

        foreach( $polygon->getRings() as $ring ) {
            array_push( $coordinates, $ring->toArray() );
        }

        $arr = array(
            'type'      => 'Polygon',
            'coordinates' => $coordinates
        );

        return $arr;
    }

    public static function fromGeoJsonArray( $array ) {

        $type = $array['type'];
        $coordinates = $array['coordinates'];

        $polygon = new Polygon( array( new LineString( $coordinates ) ) );

        return $polgyon;
    }

    /*
     * utility function that given an array of neighborhoods, returns a json
     * array
     */
    public static function neighborhoodsToJson( $neighborhoodArray ) {

        $jsonArray = array();
        foreach( $neighborhoodArray as $n )
            $jsonArray['neighborhoods'][] = $n->toArray();

        return  \Zend\Json\Json::encode($jsonArray);
    }

    /*
     * utility function that given an array of neighborhoods, returns a json
     * array
     */
    public static function jsonToNeighborhoodPolygons( $json ) {

        $array = \Zend\Json\Json::decode( $json, \Zend\Json\Json::TYPE_ARRAY );

        $neighborhoodPolygons = array();
        foreach( $array['neighborhoods'] as $neighborhoodArray ) {
            $neighborhoodPolygons[] = new UserPolygon( $neighborhoodArray );
        }
        return $neighborhoodPolygons;
    }
}
?>
