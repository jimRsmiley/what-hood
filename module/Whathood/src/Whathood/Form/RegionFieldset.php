<?php
namespace Application\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

use Application\Entity\Region;

/**
 * Description of RegionFieldset
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class RegionFieldset extends Fieldset implements InputFilterProviderInterface {
    
    public function __construct() {
        parent::__construct('region');
        
        $this->setObject( new Region() );
        $this->setHydrator(new ClassMethodsHydrator);

        $this->add(array(
            'name' => 'name',
            'id'       => 'region-name',
            'options' => array(
                'label' => 'Name of Region',
                'id'       => 'region-name'
            ),
            'attributes ' => array(
                'required' => 'false',
                'class' => 'span3'
            )
        ));
        
        $this->add(array(
            'name' => 'centerPoint',
            'id'       => 'centerPoint',
        ));
    }
    
    public function getInputFilterSpecification() {
        return array(
            /*'name' => array(
                'validators' => array(
                )
            ),*/
        );
    }
}

?>
