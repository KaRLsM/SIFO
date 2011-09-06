<?php
namespace Utilities;

class I18nSaveController extends \SeoFramework\Controller
{
	public $is_json = true;

	public function build()
	{

		$translator = $this->getClass( 'I18nTranslatorModel' );

		$filter = Filter::getInstance();

		$lang = $filter->getString('lang');
		$given_translation = $filter->getUnfiltered( 'translation' );
		$id_message = $filter->getString( 'id_message' );
		$translator_email = $user['email'];

		if ($given_translation )
		{
			// TODO: REMOVE this: Temporal fix until magic quotes is disabled:
			$given_translation = str_replace( '\\', '', $given_translation );

			$query = 'REPLACE i18n_translations (id_message, lang, translation,author) VALUES(?,?,?,?);';

			$result = DB::getInstance()->Execute( $query, array( $id_message, $lang, $given_translation, $translator_email ) );

			if ( $result )
			{
				return array(
					'status' => 'OK',
					'msg' => 'Successfully saved'
				);
			}
		}

		return array(
			'status' => 'KO',
			'msg' => 'Failed to save the translation'
		);
	}
}