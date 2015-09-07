<?php
namespace Whathood\Job;

class EmailJob extends AbstractJob {

    protected $_emailer;

    public function __construct($emailer) {
	if (empty($emailer))
	    throw new \InvalidArgumentExceptiON('emailer must be defined');
        $this->_emailer = $emailer;
    }

    public function execute() {
        $payload    = $this->getContent();
        $subject    = $payload['subject'];
        $body       = $payload['body'];

        $this->getEmailer()->send($subject, $body);
        $this->infoLog("sent email; subject='$subject'\nbody: $body");
    }

    public function getEmailer() {
        return $this->_emailer;
    }
}

