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

        try {
            $this->sm->get('doctrine.entitymanager.orm_default');
        } catch(\Exception $e) {
            $this->create_database("whathood_test");
            $this->initDb();
        }
    }

    public static function create_database($db_name) {
        $servername = "localhost";
        $username = "vagrant";
        $password = null;
        try {
            $conn = new \PDO("mysql:host=$servername;dbname=$db_name", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "CREATE DATABASE $db_name";
            // use exec() because no results are returned
            $conn->exec($sql);
            echo "Database created successfully<br>";
        }
        catch(PDOException $e)
        {
            echo $sql . "<br>" . $e->getMessage();
        }
    }

    public function initDb() {
        $configArray = $this->sm->get('config');
        $db_name = $configArray['doctrine']['connection']['orm_default'];

        \Zend\Debug\Debug::dump($db_name);
        exit;

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

    public function m() {
        return $this->sm->get('Whathood\Mapper\Builder');
    }

    public function printResponse() {
        print $this->getResponse();
    }

    public function random_number() {
        return rand(0,10000000);
    }
}
?>
