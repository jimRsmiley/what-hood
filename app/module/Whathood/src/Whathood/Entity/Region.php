<?php
namespace Whathood\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Description of Region
 * @ORM\Entity
 * @ORM\Table(name="region",uniqueConstraints={
 *              @ORM\UniqueConstraint(name="region_name_idx",
 *                  columns={"name"})})
 */
class Region extends \ArrayObject {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id = null;

    /**
     * @ORM\Column(name="name",type="string")
     */
    protected $name = null;

    /**
     * @ORM\OneToMany(targetEntity="UserPolygon",
     *                              mappedBy="region",cascade="persist")
     */
    protected $neighborhoodPolygons = null;

    public function __construct($data=null) {
        if( !empty($data) ) {
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
            $hydrator->hydrate( $data, $this );
        }
    }

    public function getName() {
        return $this->name;
    }

    public function setName( $name ) {
        $this->name = $name;
    }

    public function setNeighborhoodBoundarys( $arrayCollection ) {
        $this->neighborhoodPolygons = $arrayCollection;
    }

    public function getNeighborhoodBoundarys() {
        return $this->neighborhoodPolygons;
    }

    public function getId() {
        return $this->id;
    }

    public function setId( $id ) {
        $this->id = $id;
    }

    public function toArray() {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName()
        );
    }
}
?>
