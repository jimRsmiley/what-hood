<?php

namespace Whathood\Form;

use Zend\Form\Element;
use Zend\Form\Form;
/**
 * Description of AddNeighborhoodForm
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodPolygonForm extends Form {
    
    
    public function __construct( $edit = true ) {
        parent::__construct('AddNeighborhood');

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'Submit',
                'value' => 'Save Neighborhood',
            )
        ));
        
        /*
         * add the neighborhood
         */
        $this->add(array(
            'name'  => 'neighborhoodPolygon',
            'type' => 'Whathood\Form\NeighborhoodPolygonFieldset',
            'options' => array(
                'use_as_base_fieldset' => true
            )
        ));
        
        $this->add(array(
            'name' => 'polygonGeoJson',
            'type'  => 'hidden',
            'attributes ' => array(
                'required' => 'false'
            )
        ));
                
        /*
         * InputFilter
         */
        $inputFilter = new \Zend\InputFilter\InputFilter();
        $this->setInputFilter($inputFilter);
    }
}

?>
