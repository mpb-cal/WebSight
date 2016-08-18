<?php

namespace WebSight;

class Session
{
	private $m_phpSession = null;


	public function __construct( $name )
	{
		session_name( $name );
		// once we're using https: session_set_cookie_params( 0, '/', '', true, true );
		session_set_cookie_params( 0, '/', '', false, true );
		session_start();
		$this->m_phpSession = &$_SESSION;
		unset( $_SESSION );
	}


	public function get( $name )
	{
		if (isset( $this->m_phpSession[$name] )) {
			return $this->m_phpSession[$name];
		}

		return '';
	}


	public function set( $name, $value )
	{
		$this->m_phpSession[$name] = $value;
	}


	public function get_arr( $name, $index )
	{
		if (isset( $this->m_phpSession[$name] ) and isset( $this->m_phpSession[$name][$index] )) {
			return $this->m_phpSession[$name][$index];
		}

		return '';
	}


	public function set_arr( $name, $index, $value )
	{
		if (!isset( $this->m_phpSession[$name] )) {
			$this->m_phpSession[$name] = array();
		}

		$this->m_phpSession[$name][$index] = $value;
	}


/*
	public function value( $name )
	{
		return 'value="' . esc( $this->sessionVar( $name ) ) . '"';
	}
*/


	public function unset_( $name )
	{
		unset( $this->m_phpSession[$name] );
	}
}




