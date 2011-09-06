<?php
namespace Common;

class UserMainModel extends \SeoFramework\MysqlModel
{
	protected $user_data = array(
		'user_id'	=> null,
		'username'	=> null,
		'email'		=> null,
		'name'		=> null,
		'surname'	=> null,
		'city'		=> null,
		'country'	=> null
	);

	protected $session;
	
	private $crypted_fields = array(
		'name',
		'surname',
		'city',
		'country',
		'email'
	);
	
	const CRYPT_KEY = 'q!W?JWq)F+:97TZ-; %xs/x<84VD';
	
	const CRYPT_METHOD = MCRYPT_RIJNDAEL_128;
	
	const CRYPT_MODE = MCRYPT_MODE_CFB;
	
	const EMAIL_HASH_KEY = 'K23j49as!ks:## asK50j';
	
	const EMAIL_VALIDATION_HASH_KEY = '0j49-(s!ks:## asK50j';
	
	const PASSWORD_HASH_KEY = 'K23j40asdmks:## asK50j';
	
	const COOKIE_VALUE_SEPARATOR = '::';
	
	public function __construct()
	{
		$this->session = \SeoFramework\Session::getInstance();
		$session_data = $this->retrieveFromSession();
		if ( !empty( $session_data ) && $session_data !== $this->user_data )
		{
			$this->user_data = $session_data;
		}
		else
		{
			$this->retrieveFromCookie();
		}
	}
	
	public function init( $user_id = null )
	{
		if ( $this->session->get( 'user_id' ) !== null )
		{
			$user_data = $this->retrieveFromSession();
		}
		else
		{
			// TODO: Is this secure? maybe redirect to login?
			$raw_data = $this->retrieveFromId( $user_id );
			$user_data = $this->decryptUserData( $raw_data );
		}
		
		$this->_assignUserData( $user_data );
	}
	
	public function isLogged()
	{
		return ( null !== $this->user_data['user_id'] );
	}
	
	public function getEmailValidationToken( $email )
	{
		$time = time();
		
		return array(
			'token' => $this->_hashEmailValidationToken( $email, $time ),
			'salt' => $time
		);
	}
	
	protected function validateEmailToken( $email, $token, $salt )
	{
		return ( $this->_hashEmailValidationToken( $email, (string)$salt ) == $token );
	}
	
	public function validateEmail( $email, $token, $salt )
	{
		if ( !$this->validateEmailToken( $email, $token, $salt ) )
		{
			return false;
		}
		
		$this->db = $this->connectDb( 'user' );
		
		$sql = <<<QUERY
UPDATE
	registration r,
	profile p
SET
	status = 'enabled'
WHERE
	r.user_id = p.user_id AND
	p.hashed_email = :hashed_email
QUERY;
		
		$stmt = $this->db->prepare( $sql );
		$result = $stmt->execute(
			array(
				':hashed_email' => $this->_hashEmail( $email )
			)
		);
		
		return ( $stmt->rowCount() === 1 );
	}

	public function register( $instance, $username, $password, $email, $birth_date, $name = null, $surname = null, $city = null, $country = null )
	{
		$this->db = $this->connectDb( 'user' );
		
		$sql = <<<QUERY
INSERT INTO
	profile
SET
	name = :name,
	surname = :surname,
	city = :city,
	country = :country,
	username = :username,
	password = :password,
	email = :email,
	hashed_email = :hashed_email,
	iv = :iv,
	birth_date = :birth_date
QUERY;
		
		$iv = mcrypt_create_iv( mcrypt_get_iv_size( self::CRYPT_METHOD, self::CRYPT_MODE ), MCRYPT_DEV_URANDOM );

		$stmt = $this->db->prepare( $sql );
		$result = $stmt->execute(
			array(
				':name' => $this->encryptField( $name, $iv ),
				':surname' => $this->encryptField( $surname, $iv ),
				':city' => $this->encryptField( $city, $iv ),
				':country' => $this->encryptField( $country, $iv ),
				':username' => $username,
				':password' => $this->_hashPassword( $password ),
				':email' => $this->encryptField( $email, $iv ),
				':hashed_email' => $this->_hashEmail( $email ),
				':iv' => $iv,
				':birth_date' => $this->encryptField( $birth_date, $iv )
			)
		);
		
		if ( !$result )
		{
			throw new \SeoFramework\Exception_503( 'Could not insert the user profile registration data' );
		}
		
		$user_id = $this->db->lastInsertId();
		
		$sql = <<<QUERY
INSERT INTO
	registration
SET
	user_id = :user_id,
	instance = :instance,
	status = 'email_validation'
QUERY;
		
		$stmt = $this->db->prepare( $sql );
		$result = $stmt->execute(
			array(
				':user_id' => $user_id,
				':instance' => $instance
			)
		);

		if ( !$result )
		{
			throw new \SeoFramework\Exception_503( 'Could not register the user.' );
		}
		
		return $user_id;
	}
	
