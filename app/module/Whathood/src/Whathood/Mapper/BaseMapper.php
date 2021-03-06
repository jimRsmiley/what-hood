<?php
namespace Whathood\Mapper;

use CrEOF\Spatial\DBAL\Types\Geometry\Platforms\PostgreSql;

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
    protected $_spatial_platform;
    protected $_mapper_builder;

    protected $_cache;

    public function __construct( $serviceManager, $doctrineEntityManager, $cache = null) {

        if( !($serviceManager instanceof \Zend\ServiceManager\ServiceManager) )
            throw new \InvalidArgumentException(
                                    "serviceManager must be of type \Zend\ServiceManager\ServiceManager");

        $this->sm = $serviceManager;
        $this->em = $doctrineEntityManager;
        $this->_cache = $cache;
    }

    public function begin_trans() {
        $this->em->getConnection()->beginTransaction();
    }

    public function rollback() {
        $this->em->getConnection()->rollback();
    }

    public function commit() {
        $this->em->getConnection()->commit();
    }

    public function flush() {
        $this->em->flush();
    }

    public function clear() {
        $this->em->clear();
    }

    public function remove($entity) {
        $this->em->remove($entity);
    }

    public function m() {
        return $this->sm->get('Whathood\Mapper\Builder');
    }

    public function spatialPlatform() {
       if($this->_spatial_platform == null)
            $this->_spatial_platform = new PostgreSql();
        return $this->_spatial_platform;
    }

    public function userPolygonMapper() {
        return $this->sm->get('Whathood\Mapper\UserPolygonMapper');
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

        if (is_array($entity)) {
            foreach ($entity as $e)
                $this->em->detach($e);
            $this->em->flush();
        }

        else {
            $this->em->detach( $entity );
            $this->em->flush();
        }
    }

    public function getLastCreateEventId() {
        $sql = "SELECT DISTINCT id FROM neighborhood_polygons_create_event ORDER BY id DESC LIMIT 1";
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $query = $this->em->createNativeQuery( $sql, $rsm );
        $result = $query->getSingleResult();
        return $result['id'];
    }

    public function logger() {
        return $this->sm->get('Whathood\Logger');
    }

    public function cache() {
        return $this->_cache;
    }
}
?>
