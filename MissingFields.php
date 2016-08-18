<?php

namespace WebSight;

require_once 'Session.php';


define( 'SV_MISSING_FIELDS', 'missingFields' );

class MissingFields
{
	private static $session;
	private static $missingFields;

	// call every time
	public static function initialize( Session $session )
	{
		self::$session = $session;
		self::$missingFields = array();

		if (self::$session->get( SV_MISSING_FIELDS )) {
			self::$missingFields = self::$session->get( SV_MISSING_FIELDS );
			self::$session->set( SV_MISSING_FIELDS, null );
		}
	}

	// call during the form POST
	public static function addMissing( $name )
	{
		assert( 'isset( $_SERVER["REQUEST_METHOD"] )' );
		assert( 'is_array( self::$missingFields )' );

		self::$session->set_arr( SV_MISSING_FIELDS, $name, 1 );
	}

	// call after the redirect back to the form
	public static function isMissing( $name )
	{
		assert( 'is_array( self::$missingFields )' );

		return (isset( self::$missingFields[$name] ));
	}
};


