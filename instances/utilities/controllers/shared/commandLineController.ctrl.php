<?php
namespace Utilities;

abstract class CommandLineController extends \SeoFramework\Controller
{

	const TEST		= 'TEST';
	const VERBOSE	= 'VERBOSE';
	const ALL		= 'INFO';

	/*
	 * Used to avoid send empty mails. Should be the number of lines constant in every script execution.
	 *
	 */
	const MAX_LINES_WITHOUT_SEND_MAIL = 2; // Thes start and end time.

	private $_verbose = false;
	private $_recipient;
	private $_stdout = '';
	private $_script_name;
	private $_domain_name;

	public $debug_mode = false;
	public $test = false;
	public $command_options;
	public $help_str = "Use 'php script-name domain.ext <options>' (SIFO default help string. Redefine this property for customize this message.)";
	public $force = false;

	abstract function init();
	abstract function exec();

	/**
	 * Shell params array.
	 *
	 *
	 * @var <type>
	 */
	public $_shell_common_params = array(
		array(
			'short_param_name'	=> 'h',
			'long_param_name'	=> 'help',
			'help_string'		=> 'Show this screen.',
			'need_second_param'	=> false,
			'is_required'		=> false,
		),
		array(
			'short_param_name'	=> 't',
			'long_param_name'	=> 'test',
			'help_string'		=> 'Test mode on.',
			'need_second_param'	=> false,
			'is_required'		=> false,
		),
		array(
			'short_param_name'	=> 'v',
			'long_param_name'	=> 'verbose',
			'help_string'		=> 'Active the verbose mode',
			'need_second_param'	=> false,
			'is_required'		=> false,
		),

		array(
			'short_param_name'	=> 'r',
			'long_param_name'	=> 'recipient',
			'help_string'		=> 'Used with an email like -r user@server.com send to these mail the script execution result.',
			'need_second_param'	=> true,
			'is_required'		=> false,
		),

		array(
			'short_param_name'	=> 'f',
			'long_param_name'	=> 'force',
			'help_string'		=> 'Run the script without another instance in execution validation.',
			'need_second_param'	=> false,
			'is_required'		=> false,
		),

	);

	public function __construct()
	{
		$this->instance = CLBootstrap::$instance;
		$this->language = Domains::getInstance()->getLanguage();

		$this->params = array(
			'instance' => Bootstrap::$instance,
			'controller' => get_class( $this ),
			'has_debug' => Domains::getInstance()->getDevMode(),
			'lang' => $this->language,
		);

		$this->debug_mode = Domains::getInstance()->getDevMode();

		// Init i18n configuration.
		$this->i18n = I18N::getInstance( Domains::getInstance()->getLanguageDomain(), $this->language );
	}

	protected function showMessage( $message, $in_mode = self::ALL, $tag = true )
	{
		if ( $tag )
		{
			$message = "[".$in_mode."] ".$message;
		}
		switch ( $in_mode )
		{
			case self::TEST:
				if ( $this->test )
				{
					$this->_stdout .= $message.PHP_EOL;
					echo $message.PHP_EOL;
				}
				break;
			case self::VERBOSE:
				if ( $this->_verbose )
				{
					$this->_stdout .= $message.PHP_EOL;
					echo $message.PHP_EOL;
				}
				break;
			case self::ALL:
				$this->_stdout .= $message.PHP_EOL;
				echo $message.PHP_EOL;
				break;
			default:
				throw new OutOfBoundsException( 'Undefined in_mode selected.');
		}
	}

	/**
	 * Set a new exec param.
	 *
	 * @param char $short_param_name The short option id.
	 * @param string $long_param_name The long name option.
	 * @param string $help_string The help string.
	 * @param boolean $need_second_param True if needs a param.
	 * @param boolean $is_required Must be set.
	 */
	protected function setNewParam( $short_param_name, $long_param_name, $help_string, $need_second_param, $is_required )
	{
		foreach ( $this->_shell_common_params as $param )
		{
			if ( ( $short_param_name == $param['short_param_name'] ) || ( $long_param_name == $param['long_param_name'] ) )
			{
				throw new RuntimeException( 'You are trying to set a previously defined param.' );
			}
		}

		$this->_shell_common_params[] = array(
			'short_param_name'	=> $short_param_name,
			'long_param_name'	=> $long_param_name,
			'help_string'		=> $help_string,
			'need_second_param'	=> $need_second_param,
			'is_required'		=> $is_required,
			);

	}

	public function showHelp()
	{
		echo PHP_EOL . $this->help_str . PHP_EOL . PHP_EOL;
		foreach ( $this->_shell_common_params as $param )
		{
			echo '--' . $param['long_param_name'] . "(-" . $param['short_param_name'] . ")\t:";
			if ( $param['is_required'])
			{
				echo "(REQUIRED) ";
			}
			echo $param['help_string'];
			if ( $param['need_second_param'] )
			{
				echo " (use with a value like '--".$param['long_param_name']." value')";
			}
			echo PHP_EOL . PHP_EOL;
		}
	}

