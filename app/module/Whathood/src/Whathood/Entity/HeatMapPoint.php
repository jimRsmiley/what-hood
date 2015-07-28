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
 * @ORM\Table(name="heatmap_point")
 */
class HeatMapPoint extends \ArrayObject {

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
     * @ORM\Column(name="point",type="geometry",nullable=false)
     */
    protected $point = null;

    /**
     * @ORM\Column(name="percentage",type="decimal",nullable=false)
     */
    protected $percentage = null;

    /**
     * @ORM\Column(name="created_at",type="datetimetz",nullable=false)
     */
    protected $created_at = null;

    public function getId() {
        return $this->id;
    }

    public function setId( $id ) {
        $this->id = $id;
    }

    public function getNeighborhood() {
        return $this->neighborhood;
    }

    public function setNeighborhood( Neighborhood $n ) {
        $this->neighborhood = $n;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    public function getPoint() {
        return $this->point;
    }

    public function setPoint($point) {
        $this->point = $point;
    }

    public function getPercentage() {
        return $this->percentage;
    }

    public function setPercentage($percentage) {
        $this->percentage = $percentage;
    }

    public function __construct(array $array = null ) {
        if( $array !== null ) {
            $hydrator = new ClassMethodHydrator();
            $hydrator->hydrate( $array, $this );
        }
    }

    public static function build(array $array) {
        $hmp = new HeatMapPoint($array);
        $hmp->setCreatedAt(new \DateTime());
        return $hmp;
    }
}
?>
