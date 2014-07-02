<?php
namespace Application\Mapper;

use Application\Entity\NeighborhoodPolygonVote;
use Application\Mapper\BaseMapper;
use Application\Model\NeighborhoodPolygonVoteCollection;
/**
 * Description of RegionMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodPolygonVoteMapper extends BaseMapper {
    
    public function byId( $id ) {
        
        if( empty( $id ) )
            throw new \InvalidArgumentException("id may not be null");
        
        
        $qb = $this->em->createQueryBuilder();
        
        $qb->select('v')
            ->from('Application\Entity\NeighborhoodPolygonVote','v')
            ->where( $qb->expr()->eq('v.id', $id ) );
        
        return $qb->getQuery()->getSingleResult();
    }
    
    public function fetchAll() {
        $users = $this->em->getRepository( 'Application\Entity\NeighborhoodPolygonVote' )
                ->findAll();
        return $users;
    }
    
    public function byNeighborhoodPolygon( $neighborhoodPolygon ) {
        
        if( $neighborhoodPolygon->getId() == null )
            throw new \InvalidArgumentException(
                                    "neighborhoodPolygonId may not be null");
        
        
        $qb = $this->em->createQueryBuilder();
        
        $qb->select('v')
            ->from('Application\Entity\NeighborhoodPolygonVote','v')
            ->where( $qb->expr()->eq('v.neighborhoodPolygon', $neighborhoodPolygon->getId() ) );
        
        $neighborhoodPolygonVotes = $qb->getQuery()->getResult();
        
        return new NeighborhoodPolygonVoteCollection(
                    array(
                        'neighborhoodPolygon'      => $neighborhoodPolygon,
                        'neighborhoodPolygonVotes' => $neighborhoodPolygonVotes
            ));
    }
    
    /*
     * return votes by user, and optionally, by neighborhoodPolygonId
     */
    public function byWhathoodUserId( $whathoodUserId, $neighborhoodPolygon = null) {
        
       
        if( empty($whathoodUserId) )
            throw new \InvalidArgumentException(
                                    "whathoodUserId may not be null");
        
        $qb = $this->em->createQueryBuilder();
        $qb->select('v')
            ->from('Application\Entity\NeighborhoodPolygonVote','v')
            ->where( $qb->expr()->eq('v.whathoodUser', $whathoodUserId ) );
        
        if( !empty( $neighborhoodPolygon ) ) {
            $qb->andWhere( $qb->expr()->eq('v.neighborhoodPolygon', 
                                            $neighborhoodPolygon->getId() ) );
            
            /*
             * there can only be one vote per user/neighborhoodPolygon
             */
            return $qb->getQuery()->getSingleResult();
        }
        else {

            $neighborhoodPolygonVotes = $qb->getQuery()->getResult();

            return new NeighborhoodPolygonVoteCollection(
                        array(
                            'neighborhoodPolygon'      => $neighborhoodPolygon,
                            'neighborhoodPolygonVotes' => $neighborhoodPolygonVotes
                ));
        }
    }
    
    public function save( NeighborhoodPolygonVote $vote ) {
        
        $whathoodUserId = $vote->getWhathoodUser()->getId();
        $neighborhoodPolygon = $vote->getNeighborhoodPolygon();
        
        if( empty($whathoodUserId) )
            throw new \InvalidArgumentException('NeighborhoodPolygonVoteMapper->save(): whathoodUserId may not be empty');
        
        /**
         * WhathoodUser
         */
        $whathoodUser = $this->whathoodUserMapper()->byId($whathoodUserId);
        $vote->setWhathoodUser($whathoodUser);

        
        //\Zend\Debug\Debug::dump( $vote); exit;
        $this->em->persist( $vote );
        $this->em->flush( $vote );
    }
    
    public function update( NeighborhoodPolygonVote $vote ) {
        //\Zend\Debug\Debug::dump( $vote); exit;
        $this->em->persist( $vote );
        $this->em->flush( $vote );
    }
    
    public function getQueryBuilder() {
        throw new \Exception("not yet implemented");
    }
}

?>
