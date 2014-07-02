<?php

/**
 * Description of NeighborhoodFormTest
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodFormTest extends PHPUnit_Framework_TestCase {
    
    public function testSetData() {
        $neighborhood = new \Whathood\Entity\Neighborhood();
        $form = new \Whathood\Form\NeighborhoodPolygonForm();
        
        
        $data = array(
            'neighborhood'  => array(
                'name'  => 'Poplar',
                'latLngJson' => 'this is the latLngJson',
                'region' => array(
                    'name'  => 'Philadelphia'
                ),
                'user' => array(
                //    'name' => 'Test User Name'
                )
            )
        );
        $form->setData($data);
        $form->bind($neighborhood);
        // Validate the form
        if ($form->isValid()) {
            $validatedData = $form->getData();
        } else {
            $messages = $form->getMessages();
            $this->fail();
        }
        
        $this->assertEquals('Poplar', $neighborhood->getName() );
    }
}

?>
