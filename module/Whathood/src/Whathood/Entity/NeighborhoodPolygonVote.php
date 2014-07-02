<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Application\Model\Vote;
/**
 * @ORM\Entity
 * @ORM\Table(name="neighborhood_polygon_vote",uniqueConstraints={
 *              @ORM\UniqueConstraint(name="unique_user_neighborhood_polygon_vote_idx", 
 *                  columns={"whathood_user_id","neighborhood_polygon_id"})})
 */
class NeighborhoodPolygonVote extends \ArrayObject {
   
    public function __construct($array=null) {
        if( !empty($array) ) {
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
            $hydrator->hydrate( $array, $this );
        }
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id = null;
    
    public function getId() {
        return $this->id;
    }
    
    public function setId( $id ) {
        $this->id = $id;
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="NeighborhoodPolygon", inversedBy="neighborhoodVotes")
     * @ORM\JoinColumn(name="neighborhood_polygon_id", referencedColumnName="id",
     *                  nullable=false)
     */
    protected $neighborhoodPolygon = null;
    
    public function getNeighborhoodPolygon() {
        return $this->neighborhoodPolygon;
    }
    
    public function setNeighborhoodPolygon( $data ) {
        if( is_array( $data ) )
            $this->neighborhoodPolygon = new NeighborhoodPolygon( $data );
        else if( $data instanceof \Application\Entity\NeighborhoodPolygon )
            $this->neighborhoodPolygon = $data;
        else
            throw new \InvalidArgumentException(
                            'data must be array or NeighborhoodPolygon object');
    }
    
    public function isUp() {
        if( $this->getVote() == 1 )
            return true;
        return false;
    }
    
    public function getDirection() {
        if( $this->isUp() ) {
            return 'up';
        }
        else {
            return 'down';
        }
    }
    
    /**
     * @ORM\Column(name="vote")
     */
    protected $vote = null;
    
    public function getVote() {
        return $this->vote;
    }
    
    public function setVote( $vote ) {
        $this->vote = new Vote($vote);
    }
    
    /**
     * a neighborhood vote can have only one user, one use may have many votes
     * @ORM\ManyToOne(targetEntity="WhathoodUser",inversedBy="votes")
     * @ORM\JoinColumn(name="whathood_user_id",referencedColumnName="id")
     */
    protected $whathoodUser = null;
    
    public function getWhathoodUser() {
        return $this->whathoodUser;
    }
    
    public function setWhathoodUser( $data ) {
        if( is_array( $data ) )
            $this->whathoodUser = new WhathoodUser( $data );
        else if( $data instanceof \Application\Entity\WhathoodUser )
            $this->whathoodUser = $data;
        else
            throw new \InvalidArgumentException('data must be array or User object');
    }
    
    public function toArray() {
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
        
        $array = $hydrator->extract($this);
        unset( $array['iterator_class'] );
        unset( $array['iterator']);
        unset( $array['flags']);
        unset( $array['array_copy']);
        
        $array['whathoodUser'] = $this->getWhathoodUser()->toArray();
        $array['neighborhoodPolygon'] = array(
            'id' => $this->getNeighborhoodPolygon()->getId(),
            'neighborhood' => $this->getNeighborhoodPolygon()->getNeighborhood()->toArray()
        );
        return $array;
    }
    
    public static function votesToArray( $neighborhoodPolygonVotes ) {
        
        $array = array();
        foreach( $neighborhoodPolygonVotes as $vote ) {
            $array[] = $vote->toArray();
        }
        
        return $array;
    }
    
    public function __toString() {
        return \Zend\Debug\Debug::dump( $this, false );
    }
}
?>
