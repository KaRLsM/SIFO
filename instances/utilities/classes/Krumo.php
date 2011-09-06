<?php
/**
 * Alias of krumo::dump(). Formatted data dump. No output in production.
 *
 * @param mixed $data,...
 */
function d( $var )
{
	// Enable Krumo only when debug is present.
	if ( Domains::getInstance()->getDevMode() )
	{
		require_once( ROOT_PATH . '/libs/krumo_0.2.1a_PHP5-Only/class.krumo.php' );
		krumo( $var );
	}
	else
	{
		return false;
	}
}

/**
 * Trace a content to be dump in the debug screen.
 *
 * @param mixed $message The messsage.
 */
function trace( $message )
{
	$registry = Registry::getInstance();
	if ( $registry->keyExists( 'trace_messages' ) )
	{
		$trace_messages = $registry->get( 'trace_messages' );
	}
	$trace_messages[] = $message;
	$registry->set( "trace_messages", $trace_messages );
}

// Set a classname to allow Bootsrap::getClass()
class Krumo {}
?>
