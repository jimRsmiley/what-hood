<?php
namespace Whathood\Job;

use SlmQueue\Job\AbstractJob;
use SlmQueue\Queue\QueueAwareInterface;
use SlmQueue\Queue\QueueAwareTrait;

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
        print "sent email; subject='$subject'\n";
        print "body: $body\n";
    }

    public function getEmailer() {
        return $this->_emailer;
    }
}

