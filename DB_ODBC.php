<?php

namespace WebSight;

require_once 'DB.php';


class DB_ODBC extends DB
{
/*
	private $pdo = null;
	private $user = null;
	private $pass = null;
	private $onError = null;
	private $pdoDriver = null;
*/

	public function __construct( $pdoDriver, $user, $pass, $onError )
	{
		parent::__construct( '', '', '', $user, $pass, $onError, $pdoDriver );
	}

	protected function createPDO()
	{
		$dsn = "odbc:$this->pdoDriver";

		$pdo = 0;

		//pnl( "DSN: $dsn" );
		try {
			$pdo = new \PDO( $dsn, $this->user, $this->pass );
		} catch (PDOException $e) {
			return null;
		}
		po( $pdo );

		if ($pdo) {
			if (DEBUG) {
				$pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );
			} else {
				$pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT );
			}

		} else {
			return null;
		}

		return $pdo;
	}
}


