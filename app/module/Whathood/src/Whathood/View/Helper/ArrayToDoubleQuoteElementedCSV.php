<?php
namespace Whathood\View\Helper;
/**
 * 
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class ArrayToDoubleQuoteElementedCSV  extends \Zend\View\Helper\AbstractHelper {
    
    public function __invoke( $array ) {
        
        if( !is_array($array) )
            return '';
        
        $commaSeparated = implode( '","', $array );
        
        $commaSeparated = '"'.$commaSeparated.'"';
        //\Zend\Debug\Debug::dump( $commaSeparated );
        //exit;
        return $commaSeparated;
    }
}

?>
