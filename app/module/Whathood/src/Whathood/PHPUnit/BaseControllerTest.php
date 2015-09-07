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

        $doctrine = $this->sm->get('Whathood\Doctrine');
        $reflect = new \ReflectionClass($this);

        $testName = strtolower($reflect->getShortName()."_".$this->getName());

        putenv("WHATHOOD_DB=$testName");
        $doctrine->initDb($testName);
    }

    public function setupDb() {
        $doctrineBaseTest = new \Whathood\Doctrine();
        $doctrineBaseTest->initDb();
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
