<?php
namespace Application\View\Helper;

use Zend\View\Renderer\RendererInterface as Renderer;
/**
 * Description of showAddressSearchInLayout
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class ShowAddressSearchInLayoutHelper extends \Zend\View\Helper\AbstractHelper {
    
    protected $view = null;
    
    protected $displayInLayout = false;
    
    public function __invoke( $displayInLayout = null ) {
        $this->view->layout()->displayInLayout = false;
        if( $displayInLayout ) {
           $this->view->layout()->displayInLayout = $displayInLayout;
        }
        return $this->view->layout()->displayInLayout;
    }
    
    public function setView(Renderer $view)
    {
        $this->view = $view;
    }
    
    public function getView() {
        return $this->view;
    }
}
?>
