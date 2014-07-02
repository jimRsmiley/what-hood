<?php
namespace Application\Mapper;
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
    
    public function neighborhoodPolygonMapper() {
        if( $this->neighborhoodPolygonMapper == null )
            $this->neighborhoodPolygonMapper = 
                $this->sm->get('Application\Mapper\NeighborhoodPolygonMapper');
        
        return $this->neighborhoodPolygonMapper;
    }
    
    public function neighborhoodMapper() {
        if( $this->neighborhoodMapper == null )
            $this->neighborhoodMapper = 
                $this->sm->get('Application\Mapper\NeighborhoodMapper');
        
        return $this->neighborhoodMapper;
    }
    
    public function regionMapper() {
        if( $this->regionMapper == null )
            $this->regionMapper = $this->sm->get('Application\Mapper\RegionMapper');
        return $this->regionMapper;
    }
    
    public function whathoodUserMapper() {
        if( $this->whathoodUserMapper == null )
            $this->whathoodUserMapper = $this->sm->get('Application\Mapper\WhathoodUserMapper');
        return $this->whathoodUserMapper;
    }
    
    public function getCurrentDateTimeAsString() {
        return $this->getCurrentDateTime()->format('Y-m-d H:i:s');;
    }
    
    public function getCurrentDateTime() {
        return new \DateTime('now');
    }
    
    public abstract function getQueryBuilder();
}

?>
