<?php
namespace Whathood\Mapper;

use Whathood\Entity\WhathoodUser as UserEntity;
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
        
        $qb->select('whu')
            ->from('Whathood\Entity\WhathoodUser','whu')
            ->where( $qb->expr()->eq('whu.facebookUserId', ':facebookId' ) )
            ->setParameter('facebookId', $facebookId );
        
        return $qb->getQuery()->getSingleResult();
    }
    
    public function byId( $id ) {
        
        if( empty( $id ) ) {
            throw new \InvalidArgumentException("id may not be null");
        }
        
        $qb = $this->em->createQueryBuilder();
        
        $qb->select('u')
            ->from('Whathood\Entity\WhathoodUser','u')
            ->where( 'u.id = :id' )
            ->setParameter('id', $id );

        return $qb->getQuery()->getSingleResult();
    }
    
    public function byUserName( $userName ) {
        
        if( empty( $userName ) )
            throw new \InvalidArgumentException("name may not be null");
                
        $qb = $this->em->createQueryBuilder();
        $qb->select('u')
                ->from('Whathood\Entity\WhathoodUser','u')
                ->where( $qb->expr()->eq('u.userName', ':userName' ) )
                ->setParameter('userName', $userName );

        return $qb->getQuery()->getSingleResult();
    }
    
    public function fetchAll() {
        $users = $this->em->getRepository( 'Whathood\Entity\WhathoodUser' )
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
            $obj = $this->em->find( "Whathood\Entity\WhathoodUser",1);
            \Zend\Debug\Debug::dump( $obj );
            exit;
        }
    }
    
    public function getQueryBuilder() {
        throw new \Exception("not yet implmeneted");
    }
}

?>
