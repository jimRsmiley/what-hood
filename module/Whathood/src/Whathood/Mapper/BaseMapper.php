<?php
namespace Whathood\Mapper;
/**
 * Description of BaseMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
abstract class BaseMapper {
    
    protected $sm;
    protected $em;
    
    protected $neighborhoodPolygonMapper;
    protected $neighborhoodMapper;
    protected $regionMapper;
    protected $whathoodUserMapper;
    
    public function __construct( $serviceManager, $doctrineEntityManager ) {
        
        if( !($serviceManager instanceof \Zend\ServiceManager\ServiceManager) )
            throw new \InvalidArgumentException(
                                    "serviceManager must be of type dfafdaf");
        
        $this->sm = $serviceManager;
        $this->em = $doctrineEntityManager;
    }
    

    
    public function flush() {
        $this->em->flush();
    }
    
    public function clear() {
        $this->em->clear();
    }
    
    public function neighborhoodPolygonMapper() {
        if( $this->neighborhoodPolygonMapper == null )
            $this->neighborhoodPolygonMapper = 
                $this->sm->get('Whathood\Mapper\UserPolygonMapper');
        
        return $this->neighborhoodPolygonMapper;
    }
    
    public function neighborhoodMapper() {
        if( $this->neighborhoodMapper == null )
            $this->neighborhoodMapper = 
                $this->sm->get('Whathood\Mapper\NeighborhoodMapper');
        
        return $this->neighborhoodMapper;
    }
    
    public function regionMapper() {
        if( $this->regionMapper == null )
            $this->regionMapper = $this->sm->get('Whathood\Mapper\RegionMapper');
        return $this->regionMapper;
    }
    
    public function whathoodUserMapper() {
        if( $this->whathoodUserMapper == null )
            $this->whathoodUserMapper = $this->sm->get('Whathood\Mapper\WhathoodUserMapper');
        return $this->whathoodUserMapper;
    }
    
    public function getCurrentDateTimeAsString() {
        return $this->getCurrentDateTime()->format('Y-m-d H:i:s');;
    }
    
    public function getCurrentDateTime() {
        return new \DateTime('now');
    }
    
    public function detach( $entity ) {
        $this->em->detach( $entity );
    }
    
    public function getLastCreateEventId() {
        $sql = "SELECT DISTINCT id FROM neighborhood_polygons_create_event ORDER BY id DESC LIMIT 1";
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $query = $this->em->createNativeQuery( $sql, $rsm );
        $result = $query->getSingleResult();
        return $result['id'];
    }
}
?>
