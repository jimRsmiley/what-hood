<?php
namespace Application\Mapper;

use Application\Entity\WhathoodUser as UserEntity;
/**
 * Description of RegionMapper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class WhathoodUserMapper extends BaseMapper {
    
    public function byFacebookId( $facebookId ) {
        
        if( empty( $facebookId ) )
            throw new \InvalidArgumentException("facebookId may not be null");
        
        
        $qb = $this->em->createQueryBuilder();
        
        $qb->select('whu', 'fbu')
            ->from('Application\Entity\WhathoodUser','whu')
            ->innerjoin('whu.facebookUser', 'fbu')
            ->where( $qb->expr()->eq('fbu.id', ':facebookId' ) )
            ->setParameter('facebookId', $facebookId );
        
        return $qb->getQuery()->getSingleResult();
    }
    
    public function byId( $id ) {
        
        if( empty( $id ) ) {
            throw new \InvalidArgumentException("id may not be null");
        }
        
        $qb = $this->em->createQueryBuilder();
        
        $qb->select('u')
            ->from('Application\Entity\WhathoodUser','u')
            ->where( 'u.id = :id' )
            ->setParameter('id', $id );

        return $qb->getQuery()->getSingleResult();
    }
    
    public function byUserName( $userName ) {
        
        if( empty( $userName ) )
            throw new \InvalidArgumentException("name may not be null");
                
        $qb = $this->em->createQueryBuilder();
        $qb->select('u')
                ->from('Application\Entity\WhathoodUser','u')
                ->where( $qb->expr()->eq('u.userName', ':userName' ) )
                ->setParameter('userName', $userName );

        return $qb->getQuery()->getSingleResult();
    }
    
    public function fetchAll() {
        $users = $this->em->getRepository( 'Application\Entity\WhathoodUser' )
                ->findAll();
        return $users;
    }
    
    public function save( UserEntity $user ) {
        //$this->em->clear();
        $this->em->persist( $user );
        $this->em->flush( $user );
        
        if( $user->getId() == null ) {
            print "why didn't this save?\n";
            \Zend\Debug\Debug::dump( $user );
            $obj = $this->em->find( "Application\Entity\WhathoodUser",1);
            \Zend\Debug\Debug::dump( $obj );
            exit;
        }
    }
    
    public function getQueryBuilder() {
        throw new \Exception("not yet implmeneted");
    }
}

?>
