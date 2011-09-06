<?php
namespace Common;
use SeoFramework\Metadata as Metadata;

class SharedHeadController extends \SeoFramework\Controller
{
	protected $common_css = array();
	protected $common_js = array();

	public function build()
	{
		$this->setLayout( 'shared/head.tpl' );

		$params = $this->getParams();
		$this->assign( 'path', $params['path'] );
		$this->getClass( 'Metadata', false );

		if ( null == Metadata::get() )
		{
			Metadata::setKey( 'default' );
		}

		$this->assign( 'metadata', Metadata::get() );

		$this->assignMedia();
	}

	/**
	 * Assign a variable to the tpl with the HTML code to load the JS and CSS files.
	 */
	protected function assignMedia()
	{
		foreach ( $this->common_css as $key => $val )
		{
			$this->addCss( $val );
		}

		foreach ( $this->common_js as $key => $val )
		{
			$this->addJs( $val );
		}

		$media = $this->getParam( 'media' );

		$this->getClass( 'MediaGenerator', false );
		
		$css_generated = array();
		if ( !empty( $media['css'] ) )
		{
			$css_generated = \SeoFramework\CssGenerator::getInstance()->getGenerated( $media['css'] );
		}

		$js_generated = array();
		if ( !empty( $media['js'] ) )
		{
			$js_generated = \SeoFramework\JsGenerator::getInstance()->getGenerated( $media['js'] );
		}

		$this->assign( 'css_generated', $css_generated );
		$this->assign( 'js_generated', $js_generated );
		$this->assign( 'media_module', $this->fetch( 'shared/media.tpl' ) );
	}
}