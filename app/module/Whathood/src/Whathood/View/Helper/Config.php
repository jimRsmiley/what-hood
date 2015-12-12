<?php
namespace Whathood\View\Helper;

/**
 * Description of UserRegionUrlHelper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Config extends \Zend\View\Helper\AbstractHelper {

    protected $_config;

    public function setConfig($config) {
        $this->_config = $config;
    }

    public function getConfig() {
        return $this->_config;
    }

    public function __invoke($key) {
        return $this->getConfig()->$key;
    }
}
?>
