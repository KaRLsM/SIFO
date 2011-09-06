<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of preCommitHook
 *
 * @author C. Soriano
 */
class preCommitHook
{
	protected $output = '';

	protected $arguments = array(
		'standalone' => false,
		'files' => ''
	);

	protected $ignore_patterns = array(
		'libs/',
	);

	protected $phpcs_args = array(
		'--standard' => 'Musicjumble',
		'--tab-width' => '4'
	);

	public function __construct( $arguments )
	{
		$this->arguments = array_merge(
				$this->arguments, $this->processArguments( $arguments )
		);
	}

	protected function processArguments( Array $arguments )
	{
		$return['files'] = '';
		foreach ( $arguments as $key => $argument )
		{
			switch ( $argument )
			{
				case '--standalone':
					$return['standalone'] = true;
					break;
				default:
					if ( $key !== 0 )
					{
						$return['files'] .= $argument . ' ';
					}
			}
		}

		trim( $return['files'] );

		return $return;
	}

	public function process()
	{
		if ( $this->arguments['standalone'] )
		{
			if ( empty( $this->arguments['files'] ) )
			{
				throw new Exception( 'No files added.' );
			}
		}
		else
		{
			$this->phpcs_args[] = '-n';
			$this->addGitFiles();
		}

		$this->addMessage( $this->executePhpCs() );
		$this->addMessage( $this->executePhpMd() );

		$this->output = trim( $this->output );

		if ( false !== strpos( $this->output, "\n" ) )
		{
			throw new Exception( "\n\nMal empezamos si no hacemos esto bien..." );
		}

		$this->addMessage( "\n\nMacanudo che!" );
	}

	protected function executePhpCs()
	{
		return shell_exec(
						ROOT_PATH . "libs/php/phpcs " .
						$this->buildScriptArguments( $this->phpcs_args )
						. " " . $this->arguments['files']
		);
	}

	protected function executePhpMd()
	{
		$files = explode( ' ', $this->arguments['files'] );

		$output = '';
		foreach ( $files as $file )
		{
			if ( !empty( $file ) )
			{
				$output = shell_exec(
						ROOT_PATH .
						"libs/php/phpmd $file text codesize,design,naming,unusedcode"
				);
			}
		}
		$output = trim( $output ) . "\n";

		return $output;
	}

	protected function addGitFiles()
	{
		$output = shell_exec(
				'git diff-index --cached --name-only --diff-filter=ACMR HEAD --'
		);
		$files_to_commit = explode( "\n", trim( $output ) );

		$streamlined_files = '';
		foreach ( $files_to_commit as &$filename )
		{
			$valid_file = true;
			foreach ( $this->ignore_patterns as $pattern )
			{
				$valid_file = ( false === strpos( $filename, $pattern ) );
			}

			if ( $valid_file || $valid_file = ( false !== stripos( $filename, 'SEOFramework/' ) ) )
			{
				$streamlined_files .= ' ' . trim( $filename );
			}
		}

		$this->arguments['files'] = $streamlined_files;
	}

	protected function buildScriptArguments( $argument_template )
	{
		$arguments = '';
		foreach ( $argument_template as $property => $value )
		{
			if ( is_numeric( $property ) )
			{
				$arguments .= "$value ";
			}
			else
			{
				$arguments .= "$property=$value ";
			}
		}

		return $arguments;
	}

	public function addMessage( $message, $whitespace = "\n" )
	{
		$this->output .= $message . $whitespace;
	}

	public function getReport()
	{
		return $this->output;
	}
}

?>
