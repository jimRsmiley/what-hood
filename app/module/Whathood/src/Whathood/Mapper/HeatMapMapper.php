<?php
namespace Application\Mapper;

use Application\Entity\HeatMap;
/**
 * Description of HeatMapMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class HeatMapMapper extends BaseMapper {
    
    public function getLatestHeatMap( \Application\Entity\Neighborhood $neighborhood ) {
        $qb = $this->getQueryBuilder();
        $qb->setNeighborhoodName( $neighborhood->getName() );
        $qb->setRegionName( $neighborhood->getRegion()->getName() );
        $qb->setMaxResults(1);
        return $qb->getQuery()->getSingleResult();
    }
    
    public function save( HeatMap $heatMap ) {
        
        if( $heatMap->getRegion() == null )
            throw new \InvalidArgumentException('region may not be null');
        
        if( $heatMap->getNeighborhood() == null )
            throw new \InvalidArgumentException('neighborhood may not be null');
          
        
        /*
         * does the neighborhood already exist, if it does reset it in the neighborhood
         */
        try {
            $neighborhoodName = $heatMap->getNeighborhood()->getName();
            $regionName = $heatMap->getNeighborhood()->getRegion()->getName();
            
            $neighborhood = $this->neighborhoodMapper()->byNeighborhoodName(
                                    $neighborhoodName, $regionName );
            
            $heatMap->setNeighborhood($neighborhood);
        }
        // threw an error cause it didn't exist
        catch( \Doctrine\ORM\NoResultException $e ) {}
        
        /*
         * does the region already exist, if it does reset it in the neighborhood
         */
        try {
            $region = $this->regionMapper()
                            ->byName($heatMap->getRegion()->getName() );
            
            $heatMap->setRegion($region);
        }
        // guess there was no region by that name            
        catch( \Doctrine\ORM\NoResultException $e ) {}
        
        if( $heatMap->getDateTimeAdded() == null )
            $heatMap->setDateTimeAdded( $this->getCurrentDateTimeAsString() );

        $this->em->persist( $heatMap );
        $this->em->flush( $heatMap );
    }
    
    /* check all neighborhoods for heatmaps that are older than their newest
     * neighborhood polygons
     */
    public function getStaleNeighborhoods() {
        $neighborhoods = $this->neighborhoodMapper()->fetchAll();
        
        $staleNeighborhoods = array();
        foreach( $neighborhoods as $neighborhood ) {
            
            // pull the latest heatmap
            try {
                $heatMap = $this->getLatestHeatMap($neighborhood);
                
                try {
                    $neighborhoodPolygon = $this->neighborhoodPolygonMapper()
                                ->getLatest($neighborhood);
                    
                    if( $heatMap->getDateTimeAdded() 
                                < $neighborhoodPolygon->getDateTimeAdded() ) {
                        print $neighborhood->getName()
                                . ' has a stale heatmap';
                        $staleNeighborhoods[] = $neighborhood;
                    }
                }
                   
                catch( \Doctrine\ORM\NoResultException $e ) {
                    die( $e->getMessage() );
                }
            }
            // there might not be one, so add it to the stales
            catch( \Doctrine\ORM\NoResultException $e ) {
                $staleNeighborhoods[] = $neighborhood;
            }
                
                
                
            
        }
        
        return $staleNeighborhoods;
    }
    public function getQueryBuilder() {
        return new \Application\Doctrine\ORM\Query\HeatMapQueryBuilder(
                $this->em->createQueryBuilder() );
    }
}

?>
