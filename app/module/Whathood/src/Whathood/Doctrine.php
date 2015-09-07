<?php

namespace Whathood;

use WhathoodTest\Bootstrap;

/**
 * responsible for anything to do with the database
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Doctrine extends \PHPUnit_Framework_TestCase {

    protected static $DEBUG = false;

    protected $_config;
    protected $_eventManager;

    public function setConfig($config) {
        $this->_config = $config;
    }

    public function getConfig() { return $this->_config; }

    public function setEventManager($eventManager) { $this->_eventManager = $eventManager; }

    public function getEventManager() { return $this->_eventManager; }

    protected static $DBHOST = 'wh-postgis';
    protected static $DBUSER = "postgres";
    protected static $DBPASS = null;

    protected static $MAINTDB = 'postgres';

    protected $_conn = null;

    public function __construct(array $data = null) {
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
        $hydrator->hydrate($data, $this);
    }

    public static function execSql($conn, $sql) {
        if (static::$DEBUG) print $sql."\n";
        try {
            // use exec() because no results are returned
            $conn->exec($sql);
        }
        catch(\PDOException $e) {
            die($e->getMessage());
        }
    }

    public static function createExtension($conn, $extension) {
        $sql = "CREATE EXTENSION $extension";
        static::execSql($conn, $sql);
    }

    public static function createDb($conn, $db_name) {
        $sql = "CREATE DATABASE $db_name";

        if (static::$DEBUG) print $sql."\n";
        try {
            // use exec() because no results are returned
            $conn->exec($sql);
        }
        catch(\PDOException $e) {
            die("FATAL: Unable to create database $db_name: ". $e->getMessage());
        }
    }

    public static function listDbs($conn) {

        $sql = "SELECT * FROM pg_database";
        $query = $conn->query($sql);

        if (!$query->execute()) {
            die("could see if dbExists");
        }

        $results = $query->fetchAll();

        if (static::$DEBUG) print "existing dbs:\n";
        foreach ($results as $db) {
            $datname = $db['datname'];
            if (static::$DEBUG) print "\t$datname\n";
        }
        return $results;
    }

    public function getPostgresConnection($dbName) {
        $dsn = "pgsql:host=".static::$DBHOST;
        if (static::$DEBUG) print "getPostgresConnection-dsn: $dsn\n";
        $this->_conn = new \PDO($dsn, static::$DBUSER, static::$DBPASS);
        // set the PDO error mode to exception
        $this->_conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->_conn->setAttribute( \PDO::ATTR_EMULATE_PREPARES, false );
        return $this->_conn;
    }

    public static function dbConnections ($conn, $dbName) {
        $sql = "SELECT * from pg_stat_activity";

        $query = $conn
            ->query($sql);

        if (!$query->execute()) {
            die("could see if dbExists");
        }

        $result = $query->fetchAll();

        $connections = array();

        foreach ($result as $row) {
            $pid = $row['pid'];
            $datname = $row['datname'];
            $query = $row['query'];

            if ($dbName == $datname or $dbName == null) {
                array_push($row, $connections);
                if (static::$DEBUG) print "db_connection: $pid $datname: $query\n";
            }
        }
        return $connections;
    }

    public static function dbExists ($conn, $dbName) {
        $dbs = static::listDbs($conn);
        foreach ($dbs as $db) {
            if ($db['datname'] == $dbName) {
                return true;
            }
        }
        return false;
    }

    public static function dropDb ($conn, $dbName) {
        $sql = "DROP DATABASE $dbName";

        try {
            $conn->exec($sql);
        }
        catch (\PDOException $e) {
            if (strpos($e->getMessage(), "is being accessed by other users")) {
                static::removeUserConnections($conn, $dbName);
            }
            die("could not drop database $dbName: ".$e->getMessage());
        }
    }

    public static function removeUserConnections($conn, $dbName) {
        $connections = static::dbConnections($conn, $dbName);

        if (!empty($connections)) {
            throw new \Exception("should implement this");
        }
    }

    /**
     *  Initialize a database. Drop it if it already exists.
     *
     ***/
    public function initDb($dbName) {
        if (static::$DEBUG) print "initializing database $dbName\n";
        $conn = $this->getPostgresConnection('postgres');
        $this->dbConnections($conn, $dbName);
        if ($this->dbExists($conn, $dbName)) {
            if (static::$DEBUG) print "database $dbName already exists\n";
            $this->dropDb($conn, $dbName);
            if ($this->dbExists($conn, $dbName)) {
                die("why does db $dbName still exist?");
            }
        }
        $this->createDb($conn, $dbName);

        if (!$this->dbExists($conn, $dbName))
            die("why does the db '$dbName' not exist after createDb()?");

        $entityManager = $this->buildEntityManager(
                $this->getConfig(),
                $this->getEventManager(),
                $dbName
        );

        $entityManager->createNativeQuery(
            "CREATE EXTENSION postgis",
            new \Doctrine\ORM\Query\ResultSetMapping()
        )->execute();

        $entityManager->createNativeQuery(
            "CREATE EXTENSION pgrouting",
            new \Doctrine\ORM\Query\ResultSetMapping()
        )->execute();
        // Instantiate the schema tool
        $tool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);

        // Retrieve all of the mapping metadata
        $classes = $entityManager->getMetadataFactory()->getAllMetadata();

        if( static::$DEBUG ) print "creating schema\n";
        // Create the test database schema
        $tool->createSchema($classes);
        if( static::$DEBUG ) print "schema created\n";

        // don't really know why we have to clear this but it works to stop
        // it when the entity manager doesn't seem to persist shit
        #$em->clear();
    }

    public static function buildEntityManager($config, $eventManager, $dbName) {
        $params = array(
            'driver'   =>  'pdo_pgsql',
            'host'     =>  (getenv('PGHOST') ? getenv('PGHOST') : 'wh-postgis'),
            'port'     => '5432',
            'dbname'   => $dbName,
            'user'     => 'docker',
            # we don't allow remote sql connections
            'password' => null
        );

        $newManager = \Doctrine\ORM\EntityManager::create(
            $params,
            $config,
            $eventManager
        );
        return $newManager;
    }
}

?>
