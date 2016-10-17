<?php

namespace WebSight;

require_once 'DB.php';


class DB_OpenEdge extends DB
{
	public function __construct( $host, $port, $dbname, $user, $pass, $onError = '' )
	{
		parent::__construct( $host, $port, $dbname, $user, $pass, $onError );
	}

	protected function getDSN()
	{
		return "odbc:fdm4test";
		return "odbc:fdm4test:$this->host:$this->port;databaseName=$this->dbname";
	}
}


