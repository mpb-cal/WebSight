<?php

/*
	servers are configured in
	/etc/odbc.ini
		(database)
	and
	/etc/freetds/freetds.conf
		(host, port)
*/

namespace WebSight;

require_once __DIR__ . '/DB.php';


class DB_ODBC extends DB
{
	public function __construct( $pdoDriver, $user, $pass, $onError = '' )
	{
		parent::__construct( '', '', '', $user, $pass, $onError, $pdoDriver );
	}

	protected function getDSN()
	{
		return "odbc:$this->pdoDriver";
	}
}


