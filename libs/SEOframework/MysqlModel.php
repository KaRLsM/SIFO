<?php
namespace SeoFramework;

class MysqlModel extends Model
{
	protected $db;

	/**
	 * Returns the Database connection object.
	 *
	 * @param string $profile The profile to be used in the database connection.
	 * @return Mysql|MysqlDebug
	 */
	protected function connectDb( $profile = 'default' )
	{
		$this->getClass( 'Mysql', false );
		if ( Domains::getInstance()->getDevMode() !== true )
		{
			return Mysql::getInstance( $profile );
		}

		$this->getClass( 'MysqlDebug', false );
		return MysqlDebug::getInstance( $profile );
	}

	/**
	 * Magic method to retreive table names from a configuration file.
	 */
	public function __get( $attribute )
	{
		$tablenames = Config::getInstance()->getConfig( 'tablenames' );

		$domain = Domains::getInstance()->getDomain();

		if ( isset( $tablenames['names'][$domain][$attribute] ) )
		{
			return $tablenames['names'][$domain][$attribute];
		}
		elseif ( isset( $tablenames['names']['default'][$attribute] ) )
		{
			return $tablenames['names']['default'][$attribute];
		}

		return $attribute;
	}
}
?>