<?php
namespace Whathood\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Whathood\Model\Whathood\WhathoodResult;

/**
 * class manages the searching for regions and neighborhood names
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class SearchController extends BaseController {

    public function indexAction() {
        $queryString = $this->getUriParameter('q');

        /*
         * get regions
         */
        try {
            $regions = $this->m()->regionMapper()->nameLike( $queryString );
        } catch( \Doctrine\ORM\NoResultException $e ) {
            $regions = array();
        }

        try {
            $neighborhoods = $this->m()->neighborhoodMapper()
                                            ->nameLike( $queryString );
        } catch( \Doctrine\ORM\NoResultException $e ) {
            $neighborhoods = array();
        }

       $this->logger()->info( "neighbordhood/region search for '$queryString'");

        $this->pushEmailJob(\Whathood\View\MailMessageBuilder::buildSomeoneSearched($queryString));

       return new ViewModel( array(
            'queryString' => $queryString,
            'regions' => $regions,
            'neighborhoods' => $neighborhoods
        ));
    }
}
