<?php

$config['scriptLoader'] = array(
	'filename' => 'external/libs/lab.js',
	'group' => 'none',
	'priority' => 1
);
/* 
$config['html5enabler'] = array(
	'filename' => 'external/libs/modernizr.js',
	'group' => 'none',
	'priority' => 2
);
*/	
$config['framework'] = array(
	'filename' => 'external/frameworks/jquery/jquery.js',
	'group' => 'none',
	'priority' => 10
);
$config['namespace'] = array(
	'filename' => 'internal/namespace/namespace.js',
	'group' => 'none',
	'priority' => 20
);
$config['namespace_init'] = array(
	'filename' => 'internal/namespace/namespace_init.js',
	'group' => 'none',
	'priority' => 21
);
$config['namespace_utilities'] = array(
	'filename' => 'internal/namespace/extensions/namespace_utilities.js',
	'group' => 'none',
	'priority' => 25
);

$config['i18n'] = array(
	'filename' => 'internal/i18n/i18n.js',
	'group' => 'none',
	'priority' => 30
);
$config['i18n_msg'] = array(
	'filename' => 'internal/i18n/i18n_msgs.js',
	'group' => 'none',
	'priority' => 31
);

$config['core'] = array(
	'filename' => 'external/core/core.js',
	'group' => 'none',
	'priority' => 40
);
$config['core_extensions'] = array(
	'filename' => 'internal/extensions/core_extensions.js',
	'group' => 'none',
	'priority' => 41
);
$config['core_extensions_framework'] = array(
	'filename' => 'internal/extensions/core_extensions_jquery.js',
	'group' => 'none',
	'priority' => 42
);
$config['sandbox'] = array(
	'filename' => 'external/core/sandbox.js',
	'group' => 'none',
	'priority' => 45
);

/** Behaviours **/
$config['namespace_behaviours'] = array(
	'filename' => 'internal/namespace/extensions/namespace_behaviours.js',
	'group' => 'behaviours',
	'priority' => 1
);
/** End Behaviours **/

/** Media: Images, Videos... **/
$config['lightbox'] = array(
	'filename' => 'external/frameworks/jquery/plugins/jquery.colorbox.js',
	'group' => 'media',
	'priority' => 1
);
/** End Media: Images, Videos... **/

/** Forms **/
$config['validate'] = array(
	'filename' => 'external/frameworks/jquery/plugins/jquery.validate.js',
	'group' => 'forms',
	'priority' => 1
);
/** End Forms **/

/** jQuery UI **/
$config['jquery_ui_core'] = array(
	'filename' => 'external/frameworks/jquery/jquery.ui/jquery.core.js',
	'group' => 'jquery.ui',
	'priority' => 1
);
$config['jquery_ui_widget'] = array(
	'filename' => 'external/frameworks/jquery/jquery.ui/jquery.widget.js',
	'group' => 'jquery.ui',
	'priority' => 1
);
$config['jquery_ui_mouse'] = array(
	'filename' => 'external/frameworks/jquery/jquery.ui/jquery.mouse.js',
	'group' => 'jquery.ui',
	'priority' => 1
);
$config['jquery_ui_position'] = array(
	'filename' => 'external/frameworks/jquery/jquery.ui/jquery.position.js',
	'group' => 'jquery.ui',
	'priority' => 1
);
/** End jQuery UI **/

?>