<?php
namespace Whathood\Mapper;

use Whathood\Model\HeatMap;
use Whathood\Entity\Neighborhood;
use \Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\ResultSetMapping;
/**
 * Description of HeatMapMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodPointStrengthOfIdentityMapper extends BaseMapper {
    
    public function getHeatMapByNeighborhood( Neighborhood $neighborhood ) {
        
        // @XXX remove hardcoded setNum
        $setNum = 100;
        
        $sql = "SELECT * FROM neighborhood_heat_map(:neighborhood_id,:set_number)";
        
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('x', 'x');
        $rsm->addScalarResult('y', 'y');
        $rsm->addScalarResult('strength_of_identity', 'strength_of_identity');
        $query = $this->em->createNativeQuery($sql,$rsm);
        $query->setParameter(':neighborhood_id', $neighborhood->getId() );
        $query->setParameter(':set_number', $setNum );
        
        $result = $query->getResult();
        
        return $result;
    }
    
    public function getEntityName() {
        return "Whathood\Entity\NeighborhoodHeatMapPoint";
    }
}

?>
