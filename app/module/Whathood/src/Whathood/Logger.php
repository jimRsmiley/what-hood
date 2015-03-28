<?php
namespace Whathood;

/**
 * Description of MyLogger
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Logger extends \Zend\Log\Logger {

    protected $logger;
    protected $emailer;

    public function error($msg) {
        $this->err($msg);
    }
}

?>
