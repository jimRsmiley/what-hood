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
class WhathoodResultSummaryHelper extends \Zend\View\Helper\AbstractHelper {
    

    
    public function __invoke( \Whathood\Model\Whathood\EntityConsensus $consensus ) {
        
        $winners = $consensus->getWinnerUnits();
        
        if( count( $winners ) == 0 ) {
            return "we don't even have any neighborhoods here";
        }
        else if( count( $winners ) > 1 ) {
            return "Awe man, it's a toss up";
        }
        
        $winningUnit = $winners[0];
        
        $percentage = $winningUnit->getVotePercentage();
        
        if( $percentage > .55 ) {
            return "This is definitely " . $winningUnit->getName();
        }
        else {
            return "Eh, it's on the border.";
        }
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
