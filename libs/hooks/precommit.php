<?php

define( 'ROOT_PATH', realpath( __DIR__ . '/../../' ) . '/' );

set_include_path( ROOT_PATH . 'libs/php/pear' );

require_once ROOT_PATH . 'libs/hooks/preCommitHook.php';

$return = 0;
try
{
	$precommit = new preCommitHook( $argv );
	$precommit->process();
}
catch( Exception $e )
{
	$precommit->addMessage( $e->getMessage() );
	$return = 1;
}

echo $precommit->getReport();
exit ( $return );