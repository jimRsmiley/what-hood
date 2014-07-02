<?php

namespace WhathoodTest\Mapper;

use Whathood\PHPUnit\DoctrineBaseTest;
use Whathood\Entity\WhathoodUser;

/**
 * Description of NeighborhoodORMTest
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class FacebookUserMapperTest extends DoctrineBaseTest {

    protected $object = null;

    public function setUp() {
        parent::setUp();
        $this->object = $this->sm->get( 'Whathood\Mapper\WhathoodUserMapper' );
    }
    
    public function testSave() {
        print "running test save\n";
        
        $user = new \Whathood\Entity\WhathoodUser(
                array( 
                    'userName' => 'Azevea3',
                    'authority' => true,
                    'facebookUser'  => new \Whathood\Entity\FacebookUser(
                            array(
                                'name' => 'Joe Schmoe',
                                'id'    => '123'
                            ))
            ));
        
        $this->object->save($user);
        $this->assertEquals($user->getId(), '1');
        print "done with test save\n";

    }

    /**
     * @depends testSave
     */
    public function testFetchAll() {
        $user = new User(
                array( 
                    'name' => 'Azevea3',
                    'authority' => true 
            ));
        
        $this->object->save($user);
        $user = $this->object->fetchAll();
        $this->assertTrue( count( $user ) == 1 );
    }
    
 }

?>