	private function _hashEmail( $email )
	{
		return hash_hmac( 'sha256', $email, self::EMAIL_HASH_KEY );
	}
	
	private function _hashEmailValidationToken( $email, $salt )
	{
		return hash_hmac( 'sha256', $email . $salt, self::EMAIL_VALIDATION_HASH_KEY );
	}
	
	private function _hashPassword( $password )
	{
		return hash_hmac( 'sha256', $password, self::PASSWORD_HASH_KEY );
	}
	
	public function login( $login, $password, $password_is_hashed = false )
	{
		$sql = <<<QUERY
SELECT
	up.user_id,
	name,
	surname,
	city,
	country,
	username,
	email,
	iv
FROM
	profile up
	INNER JOIN registration r USING (user_id)
WHERE
	( username = :username OR
	hashed_email = :hashed_email ) AND
	password = :password AND
	r.status = 'enabled'
QUERY;
		
		$this->db = $this->connectDb( 'user' );
		$stmt = $this->db->prepare( $sql );
		
		if ( !$password_is_hashed )
		{
			$password = $this->_hashPassword( $password );
		}
		$result = $stmt->execute(
			array(
				':username' => $login,
				':hashed_email' => $this->_hashEmail( $login ),
				':password' => $password
			)
		);
		$result = $stmt->fetch();

		if ( $result )
		{
			$this->_assignUserData( $this->decryptUserData( $result ) );
			return true;
		}
		
		return false;
	}
	
	public function logout()
	{
		$this->user_data = null;
		\SeoFramework\Cookie::delete( 'user_remember' );
	}
	
	public function saveRememberCookie( $login, $password )
	{
		$password = $this->_hashPassword( $password );
		\SeoFramework\Cookie::set( 'user_remember', $password . self::COOKIE_VALUE_SEPARATOR . $login, 365 );
	}
	
	protected function retrieveFromSession()
	{
		return $this->session->get( 'user_data' );
	}
	
	protected function retrieveFromCookie()
	{
		$user_remember = \SeoFramework\FilterCookie::getInstance()->getString( 'user_remember' );
		$values = explode( self::COOKIE_VALUE_SEPARATOR, $user_remember );
		if ( count( $values ) === 2 )
		{
			$this->login( $values[1], $values[0], true );
		}
	}
	
	protected function retrieveFromId( $user_id )
	{
		if ( $user_id === null )
		{
			throw new UserException( 'Could not retrieve the user. "$user_id" not given.' );
		}
		
		$sql = <<<QUERY
SELECT
	up.user_id,
	up.name,
	up.surname,
	up.city,
	up.country,
	up.username,
	up.email,
	up.iv
FROM
   $this->profile up
WHERE
   up.user_id = :user_id
QUERY;
		
		$this->db = $this->connectDb( 'user' );

		$stmt = $this->db->prepare( $sql );
		$result = $stmt->execute( array( ':user_id' => $user_id ) );

		if ( !$result )
		{
			throw new UserException( 'Could not retrieve the user "' . $user_id . '"' );
		}
		
		$user_data = $stmt->fetch();
		if ( $user_data === null )
		{
			throw new UserException( 'User "' . $user_id . '" not found in DB' );
		}
		
		return $user_data;
	}
	
	private function _assignUserData( $user_data )
	{
		foreach ( $this->user_data as $field => $value )
		{
			if ( isset( $user_data[$field] ) )
			{
				$this->user_data[$field] = $user_data[$field];
			}
		}
	}
	
	protected function decryptUserData( $raw_data )
	{
		$iv = $this->_getComposedIv( $raw_data );

		$user_data = array();
		foreach ( $raw_data as $field => $value )
		{
			$user_data[$field] = $value;
			if ( in_array( $field, $this->crypted_fields ) && !empty( $value ) )
			{
				$user_data[$field] = mcrypt_decrypt( self::CRYPT_METHOD, self::CRYPT_KEY, $value, self::CRYPT_MODE, $iv );
			}
		}
		
		return $user_data;
	}
	
	protected function encryptField( $field, $iv )
	{
		if ( null === $field )
		{
			return null;
		}
		return mcrypt_encrypt( self::CRYPT_METHOD, self::CRYPT_KEY, $field, self::CRYPT_MODE, $iv );
	}
	
	private function _getComposedIv( $raw_data )
	{
		return $raw_data['iv'];
	}
	
	public function get( $field )
	{
		return $this->user_data[$field];
	}
	
	public function __destruct()
	{
		if ( null === $this->user_data )
		{
			$this->user_data = array();
		}
		$this->session->set( 'user_data', $this->user_data );
	}
	
}

class UserException extends \SeoFramework\SEO_Exception
{
	
}
