<?php

namespace Whathood\PHPUnit;

use WhathoodTest\Bootstrap;

/**
 * responsible for anything to do with the database
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Database extends \PHPUnit_Framework_TestCase {

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

    // return results
    public static function querySql($conn, $sql) {
        if (static::$DEBUG) print $sql."\n";
        $query = $conn
            ->query($sql);

        if (!$query->execute()) {
            throw new \Exception("could not execute query");
        }

        return $query->fetchAll();
    }

    public static function execSql($conn, $sql) {
        if (static::$DEBUG) print $sql."\n";
        try {
            // use exec() because no results are returned
            $conn->exec($sql);
        }
        catch(\PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     * create a new database
     *
     * @param conn - Doctrine db connection
     * @param db_name [String] the database name
     */
    public static function createDb($conn, $db_name) {

        if (static::dbExists($conn,$db_name))
            die("why existies?");
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
        $results = static::querySql($conn, "SELECT * from pg_stat_activity");

        $connections = array();
        foreach ($results as $row) {
            $pid = $row['pid'];
            $datname = $row['datname'];
            $query = $row['query'];

            if ($dbName == $datname or $dbName == null) {
                array_push($connections, $row);
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

    public static function log($str) {
        if (self::$DEBUG)
            print "$str\n";
    }

    public static function dropDb ($conn, $dbName) {

        if (!static::dbExists($conn, $dbName)) {
            self::log("database does not exist, not attempting to drop");
            return;
        }
        static::removeUserConnections($conn, $dbName);
        static::execSql($conn, "DROP DATABASE $dbName");
    }

    public static function removeUserConnections($conn, $dbName) {
        $connections = static::dbConnections($conn, $dbName);

        if (!empty($connections))
            static::execSql($conn, "SELECT pg_terminate_backend(pg_stat_activity.pid)
                FROM pg_stat_activity
                WHERE pg_stat_activity.datname = '$dbName'
                  AND pid <> pg_backend_pid()");
    }

    /**
     *  Initialize a database and load whathood's schema
     *
     *  Drop the database if it already exists.
     **/
    public function createDbWithSchema($dbName) {
        if (static::$DEBUG) print "initializing database $dbName\n";

        $conn = $this->getPostgresConnection('postgres');

        if (static::dbExists($conn, $dbName)) {
            if (static::$DEBUG) print "database $dbName already exists\n";

            static::dropDb($conn, $dbName);

            if (static::dbExists($conn, $dbName)) {
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
            "CREATE SCHEMA whathood",
            new \Doctrine\ORM\Query\ResultSetMapping()
        )->execute();

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

        exec(\Whathood\Util::getApplicationRoot()."/../bin/import_db --load-functions-only --db-name $dbName 2>&1 >> /dev/null");

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
