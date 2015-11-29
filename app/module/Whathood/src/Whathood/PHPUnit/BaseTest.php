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

    function rand($length = 10) {
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
}

?>
