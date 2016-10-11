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

require_once 'DB.php';


class DB_sqlite extends DB
{
	private $dbFilename = '';

	public function __construct( $dbFilename, $onError = '' )
	{
		parent::__construct( '', '', '', '', '', $onError );
		$this->dbFilename = $dbFilename;
	}

	protected function getDSN()
	{
		return "sqlite:" . $this->dbFilename;
	}
}


