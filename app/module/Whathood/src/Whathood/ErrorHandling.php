<?php

namespace Whathood;

class ErrorHandling
{
    protected $logger;
    protected $emailer;

    function __construct($logger,\Whathood\Email $emailer = null)
    {
        $this->logger = $logger;
        $this->emailer = $emailer;
    }

    function logException(\Exception $e)
    {
        $exception = $e;
        $trace = $e->getTraceAsString();
        $i = 1;
        do {
            $messages[] = $i++ . ": " . $e->getMessage();
        } while ($e = $e->getPrevious());

        $log = "Exception:\n" . implode("n", $messages);
        $log .= "\nTrace:\n" . $trace;

        $this->logger->err($log);

        if ($this->emailer)
            static::emailException($exception, $this->emailer);
    }

    public static function emailException(\Exception $e, Email $emailer) {
        $subject = $e->getMessage()."  ".rand(1000,9999);
        $messageBody = sprintf("<strong>%s</strong><br/><br/>%s",
            $e->getMessage(), $e->getTraceAsString() );

        $emailer->send($subject, $messageBody);
    }
}
