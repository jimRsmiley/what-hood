<?php

namespace Whathood\Service;

use Whathood\Job\EmailJob;

class MessageQueueService {

    public static function pushJob() {
        $job = new EmailJob;
        $job->setContent(array(
            'to'      => 'john@doe.com',
            'subject' => 'Just hi',
            'message' => 'Hi, I want to say hi!'
        ));

        $this->queue->push($job);
    }
}
