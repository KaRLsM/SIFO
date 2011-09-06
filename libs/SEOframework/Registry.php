<?php
namespace SeoFramework;

/**
 * Global storage of objects.
 */
class Registry
{

	/**
	 * Registry object provides storage for shared objects.
	 */
	private static $instance = null;

	/**
	 * Array where all the storage is done.
	 *
	 * @var array
	 */
	private static $storage = array();

	/**
	 * Retrieves the default registry instance.
	 *
	 * @return Registry
	 */
	public static function getInstance()
	{
		if ( self::$instance === null )
		{
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Avoid external construction of class without singleton usage.
	 *
	 */
	private function __construct()
	{

	}

	/**
	 * Get a value from the registry.
	 *
	 * @param string $key Name you used to store the value.
	 * @return mixed
	 */
	public static function get( $key )
	{
		$instance = self::getInstance();

		if ( self::keyExists( $key ) )
		{
			return self::$storage[$key];
		}
		return false;
	}

	/**
	 * Stores the object with the name given in $key.
	 *
	 * @param string $key Name you want to store the value with.
	 * @param mixed $value The object to store in the array.
	 * @return void
	 */
	public static function set( $key, $value )
	{
		self::$storage[$key] = $value;
	}

	/**
	 * Unset the object with the name given in $key.
	 *
	 * @param string $key Name you want to store the value with.
	 * @return void
	 */
	public static function invalidate( $key )
	{
		if ( isset( self::$storage[$key] ) )
		{
			unset( self::$storage[$key] );
		}
	}

	/**
	 * Stores the object with the name given in $key and $sub_key.
	 *
	 * Example: array( $key => array( $subkey => $value ) )
	 *
	 * @param string $key Name you want to store the value with.
	 * @param mixed $value The object to store in the array.
	 * @return void
	 */
	public static function subSet( $key, $sub_key, $value  )
	{
		self::$storage[$key][$sub_key] = $value;
	}

	/**
	 * Adds another element to the end of the array.
	 *
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @return int New number of elements in the array.
	 */
	public static function push( $key, $value )
	{
		if ( !self::keyExists( $key ) )
		{
			self::$storage[$key] = array();
		}

		if ( !is_array( self::$storage[$key] ) )
		{
			throw new Exception_Registry( 'Failed to PUSH an element in the registry because the given key is not an array.' );
		}

		return array_push( self::$storage[$key], $value );
	}

	/**
	 * @param string $index
	 * @returns boolean
	 *
	 */
	public static function keyExists( $key )
	{
		return array_key_exists( $key, self::$storage );
	}

}

class Exception_Registry extends \Exception {};
