<?php

$message = trim( file_get_contents( $argv[1] ) );

preg_match( '/#[0-9]+ .+(\n\*.+)+/', $message, $matches );

if ( !isset( $matches[0] ) || isset( $matches[0] ) && $matches[0] != $message )
{
	echo <<<MESSAGE
Message must follow the following format:

#BUGID #BUG_TITLE
* Description 1 of your commit
* ... as many descriptions as you want
* but at least one is needed!
MESSAGE;
	
	exit( 1 );
}

exit( 0 );