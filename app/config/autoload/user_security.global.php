<?php

return array(
    'bjyauthorize' => array(
		
		'default_role' => 'guest',
		// Using the authentication identity provider, which basically reads the roles from the auth service's identity
		
		#'identity_provider' => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',
		/* here, 'guest' and 'user are defined as top-level roles, with
		 * 'admin' inheriting from user
		 */
		\BjyAuthorize\Provider\Role\Config::class => [
			'guest' => [],
			'user'  => ['children' => [
				'administrator' => [],
				'never-any-user' => []
			]],
		],
		
		'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
				array(
					'controller' => 'Whathood\Controller\Region',
					'roles' => array('guest')
				),
				array(
                    'controller' => 'zfcuser',
                    'roles' => array('guest')
                ),
				array(
					'controller' => 'Whathood\Controller\Admin',
					'roles' => array('administrator'))
            ),
        ),
    ), /* end bjyauthorize */
);
