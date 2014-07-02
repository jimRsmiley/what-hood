<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Entity\NeighborhoodPolygonVote;
use Application\Model\NeighborhoodPolygonVoteCollection;

/**
 * Description of RegionController
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class NeighborhoodPolygonVoteController extends BaseController {
    
    public function castAction() {
        
        $request = $this->getRequest();
        if( $request->isPost() ) {
            
            $data = $request->getPost();
            $neighborhoodPolygonId = $data['neighborhoodPolygonId'];
            $vote = $data['vote'];

            if( empty( $neighborhoodPolygonId ) )
                throw new \InvalidArgumentException( 
                                    'neighborhoodPolygonId may not be empty');
            
            if( empty( $vote ) )
                throw new \InvalidArgumentException( 'vote may not be empty' );
            
            $whathoodUser = $this->getAuthenticationService()->getWhathoodUser();
            
            $neighborhoodPolygon = $this->neighborhoodPolygonMapper()
                                            ->byId( $neighborhoodPolygonId );
            /*
             * see if the user already voted
             */
            try {
                $neighborhoodPolygonVote = $this->neighborhoodPolygonVoteMapper()
                            ->byWhathoodUserId(
                                $whathoodUser->getId(), $neighborhoodPolygon );
                $neighborhoodPolygonVote->setVote( $vote );
                $this->neighborhoodPolygonVoteMapper()
                                            ->update( $neighborhoodPolygonVote );
            } 
            /*
             * must not have voted yet
             */
            catch( \Doctrine\ORM\NoResultException $e ) {
                /*
                 * Cast the vote
                 */
                $neighborhoodPolygonVote = new NeighborhoodPolygonVote( array(
                    'neighborhoodPolygon' => $neighborhoodPolygon,
                    'vote'                => $vote,
                    'whathoodUser'        => $whathoodUser
                ));

                $this->neighborhoodPolygonVoteMapper()
                                            ->save( $neighborhoodPolygonVote );
            }
            
            return new JsonModel( array('status' => 'ok' ) );
        }
    }
    
    public function byNeighborhoodPolygonIdAction() {
        
        $neighborhoodPolygonId = $this->getUriParameter('neighborhoodPolygonId');
        
        if( empty( $neighborhoodPolygonId ) )
            throw new \InvalidArgumentException( 
                                'neighborhoodPolygonId may not be empty');

        $whathoodUser = $this->getAuthenticationService()->getWhathoodUser();
        $neighborhoodPolygon = $this->neighborhoodPolygonMapper()
                                        ->byId( $neighborhoodPolygonId );
        
        if( !empty( $whathoodUser ) ) {
            
            /** 
             * see if the user voted for the neighborhoodPolygon
             */
            try {
                $thisUsersVote = $this->neighborhoodPolygonVoteMapper()
                        ->byWhathoodUserId(
                                $whathoodUser->getId(), $neighborhoodPolygon );
            } catch( \Doctrine\ORM\NoResultException $e ) {}
        }
        
        $voteResult = new \Application\Model\VoteResult\VoteResult();
        $voteResult->setThisUsersVote( $thisUsersVote );

        $neighborhoodPolygonVoteCollection  = $this->neighborhoodPolygonVoteMapper()
                                    ->byNeighborhoodPolygon( 
                                        $neighborhoodPolygon );

        $voteResult->setAllVotesForNeighborhoodPolygon($neighborhoodPolygonVoteCollection);
        $jsonModel = new JsonModel( array(
            'voteResult' => $voteResult->toArray()
        ));

        return $jsonModel;
    }
}

?>
