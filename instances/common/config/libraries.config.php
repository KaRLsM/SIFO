<?php
/**
 * The 'default' profile must always exist and contain any existing library.
 *
 * The rest of profiles EXTEND the default profile, so you only have to include
 * the libraries that are different.
 */

/**
 * This classes will be loaded in this order and ALWAYS before starting.
 */
$config['classes_always_preloaded'] = array(
	'Exceptions',
	'Registry',
	'Filter',
	'Domains',
	'Urls',
	'Router',
	'Database',
	'Controller',
	'Model',
	'MysqlModel',
	'View',
	'I18N',
	'Benchmark',
	'Cache',
);

// Contains all the libraries available.
$config['default'] = array(
	'smarty' => 'Smarty-3.0.7',
	'adodb' => 'adodb5',
	'googleTranslate' => 'googleTranslate-1.7',
	'phpthumb' => 'PhpThumb',
	'phpmailer' => 'phpMailer_v5.1',
	'predis' => 'Predis-0.6.0-PHP5.2',
	'sphinx' => 'Sphinx'
);