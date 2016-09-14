<?php

namespace Session;

class Session {
	
	/**
	 * @var bool
	 */
	private $sessionActive = false;
	/**
	 * @var string
	 */
	private $sessionId = 'PHPSESSID';
	
	/**
	 * Returns whether or not the session is active
	 * @return boolean
	 */
	public function isSessionActive() {
		return $this->sessionActive;
	}
	
	/**
	 * @param boolean $sessionState
	 */
	private function setSessionActive( $sessionState ) {
		$this->sessionActive = $sessionState;
	}
	
	/**
	 * Session constructor.
	 * @param bool $regenerateSessionId
	 */
	public function __construct( $regenerateSessionId = false ) {
		session_start();
		session_regenerate_id( $regenerateSessionId );
		$this->setSessionActive( true );
	}
	
	/**
	 * Push one or more elements onto the end of the session array
	 * @param $data
	 * @return int
	 */
	public function push( $data ) {
		if ( $this->isSessionActive() ) {
			return array_push( $_SESSION, $data );
		}
	}
	
	/**
	 * Pop the element off the end of the session array
	 * @return mixed
	 */
	public function pop() {
		if ( $this->isSessionActive() ) {
			return array_pop( $_SESSION );
		}
	}
	
	/**
	 * @param $data
	 * @return int
	 */
	public function unshift( $data ) {
		if ( $this->isSessionActive() ) {
			return array_unshift( $_SESSION, $data );
		}
	}
	
	/**
	 * Prepend one or more elements to the beginning of the session array
	 * @return mixed
	 */
	public function shift() {
		return array_shift( $_SESSION );
	}
	
	/**
	 * Set a key => value pair on the session array
	 * @param $key
	 * @param $value
	 * @return $this
	 */
	public function set( $key, $value ) {
		if ( $this->isSessionActive() ) {
			$_SESSION[ $key ] = $value;
			
			return $this;
		}
	}
	
	/**
	 * Get a value from the session array by a given key
	 * @param string $key
	 * @return mixed
	 */
	public function get( $key = '' ) {
		if ( $this->isSessionActive() ) {
			if ( empty($key) || gettype( $key ) !== 'string' ) {
				return $_SESSION;
			}
			
			return $_SESSION[ $key ];
		}
	}
	
	/**
	 * Get a value from the session array by a numeric index
	 * @param int $index
	 * @return mixed
	 */
	public function at( $index = 0 ) {
		if ( $this->isSessionActive() ) {
			if ( gettype( $index ) !== 'integer' ) {
				return $_SESSION;
			}
			
			return $_SESSION[ $index ];
		}
	}
	
	/**
	 * Get the current session session id
	 * @return string
	 */
	public function getSessionId() {
		if ( $this->isSessionActive() ) {
			$this->sessionId = session_id();
			
			return $this->sessionId;
		}
	}
	
	/**
	 * Set the current session id
	 * @param $sessionId
	 * @return string
	 */
	public function setSessionId( $sessionId ) {
		if ( $this->isSessionActive() ) {
			$this->sessionId = $sessionId;
			
			return session_id( $sessionId );
		}
	}
	
	/**
	 * Resets a session with original values stored in session storage. This function requires an active session and discards changes in $_SESSION.
	 * @return $this
	 */
	public function reset() {
		if ( $this->isSessionActive() ) {
			session_reset();
		}
		
		return $this;
	}
	
	/**
	 * Free (unset) all session variables up for garbage collection, and [optionally] set new data
	 * @param array $data
	 * @return $this
	 */
	public function initialize( $data = [] ) {
		if ( $this->isSessionActive() ) {
			session_unset();
			if ( is_array( $data ) ) {
				$_SESSION = $data;
			}
		}
		
		return $this;
	}
	
	/**
	 * Delete all session variables
	 * @return $this
	 */
	public function delete() {
		if ( $this->isSessionActive() ) {
			session_unset();
		}
		
		return $this;
	}
	
	/**
	 * Remove a session variable at a given key/index
	 * @param $key
	 */
	public function remove( $key ) {
		if ( $this->isSessionActive() && isset($_SESSION[ $key ]) ) {
			unset($_SESSION[ $key ]);
		}
	}
	
	/**
	 * Destroy the current session and empty the client session cookie
	 * @return bool
	 */
	public function destroy() {
		if ( $this->isSessionActive() ) {
			setcookie( $this->sessionId );
			
			return session_destroy();
		}
	}
	
	/**
	 * Save the current session variables and close the session
	 */
	public function close() {
		if ( $this->isSessionActive() ) {
			session_write_close();
			$this->setSessionActive( false );
		}
	}
	
	/**
	 * PHP_SESSION_DISABLED if sessions are disabled.
	 * PHP_SESSION_NONE if sessions are enabled, but none exists.
	 * PHP_SESSION_ACTIVE if sessions are enabled, and one exists.
	 * @return int
	 */
	public function getStatus() {
		if ( $this->isSessionActive() ) {
			return session_status();
		}
	}
	
}