<?php
namespace Whathood\Model;

use Whathood\Entity\UserPolygon;
/**
 * Description of EmailMessageBuilder
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class EmailMessageBuilder {
    
    public static function neighborhoodAdd( UserPolygon $np ) {
        return 'A new neighborhood polygon has been added<br/><br/>'
        . '<a href="http://whathood.in/n/id/'.$np->getId().'">Go to neighborhood</a>';
    }
    
    public static function addressRequest( $regionName, $addressStr ) {
        return '<a href="http://whathood.in/a/'.$regionName.'/'.str_replace(' ', '+', $addressStr ) . '">'.$addressStr.'</a>';
    }
    
    public static function search( $queryString ) {
        return '<a href="http://whathood.in/search?q='.$queryString.'">'.$queryString.'</a>';
    }
}

?>
