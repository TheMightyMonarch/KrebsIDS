<?php

return new \Phalcon\Config(array(
	'database' => array(
		'adapter'     => 'Mysql',
		'host'        => 'localhost',
		'username'    => 'root',
		'password'    => 'rootpass',
		'dbname'      => 'ptest',
	),
	'application' => array(
		'controllersDir' => __DIR__ . '/../../app/controllers/',
		'modelsDir'      => __DIR__ . '/../../app/models/',
		'viewsDir'       => __DIR__ . '/../../app/views/',
		'formsDir'		 => __DIR__ . '/../../app/forms/',
		'pluginsDir'     => __DIR__ . '/../../app/plugins/',
		'libraryDir'     => __DIR__ . '/../../app/library/',
		'cacheDir'       => __DIR__ . '/../../app/cache/',
		'baseUri'        => '/',
	),
	'email' => array(
		'host'		=> 'ssl://smtp.gmail.com',
		'port'		=> '465',
		'auth'		=> true,
		'username'	=> '',
		'password'	=> '',
		'from'		=> ''
	)
));
