<?php
return array(
    'bower' => array(
        'bower_folder' => array(
            'os' => 'bower_components',
        ),
        'pack_folder' => array(
            'os' => 'public/js',
            'web' => '/js',
        ),
        'debug_folder' => array(
            'os' => 'public/js/dev',
            'web' => '/js/dev',
        ),
        'debug_mode' => true,
		'packs' => array(
			'backend' => array(
				'token' => 'admin',
				'modules' => array(
					'jquery',
					'bootstrap',
					'bootstrap-switch',
					'select2',
					'jquery-sortable-lists',
				)
			),
			'frontend' => array(
				'token' => 'main',
				'modules' => array(
					'jquery',
					'bootstrap',
					'select2',
				),
			),
			'angular' => array(
				'token' => 'angular',
				'modules' => array(
					'angular',
					'angular-route',
					'angular-loader',
					'angular-mocks',
					'angular-messages',
					'angular-animate',
					'angular-aria',
					'angular-material',
					'angular-file-upload',
					'vis',
				)
			),
			'ieLT9' => array(
				'token' => 'ieLT9',
				'modules' => array(
					'html5shiv',
					'respond',
				),
				'attributes' =>  array(
					'conditional' => 'lt IE 9',
				),
			)
		)
    ),
);
