<?php

namespace WebSight;


function bs_container( $contents )
{
	return
		div( 'class="container"',
			$contents
		)
	;
}


function bs_container_full_width( $contents )
{
	return
		div( 'class="container-fluid"',
			$contents
		)
	;
}


function bs_row( $contents )
{
	return
		div( 'class="row"',
			$contents
		)
	;
}


function bs_column( $contents = '', $width = 1, $columnSize = 'md' )
{
	return
		div( "class='col-$columnSize-$width'",
			$contents
		)
	;
}


function bs_equal_columns( $columns = array(), $columnSize = 'md' )
{
	$width = 12 / count( $columns );

	$rc = '';
	foreach ($columns as $column) {
		$rc .= bs_column( $column, $width, $columnSize );
	}

	return $rc;
}



