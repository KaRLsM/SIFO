<?php
namespace Utilities;

class SharedHeaderController extends \SeoFramework\Controller
{
	public function build()
	{
		$this->setLayout( 'shared/header.tpl' );
	}
}
?>