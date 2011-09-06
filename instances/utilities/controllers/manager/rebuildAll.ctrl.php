<?php

namespace Utilities;

use \SeoFramework\Cookie as Cookie;
use \SeoFramework\Dir as Dir;
use \SeoFramework\Domains as Domains;

class ManagerRebuildAllController extends \SeoFramework\Controller
{

	public function build()
	{
		if ( true !== $this->hasDebug() )
		{
			throw new Exception_404( 'User tried to access the rebuild page, but he\'s not in development' );
		}

		$this->setLayout( 'manager/templates.tpl' );

		$d = new Dir();
		$all_instances = $d->getDirs( ROOT_PATH . "/instances/" );

		foreach ( $all_instances as $instance )
		{
			$domains_config = ROOT_PATH . "/instances/{$instance}/config/domains.config.php";
			if ( is_readable( $domains_config ) )
			{
				include $domains_config;
			}
			if ( isset( $config['instance_type'] ) && $config['instance_type'] === 'instantiable' )
			{
				$instances[$instance] = $config;
				$instances[$instance]['instance_inheritance'][] = $instance;
			}
			unset( $config );
		}

		$all_configs = $all_templates = $all_classes = array( );

		foreach ( $instances as $instance => $domains_config )
		{
			// Calculate where the config files are taken from.
			$configs = $this->getAvailableFiles( 'config', $domains_config );
			$all_configs = array_merge( $all_configs, $configs );
			$this->assign( 'config', $configs );
			$configs_content = $this->grabHtml();
			file_put_contents( ROOT_PATH . "/instances/" . $instance . "/config/configuration_files.config.php", $configs_content );

			// Calculate where the templates are taken from
			$templates = $this->getAvailableFiles( 'templates', $domains_config );
			$all_templates = array_merge( $all_templates, $templates );
			$this->assign( 'config', $templates );
			$template_content = $this->grabHtml();
			file_put_contents( ROOT_PATH . "/instances/" . $instance . "/config/templates.config.php", $template_content );

			// Calculate where the controllers, models and unsuitable classes are taken from.
			$controllers = $this->getAvailableFiles( 'controllers', $domains_config );
			$core = $this->getAvailableFiles( 'core', $domains_config );
			$models = $this->getAvailableFiles( 'models', $domains_config );
			$classes = $this->getAvailableFiles( 'classes', $domains_config );
			$classes = array_merge( $core, $classes, $controllers, $models );
			$all_classes = array_merge_recursive( $all_classes, $classes );
			$this->assign( 'config', $classes );
			$classes_content = $this->grabHtml();
			file_put_contents( ROOT_PATH . "/instances/" . $instance . "/config/classes.config.php", $classes_content );
		}

		// Now populate the configs for the tests.
		$all_configs['classes'] = "tests/config/classes.config.php";
		$all_configs['templates'] = "tests/config/templates.config.php";
		$this->assign( 'config', $all_configs );
		$configs_content = $this->grabHtml();
		file_put_contents( ROOT_PATH . "/tests/config/configuration_files.config.php", $configs_content );

		$this->assign( 'config', $all_templates );
		$template_content = $this->grabHtml();
		file_put_contents( ROOT_PATH . "/tests/config/templates.config.php", $template_content );

		$this->assign( 'config', $all_classes );
		$classes_content = $this->grabHtml();
		file_put_contents( ROOT_PATH . "/tests/config/classes.config.php", $classes_content );

		// Reset the layout and paste the content in the empty template:
		$this->setLayout( 'empty.tpl' );
		// Disable debug on this page.
		$this->setDebug( false );
		$instances = implode( ', ', array_keys( $instances ) );
		$message = <<<MESG
INSTANCES '{$instances}'.
templates.config.php
====================
				$template_content

classes.config.php
====================
				$classes_content

configuration_files.config.php
====================
				$configs_content
MESG;
		$this->assign( 'content', $message );


		header( 'Content-Type: text/plain' );
	}

	protected function getRunningInstances()
	{
		$d = new Dir();
		$instances = $d->getDirs( ROOT_PATH . '/instances' );

		return $instances;
	}

	private function cleanStartingSlash( $path )
	{
		if ( 0 === strpos( $path, "/" ) )
		{
			// Remove starting slashes.
			return substr( $path, 1 );
		}
		return $path;
	}

	/**
	 * Converts something like home/index.ctrl.php to HomeIndex.
	 *
	 * @param string $path
	 * @return string
	 */
	private function getClassTypeStandarized( $path )
	{
		$class = '';

		$ctrl_parts = explode( '/', $path );

		while ( $class_name = array_shift( $ctrl_parts ) )
		{
			$class .= ucfirst( $class_name );
		}

		return $class;
	}

	protected function getAvailableFiles( $type, $domains_config )
	{
		$d = new Dir();
		$type_files = array( );

		//TODO: Poner en config.
		$core_inheritance = $domains_config['core_inheritance'];
		$instance_inheritance = $domains_config['instance_inheritance'];


		if ( $type == 'core' )
		{
			foreach ( $core_inheritance as $corelib )
			{
				$available_files = $d->getFileListRecursive( ROOT_PATH, '/libs/' . $corelib );
				if ( count( $available_files ) > 0 )
				{
					foreach ( $available_files as $v )
					{
						// Allow only extensions PHP, TPL, CONF
						$desired_file_pattern = preg_match( "/\.(php|tpl|conf)$/i", $v["relative"] );

						if ( $desired_file_pattern )
						{
							$rel_path = $this->cleanStartingSlash( $v["relative"] );
							$path = $rel_path;
							$rel_path = str_replace( 'libs/' . $corelib . '/', '', $rel_path );
							$rel_path = str_replace( 'libs/SEOWrappers/', '', $rel_path );
							$rel_path = str_replace( '.php', '', $rel_path ); // Default

							$class = $this->getClassTypeStandarized( $rel_path );
							$type_files[$class]['SeoFramework'] = $path;
						}
					}
				}
			}
		}
		else
		{
			foreach ( $instance_inheritance as $current_instance )
			{
				$available_files = $d->getFileListRecursive( ROOT_PATH . "/instances/" . $current_instance . "/$type" );

				if ( is_array( $available_files ) === true && count( $available_files ) > 0 )
				{
					foreach ( $available_files as $v )
					{
						$rel_path = $this->cleanStartingSlash( $v["relative"] );
						$class = '';

						$path = str_replace( '//', '/', "instances/$current_instance/$type/$rel_path" );

						// Calculate the class name for the given file:
						$rel_path = str_replace( '.model.php', '', $rel_path );
						$rel_path = str_replace( '.ctrl.php', '', $rel_path );
						$rel_path = str_replace( '.config.php', '', $rel_path );
						$rel_path = str_replace( '.php', '', $rel_path ); // Default

						$class = $this->getClassTypeStandarized( $rel_path );

						switch ( $type )
						{
							case 'controllers':
								$class .= 'Controller';
								$type_files[$class][ucfirst( $current_instance )] = $path;
								break;
							case 'models':
								$class .= 'Model';
								$type_files[$class][ucfirst( $current_instance )] = $path;
								break;
							case 'classes':
								$type_files[$class][ucfirst( $current_instance )] = $path;
								break;
							case 'config':
								if ( $rel_path == 'configuration_files' )
								{
									continue;
								}
							case 'templates':
							default:
								$type_files[$rel_path] = $path;
						}
					}
				}
			}
		}


		ksort( $type_files );
		return $type_files;
	}
}

?>