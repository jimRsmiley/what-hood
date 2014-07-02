<?php

namespace Whathood\View\Model;
/**
 * Description of ErrorViewModel
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class ErrorViewModel extends \Zend\View\Model\ViewModel {
    
/**
     * Constructor
     *
     * @param  null|array|Traversable $variables
     * @param  array|Traversable $options
     */
    public function __construct($variables = null, $options = null)
    {
        $this->setTemplate('whathood/error/index.phtml');
        parent::__construct($variables,$options);
    }
}

?>
