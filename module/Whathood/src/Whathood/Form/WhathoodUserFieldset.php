<?php
namespace Application\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

/**
 * Description of RegionFieldset
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class WhathoodUserFieldset extends Fieldset implements InputFilterProviderInterface {
    
    public function __construct() {
        
        parent::__construct('user');
        
        $this->setObject( new \Application\Entity\WhathoodUser() );
        $this->setHydrator(new ClassMethodsHydrator(
                                            $underscoreSeparatedKeys = false));

        $this->add(array(
            'name' => 'id',
            'id'       => 'user_id',
            'options' => array(
                'label' => 'Id',
            ),
            'attributes ' => array(
                'required' => 'false',
            )
        ));
        
        $this->add(array(
            'name' => 'userName',
            'id'       => 'user_name',
            'options' => array(
                'label' => 'User',
            ),
            'attributes ' => array(
                'required' => 'false',
            )
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
