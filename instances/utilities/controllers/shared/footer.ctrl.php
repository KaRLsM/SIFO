<?php
namespace Utilities;

class SharedFooterController extends \SeoFramework\Controller
{
	public function build()
	{
		$this->setLayout( 'shared/footer.tpl' );
	}
}
?>