<?php

namespace WebSight;

require_once 'utilities.php';
require_once 'WebPage.php';

const DATATABLES_CSS = '//cdn.datatables.net/v/bs/dt-1.10.12/datatables.min.css';
const DATATABLES_JS = '//cdn.datatables.net/v/bs/dt-1.10.12/datatables.min.js';
const DATATABLE_CLASS = 'websightDatatable';
const TABLE_CLASSES = 'table table-bordered compact order-column ' . DATATABLE_CLASS;

function dataTableInit( WebPage $webPage )
{
	JS::addJSFile( DATATABLES_JS );

	JS::addToDocumentReady( '
		$("table.' . DATATABLE_CLASS . '").dataTable( {
			searching: false,
			stateSave: true,
			paging: false,
			info: true
		} );
		'
	);

	$webPage->addStyleSheet( DATATABLES_CSS );
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
	$tableId = '',
	$tableClasses = ''
)
{
	if ($csv) {
		ob_start();
		if ($heads) {
			csvRow( $heads );
		}

		foreach ($rows as $cells) {
			csvRow( $cells );
		}

		return ob_get_clean();
	}

	return
		table( "class='" . TABLE_CLASSES . ' ' . $tableClasses . "' id='$tableId'",
			dataTableHead( $heads ) .
			dataTableFeet( $feet ) .
			dataTableBody( $rows )
		);
}

function dataTableHead( $heads )
{
	if ($heads) {
		return thead( '', tr( '', th( '', $heads ) ) );
	}

	return '';
}

function dataTableBody( $rows )
{
	$body = '';
	if ($rows) foreach ($rows as $row) {
		$body .= tr( '', td( '', $row ) );
	}
	return $body;
}

function dataTableFeet( $feet )
{
	if ($feet) {
		return tfoot( '', tr( '', th( '', $feet ) ) );
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




