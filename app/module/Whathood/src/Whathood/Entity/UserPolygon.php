<?php

namespace Whathood\Entity;

// need this even though Netbeans says you don't
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodHydrator;
use Zend\Config\Config;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
/**
 * @ORM\Entity
 * @ORM\Table(name="user_polygon")
 */
class UserPolygon extends \ArrayObject {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="Neighborhood",
     *      inversedBy="userPolygons")
     * @ORM\JoinColumn(name="neighborhood_id", referencedColumnName="id",
     *      nullable=false)
     */
    protected $neighborhood = null;

    /**
     * @ORM\ManyToOne(targetEntity="Region",
     *      inversedBy="neighborhoodPolygons")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id",
     *      nullable=false)
     */
    protected $region = null;

    /**
     * @ORM\Column(name="date_time_added")
     */
    protected $dateTimeAdded = null;

    /**
     * @ORM\Column(name="polygon",type="polygon",nullable=false)
     */
    protected $polygon = null;

    /**
    *  @ORM\Column(name="is_deleted",type="boolean",nullable=true)
    **/
    protected $_is_deleted = null;

    /**
     * @ORM\ManyToOne(targetEntity="WhathoodUser",
     *      inversedBy="user_polygons",cascade="persist")
     * @ORM\JoinColumn(name="whathood_user_id", referencedColumnName="id",
     *      nullable=false)
     **/
    protected $whathood_user = null;

    /**
     * @ORM\ManyToMany(targetEntity="NeighborhoodPolygon",inversedBy="user_polygons")
     * @ORM\JoinTable(name="up_np")
     **/
    protected $neighborhood_polygons;

    public function getIsDeleted() {
        return $this->_is_deleted;
    }

    public function setIsDeleted($is_deleted) {
        $this->_is_deleted = $is_deleted;
    }

    // sugar
    public function isDeleted() {
        return $this->getIsDeleted();
    }

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

    public function getRegion() {
        return $this->region;
    }

    public function setRegion( $data ) {
        if( is_array( $data ) )
            $this->region = new Region( $data );
        else if( $data instanceof \Whathood\Entity\Region )
            $this->region = $data;
        else
            throw new \InvalidArgumentException(
                                'data must be array or Region object');
    }

    public function getNeighborhoodPolygons() {
        return $this->neighborhood_polygons;
    }

    public function setNeighborhoodPolygons($neighborhood_polygons) {
        $this->neighborhood_polygons = $neighborhood_polygons;
    }

    public function getWhathoodUser() {
        return $this->whathood_user;
    }

    public function setWhathoodUser( $data ) {
        if( is_array( $data ) )
            $this->whathood_user = new WhathoodUser( $data );
        else if( $data instanceof \Whathood\Entity\WhathoodUser )
            $this->whathood_user = $data;
        else
            throw new \InvalidArgumentException('data must be array or User object');
    }

    public function getDateTimeAdded() {
        return $this->dateTimeAdded;
    }

    public function setDateTimeAdded($dateTimeAdded) {
        $this->dateTimeAdded = $dateTimeAdded;
    }

    public function setPolygon(  $data ) {
        if( $data instanceof Polygon )
            $this->polygon = $data;
        else if( is_array( $data ) ) {
            $ring = array();
            foreach( $data['points'] as $point ) {
                $ring[] = new Point( $point['x'],$point['y'] );
            }
            // close the ring
            $ring[] = $ring[0];

            $this->polygon = new Polygon(array($ring));
        }
    }

    public function getPolygon() {
        return $this->polygon;
    }
    public function __construct( $array = null ) {

        if( $array !== null ) {
            $hydrator = new ClassMethodHydrator();
            $hydrator->hydrate( $array, $this );
        }
    }


    public static function polygonToGeoJsonArray( $polygon ) {

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

    /**
     * if the dateTimeAdded timestamps don't exist, create them using the time now
     * @param \Whathood\Mapper\ArrayCollection $neighborhoodPolygons
     */
    public static function setTimes(
                                    ArrayCollection $neighborhoodPolygons ) {

        if( empty( $neighborhoodPolygons ) )
            return;

        foreach( $neighborhoodPolygons as $neighborhoodPolygon )
            $neighborhoodPolygon->setDateTimeAdded( date("Y-m-d H:i:s") );

    }

    /**
     * if the dateTimeAdded timestamps don't exist, create them using the time now
     * @param \Whathood\Mapper\ArrayCollection $neighborhoodPolygons
     */
    public static function setNeighborhoods(
                                    $neighborhoodPolygons,
                                    Neighborhood $neighborhood ) {

        if( empty( $neighborhoodPolygons ) )
            return;

        foreach( $neighborhoodPolygons as $neighborhoodPolygon )
            $neighborhoodPolygon->setNeighborhood( $neighborhood );
    }

    public function toArray(array $opts=null) {
		if (empty($opts)) $opts = array();
        $config = new Config($opts);

        if($config->get('strings-only')) {
            $array=array(
                'id' => $this->getId(),
                'neighborhood_name' => $this->getNeighborhood()->getName(),
                'datetime_added' => $this->getDateTimeAdded()
            );
        }
        else {
			$hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
            $array = $hydrator->extract($this);
            unset( $array['iterator_class'] );
            unset( $array['iterator']);
            unset( $array['flags']);
            unset( $array['array_copy']);
            unset( $array['polygon']);

            // for geojson, we want to merge the polygon
            $array = array_merge( $array, $this->polygonToGeoJsonArray( $this->polygon ) );

            if( $this->getNeighborhood() != null ) {
                $array['neighborhood'] = $this->getNeighborhood()->toArray();
            }

            if( $this->getWhathoodUser() != null ) {
                $array['user'] = $this->getWhathoodUser()->toArray();
            }
        }
        return $array;
    }

    public function __toString() {
        $str = "";
        foreach($this->toArray(array('strings-only'=>true)) as $key => $value) {
            if(is_string($value) or is_numeric($value))
                $str .= "$key: $value ";
        }

        return $str;
    }
}
?>