	private function _getParams()
	{
		$i = -1;
		$params = array();
		if ( $argv = FilterServer::getInstance()->getArray( 'argv' ) )
		{
			foreach ( $argv as $option )
			{
				if ( preg_match("/^--(\w+)/", $option, $matchs) )
				{
					$params[++$i][0] = $matchs[1];
				}
				else
				{
					if ( preg_match("/^-(\w+)/", $option, $matchs) )
					{
						$params[++$i][0] = $matchs[1];
					}
					else
					{
						if ( $i>-1 )
						{
							$params[$i++][1] = $option;
						}
					}
				}
			}
		}
		$this->command_options = $params;
	}

	private function _validateParams()
	{
		foreach ( $this->command_options as $option )
		{
			$found = false;
			foreach ( $this->_shell_common_params as $defined_option )
			{
				if ( ( $option[0] == $defined_option['short_param_name'] ) || ( $option[0] == $defined_option['long_param_name'] ) )
				{
					$found = true;
					if ( $defined_option['need_second_param'] )
					{
						if ( !isset( $option[1] ) )
						{
							$this->showMessage( "Need define a param in for use '$option[0]' option." );
							$this->showHelp();
							return false;
						}
					}
					break;
				}
			}
			if ( !$found )
			{
				$this->showMessage( "Error in options. Option '$option[0]' undefinded." );
				$this->showHelp();
				return false;
			}
		}

		// Validating required options:
		foreach ( $this->_shell_common_params as $defined_option )
		{
			if ( $defined_option['is_required'] )
			{
				$found = false;
				foreach ( $this->command_options as $option )
				{
					if ( ( $option[0] == $defined_option['short_param_name'] ) || ( $option[0] == $defined_option['long_param_name'] ) )
					{
						$found = true;
						break;
					}
				}
				if ( !$found )
				{
					$this->showMessage( "Error: '" . $defined_option["long_param_name"] . "' required option not found." );
					$this->showHelp();
					return false;
				}
			}
		}

		$argv = FilterServer::getInstance()->getArray( 'argv' );
		preg_match("/([^\/]+)$/", $argv[0], $matchs);
		$this->_script_name = $matchs[0];
		$this->_domain_name = $argv[1];

		return true;
	}
	
	private function _validateCommandCall()
	{
		if ( !( $this instanceof  CommandLineController ) )
		{
			$this->showMessage( 'For make a script runnable controller, these must be instance of CommandLineController' );
			return false;
		}
		return true;
	}

	private function _common_exec()
	{
		foreach ( $this->command_options as $option )
		{
			switch ( $option[0] )
			{
				case "h":
				case "help":
					$this->showHelp();
					die;
				case "v":
				case "verbose":
					$this->_verbose = true;
					break;
				case "t":
				case "test":
					$this->test = true;
					break;
				case "r":
				case "recipient":
					$this->_recipient = $option[1];
					break;
				case "f":
				case "force":
					$this->force = true;
					break;
			}
		}
	}

	/**
	 * Returns the subject of the email.
	 */
	protected function getSubject()
	{
		return 'STDOUT '.$this->_script_name.' in '. $this->_domain_name. ' at ' . date( 'Y-m-d' );
	}

	private function _sendMail()
	{
		if ( isset( $this->_recipient ) )
		{
			if ( self::MAX_LINES_WITHOUT_SEND_MAIL < ( count( explode( PHP_EOL, $this->_stdout ) ) -1 ) )
			{
				$this->showMessage( "Now I would try send an email with subject: '" . $this->getSubject() . "' to '".$this->_recipient ."'", self::TEST );
				if ( !$this->test )
				{
					$mail = $this->getClass('Mail');
					$mail->send( $this->_recipient, $this->getSubject(), nl2br( $this->_stdout ) );
				}
			}
			else
			{
				$this->showMessage( "Unsent email because the script output was empty." );
			}
		}
	}

	private function _startScript()
	{
		$this->showMessage( 'Script '.$this->_script_name.' in '. $this->_domain_name.' started at:'. date( 'd-M-Y H:i:s' ) );
	}

	private function _stopScript()
	{
		$this->showMessage( 'Finished at: '. date( 'd-M-Y H:i:s' ) );
	}

	private function _validateScriptRunning()
	{
		if ( $this->force )
		{
			$this->showMessage( 'Running without another instance execution validation.' );
			return true;
		}
		$my_pid = getmypid();
		$pids = array();
		//$command = "ps -eo pid,args| grep \"$this->_script_name $this->_domain_name\" | grep -v grep| grep -v $my_pid | cut -f2 -d\" \"";
		$command = "ps -eo pid,args| grep \"$this->_script_name $this->_domain_name\" | grep -v grep| grep -v /sh| grep -v $my_pid | cut -f2 -d\" \"";
		exec( $command, $pids, $err );
		if ( $err )
		{
			$this->showMessage( 'Error trying to search another instance execution. Run with -f option.' );
			return false;
		}
		if ( count( $pids ) > 0 )
		{
			$this->showMessage( "Is running another instance of '$this->_script_name $this->_domain_name'. Wait until finish, use -f for force or run 'kill -9 " . implode (' ', $pids) . "' for assassinate it." );
			return false;
		}
		$this->showMessage( "There are not other running instances", self::VERBOSE );
		return true;
	}

	public function build()
	{
		$this->_startScript();
		$this->init();
		$this->_getParams();
		if ( $this->_validateCommandCall() && $this->_validateParams() )
		{
			$this->_common_exec();
			if ( $this->_validateScriptRunning() )
			{
				$this->exec();
			}
		}
		$this->_stopScript();
		$this->_sendMail();
	}
}
