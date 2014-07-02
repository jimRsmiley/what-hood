<?php

namespace Whathood\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

use Whathood\Entity\Neighborhood;

class NeighborhoodFieldset extends Fieldset implements InputFilterProviderInterface {
    
    public function __construct()
    {
        parent::__construct('neighborhood');
        
        $this->setObject( new Neighborhood() );
        $this->setHydrator(new ClassMethodsHydrator(false));
        
        $this->add(array(
            'name' => 'id',
            'id'       => 'neighborhood_id',
            'options' => array(
                'label' => 'Neighborhood ID',
                'id'       => 'neighborhood_id'
            ),
            'attributes ' => array(
                'required' => 'false',
            )
        ));
        
        $this->add(array(
            'name' => 'name',
            'id'       => 'neighborhood-name',
            'options' => array(
                'label' => 'Neighborhood Name',
                'id'       => 'neighborhood-name'
            ),
            'attributes ' => array(
                'required' => 'false',
            )
        ));

        $this->add(array(
            'name' => 'region',
            'type' => 'Whathood\Form\RegionFieldset',
            'attributes ' => array(
                'required' => 'false',
            ),
            'options' => array(
                'use_as_base_fieldset' => false
            )
        ));
    }
    
    public function getInputFilterSpecification()
    {
        return array(
            /*'name' => array(
                'validators' => array(
                )
            ),
            'latLngJson' => array(
                'validators' => array(
                )
            )*/
        );
    }
}
?>
