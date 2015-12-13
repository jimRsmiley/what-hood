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
        putenv("WH_PHPUNIT_DB_NAME=".$this->getTestName());
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

    public static function heatMap() {
        return new HeatMap( array(
            'points' => array( 
                    new HeatMapPoint( '0', '1', $value = 25 )
                ),
            'neighborhood' => self::neighborhood(),
            'region' => self::region()
        ));
    }
    
    /*
     * return a filler polygon, useful for when not testing polygon stuff
     */
    public static function polygon() {
        
        $lineString = new LineString(array( 
                        new Point(0,0),
                        new Point(10,0),
                        new Point(10,10),
                        new Point(0,10),
                    ));
        $lineString->close();
        
        return new Polygon( $rings = array( $lineString ) );
    }
    
    public static function neighborhoodPolygon($whathoodUser) {
        
        if( empty( $whathoodUser ) )
            throw new \InvalidArgumentException('must supply an already saved whathoodUser');
        
        $n = new UserPolygon( array(
            'neighborhood'  => self::neighborhood(),
            'region'        => self::region(),
            'polygon'       => self::polygon(),
            'whathoodUser'  => $whathoodUser
        ));
        return $n;
    }
    
    public static function neighborhood() {
                return new Neighborhood( array(
                'name'          => 'Mayfair',
                'dateTimeAdded' => '2013-01-01 12:30:00',
                'polygon'       => self::polygon(),
                'region'        => self::region(),
                'whathoodUser'  => self::whathoodUser()
                ));
    }
    
    public static function neighborhoodPolygonVote() {
        return new NeighborhoodBoundaryVote( array(
            'dateTimeAdded' => '2013-01-01 12:30:00',
            'whathoodUser'  => self::whathoodUser(),
            'vote'          => -1
        ));
    }
    
    public static function region() {
        return new Region( array(
            'name' => 'Philadelphia',
            'centerPoint' => new Point(0,1)
        ));
    }
    
    public static function whathoodUser() {
        return new WhathoodUser( array(
            'userName'  => 'test_ whathood user name',
        ));
    }
    
    public static function facebookUser() {
        return new FacebookUser( array(
            'id'    => '123',
            'name'  => 'Joe Schmoe',
            'firstName' => 'Joe',
            'lastName' => 'Schmoe',
            'link'  => 'http://facebook.com/joe.schmoe',
            'username'  => 'joe123',
            'gender' => 'male',
            'timezone' => 'eastern',
            'locale'    => 'US',
            'verified'  => 'sure',
            'updatedTime' => '2013-01-01 12:34:45'
        ));
    }
}
