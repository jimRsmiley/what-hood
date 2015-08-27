<?php
namespace Whathood\Job;

use SlmQueue\Job\AbstractJob;

class TestJob extends AbstractJob {

    public function execute() {
        print "Hello World";
    }
}

