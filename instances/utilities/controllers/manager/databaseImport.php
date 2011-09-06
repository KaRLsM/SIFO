<?php
namespace Utilities;

class ManagerDatabaseImportController extends \SeoFramework\Controller
{
	public function build()
	{
		$this->setLayout( 'empty.tpl' );
		
		$path = ROOT_PATH . '/libs/utils/';
		$filename = $path . 'mj_dump.sql.gz';

		if ( is_file( $filename ) === false )
		{
			$content = $filename . ' does not exist.<br>Please, put the gzipped dump file "' . $filename . '" in the ' . getcwd() . ' folder.';
			$this->assign( 'content', $content );
			return;
		}

		shell_exec( 'gunzip ' . $filename );
		$filename = $path . 'mj_dump.sql';
		$content = "<br>$filename unzipped!<br>";

		$content .= "$filename was created and the database names where changed correctly.<br>";
		$content .= "Starting database import...<br>";
		shell_exec( 'mysql -h localhost -u root -proot < ' . $filename );

		$privileges_file = ROOT_PATH . '/instances/musicjumble/db-schema/grant_privileges.sql';
		shell_exec( 'mysql -h localhost -u root -proot < ' . $privileges_file );

		$content .= "<br>Database import done!<br>";
		unlink($filename);
		$content .= "$filename removed.<br>";
		$content .= '-- FINISHED --';
		
		$this->assign( 'content', $content );
	}
}
