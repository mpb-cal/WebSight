<?php

namespace WebSight;

require_once __DIR__ . '/utilities.php';
require_once __DIR__ . '/WebPage.php';

const DATATABLES_CSS = '//cdn.datatables.net/v/bs/dt-1.10.12/datatables.min.css';
const RESPONSIVE_CSS = '//cdn.datatables.net/responsive/2.1.0/css/responsive.dataTables.min.css';
const DATATABLES_JS = '//cdn.datatables.net/v/bs/dt-1.10.12/datatables.min.js';
const RESPONSIVE_JS = '//cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js';
const DATATABLE_CLASS = 'websightDatatable';
const TABLE_CLASSES = 'table table-bordered compact table-condensed order-column ' . DATATABLE_CLASS;

function dataTableInit( WebPage $webPage )
{
	JS::addJSFile( DATATABLES_JS );
	//JS::addJSFile( RESPONSIVE_JS );

	JS::addToDocumentReady( '
		$("table.' . DATATABLE_CLASS . '").dataTable( {
			searching: false,
			stateSave: true,
			paging: false,
			info: false
		} );
		'
	);

	$webPage->addStyleSheet( DATATABLES_CSS );
	//$webPage->addStyleSheet( RESPONSIVE_CSS );
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

function dataTableSetDefaultOrderColumn( Session $session, $tableId, $column, $direction = 'asc' )
{
	$SESSION_VAR = 'dataTableVisited';
	$dataTableVisited = $session->get_arr( $SESSION_VAR, $tableId );

	if (!$dataTableVisited) {
		$js = <<<EOF

			$('table#$tableId').DataTable().order( [ $column, '$direction' ] ).draw();

EOF;

		JS::addToDocumentReady( $js );

		$session->set_arr( $SESSION_VAR, $tableId, true );
	}
}


// not working?
function dataTableSetOption( $tableId, $optionName, $optionValue )
{
	$js = <<<EOF

		$('table#$tableId').dataTable( {
			$optionName: $optionValue
		} );

EOF;

	JS::addToDocumentReady( $js );
}




