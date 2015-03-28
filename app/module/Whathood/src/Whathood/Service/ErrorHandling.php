<?php

namespace Whathood\Service;
 
class ErrorHandling
{
    protected $logger;
    protected $sm;
    
    function __construct($logger,$sm)
    {
        $this->logger = $logger;
        $this->sm = $sm;
    }
 
    function logException(\Exception $e)
    {
        /*$trace = $e->getTraceAsString();
        $i = 1;
        do {
            $messages[] = $i++ . ": " . $e->getMessage();
        } while ($e = $e->getPrevious());
 
        $log = "Exception:\n" . implode("\n", $messages) . "\n";
        $log .= 'User Agent:' . $this->getUserAgent() . "\n";
        $log .= "\nTrace:\n" . $trace;
 
        $this->logger->err($subject = 'ERROR ENCOUNTERED', $log);*/
    }
    
    function getUserAgent() {
        $request = $this->sm->get('request');
        $header = $request->getHeader('useragent');
        if( null !== $header )
            return $header->getFieldValue();
        else
            return null;
    }
}
?>