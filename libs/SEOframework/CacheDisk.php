<?php
namespace SeoFramework;

/**
 * Caching system based on Disk. Only TEXT objects can be cached.
 *
 * Serialize data before setting elements if needed.
 */
class CacheDisk
{
	private static $instance;

	public static function singleton()
	{
		self::$instance ||
				self::$instance = new CacheDisk();
		return self::$instance;
	}

	/**
	 * Write content in cache
	 *
	 * @param string $key
	 * @param string $contents
	 * @param integer $compress Ignored, backwards compatibility.
	 * @param integer $expire Ignored, backwards compatibility.
	 * @return boolean
	 */
	static public function set($key, $contents, $compress=0, $expire=0 )
	{
		$final_key = self::_finalKeyGeneration( $key );
		$source_file = self::_getSystemPath( $final_key );

		if($fp = @fopen($source_file, 'w'))
		{
			fwrite($fp, $contents);
			chmod ( $source_file, 0777 );
			fclose($fp);

			return true;
		}
		else
		{
			return false;
		}
	}

	static public function get($key)
	{
		$final_key = self::_finalKeyGeneration( $key );
		$source_file = self::_getSystemPath( $final_key );

		$fp = @fopen( $source_file, 'r' );
		$filesize = @filesize($source_file);

		if ( $filesize > 0 )
		{
			$contents = fread($fp, $filesize);
			fclose($fp);

		}
		else
		{
			$contents = " ";
		}

		return $contents;
	}

	static public function hasExpired( $key, $expiration )
	{
		$final_key = self::_finalKeyGeneration( $key );
		$source_file = self::_getSystemPath( $final_key );

		if( !file_exists( $source_file ) || !( $mtime = filemtime( $source_file ) ) )
		{
			return true;
		}

		// Cache expired?
		if( ( $mtime + $expiration ) < time() )
		{
			@unlink( $source_file );
			return true;
		}

		return false;
	}

	static public function delete( $key )
	{
		$final_key = self::_finalKeyGeneration( $key );
		$source_file = self::_getSystemPath( $final_key );
		@unlink( $source_file );
	}

	/**
	 * Generate the cache key to be used in the save disk process.
	 * This function was created for avoid bug trying to write utf8 chars like filename.
	 *
	 * @param <type> $suggested_key
	 */
	static private function _finalKeyGeneration( $key )
	{
		 $final_key  = preg_replace( '/[^0-9a-z_\-]/', '', strtolower( $key ) ).'-'.sha1( $key );

		 return $final_key;
	}


	static private function _getSystemPath( $key )
	{
		$key = str_replace( '..', '', $key );
		return ROOT_PATH . '/instances/' . Bootstrap::$instance . '/templates/_smarty/cache/' . $key .'.cached.html';
	}
}