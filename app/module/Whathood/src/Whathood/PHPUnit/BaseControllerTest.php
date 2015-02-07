<?php
namespace Whathood\PHPUnit;

use WhathoodTest\Bootstrap;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
/**
 * Description of NeighborhoodControllerTest
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class BaseControllerTest extends AbstractHttpControllerTestCase {

    protected $sm;

    public function setUp()
    {
        parent::setUp();

        if( getenv('APPLICATION_ENV') !==  'test' )
            throw new \Exception("you must set APPLICATION_ENV to 'test'");

        $this->sm = Bootstrap::getServiceManager();

        $this->setApplicationConfig(
            include 'TestConfig.php'
        );
    }

    public function initDb() {

        $DEBUG = true;
        // Retrieve the Doctrine 2 entity manager
        $em = $this->sm->get('doctrine.entitymanager.orm_default');

        // Instantiate the schema tool
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        // Retrieve all of the mapping metadata
        $classes = $em->getMetadataFactory()->getAllMetadata();

        if( $DEBUG ) print "dropping schema\n";
        // Delete the existing test database schema
        $tool->dropSchema($classes);

        if( $DEBUG ) print "creating schema\n";
        // Create the test database schema
        $tool->createSchema($classes);
        if( $DEBUG ) print "schema created\n";
    }

    private $whathoodUserMapper   = null;
    private $neighborhoodMapper   = null;
    private $neighborhoodPolygonMapper = null;
    private $neighborhoodVoteMapper   = null;
    private $regionMapper         = null;

    public function whathoodUserMapper() {
        return $this->whathoodUserMapper = $this->sm->get(
                                                    'Whathood\Mapper\WhathoodUserMapper');
    }

    public function neighborhoodMapper() {
        if( $this->neighborhoodMapper == null )
            $this->neighborhoodMapper = $this->sm->get('Whathood\Mapper\NeighborhoodMapper');

        return $this->neighborhoodMapper;
    }

    public function userPolygonMapper() {
        if( $this->neighborhoodPolygonMapper == null )
            $this->neighborhoodPolygonMapper = $this->sm->get('Whathood\Mapper\UserPolygonMapper');

        return $this->neighborhoodPolygonMapper;
    }

    public function neighborhoodVoteMapper() {
        if( $this->neighborhoodVoteMapper == null )
            $this->neighborhoodVoteMapper =
                $this->sm->get('Whathood\Mapper\NeighborhoodPolygonVoteMapper');

        return $this->neighborhoodVoteMapper;
    }

    public function regionMapper() {
        if( $this->regionMapper == null )
            $this->regionMapper = $this->sm->get('Whathood\Mapper\Region');

        return $this->regionMapper;
    }
}
?>
