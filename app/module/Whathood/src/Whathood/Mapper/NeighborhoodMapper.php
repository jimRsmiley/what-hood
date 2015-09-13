<?php

namespace Whathood\Mapper;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Whathood\Entity\Neighborhood as NeighborhoodEntity;
use Whathood\Spatial\PHP\Types\Geometry\NeighborhoodCollection;
use Whathood\Doctrine\ORM\Query\NeighborhoodQueryBuilder;

/**
 * Description of NeighborhoodMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodMapper extends BaseMapper {

    public function getByRegionId( $regionId ) {

        if( empty( $regionId ) ) {
            throw new \InvalidArgumentException("region may not be null");
        }

        $dql = "SELECT n FROM Whathood\Entity\Neighborhood  n JOIN n.region r WHERE r.id = $regionId AND n.deleted = 0";

        return $this->em->createQuery($dql)->getResult();
    }

    /**
     * return a list
     * @param type $regionName
     * @return type
     * @throws \InvalidArgumentException
     */
    public function byRegionName( $regionName ) {

        if( empty( $regionName ) )
            throw new \InvalidArgumentException('regionName may not be null');

        $qb = new NeighborhoodQueryBuilder( $this->em->createQueryBuilder() );
        $qb->setRegionName( $regionName );

        return $qb->getQuery()->getResult();
    }

    public function nameLike( $name ) {

        if( empty( $name ) ) {
            throw new \InvalidArgumentException("neighborhoodName may not be null");
        }

        $qb = $this->em->createQueryBuilder();

        $qb->select('n')
                ->from('Whathood\Entity\Neighborhood','n')
                ->where( $qb->expr()->like('LOWER(n.name)', 'LOWER(:name)' ) )
                ->setParameter('name', '%'.$name.'%' );

        return $qb->getQuery()->getResult();
    }

    public function byId($id) {
        return $this->getNeighborhoodById($id);
    }

    public function getNeighborhoodById( $id ) {

        if( empty( $id ) )
            throw new \InvalidArgumentException( 'id may not be null' );

        $qb = $this->em->createQueryBuilder();
        $qb->select( array( 'n','r' ) )
                ->from('Whathood\Entity\Neighborhood', 'n')
                ->innerjoin( 'n.region','r')
                ->where( 'n.id = ?1' )
                ->setParameter(1, $id );

        try {
            return $qb->getQuery()->getSingleResult();
        } catch( \Exception $e ) {
            print $e->getMessage();
            exit;
        }
    }

    public function byName($n,$r) {
        return $this->getNeighborhoodByName($n,$r);
    }

   public function getNeighborhoodByName( $neighborhoodName, $regionName ) {

       if (empty($neighborhoodName))
           throw new \InvalidArgumentException("neighborhoodName may not be empty");
       if (empty($regionName))
           throw new \InvalidArgumentException("regionName may not be empty");

        $dql = "SELECT n"
                . " FROM Whathood\Entity\Neighborhood n"
                . " JOIN n.region r"
                . " WHERE n.name = :n_name"
                . ' AND r.name = :r_name';

        return $this->em->createQuery($dql)
                ->setParameter(':n_name',$neighborhoodName)
                ->setParameter(':r_name',$regionName)
                ->getSingleResult();
    }

    public function save( NeighborhoodEntity $neighborhood ) {

        if( $neighborhood->getName() == null )
            throw new \InvalidArgumentException("neighborhood.name may"
                                                            . " not be null");

        /*
         * does the region already exist, if it does reset it in the neighborhood
         */
        try {
            $region = $this->regionMapper()
                            ->getRegionByName($neighborhood->getRegion()->getName() );
            $neighborhood->setRegion($region);
        }
        // guess there was no region by that name
        catch( \Doctrine\ORM\NoResultException $e ) {}

        if( $neighborhood->getDateTimeAdded() == null ) {
            $neighborhood->setDateTimeAdded( date("Y-m-d H:i:s") );
        }

        $this->em->persist( $neighborhood );
        $this->em->flush( $neighborhood );
    }

    /*
     * fetch all neighborhoods, ALL of them
     */
    public function fetchAll() {

        $qb = $this->getQueryBuilder();
        $qb->orderBy('n.name','ASC');

        $neighborhoods = $qb->getQuery()->getResult();

        return $neighborhoods;
    }

    public function getQueryBuilder() {
        return new \Whathood\Doctrine\ORM\Query\NeighborhoodQueryBuilder(
                $this->em->createQueryBuilder() );
    }


    /**
     *
     * when deleting, must delete associated UserPolygons
     *
     **/
    public function delete($neighborhood,$userPolygonMapper,$neighborhoodPolygonMapper) {
        $this->begin_trans();

        try {
            $user_polygons = $userPolygonMapper->byNeighborhood($neighborhood);

            foreach($user_polygons as $up) {
                $this->remove($up);
            }

            $neighborhood_polygons = $neighborhoodPolygonMapper->byNeighborhood($neighborhood);

            foreach($neighborhood_polygons as $np) {
                $this->remove($np);
            }

            // and finally remove the neighborhood
            $this->remove($neighborhood);

            $this->flush();
            $this->commit();
        }
        catch( \Exception $e) {
            $this->logger()->warn($e);
            $this->rollback();
        }
    }

    public function byBuildBorder($should_build_border) {

        $dql = "SELECT n FROM Whathood\Entity\Neighborhood n WHERE n.no_build_border = :no_build_border";

        if ($should_build_border == true)
            $no_build_border = 'false';
        else
            $no_build_border = 'true';
        return $this->em->createQuery($dql)
            ->setParameter(':no_build_border', $no_build_border)
            ->getResult();
    }

    /**
     *  returns neighborhoods in order of oldest polygon
     **/
    public function sortByOldestBorder(array $neighborhoods) {
        usort($neighborhoods,array($this,'sortNeighborhoodsByYoungestPolygon'));
        return $neighborhoods;
    }

    /**
     * returns 1 if n1 has a younger(or no) polygon than n2, else if n2 is younger, returns -1, if they're equal, returns 0
    **/
    public function sortNeighborhoodsByYoungestPolygon(NeighborhoodEntity $n1, NeighborhoodEntity $n2) {
        $mapper = $this->m()->neighborhoodPolygonMapper();

        try {
            $np1 = $mapper->latestByNeighborhood($n1);
        }
        catch(\Exception $e) {
            $np1 = null;
        }

        try {
            $np2 = $mapper->latestByNeighborhood($n2);
        }
        catch(\Exception $e) {
            $np2 = null;
        }

        if ($np1 == null and $np2 == null)
            return 0;
        else if ($np1 == null and $np2)
            return 1;
        else if ($np1 and $np2 == null)
            return -1;
        else if ($np1->getCreatedAt() < $np2->getCreatedAt())
            return 1;
        else if ($np1->getCreatedAt() > $np2->getCreatedAt())
            return -1;
        return 0;
    }
}
?>
