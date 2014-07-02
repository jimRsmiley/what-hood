<?php
namespace Application\View\Helper;

use Zend\ServiceManager\ServiceManager;
/**
 * Helps view scripts with displaying authenticated user information
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class StaticGoogleMapImageUrl extends \Zend\View\Helper\AbstractHelper {

    protected $apiKey = 'AIzaSyBLys7j5e3csSnQojBh_9fKqq19DPvEOJ0';
    
    public function __invoke($lat,$lng) {
        return 'http://maps.googleapis.com/maps/api/staticmap?center='.$lat.','.$lng.'&zoom=17&size=400x300&maptype=roadmap
&markers=color:red%7Ccolor:red%7Clabel:C%7C'.$lat.','.$lng.'&sensor=false&key='.$this->apiKey;
    }
}

?>
