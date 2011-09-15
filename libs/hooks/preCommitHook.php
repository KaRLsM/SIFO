<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'PHP/CodeSniffer/CLI.php';
require_once 'PHP/PMD/TextUI/Command.php';
require_once 'html5lib/Parser.php';

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
		'files' => array()
	);

	protected $ignore_patterns = array(
		'libs/',
		'Bootstrap.php'
	);

	protected $phpcs_args = array(
		'files' => array(),
		'standard' => 'Musicjumble',
		'verbosity' => 0,
		'interactive' => false,
		'local' => false,
		'showSources' => false,
		'extensions' => array( 'php', 'css', 'js' ),
		'sniffs' => array(),
		'ignored' => array(),
		'reportFile' => null,
		'generator' => '',
		'reports' => array(),
		'warningSeverity' => null,
		'tabWidth' => 4,
		'encoding' => 'utf-8',
		'errorSeverity' => null,
		'reportWidth' => 80,
		'showProgress' => false
	);

	protected $phpmd_args = array(
		'',
		'', // This will be replaced later with the file to be processed.
		'text',
		'codesize,design,naming,unusedcode'
	);

	protected $has_errors = false;

	public function __construct( $arguments )
	{
		$this->arguments = array_merge(
				$this->arguments, $this->processArguments( $arguments )
		);
	}

	protected function processArguments( Array $arguments )
	{
		$return['files'] = array();
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
						$return['files'][] = $argument;
					}
			}
		}

		return $return;
	}

	public function process()
	{
		if ( !$this->arguments['standalone'] )
		{
			$this->phpcs_args['warningSeverity'] = 0;
			$this->addGitFiles();
		}

		if ( !empty( $this->arguments['files'] ) )
		{
			$this->addMessage( $this->executePhpCs() );
			$this->addMessage( $this->executePhpMd() );
			$this->addMessage( $this->executeDomDocument() );
			$this->output = trim( $this->output );

			if ( $this->has_errors )
			{
				throw new Exception( "\n\nMal empezamos si no hacemos esto bien..." );
			}
		}

		$this->addMessage( "\n\nMacanudo che!" );
	}

	protected function executePhpCs()
	{
		$phpcs = new PHP_CodeSniffer_CLI();
		$this->phpcs_args['files'] = $this->arguments['files'];

		if ( 1 === count( $this->phpcs_args['files'] ) && false === stripos( $this->phpcs_args['files'][0], '.tpl' ) )
		{
			if ( 0 !== $phpcs->process( $this->phpcs_args ) )
			{
				$this->has_errors = true;
			}
		}
	}

	protected function executePhpMd()
	{
		foreach ( $this->arguments['files'] as $file )
		{
			if ( !empty( $file ) && false === stripos( $file, '.tpl' ) )
			{
				$this->phpmd_args[1] = $file;
				if ( 0 !== PHP_PMD_TextUI_Command::main( $this->phpmd_args ) )
				{
					$this->has_errors = true;
				}
			}
		}
	}

	protected function executeDomDocument()
	{
		$errors = array();
		foreach ( $this->arguments['files'] as $file )
		{
			if ( false !== stripos( $file, '.tpl' ) )
			{
				echo "File $file: ";
				$errors = HTML5_Parser::detectErrors( file_get_contents( $file ) );

				if ( array() !== $errors )
				{
					$this->has_errors = true;
					echo '[KO]';
				}
				else
				{
					echo '[OK]';
				}

				echo "\n";
			}
		}

		foreach ( $errors as $error )
		{
			echo $error;
		}
	}

	protected function addGitFiles()
	{
		$filtered_files = array();
		foreach ( $this->arguments['files'] as &$filename )
		{
			$valid_file = true;
			foreach ( $this->ignore_patterns as $pattern )
			{
				$valid_file = ( false === strpos( $filename, $pattern ) );

				if ( !$valid_file )
				{
					break;
				}
			}

			if ( $valid_file )
			{
				$filtered_files[] = ROOT_PATH . trim( $filename );
			}
		}

		$this->arguments['files'] = $filtered_files;
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
