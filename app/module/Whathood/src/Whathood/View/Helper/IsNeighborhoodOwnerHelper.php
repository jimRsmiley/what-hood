<?php
namespace Whathood\View\Helper;

use Zend\View\Renderer\RendererInterface as Renderer;
/**
 * I need a summary line like "This spot is most likely in Frankford" or
 * Yeah this is definitely Fishtown
 * or "It's a tossup"
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class IsNeighborhoodOwnerHelper extends \Zend\View\Helper\AbstractHelper {
    

    public function __invoke(
            \Whathood\Form\NeighborhoodBoundaryFieldset $neighborhood, 
            $user ) {
        
        if( empty( $user ) )
            return false;
        
        if( $neighborhood->get('whathoodUser')->get('id')->getValue() == $user->getId() ) {
            return true;
        }
        return false;
    }

    protected $view = null;
    
    public function setView(Renderer $view)
    {
        $this->view = $view;
    }
    
    public function getView() {
        return $this->view;
    }
}
?>
