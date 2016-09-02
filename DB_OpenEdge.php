<?php

namespace WebSight;

require_once 'DB.php';


class DB_OpenEdge extends DB
{
	public function __construct( $host, $port, $dbname, $user, $pass, $onError )
	{
		parent::__construct( $host, $port, $dbname, $user, $pass, $onError );
	}

	protected function createPDO()
	{
		$dsn = "jdbc:datadirect:openedge:$this->host:$this->port;databaseName=$this->dbname";
		$pdo = 0;
		pnl( "DSN: $dsn" );

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


