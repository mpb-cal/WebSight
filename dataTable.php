<?php

namespace WebSight;

const DATATABLES_JS = '//cdn.datatables.net/s/dt/dt-1.10.10/datatables.min.js';

function dataTableInit()
{
	JS::addJSFile( DATATABLES_JS );

	JS::addToDocumentReady( '
		$("table.kollabra-datatable").dataTable( {
			searching: false,
			stateSave: true,
			paging: false,
			info: true
		} );
		'
	);
}

/*
$heads: 	array( 'h1', 'h2' )
$feet: 	array( 'f1', 'f2' )
$rows: 	array( array( 'c1', 'c2' ), array( 'c3', 'c4' ) )
*/
function dataTable( 
	$heads, 
	$feet, 
	$rows, 
	$csv = false, 
	$tableId = ''
)
{
	if ($csv) {
		$rc = '';

		if ($heads) {
			$rc .= csvRow( $heads );
		}

		foreach ($rows as $cells) {
			$rc .= csvRow( $cells );
		}

		return $rc;
	}

	return
		table(
			dataTableHead( $heads ) .
			dataTableFeet( $feet ) .
			dataTableBody( $rows )
			,
			DATATABLE_CLASSES . " id='$tableId'"
		);
}

function dataTableHead( $heads )
{
	if ($heads) {
		return thead( tr( th( $heads ) ) );
	}

	return '';
}

function dataTableBody( $rows )
{
	$body = '';
	if ($rows) foreach ($rows as $row) {
		$body .= tr( td( $row ) );
	}
	return $body;
}

function dataTableFeet( $feet )
{
	if ($feet) {
		return tfoot( tr( th( $feet ) ) );
	}

	return '';
}

function dataTableSetDefaultOrderColumn( $tableId, $column, $direction = 'asc' )
{
	$SESSION_VAR = 'dataTableVisited';
	$dataTableVisited = sessionArr( $SESSION_VAR, $tableId );

	if (!$dataTableVisited) {
		$js = <<<EOF

			$('table#$tableId').DataTable().order( [ $column, '$direction' ] ).draw();

EOF;

		JS::addToDocumentReady( $js );

		setSessionArr( $SESSION_VAR, $tableId, true );
	}
}




