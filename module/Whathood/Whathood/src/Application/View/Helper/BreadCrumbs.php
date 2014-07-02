<?php
namespace Application\View\Helper;

use Zend\View\Renderer\RendererInterface as Renderer;
/**
 * this class is for the breadcrumb url stuff that sits on top of the main
 * center container
 * 
 * it follows \ region \ neighborhood
 * 
 * or
 * 
 * \ region \ address
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class BreadCrumbs  extends \Zend\View\Helper\AbstractHelper {
    
    protected $view = null;
    
    public function __invoke( $params = array() ) {
        
        if( empty( $params ) )
            return "";
        
        $html = '<ul class="breadcrumb" style="text-align: left">';
        
        /*
         * Home
         */
        $html .= '<li><a href="'.$this->view->url('home').'">Home</a></li>';
        
        /**
         * Region
         */
        if( array_key_exists( 'regionName', $params ) ) {
            $region = $params['regionName'];
            
            if( !array_key_exists('neighborhoodName', $params ) && !array_key_exists('address', $params ) ) {
                $html .= '<li>'.$region.'</li>';
            } else {
               $html .= '<li><a href="'.$this->view->url('region',array('region_name' => $region )).'">'.$region.'</a></li>';
            }
        }        
    
        /*
         * Current Location
         */
        if( array_key_exists( 'currentLocation', $params ) ) {
            $currentLocation = $params['currentLocation'];
            
            if( $currentLocation ) {
                $html .= '<li>Current Location</li>';
            }
        }
        
        /*
         * User
         */
        if( array_key_exists( 'user', $params ) ) {
            $userName = $params['user'];
            $html = '<li class="active">'
                    .'user: <a href="'.$this->view->url('user_by_name', array(
                                                    'whathood_user_name'=> $userName
                                        ))
                                .'">'
                                .$userName
                            .'</a>'
                    .'</li>';
        }
    
        /*
         * Neighborhood
         */
        if( array_key_exists( 'neighborhoodName', $params ) ) {
            $neighborhoodName = $params['neighborhoodName'];
            
            if( !array_key_exists( 'userName', $params ) ) {
                $html .= '<li>'.$neighborhoodName.'</li>';
            }else {
                $html .= '<li><a href="">'.$neighborhoodName.'</a></li>';
            }
        }
            
        /*
         * Address
         */
        if( array_key_exists( 'address', $params ) ) {
            $address = $params['address'];
            
            if( !empty( $address ) )
                $html .= '<li class="active">'.$address.'</li>';
        }
        


        $html .= '</ul>';

        return '<div class="">'.$html.'</div>';
    }
    
    public function setView(Renderer $view)
    {
        $this->view = $view;
    }
    
    public function getView() {
        return $this->view;
    }
}

?>
