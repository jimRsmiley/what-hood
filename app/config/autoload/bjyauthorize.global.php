<?php

return array(
    'bjyauthorize' => array(
        
	'default_role' => 'guest',

	// Using the authentication identity provider, which basically reads the roles from the auth service's identity
        'identity_provider' => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',
 
            /* here, 'guest' and 'user are defined as top-level roles, with
             * 'admin' inheriting from user
             */
            \BjyAuthorize\Provider\Role\Config::class => [
                'guest' => [],
                'user'  => ['children' => [
                    'admin' => [],
                ]],
       ],

       'role_providers'        => array(
            // using an object repository (entity repository) to load all roles into our ACL
            'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' => array(
                'object_manager'    => 'doctrine.entitymanager.orm_default',
                'role_entity_class' => 'Whathood\Entity\Role',
            ),
        ),

	
    ), /* end bjyauthorize */
);
