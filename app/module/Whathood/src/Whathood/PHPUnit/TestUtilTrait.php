<?php

/**
 * will this show up in the namespace?
 */
namespace Whathood\PHPUnit;

use WhathoodTest\Bootstrap;
use Whathood\Spatial\PHP\Types\Geometry\LineString;
use Whathood\Spatial\PHP\Types\Geometry\Point;
use Whathood\Spatial\PHP\Types\Geometry\Polygon;
use Whathood\Entity\Neighborhood;
use Whathood\Entity\NeighborhoodVote;
use Whathood\Entity\Region;
use Whathood\Entity\UserPolygon;
use Whathood\Election\PointElection;

/**
 * hold utility methods for building tests
 *
 * NOTE: don't put setUp or tearDown in here. Controller tests and other tests
 * should implement them specifically
 *
 */
trait TestUtilTrait {


    protected $_testName;

    public function setTestName($testName) {
        $this->_testName = $testName;
    }

    public function initTestName() {
        $reflect = new \ReflectionClass($this);
        $testName = strtolower($reflect->getShortName()."_".$this->getName());
        $this->setTestName($testName);
    }

    public static function rand_int() {
        return rand();
    }

    public static function rand($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    /**
     * sets the database name by setting the environment variable
     * WHATHOOD_DB
     */
    public function setDbName() {
        putenv("WHATHOOD_DB=".$this->getTestName());
    }

    /**
     * initialize a new database
     *
     * sets the db name using setDbName()
     */
    public function initDb() {
        $this->setDbName();
        $doctrine = $this->getServiceLocator()
            ->get('Whathood\Database');

        $doctrine->createDbWithSchema($this->getTestName());
    }

    public function getTestName() {
        return $this->_testName;
    }

    public function random_number() {
        return rand(0,10000000);
    }

    public function m() {
        return $this->getServiceLocator()->get('Whathood\Mapper\Builder');
    }

    public static function getServiceLocator() {
        return \WhathoodTest\Bootstrap::getServiceManager();
    }

    public static function buildUserPolygon($data) {
        $defaults = array(
            'neighborhood' => static::buildTestNeighborhood(),
            'polygon' => static::buildTestPolygon(0, 100)
        );

        return new UserPolygon(array_merge($defaults, $data));
    }

    public static function buildTestPolygon($low = 0, $high = 100, $srid = 4326) {
        $start = new Point(rand($low, $high), rand($low, $high));
        return Polygon::build(array(
                new LineString( array(
                    $start,
                    new Point(rand($low, $high), rand($low, $high)),
                    new Point(rand($low, $high), rand($low, $high)),
                    new Point(rand($low, $high), rand($low, $high)),
                    $start
                    )
                ),
            ),$srid);
    }

    /**
     * will return a random point election
     * @return PointElection
     */
    public static function buildTestPointElection(array $data = null) {
        if (empty($data))
            $data = array();

        $user_polygons = array(
            static::buildTestUserPolygon(),
            static::buildTestUserPolygon(),
            static::buildTestUserPolygon()
        );

        $x = static::rand_int();
        $y = static::rand_int();
        $defaults = array(
            'point' => Point::buildFromText("POINT($x $y)"),
            'user_polygons' => $user_polygons,
            'logger' => new \Whathood\Logger()
        );

        return PointElection::build(array_merge($defaults, $data));
    }

    public static function buildTestUserPolygon(array $data = null) {
        if (empty($data))
            $data = array();
        $defaults = array(
            'neighborhood' => static::buildTestNeighborhood()
        );

        return UserPolygon::build(array_merge($defaults, $data));
    }

    public static function buildTestNeighborhood(array $data = null) {

        return new Neighborhood(array(
            'name' => "T_NEIB".static::rand(),
            'region' => new \Whathood\Entity\Region(array(
                'name' => 'TestRegion'.static::rand()
            ))
        ));

    }

}
