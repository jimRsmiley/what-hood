<?php

namespace ApplicationTest\Mapper;

use Application\PHPUnit\DoctrineBaseTest;
use Application\Entity\NeighborhoodVote;
use Application\Entity\Neighborhood;
use Application\PHPUnit\DummyEntityBuilder;
/**
 * Description of NeighborhoodORMTest
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodVoteMapperTest extends DoctrineBaseTest {

    protected $object = null;
    protected $em = null;
    public function setUp() {
        
        parent::setUp();
        $this->object = $this->getServiceManager()->get(
                        'Application\Mapper\NeighborhoodPolygonVoteMapper' );
        $this->em = $this->getServiceManager()->get('mydoctrineentitymanager');
        
        $this->initDb();
    }
    
    public function testSave() {
        $votingUser = $this->getSavedUser('testSave votingUser');
        
        $neighborhoodPolygon = $this->getSavedNeighborhoodPolygon(
                                    'creatorUser','my neighborhood polygon' );
        
              
        $neighborhoodVote = new \Application\Entity\NeighborhoodPolygonVote(
            array( 
                'whathoodUser' => $votingUser,
                'neighborhoodPolygon'  => $neighborhoodPolygon,
                'vote'  => -1
        ));
       
        $this->object->save($neighborhoodVote);
        //die( 'we actually made it past here');
        $this->assertEquals( 1, $neighborhoodVote->getId() );
    }
    

    public function testDifferentUsersVoteSameNeighborhood() {
        $this->initDb();
        
        $n1 = $this->getSavedNeighborhoodPolygon('user1','Dummy1');
        
        $votingUser1 = $this->getSavedUser('myVotingUser1');
        
        $votingUser2 = $this->getSavedUser('myVotingUser2');
        
        $n2 = $this->getSavedNeighborhoodPolygon('user2','Dummy2');
        
        $neighborhoodVote = new \Application\Entity\NeighborhoodPolygonVote(
            array( 
                'whathoodUser' => $votingUser1,
                'neighborhoodPolygon'  => $n1,
                'vote'  => -1
        ));
       
        $this->object->save($neighborhoodVote);
        
        $neighborhoodVote = new \Application\Entity\NeighborhoodPolygonVote(
            array( 
                'whathoodUser' => $votingUser2,
                'neighborhoodPolygon'  => $n1,
                'vote'  => 1
        ));
        $this->object->save( $neighborhoodVote );
        $this->assertEquals( 2, $neighborhoodVote->getId() );
    }
    
    /**
     * @group 20130823
     */
    public function testSameUserDifferentNeighborhoods() {

        $votingUser = $this->getSavedUser('myVotingUser');
        
        $np1 = DummyEntityBuilder::neighborhoodPolygon( $votingUser );
        $this->neighborhoodPolygonMapper()->save($np1);
        
        $np2 = DummyEntityBuilder::neighborhoodPolygon( $votingUser );
        
        $this->neighborhoodPolygonMapper()->save($np2);
        
        $neighborhoodVote = new \Application\Entity\NeighborhoodPolygonVote(
            array( 
                'whathoodUser' => $votingUser,
                'neighborhoodPolygon'  => $np1,
                'vote'  => -1
        ));
       
        $this->object->save($neighborhoodVote);
        
        $neighborhoodVote = new \Application\Entity\NeighborhoodPolygonVote(
            array( 
                'whathoodUser' => $votingUser,
                'neighborhoodPolygon'  => $np2,
                'vote'  => 1
        ));
        $this->object->save( $neighborhoodVote );
        
        $this->assertEquals( 2, $neighborhoodVote->getId() );
    }
    
    /**
     * @depends testSave
     * @depends testSameUserDifferentNeighborhoods
     */
    public function testOneUserSavingMultipleVotesSameNeighborhood()
    {
        $votingUser = $this->getSavedUser('my voting user');
        
        $n1 = DummyEntityBuilder::neighborhoodPolygon($votingUser);
        $this->neighborhoodPolygonMapper()->save($n1);
        
        $neighborhoodVote = new \Application\Entity\NeighborhoodPolygonVote(
            array( 
                'whathoodUser' => $votingUser,
                'neighborhoodPolygon'  => $n1,
                'vote'  => -1
        ));
       
        $this->object->save($neighborhoodVote);
        
        $neighborhoodVote = new \Application\Entity\NeighborhoodPolygonVote(
            array( 
                'whathoodUser' => $votingUser,
                'neighborhoodPolygon'  => $n1,
                'vote'  => 1
        ));
       
        try {
            $this->object->save($neighborhoodVote);
            $this->assertTrue( false );
        }
        catch( \Doctrine\DBAL\DBALException $e ) {
            $message = $e->getMessage();
            $this->assertRegExp( '/Integrity constraint violation/',$message);
        }
    }
 }
?>