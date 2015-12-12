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
    protected $_entityManager;
    protected $_testName;

    public function setUp()
    {
        parent::setUp();

        if( getenv('APPLICATION_ENV') !==  'test' )
            throw new \Exception("you must set APPLICATION_ENV to 'test'");
        $this->sm = Bootstrap::getServiceManager();
        $this->setApplicationConfig(include 'TestConfig.php');
        $this->setRemoteAddr();
        $this->initTestName();
        $this->setDbName();
    }

    public function tearDown() {
        /*$doctrine = $this->sm->get('Whathood\Doctrine');
        $doctrine->dropDb(
            $doctrine->getPostgresConnection($this->getTestName()),
            $this->getTestName()
        );*/
    }

    public function initTestName() {
        $reflect = new \ReflectionClass($this);
        $testName = strtolower($reflect->getShortName()."_".$this->getName());
        $this->setTestName($testName);
    }

    public function setDbName() {
        putenv("WHATHOOD_DB=".$this->getTestName());
    }

    public function setRemoteAddr() {
        $serverParams = $this->getRequest()->getServer();
        $serverParams->set("REMOTE_ADDR","0.0.0.0");
        $this->getRequest()->setServer($serverParams);
    }

    public function initDb() {
        $doctrine = $this->sm->get('Whathood\Doctrine');
        $doctrine->createDbWithSchema($this->getTestName());
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

    public function setTestName($testName) {
        $this->_testName = $testName;
    }

    public function getTestName() {
        return $this->_testName;
    }

}
?>
