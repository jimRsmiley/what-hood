<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Module;
class AuthController extends BaseController
{
    public function loginAction()
    {
        $redirectTo = $this->params()->fromQuery('redirect_to');
        
        $auth = $this->getAuthService();

        $facebook = $auth->getFacebook();

        // Get User ID
        $facebookUserId = $facebook->getUser();
        
        // We may or may not have this data based on whether the user is logged in.
        //
        // If we have a $user id here, it means we know the user is logged into
        // Facebook, but we don't know if the access token is valid. An access
        // token is invalid if the user logged out of Facebook.

        if ($facebookUserId) {
          try {
            // Proceed knowing you have a logged in user who's authenticated.
            $facebookUserProfile = $facebook->api('/me');
            
          } catch (FacebookApiException $e) {
            error_log($e);
            $facebookUserId = null;
          }
        }
        
        // Login or logout url will be needed depending on current user state.
        if ($facebookUserId) {

            $facebookUser = new \Application\Entity\FacebookUser($facebookUserProfile);
            
            // see if we've already stored the user
            try {
                $whathoodUser = $this->whathoodUserMapper()
                                    ->byFacebookId( $facebookUser->getId() );
            }
            /**
             * No whathoodUser found in database
             */
            catch( \Doctrine\ORM\NoResultException $e ) {
                
                $userName = $this->getUriParameter('userName');
                
                /*
                 * we need the user to pick a username
                 */
                if( empty( $userName ) ) {
                    $url = $this->url()->fromRoute('auth', array( 'action'=> 'create-user-name' ) );
                    $this->redirect()->toUrl($url);
                    return;
                }
                
                /*
                 * create it and save it
                 */
                $whathoodUser = new \Application\Entity\WhathoodUser( array(
                    'userName'      => $userName,
                    'facebookUser'  => $facebookUser 
                ));
                
                // then save it
                $this->whathoodUserMapper()->save( $whathoodUser );
                $this->getLogger()->info('new user created ' . $whathoodUser->getUserName(),'' );
            }
            
            // save the user in the session
            $auth->setWhathoodUser($whathoodUser);
            
            /*
             * User is now logged in
             */
            
            $mylogger = $this->getLogger()->info( $whathoodUser->getUserName() . ' has logged in' );
            if( $redirectTo ) {
                $response = $this->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $redirectTo );
                $response->setStatusCode(302);
            }
            else {
                $response = $this->getResponse();
                $response->getHeaders()->addHeaderLine('Location', "/" );
                $response->setStatusCode(302);
            }
            
            $viewModel = new ViewModel();
        } 
        
        /*
         * we're not logged in, present the login prompt
         */
        else {
            $viewModel = new ViewModel();
            $viewModel->setVariable(
                    'loginUrl', $facebook->getLoginUrl() );
            $viewModel->setTemplate('application/auth/login-prompt.phtml');
        }
        
        return $viewModel;
    }
    
    public function logoutAction() {
        $this->getAuthService()->clear();

        $redirectTo = $this->params()->fromQuery('redirect_to');
        
        if( $redirectTo && !$this->isAuthenticateRoute($redirectTo) ) {
            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $redirectTo );
            $response->setStatusCode(302);
        }
        else {
            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Location', "/" );
            $response->setStatusCode(302);
        }
    }
    
    public function getAuthService() {
        return $this->getServiceLocator()
                ->get('Application\Model\AuthenticationService');
    }
    
    public function isAuthenticateRoute( $urlString ) {
        $request = new \Zend\Http\Request();
        $request->setUri( new \Zend\Uri\Http( $urlString ) );
        
        // should be a Zend\Mvc\Router\Http\TreeRouteStack
        $match = $this->getEvent()->getRouter()->match( $request );
        
        foreach( Module::$authenticateRoutes as $routeName => $params ) {
            $matchRoute = new \Zend\Mvc\Router\Http\RouteMatch( $params );

            $controller = $match->getParam( 'controller' );

            /*
             * if the controller and action names match
             */
            if( preg_match( '/'.$controller.'$/', $matchRoute->getParam('controller') )
                    && strtolower($match->getParam( 'action' )) == strtolower($matchRoute->getParam( 'action' )) ) {
                return true;
            }
        }
        return false;
    }
    
    public function createUserNameAction() {}
}
