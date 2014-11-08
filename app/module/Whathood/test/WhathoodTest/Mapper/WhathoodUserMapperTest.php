<?php

namespace WhathoodTest\Mapper;

use Whathood\PHPUnit\DoctrineBaseTest;
use Whathood\Entity\WhathoodUser;
use Whathood\PHPUnit\DummyEntityBuilder;
/**
 * Description of NeighborhoodORMTest
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class WhathoodUserMapperTest extends DoctrineBaseTest {

    protected $object = null;
    
    public function setUp() {
        parent::setUp();
        $this->initDb();
        $this->object = $this->getServiceManager()
                                            ->get( 'Whathood\Mapper\WhathoodUserMapper' );
    }
    
    public function testNoFbUserSave() {
        
        $user = new \Whathood\Entity\WhathoodUser(
                array( 
                    'userName' => 'Azevea3',
            ));
        
        $this->object->save($user);
        $this->assertEquals($user->getId(), '1');
    }
    
    /*
     * @depends testNoFbUserSave
     */
    public function testWithFbUserSave() {
        $this->initDb();
        $user = new \Whathood\Entity\WhathoodUser(
                array( 
                    'userName' => 'Azevea3',
                    'facebookUser' => DummyEntityBuilder::facebookUser()
            ));
        
        $this->object->save($user);
        $this->assertEquals($user->getId(), '1');

    }

    public function testFetchAll() {
        //don't really know why the entity manager is closing
        $this->initDb();
        
        $user = new WhathoodUser(
                array( 
                    'userName' => 'Azevea3',
                    'authority' => true 
            ));
        
        $this->object->save($user);
        $users = $this->object->fetchAll();
        $this->assertTrue( count( $users ) == 1 );
    }
 }

?>
