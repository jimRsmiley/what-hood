<?php
/**
 * This is the config file for SlmQueue. Just drop this file into your config/autoload folder (don't
 * forget to remove the .dist extension from the file), and configure it as you want
 */

return array(
    'slm_queue' => array(
        'queues' => array(
            'message_queue' => array(

                /* How long to keep deleted (successful) jobs (in minutes). */
                'deleted_lifetime' => 0,

                /* How long to keep buried (failed) jobs (in minutes) */
                'buried_lifetime' => 60,

                // connection => '',

                // table_name => ''
            )
        ),

        'worker_strategies' => array(
            'default' => array( // per worker
                // memory max 512 megs
                \SlmQueue\Strategy\MaxMemoryStrategy::class => ['max_memory' => 512 * 1024 * 1024],
            ),
            'queues' => array( // per queue
                'default' => array(
                ),
            ),
        ),

        'strategy_manager' => array(),

        'job_manager' => array(
            'factories' => array(
                'Whathood\Job\EmailJob'                     => 'Whathood\Factory\EmailJobFactory',
                'Whathood\Job\NeighborhoodBorderBuilderJob' => 'Whathood\Factory\NeighborhoodBorderBuilderJobFactory',
                'Whathood\Job\HeatmapBuilderJob'            => 'Whathood\Factory\HeatmapBuilderJobFactory'
            )
        ),

        'queue_manager' => array(
            'factories' => array(
                'message_queue' => 'SlmQueueDoctrine\Factory\DoctrineQueueFactory'
            )
        ),
    ),
);
