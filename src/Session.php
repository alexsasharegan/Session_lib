<?php

namespace Session;

class Session implements \JsonSerializable {
	
	const ONE_MINUTE  = 60;
	const ONE_HOUR    = self::ONE_MINUTE * 60;
	const ONE_DAY     = self::ONE_HOUR * 24;
	const ONE_WEEK    = self::ONE_DAY * 7;
	const ONE_MONTH   = self::ONE_DAY * 30;
	const ONE_YEAR    = self::ONE_DAY * 365;
	const SIX_MONTHS  = self::ONE_YEAR / 2;
	const ONE_QUARTER = self::ONE_YEAR * .25;
	
	protected static $sessionName = NULL;
	
	/**
	 * @var bool
	 */
	protected $sessionIsActive = FALSE;
	/**
	 * @var string
	 */
	protected $sessionId;
	
	protected $previousSessionName = NULL;
	
	/**
	 * @param mixed $sessionName
	 *
	 * @return Session
	 */
	public function setSessionName( $sessionName )
	{
		$this->previousSessionName = session_name( $sessionName );
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getSessionName()
	{
		return session_name();
	}
	
	/**
	 * Returns whether or not the session is active
	 * @return boolean
	 */
	public function isSessionActive()
	{
		return $this->sessionIsActive;
	}
	
	/**
	 * @param boolean $sessionIsActive
	 */
	protected function setSessionStatus( $sessionIsActive )
	{
		$this->sessionIsActive = (boolean) $sessionIsActive;
	}
	
	/**
	 * Session constructor.
	 *
	 * @param null $sessionName
	 * @param null $sessionId
	 * @param bool $regenerateSessionId
	 */
	public function __construct( $sessionName = NULL, $sessionId = NULL, $regenerateSessionId = FALSE )
	{
		// In order to set a session name other than the default,
		// this must be called before session_start
		if ( ! is_null( $sessionName ) ) $this->setSessionName( $sessionName );
		
		// In order to set a session id other than the default,
		// this must be called before session_start
		$this->initSessionId( $sessionId );
		
		$this->startSession();
		
		if ( $regenerateSessionId ) $this->regenerateId( TRUE );
	}
	
	protected function startSession()
	{
		$this->setSessionStatus( session_start() );
		
		return $this;
	}
	
	/**
	 * Close session on object destruction
	 */
	public function __destruct()
	{
		$this->close();
	}
	
	protected function verifySession()
	{
		if ( ! $this->isSessionActive() ) throw new \Exception( "Session not started; cannot perform session methods." );
	}
	
	/**
	 * Push one or more elements onto the end of the session array
	 *
	 * @param $data
	 *
	 * @return int
	 */
	public function push( $data )
	{
		$this->verifySession();
		
		return array_push( $_SESSION, $data );
	}
	
	/**
	 * Pop the element off the end of the session array
	 * @return mixed
	 */
	public function pop()
	{
		$this->verifySession();
		
		return array_pop( $_SESSION );
	}
	
	/**
	 * @param $data
	 *
	 * @return int
	 */
	public function unshift( $data )
	{
		$this->verifySession();
		
		return array_unshift( $_SESSION, $data );
	}
	
	/**
	 * Prepend one or more elements to the beginning of the session array
	 * @return mixed
	 */
	public function shift()
	{
		$this->verifySession();
		
		return array_shift( $_SESSION );
	}
	
	/**
	 * Set a key => value pair on the session array
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return $this
	 */
	public function set( $key, $value )
	{
		$this->verifySession();
		$_SESSION[ $key ] = $value;
		
		return $this;
	}
	
	/**
	 * Get a value from the session array by a given key
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get( $key = '' )
	{
		$this->verifySession();
		
		if ( empty( $key ) || gettype( $key ) !== 'string' )
		{
			return $_SESSION;
		}
		
		return isset( $_SESSION[ $key ] ) ? $_SESSION[ $key ] : NULL;
	}
	
	/**
	 * Returns the session array.
	 *
	 * @return mixed
	 */
	public function getAll()
	{
		$this->verifySession();
		
		return $_SESSION;
	}
	
	/**
	 * Get a value from the session array by a numeric index
	 *
	 * @param int $index
	 *
	 * @return mixed
	 */
	public function at( $index = 0 )
	{
		$this->verifySession();
		settype( $index, 'integer' );
		
		return isset( $_SESSION[ $index ] ) ? $_SESSION[ $index ] : NULL;
	}
	
	/**
	 * @param null $sessionId
	 *
	 * @return string
	 */
	protected function initSessionId( $sessionId = NULL )
	{
		$this->sessionId = is_null( $sessionId ) ? session_id() : $this->setSessionId( $sessionId );
		
		return $this->sessionId;
	}
	
	/**
	 * Get the current session session id
	 * @return string
	 */
	public function getSessionId()
	{
		$this->verifySession();
		$this->sessionId = session_id();
		
		return $this->sessionId;
	}
	
	/**
	 * Set the current session id
	 *
	 * @param $sessionId
	 *
	 * @return string
	 */
	public function setSessionId( $sessionId )
	{
		$this->sessionId = $sessionId;
		
		return session_id( $sessionId );
	}
	
	/**
	 * Update the current session id with a newly generated one
	 *
	 * @param bool $deleteOldSession
	 *
	 * @return bool
	 */
	public function regenerateId( $deleteOldSession = FALSE )
	{
		$this->verifySession();
		
		return session_regenerate_id( $deleteOldSession );
	}
	
	/**
	 * Resets a session with original values stored in session storage. This function requires an active session and
	 * discards changes in $_SESSION.
	 * @return $this
	 */
	public function reset()
	{
		$this->verifySession();
		session_reset();
		
		return $this;
	}
	
	/**
	 * Free (unset) all session variables up for garbage collection, and [optionally] set new data
	 *
	 * @param array $data
	 *
	 * @return $this
	 */
	public function initialize( array $data = [] )
	{
		$this->verifySession();
		session_unset();
		$_SESSION = $data;
		
		return $this;
	}
	
	/**
	 * Delete all session variables
	 * @return $this
	 */
	public function delete()
	{
		$this->verifySession();
		session_unset();
		
		return $this;
	}
	
	/**
	 * Remove a session variable at a given key/index
	 *
	 * @param $key
	 *
	 * @return $this
	 */
	public function remove( $key )
	{
		$this->verifySession();
		unset( $_SESSION[ $key ] );
		
		return $this;
	}
	
	/**
	 * Destroy the current session and empty the client session cookie
	 * @return bool
	 */
	public function destroy()
	{
		$this->verifySession();
		setcookie( $this->getSessionName() );
		
		return session_destroy();
	}
	
	/**
	 * Takes an array of cookie options:
	 * [
	 *  'name'     => $this->getSessionName(),
	 *  'value'    => NULL,
	 *  'expire'   => NULL,
	 *  'path'     => NULL,
	 *  'domain'   => NULL,
	 *  'secure'   => NULL,
	 *  'httpOnly' => NULL,
	 * ]
	 *
	 * @param array $options
	 *
	 * @return bool
	 */
	public function setCookie( array $options = [] )
	{
		$this->verifySession();
		
		$defaults = [
			'name'     => $this->getSessionName(),
			'value'    => NULL,
			'expire'   => NULL,
			'path'     => NULL,
			'domain'   => NULL,
			'secure'   => NULL,
			'httponly' => NULL,
		];
		
		$options = array_merge( $defaults, $options );
		
		return setcookie(
			$options['name'],
			$options['value'],
			$options['expire'],
			$options['path'],
			$options['domain'],
			$options['secure'],
			$options['httponly']
		);
	}
	
	/**
	 * <h1>$params['lifetime']</h1>
	 * <p>Lifetime of the session cookie, defined in seconds.</p>
	 * <h1>$params['path']</h1>
	 * <p>Path on the domain where the cookie will work. Use a single slash ('/') for all paths on the domain.</p>
	 * <h1>$params['domain']</h1>
	 * <p>Cookie domain, for example 'www.php.net'. To make cookies visible on all subdomains then the domain must be
	 * prefixed with a dot like '.php.net'.</p>
	 * <h1>$params['secure']</h1>
	 * <p>If TRUE cookie will only be sent over secure connections.</p>
	 * <h1>$params['httponly']</h1>
	 * <p>If set to TRUE then PHP will attempt to send the httponly flag when setting the session cookie.</p>
	 *
	 * @param array $params
	 *
	 * @return void
	 */
	public static function setCookieParams( array $params )
	{
		$options = array_merge( self::getCookieParams(), $params );
		
		session_set_cookie_params(
			$options['lifetime'],
			$options['path'],
			$options['domain'],
			$options['secure'],
			$options['httponly']
		);
	}
	
	/**
	 * session.cookie_lifetime specifies the lifetime of the cookie in seconds which is sent to the browser.
	 * The value 0 means "until the browser is closed." Defaults to 0.
	 *
	 * @param int $lifetime
	 */
	public static function setCookieLifetime( $lifetime = 0 )
	{
		self::setCookieParams( compact( 'lifetime' ) );
	}
	
	/**
	 * session.cookie_path specifies path to set in the session cookie. Defaults to /.
	 *
	 * @param string $path
	 */
	public static function setCookiePath( $path = '/' )
	{
		self::setCookieParams( compact( 'path' ) );
	}
	
	/**
	 * session.cookie_domain specifies the domain to set in the session cookie.
	 * Default is none at all meaning the host name of the server which generated the cookie according to cookies
	 * specification.
	 * To make cookies visible on all subdomains then the domain must be
	 * prefixed with a dot like '.php.net'.
	 *
	 * @param $domain
	 */
	public static function setCookieDomain( $domain )
	{
		self::setCookieParams( compact( 'domain' ) );
	}
	
	/**
	 * session.cookie_secure specifies whether cookies should only be sent over secure connections.
	 * Defaults to off.
	 * This setting was added in PHP 4.0.4.
	 *
	 * @param $secure
	 */
	public static function setCookieSecure( $secure )
	{
		self::setCookieParams( compact( 'secure' ) );
	}
	
	/**
	 * Marks the cookie as accessible only through the HTTP protocol.
	 * This means that the cookie won't be accessible by scripting languages, such as JavaScript.
	 * This setting can effectively help to reduce identity theft through XSS attacks (although it is not supported by
	 * all browsers).
	 *
	 * @param $httponly
	 */
	public static function setCookieHttpOnly( $httponly )
	{
		self::setCookieParams( compact( 'httponly' ) );
	}
	
	/**
	 * an array with the current session cookie information,
	 * the array contains the following items:
	 * "lifetime" - The lifetime of the cookie in seconds.
	 * "path" - The path where information is stored.
	 * "domain" - The domain of the cookie.
	 * "secure" - The cookie should only be sent over secure connections.
	 * "httponly" - The cookie can only be accessed through the HTTP protocol.
	 * @return array
	 */
	public static function getCookieParams()
	{
		return session_get_cookie_params();
	}
	
	/**
	 * Save the current session variables and close the session
	 */
	public function close()
	{
		$this->verifySession();
		session_write_close();
		$this->setSessionStatus( FALSE );
		
		return $this;
	}
	
	/**
	 * PHP_SESSION_DISABLED if sessions are disabled.
	 * PHP_SESSION_NONE if sessions are enabled, but none exists.
	 * PHP_SESSION_ACTIVE if sessions are enabled, and one exists.
	 * @return int
	 */
	public function getStatus()
	{
		$this->verifySession();
		
		return session_status();
	}
	
	/**
	 * @param null $sessionId
	 * @param bool $regenerateSessionId
	 *
	 * @return static
	 */
	public static function newSession( $sessionId = NULL, $regenerateSessionId = FALSE )
	{
		return new static( self::$sessionName, $sessionId, $regenerateSessionId );
	}
	
	/**
	 * @param string $name
	 */
	public static function setGlobalSessionName( $name = 'PHPSESSID' )
	{
		self::$sessionName = $name;
	}
	
	/**
	 * @return string|null
	 */
	public static function getGlobalSessionName()
	{
		return self::$sessionName;
	}
	
	/**
	 * @return null|string
	 */
	public function getPreviousSessionName()
	{
		return $this->previousSessionName;
	}
	
	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize()
	{
		return array_merge( $_SESSION, [
			'sessionId'   => $this->getSessionId(),
			'sessionName' => $this->getSessionName(),
		] );
	}
	
	/**
	 * @return array
	 */
	public function toArray()
	{
		return $this->jsonSerialize();
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		return json_encode( $this );
	}
}
