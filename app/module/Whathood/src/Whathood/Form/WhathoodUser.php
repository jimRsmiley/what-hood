<?php

namespace Whathood\Form;

use Zend\Form\Element;
use Zend\Form\Form;
/**
 * Description of AddNeighborhoodForm
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class WhathoodUser extends Form {
    
    
    public function __construct( $params = array() ) {
        parent::__construct('user');

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Send',
            )
        ));
        
        /*
         * add the neighborhood
         */
        $this->add(array(
            'name'  => 'user',
            'type' => 'Whathood\Form\WhathoodUserFieldset',
            'options' => array(
                'use_as_base_fieldset' => true
            )
        ));
        
        $inputFilter = new \Zend\InputFilter\InputFilter();
        
        $this->setInputFilter($inputFilter);
        
        if(array_key_exists('editable', $params) && !$params['editable'] ) {
            $this->get('user')->get('id')->setAttribute('readonly','true');
            $this->get('user')->get('userName')->setAttribute('readonly','true');
            
        }
    }
}

?>
