<?php
namespace Whathood;

/**
 * Description of MyLogger
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class MyLogger {
    
    protected $logger;
    protected $emailer;
    
    public function __construct($logger,$emailer) {
        $this->logger = $logger;
        $this->emailer = $emailer;
    }
    
    public function info( $subject, $message = null ) {
        
        $this->logger->info( $this->getLogString($subject, $message) );
        
        $this->sendMail( $subject, $message );
    }
    
    public function addressRequest( $regionStr, $addressStr ) {
        
        $subject = $addressStr." requested";
        $this->logger->info( $this->getLogString( $subject ) );
        
        $message = Model\EmailMessageBuilder::addressRequest(
                                                    $regionStr, $addressStr);
        //$this->sendMail($subject, $message);
    }
    
    public function search( $queryString ) {
        
        $subject = $queryString." searched";
        $this->logger->info( $this->getLogString( $subject ) );
        
        //$message = Model\EmailMessageBuilder::search( $queryString );
        //$this->sendMail($subject, $message);
    }
    
    public function error( $subject, $message = null ) {
        $this->logger->err( $this->getLogString($subject, $message) );
        $this->sendMail( $subject, $message );
    }
    
    public function err( $subject, $message = null ) {
        $this->error( $subject, $message );
    }
    
    public function getLogString( $subject, $message = null ) {
        $logStr = $subject;
        if( !empty($message) )
            $logStr = $subject . " " . $message;
        
        return $logStr;
    }
    
    public function sendMail( $subject, $message ) {
        
        try {
            $this->emailer->send( $subject, $message );
        }
        catch( \Exception $e ) {
            $this->logger->err( 'error sending mail:' . $e->getMessage() );
        }
    }
}

?>
