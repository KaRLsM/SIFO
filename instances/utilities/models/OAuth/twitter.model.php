<?php
/**
 * Example of implementation:
 * // Anywhere:
 * echo '<a href="' . $this->getClass( 'OAuthTwitterModel' )->getAuthenticationUrl() . '">Authorize with Twitter</a>';
 * // In the callback controller:
 * $token = $this->getClass( 'OAuthTwitterModel' )->getAuth( $oauth_token );
 * $this->getClass( 'OAuthTwitterModel' )->testGetFriends( $token['oauth_token'], $token['oauth_token_secret'] );
 *
 * This classes is used in all Splitweet operations when using OAuth.
 */

include_once ROOT_PATH . '/libs/EpiClasses/EpiCurl.php';
include_once ROOT_PATH . '/libs/EpiClasses/EpiOAuth.php';
include_once ROOT_PATH . '/libs/EpiClasses/EpiTwitter.php';

class OAuthTwitterModel extends EpiTwitter
{
	/**
	 * Twitter Object
	 * @var EpiTwitter
	 */
	protected $twitterObj;

	/**
	 * Twitter credentials
	 * @var array
	 */
	protected $credentials;


	public function __construct()
	{
		try
		{
			$this->credentials = Config::getInstance()->getConfig( 'twitter_oauth', Domains::getInstance()->getDomain() );
			parent::__construct( $this->credentials['consumer_key'], $this->credentials['consumer_secret'] );
			$this->useAsynchronous( true );
			$this->setTimeout( $this->credentials['request_timeout'], $this->credentials['connection_timeout'] );
		}
		catch( Exception $e )
		{
			throw new Exception_500( $e->getMessage() );
		}

	}

	public function __call($name, $params = null/*, $username, $password*/)
	{
		try
		{
			return parent::__call( $name, $params );
		}
		catch( Exception $e )
		{
			throw new Exception_500( $e->getMessage() );
		}
		
	}

	/**
	 * Returns the authentication token and secret.
	 *
	 * @param string $oauth_token Authorization request given by Twitter via GET when calling the callback Url.
	 * @return array
	 */
	public function getAuth( $oauth_token )
	{
		try
		{
			$this->setToken( $oauth_token );
			$token = $this->getAccessToken();
			$this->setToken( $token->oauth_token, $token->oauth_token_secret );

			return array(
				'oauth_token' => $token->oauth_token,
				'oauth_token_secret' => $token->oauth_token_secret,
				'user_id' => $token->user_id,
				'screen_name' => $token->screen_name
			);
		}
		catch( Exception $e )
		{
			throw new Exception_500( $e->getMessage() );
		}

		
	}

	/**
	 * Method for testing if Oauth worked.
	 * 
	 * @param string $oauth_token
	 * @param string $oauth_token_secret
	 */
	public function testGetFriends( $oauth_token, $oauth_token_secret )
	{
		$this->setToken( $oauth_token, $oauth_token_secret );
		$twitterInfo = $this->get_statusesFriends();
		try
		{
			foreach ( $twitterInfo as $friend )
			{
				echo $friend->screen_name;
			}
		}
		catch ( EpiTwitterException $e )
		{
			echo $e->getMessage();
		}
	}
}

