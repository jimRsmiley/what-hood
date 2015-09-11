<?php
/**
 * This is the config file for SlmQueue. Just drop this file into your config/autoload folder (don't
 * forget to remove the .dist extension from the file), and configure it as you want
 */

return array(
    'slm_queue' => array(
        'queues' => array(
            'message_queue' => array(
                'deleted_lifetime' => 7,
                'buried_lifetime' => 120
            )
        ),

        'worker_strategies' => array(
            'default' => array( // per worker
            ),
            'queues' => array( // per queue
                'default' => array(
                ),
            ),
        ),

        'strategy_manager' => array(),

        'job_manager' => array(
            'factories' => array(
                'Whathood\Job\EmailJob' => 'Whathood\Factory\EmailJobFactory',
                'Whathood\Job\NeighborhoodBorderBuilderJob' => 'Whathood\Factory\NeighborhoodBorderBuilderJobFactory'
            )
        ),

        'queue_manager' => array(
            'factories' => array(
                'message_queue' => 'SlmQueueDoctrine\Factory\DoctrineQueueFactory'
            )
        ),
    ),
);
