<?php
namespace Whathood\PHPUnit;

/**
 * responsible for anything to do with the database
 */
class BaseTest extends \PHPUnit_Framework_TestCase {

    use TestUtilTrait;

    /**
     * set up the test
     *
     * initializes the test name
     */
    public function setUp() {
        $this->initTestName();
    }

    public function tearDown() {
        $this->_conn = null;
    }
}
