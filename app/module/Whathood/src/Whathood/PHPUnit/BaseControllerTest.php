<?php
namespace Whathood\PHPUnit;

use WhathoodTest\Bootstrap;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class BaseControllerTest extends AbstractHttpControllerTestCase {

    use TestUtilTrait;

    protected $sm;
    protected $_entityManager;

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

    public function setRemoteAddr() {
        $serverParams = $this->getRequest()->getServer();
        $serverParams->set("REMOTE_ADDR","0.0.0.0");
        $this->getRequest()->setServer($serverParams);
    }

    public function initDb() {
        $doctrine = $this->sm->get('Whathood\Database');
        $doctrine->createDbWithSchema($this->getTestName());
    }

    public function printResponse() {
        print $this->getResponse();
    }
}
