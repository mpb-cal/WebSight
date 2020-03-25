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


class DB_sqlite extends DB
{
	protected $dbFilename = '';

	public function __construct( $dbFilename, $onError = '' )
	{
		$this->dbFilename = $dbFilename;
		parent::__construct( '', '', '', '', '', $onError );
	}

	protected function getDSN()
	{
		return "sqlite:" . $this->dbFilename;
	}
}


