<?php

namespace WebSight;

require_once __DIR__ . '/Session.php';

// SV_ = session variable
define( 'SV_USER_MESSAGE', 'userMessage' );

class Flash
{
	private static $session = null;

	public static function init( \WebSight\Session $session )
	{
		self::$session = $session;
	}


	public static function userMessage( $msg )
	{
		self::$session->set( SV_USER_MESSAGE, $msg );
	}


	public static function addToUserMessage( $msg )
	{
		self::$session->set(
			SV_USER_MESSAGE,
			self::$session->get( SV_USER_MESSAGE ) . $msg . br()
		);
	}


	// this also clears the message
	public static function getUserMessage()
	{
		$message = self::$session->get( SV_USER_MESSAGE );
		self::$session->set( SV_USER_MESSAGE, '' );
		return $message;
	}
}


