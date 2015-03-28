<?php
namespace Whathood\Mapper;

class Builder extends BaseMapper {

    protected $sm;
    protected $em;

    protected $neighborhoodPolygonMapper;
    protected $neighborhoodMapper;
    protected $regionMapper;
    protected $whathoodUserMapper;
    protected $_spatial_platform;
    protected $_concave_hull_mapper;
    protected $_user_polygon_mapper;
    protected $_test_point_mapper;

    public function __construct( $serviceManager, $doctrineEntityManager ) {
        if( !($serviceManager instanceof \Zend\ServiceManager\ServiceManager) )
            throw new \InvalidArgumentException(
                                    "serviceManager must be of type dfafdaf");
        $this->sm = $serviceManager;
        $this->em = $doctrineEntityManager;
    }

    public function doctrineEntityManager() {
        return $this->sm->get('mydoctrineentitymanager');
    }

    public function electionMapper() {
        return $this->sm->get('Whathood\Mapper\Election');
    }

    public function concaveHullMapper() {
        if( $this->_concave_hull_mapper == null )
            $this->_concave_hull_mapper =
                $this->sm->get('Whathood\Mapper\ConcaveHullMapper');
        return $this->_concave_hull_mapper;
    }

    public function testPointMapper() {
        if( $this->_test_point_mapper == null )
            $this->_test_point_mapper =
                $this->sm->get('Whathood\Mapper\TestPointMapper');
        return $this->_test_point_mapper;
    }
    public function userPolygonMapper() {
        if( $this->_user_polygon_mapper == null )
            $this->_user_polygon_mapper =
                $this->sm->get('Whathood\Mapper\UserPolygonMapper');
        return $this->_user_polygon_mapper;
    }

    public function neighborhoodPolygonMapper() {
        if( $this->neighborhoodPolygonMapper == null )
            $this->neighborhoodPolygonMapper =
                $this->sm->get('Whathood\Mapper\NeighborhoodPolygonMapper');
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
}
?>
