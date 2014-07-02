<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
/**
 * Description of RegionController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class WhathoodUserController extends BaseController {
    
    public function listAction() {
        $users = $this->whathoodUserMapper()->fetchAll();
        return new ViewModel( array( 'users' => $users ) );
    }
    
    public function editAction() {
        $id = $this->params()->fromQuery('id');
        
        if( !empty($id) ) {
            $user = $this->whathoodUserMapper()->byId($id);
        } else
            $user = new \Application\Entity\WhathoodUser();
        
        $form = new \Application\Form\WhathoodUser();
        $form->bind($user);
        
        if( $this->getRequest()->isPost() ) {
            
            $form->setData( $this->getRequest()->getPost() );
            
            if( $form->isValid() ) {
                
                $this->whathoodUserMapper()->save($user);
                $this->redirect()->toUrl('/application/user/show?id=' . $user->getId() );
            }
        }
        $viewModel = new ViewModel(array(
            'form' => $form
        ));
        return $viewModel;
    }
    
    public function byIdAction() {
        $whathood_user_id = $this->getUriParameter('whathood_user_id');
        
        if( empty($whathood_user_id) )
            throw new \InvalidArguementException('id may not be null');
        
        $user = $this->whathoodUserMapper()->byId($whathood_user_id);
        
        $form = new \Application\Form\WhathoodUser( array('editable'=> false) );
        $form->bind($user);
        
        $viewModel = new ViewModel( array( 'form' => $form ) );
        $viewModel->setTemplate('application/user/show.phtml');
        return $viewModel;
    }
    
    public function checkUserNameAvailabilityAction() {
        
        if( $this->getRequest()->isPost() ) {
            
            $userName = $this->params()->fromPost('userName');

            try {
                $this->whathoodUserMapper()->byUserName( $userName );
                $result = 'taken';
            } catch( \Doctrine\ORM\NoResultException $e ) {
                $result = 'available';
            }

            return new JsonModel( array( 'result' => $result ) );
        }
    }
}

?>
