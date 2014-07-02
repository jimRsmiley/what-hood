<?php

namespace Application\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

use Application\Entity\NeighborhoodPolygon;

class NeighborhoodPolygonFieldset extends Fieldset implements InputFilterProviderInterface {
    
    public function __construct()
    {
        parent::__construct('neighborhoodPolygon');
        
        $this->setObject( new NeighborhoodPolygon() );
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
            'name' => 'neighborhood',
            'type' => 'Application\Form\NeighborhoodFieldset',
            'attributes ' => array(
                'required' => 'false',
            ),
            'options' => array(
                'use_as_base_fieldset' => false
            )
        ));
        
        $this->add(array(
            'name' => 'whathoodUser',
            'type' => 'Application\Form\WhathoodUserFieldset',
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
