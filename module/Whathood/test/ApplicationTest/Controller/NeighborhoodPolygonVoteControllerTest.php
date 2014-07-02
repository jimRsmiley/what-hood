<?php

namespace WhathoodTest\Controller;

use Zend\Http\Request;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Stdlib\Parameters;
use Whathood\PHPUnit\BaseControllerTest;
use Whathood\PHPUnit\DummyEntityBuilder;
use Whathood\Model\AuthenticationService;
use Whathood\Entity\NeighborhoodPolygonVote;

/**
 * Description of NeighborhoodControllerTest
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodPolygonVoteControllerTest extends BaseControllerTest {

    public function setUp() {
        parent::setUp();
        $this->initDb();
    } 

    /*
     * @group 20130821
     */
    public function testGetVoteByNeighborhoodPolygon() {
        $this->initDb();
        // we need a neighborhood polygon user
        $npCreatorUser = $this->getSavedAuthenticatedWhathoodUser();
        
        /*
         * and the vote casters
         */
        $voteCastingUser1 = DummyEntityBuilder::whathoodUser();
        $voteCastingUser1->setUserName('vote casting user 1');
        $this->whathoodUserMapper()->save($voteCastingUser1);
        
        $voteCastingUser2 = DummyEntityBuilder::whathoodUser();
        $voteCastingUser2->setUserName('vote casting user 2');
        $this->whathoodUserMapper()->save($voteCastingUser2);

        /*
         * save the neighborhood polygon
         */
        $neighborhoodPolygon = DummyEntityBuilder
                                        ::neighborhoodPolygon($npCreatorUser);
        $this->neighborhoodPolygonMapper()->save( $neighborhoodPolygon );
        
        /*
         * cast user 1 vote
         */
        $this->neighborhoodVoteMapper()->save( new NeighborhoodPolygonVote(
                array(
                    'neighborhoodPolygon' => $neighborhoodPolygon,
                    'whathoodUser' => $voteCastingUser2,
                    'vote' => 'up'
                )));

        $this->neighborhoodVoteMapper()->save( new NeighborhoodPolygonVote(
                array(
                    'neighborhoodPolygon' => $neighborhoodPolygon,
                    'whathoodUser' => $voteCastingUser1,
                    'vote' => 'up'
                )));

        $url = '/n/vote/by-neighborhood-polygon-id?neighborhoodPolygonId=1';
        
        /*
         * now poll the controller for the votes for the neighborhood polygon
         */
        $this->dispatch( $url );
        
        $this->assertResponseStatusCode(200);

        $json = $this->getResponse()->getBody();
        
        try {
            $object = \Zend\Json\Json::decode( $json, \Zend\Json\Json::TYPE_ARRAY );
            $this->assertNotNull( $object );
            $this->assertTrue( array_key_exists('allVotes',$object) );
            $this->assertEquals(2, $object->allVotes->upCount );
        }   
        catch( \Zend\Json\Exception\RuntimeException $e ) {
            $this->assertTrue(false, "exception during json decode");
        }
    }
    
    public function testCastAction() {

        $whathoodUser = $this->getSavedAuthenticatedWhathoodUser();
        
        $neighborhoodPolygon = DummyEntityBuilder
                                ::neighborhoodPolygon($whathoodUser);
        $neighborhoodPolygon->setWhathoodUser( $whathoodUser );
        
        $this->neighborhoodPolygonMapper()->save( $neighborhoodPolygon );
        
        $this->getRequest()
                ->setMethod('POST')
                ->setPost(
                    new Parameters(
                        array(
                            'neighborhoodPolygonId' => $neighborhoodPolygon->getId(),
                            'vote'  => 'up'
                        )
        ));
        
        $this->dispatch( '/n/vote/cast' );
        $this->assertResponseStatusCode(200);
        
        try {
            $neigbhorhoodPolygonVote = $this->neighborhoodVoteMapper()->byId(1);
        } catch( \Exception $e ) {
            $this->assertTrue( false );
        }
        $this->assertInstanceof( '\Whathood\Entity\NeighborhoodPolygonVote', 
                                                    $neigbhorhoodPolygonVote );
    }
    
    
    /**
     * @group 20130821
     */
    public function testGetThisUsersVote() {
        $this->initDb();
        
        // we need a neighborhood polygon user
        $voteCastingUser = $this->getSavedAuthenticatedWhathoodUser(
                                                        'vote casting user');
        
        /*
         * and the vote casters
         */
        $npCreatorUser = DummyEntityBuilder::whathoodUser();
        $npCreatorUser->setUserName('polygon neighborhood creator');
        $this->whathoodUserMapper()->save($npCreatorUser);
        
        /*
         * save the neighborhood polygon
         */
        $neighborhoodPolygon = DummyEntityBuilder
                                        ::neighborhoodPolygon($npCreatorUser);
        $this->neighborhoodPolygonMapper()->save( $neighborhoodPolygon );
        
        /*
         * cast user 1 vote
         */
        $this->neighborhoodVoteMapper()->save( new NeighborhoodPolygonVote(
                array(
                    'neighborhoodPolygon' => $neighborhoodPolygon,
                    'whathoodUser' => $voteCastingUser,
                    'vote' => 'up'
                )));

        /*
         * now poll the controller for the votes for the neighborhood polygon
         */
        $this->dispatch( '/n/vote/by-neighborhood-polygon-id?neighborhoodPolygonId='.$neighborhoodPolygon->getId() );
        
        $this->assertResponseStatusCode(200);

        try {
            $object = \Zend\Json\Json::decode( $this->getResponse()->getBody() );
            $this->assertNotNull( $object->thisUsersVote );
            $this->assertEquals('1', $object->thisUsersVote->vote );
        }   
        catch( \Zend\Json\Exception\RuntimeException $e ) {
            $this->assertTrue(false, "exception during json decode");
        }
    }
}
?>