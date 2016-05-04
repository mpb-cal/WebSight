<?php

namespace WebSight;

define( 'SHOW_SQL', false );
define( 'SHOW_SQL_RESULTS', false );
define( 'FETCH_STYLE', \PDO::FETCH_BOTH );

// a wrapper class for database queries

class DB
{
	private $pdo = null;
	private $host = null;
	private $port = null;
	private $dbname = null;
	private $user = null;
	private $pass = null;
	private $onError = null;

	public function __construct( $host, $port, $dbname, $user, $pass, $onError )
	{
		$this->host = $host;
		$this->port = $port;
		$this->dbname = $dbname;
		$this->user = $user;
		$this->pass = $pass;
		$this->onError = $onError;
		$this->pdo = $this->createPDO();
	}

	public function lastInsertId()
	{
		if (!$this->pdo) return;
		return $this->pdo->lastInsertId();
	}

	public function sql( $sql, $parameters = array(), $printOnly = false )
	{
		if (!$this->pdo) return;

/*
		if (LOG_SQL) {
			$p = '';

			if ($parameters) {
				ob_start();
				print_r( $parameters );
				$p = "\n" . ob_get_clean();
			}

			writeLog( "SQL: $sql" . $p );
		}
*/

/*
		if (DEBUG and (SHOW_SQL or $printOnly)) {
			pbr( "<code>$sql</code>" );
			if ($parameters) printObj( $parameters );
			pbr();
			flush();
		}
*/

		if ($printOnly) {
			return;
		}

		if (($pdoStatement = $this->pdo->prepare( $sql )) === false) {
			$this->showSQLError( $sql );
		}

		if (($pdoStatement->execute( $parameters )) === false) {
			$this->showSQLError( $sql );
		}

		return $pdoStatement;
	}

	public function selectRow( $sql, $parameters = array() )
	{
		if (!$this->pdo) return array();

		$pdoStatement = $this->sql( $sql, $parameters );
		$row = $pdoStatement->fetch( FETCH_STYLE );

/*
		if (DEBUG and SHOW_SQL)
		{
			$size = round( mb_strlen( serialize( $row ), '8bit' ) / 1024 );
			pbr( "<code>Row(s) fetched: 1. Size: $size KB.</code>" );
			pbr();
			flush();
		}

		if (DEBUG and SHOW_SQL_RESULTS)
		{
			printObj( $row );
		}
*/

		return $row;
	}

	public function selectRows( $sql, $parameters = array() )
	{
		if (!$this->pdo) return;

		$pdoStatement = $this->sql( $sql, $parameters );
		$rows = $pdoStatement->fetchAll( FETCH_STYLE );

/*
		if (DEBUG and SHOW_SQL)
		{
			$size = round( mb_strlen( serialize( $rows ), '8bit' ) / 1024 );
			//pnl( "<pre>Row(s) fetched: " . count( $rows ) . ". Size: $sie KB.\n</pre>" );
			pbr( "<span class=sqlresult>Row(s) fetched: " . count( $rows ) . ". Size: $size KB.</span>" );
			pbr();
			flush();
		}

		if (DEBUG and SHOW_SQL_RESULTS)
		{
			printObj( $rows );
		}
*/

		return $rows;
	}

	// table must have 'id' column if findId == true
	// should only be called by Model
	public function createRow( $table, $parameters = array(), $findId = false, $printOnly = false )
	{
		$sql = "insert into $table ";

		if ($findId or $parameters) {
			$sql .= "set ";

			if ($findId) {
				$this->sql( "lock tables $table write" );
				$row = $this->selectRow( "select max(id) from $table" );
				$id = $row['max(id)'] + 1;
				$sql .= "id=$id, ";
			}

			foreach (array_keys( $parameters ) as $pname) {
				$sql .= "$pname=:$pname, ";
			}
		} else {
			$sql .= "values() ";
		}

		$sql = preg_replace( "/, $/", "", $sql );

		$this->sql( $sql, $parameters, $printOnly );

		if ($findId) {
			$this->sql( "unlock tables" );
			return $id;
		}
	}

	public function updateRow( $table, $parameters = array(), $where )
	{
		$sql = "update $table set ";

		foreach (array_keys( $parameters ) as $pname)
		{
			$sql .= "$pname=:$pname, ";
		}

		$sql = preg_replace( "/, $/", " where $where", $sql );

		$this->sql( $sql, $parameters );
	}

	// private
	private function createPDO()
	{
		$dsn = "mysql:host=$this->host;port=$this->port;dbname=$this->dbname";

		$pdo = 0;

		try {
			$pdo = new \PDO( $dsn, $this->user, $this->pass );
		} catch (PDOException $e) {
			return null;
		}

		if ($pdo) {
			if (DEBUG) {
				$pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );
			} else {
				$pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT );
			}

			$pdo->query( "set time_zone='US/Pacific'" );
		} else {
			return null;
		}

		return $pdo;
	}

	// private
	private function showSQLError( $sqlStatement )
	{
		$this->onError( "Error in SQL:\n$sqlStatement\n\n\n" );
	}
}


