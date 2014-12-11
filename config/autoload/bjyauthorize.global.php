<?php

$bjyConfig = array(
	// Using the authentication identity provider, which basically reads the roles from the auth service's identity
	'identity_provider' => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',

	'role_providers'        => array(
		// using an object repository (entity repository) to load all roles into our ACL
		'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' => array(
			'object_manager'    => 'doctrine.entitymanager.orm_default',
			'role_entity_class' => 'SkelletonApplication\Entity\Role',
		),
	),
);

return array(
	'bjyauthorize' => $bjyConfig
);