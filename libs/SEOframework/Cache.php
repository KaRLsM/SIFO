<?php
namespace SeoFramework;

class Cache
{
	static private $instance;
	static public $cache_type;

	private function __construct() {}

	/**
	 * Singleton of config class.
	 *
	 * @param string $instance_name Instance Name, needed to determine correct paths.
	 * @return object Config
	 */
	public static function getInstance()
	{
		if ( !isset ( self::$instance ) )
		{
			$memcache_config = Config::getInstance()->getConfig( 'memcache' );
			// Check if Memcached is enabled by configuration:
			if ( true === $memcache_config['active'] )
			{
				if ( isset( $memcache_config['client'] ) && $memcache_config['client'] === 'Memcached' )
				{
					// Use the newer client library MemcacheD.
					include_once ROOT_PATH . '/libs/MemCached/memcached.class.php';
					$memcached = MemcachedClient::getInstance();
				}
				else
				{
					// Use the old client library Memcache.
					include_once ROOT_PATH . '/libs/MemCached/memcache.class.php';
					$memcached = MemcacheClient::getInstance();
				}

				// Check that Memcached is listening:
				if ( $memcached->isActive() )
				{
					self::$instance = $memcached;
					self::$cache_type = 'Memcached';
				}
				else
				{
					trigger_error( 'Memcached is down!' );
					// Failed to connect to Memcached hosts
					$memcache_config['active'] = false;

					// Use cache disk instead:
					include ROOT_PATH . '/libs/SEOframework/CacheDisk.php';
					self::$instance = CacheDisk::singleton();
					self::$cache_type = 'Disk';
				}
			}
			else
			{
				// Use cache disk instead:
				include ROOT_PATH . '/libs/SEOframework/CacheDisk.php';
				self::$instance = CacheDisk::singleton();
				self::$cache_type = 'Disk';
			}
		}

		return self::$instance;
	}

	/**
	 * Delegate all calls to the proper class.
	 *
	 * @param string $method
	 * @param mixed $args
	 * @return mixed
	 */
	function __call($method, $args)//call adodb methods
	{
		return call_user_func_array(array(self::$instance, $method),$args);
	}
}