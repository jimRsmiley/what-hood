<?php

namespace Whathood\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

use Whathood\Entity\UserPolygon;

class NeighborhoodBoundaryFieldset extends Fieldset implements InputFilterProviderInterface {

    public function __construct()
    {
        parent::__construct('neighborhoodPolygon');

        $this->setObject( new UserPolygon() );
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
            'type' => 'Whathood\Form\NeighborhoodFieldset',
            'attributes ' => array(
                'required' => 'false',
            ),
            'options' => array(
                'use_as_base_fieldset' => false
            )
        ));

        $this->add(array(
            'name' => 'whathoodUser',
            'type' => 'Whathood\Form\WhathoodUserFieldset',
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
