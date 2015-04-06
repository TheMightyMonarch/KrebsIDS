<?php

$loader = new \Phalcon\Loader();

/**
 * Register all our config directories
 */
$loader->registerDirs(
    array(
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->formsDir,
		$config->application->libraryDir,
        $config->application->pluginsDir
    )
);

/**
 * Register class autoloading prefixes
 */
$loader->registerPrefixes(
	array(
		'Forms_'	=> $config->application->formsDir,
		'Library_'	=> $config->application->libraryDir
	)
);

$loader->register();
