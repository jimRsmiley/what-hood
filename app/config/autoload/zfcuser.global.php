<?php

return array(
    'zfcuser' => array(
    	// telling ZfcUser to use our own class
         'user_entity_class'       => 'Article\Entity\User',
         // telling ZfcUserDoctrineORM to skip the entities it defines
         'enable_default_entities' => false,
    )
);
