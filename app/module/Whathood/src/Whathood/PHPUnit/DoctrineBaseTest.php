<?php
namespace Whathood\PHPUnit;

use WhathoodTest\Bootstrap;
use Whathood\Spatial\PHP\Types\Geometry\LineString;
use Whathood\Spatial\PHP\Types\Geometry\Point;
use Whathood\Spatial\PHP\Types\Geometry\Polygon;
use Whathood\Entity\Neighborhood;
use Whathood\Entity\NeighborhoodVote;
use Whathood\Entity\Region;
use Whathood\Entity\UserPolygon;

/**
 * responsible for anything to do with the database
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class DoctrineBaseTest extends \PHPUnit_Framework_TestCase {

    protected $_db_host = 'wh-postgis';
    protected $_db_name = "whathood_test";
    protected $_db_user = "docker";
    protected $_db_pass = null;

    protected $_maintenance_db = 'postgres';

    protected $_conn = null;

    public function getDbHost() { return $this->_db_host; }

    public function getDbName() { return $this->_db_name; }
    public function setDbName($dbName) { $this->_db_name = $dbName; }

    public function getDbPass() { return $this->_db_pass; }
    public function getDbUser() { return $this->_db_user; }
    public function getMaintenanceDb() { return $this->_maintenance_db; }

    public function setUp() {

        $testName = $this->getName();

        $this->setDbName('whathood_test'.$testName);

        if( getenv('APPLICATION_ENV') !==  'test' )
            throw new \Exception("you must set APPLICATION_ENV to 'test'");
        parent::setUp();
        $this->initDb();
    }

    public function tearDown() {
        $this->_conn = null;
    }

    public function createDb($db_name) {
        die("let us create the database");
        $conn = $this->getPostgresConnection($this->getMaintenanceDb());

        $sql = "CREATE DATABASE $db_name";

        try {
            // use exec() because no results are returned
            $conn->exec($sql);
        }
        catch(\PDOException $e) {
            die("could not create database: ".$e->getMessage());
        }
    }

    public function getPostgresConnection($dbName) {
        if ($this->_conn == null)
            $this->_conn = array();

        if (array_key_exists($dbName, $this->_conn)) {
            return $this->_conn[$dbName];
        }
        else {
            $dsn = "pgsql:host=".$this->getDbHost().";dbname=$dbName";
            $this->_conn[$dbName] = new \PDO($dsn, $this->getDbUser(), $this->getDbPass());
            // set the PDO error mode to exception
            $this->_conn[$dbName]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->_conn[$dbName]->setAttribute( \PDO::ATTR_EMULATE_PREPARES, false );
        }
        return $this->_conn[$dbName];
    }

    public function dbConnections ($dbName) {
        $sql = "SELECT * from pg_stat_activity";

        $query = $this->getPostgresConnection($this->getMaintenanceDb())
            ->query($sql);

        if (!$query->execute()) {
            die("could see if dbExists");
        }

        $result = $query->fetchAll();


        foreach ($result as $row) {
            #print \Zend\Debug\Debug::dump($row);
            $pid = $row['pid'];
            $datname = $row['datname'];
            $query = $row['query'];
            $testName = $this->getName();

            if ($dbName == $datname) {
                print "$testName $pid $datname: $query\n";
            }
        }
        die('die here');
    }

    public function dbExists ($dbName) {
        $sql = "SELECT 1 AS result FROM pg_database WHERE datname='$dbName'";

        $query = $this->getPostgresConnection($this->getMaintenanceDb())
            ->query($sql);

        if (!$query->execute()) {
            die("could see if dbExists");
        }

        $result = $query->fetchAll();

        if (empty($result))
            return false;
        else
            return true;
    }

    public function dropDb ($dbName) {
        $conn = $this->getPostgresConnection($this->getMaintenanceDb());
        $sql = "DROP DATABASE $dbName";

        try {
            $conn->exec($sql);
        }
        catch (\PDOException $e) {
            die("could not drop database $dbName: ".$e->getMessage());
        }
    }

    /**
     *  Initialize a database. Drop it if it already exists.
     *
     ***/
    public function initDb($dbName) {

        print "initDb\n";
        $this->dbConnections($this->getDbName());
        if ($this->dbExists($this->getDbName())) {
            $this->dropDb($this->getDbName());
            if ($this->dbExists($this->getDbName())) {
                die("why does the db still exist?");
            }
        }
        $this->createDb($this->getDbName());
        if (!$this->dbExists($this->getDbName()))
            die("why does the db not exist?");
        $DEBUG = true;
        // Retrieve the Doctrine 2 entity manager
        $em = $this->getServiceManager()->get('mydoctrineentitymanager');

        \Zend\Debug\Debug::dump($em->getConnection());
        die("now");
        // Instantiate the schema tool
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        // Retrieve all of the mapping metadata
        $classes = $em->getMetadataFactory()->getAllMetadata();

        if( $DEBUG ) print "creating schema\n";
        // Create the test database schema
        $tool->createSchema($classes);
        if( $DEBUG ) print "schema created\n";

        // don't really know why we have to clear this but it works to stop
        // it when the entity manager doesn't seem to persist shit
        #$em->clear();

        $conn1 = Doctrine_Manager::connection('mysql://username:password@localhost/database1', 'connection1');

    }

    public function getServiceManager() {
        return \WhathoodTest\Bootstrap::getServiceManager();
    }
}

?>
