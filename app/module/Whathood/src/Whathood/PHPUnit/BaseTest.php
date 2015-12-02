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
use Whathood\Election\PointElection;

/**
 * responsible for anything to do with the database
 */
class BaseTest extends \PHPUnit_Framework_TestCase {

    protected $_testName;

    public function getTestName() {
        return $this->_testName;
    }

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

    public function setUp() {
        $this->initTestName();
    }

    public function setDbName() {
        putenv("WHATHOOD_DB=".$this->getTestName());
    }

    public function initDb() {
        $this->setDbName();
        $doctrine = $this->getServiceLocator()
            ->get('Whathood\Doctrine');
        $doctrine->initDb($this->getTestName());
    }

    public function tearDown() {
        $this->_conn = null;
    }

    public function m() {
        return $this->getServiceLocator()->get('Whathood\Mapper\Builder');
    }

    public static function getServiceLocator() {
        return \WhathoodTest\Bootstrap::getServiceManager();
    }

    public static function buildUserPolygon($data) {
        $defaults = array(
            'neighborhood' => $neighborhood1
        );

        return new UserPolygon(array_merge($defaults, $data));
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

?>
