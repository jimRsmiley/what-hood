<?php

namespace ApplicationTest\Mapper;

use Application\PHPUnit\DoctrineBaseTest;
use Application\Entity\WhathoodUser;
use Application\PHPUnit\DummyEntityBuilder;
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
                                            ->get( 'Application\Mapper\WhathoodUserMapper' );
    }
    
    public function testNoFbUserSave() {
        
        $user = new \Application\Entity\WhathoodUser(
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
        $user = new \Application\Entity\WhathoodUser(
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
