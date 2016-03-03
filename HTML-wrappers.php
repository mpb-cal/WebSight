<?php

namespace WebSight;

function element( $name, $contents = '', $atts = '', $end_tag = true )
{
	if (is_array( $contents ))
	{
		$e = '';
		foreach ($contents as $c)
		{
			$e .= element( $name, $c, $atts, $end_tag );
		}
		return $e;
	}

	if ($atts) {
		$atts = " $atts";
	}

	if ($contents) {
		$contents = "" . $contents . "";
	}

	return "<$name$atts>" . $contents . ($end_tag ? "</$name>" : "") . "";
}



function a( $contents = '', $atts = '' )
{
	return element( 
		'a', 
		$contents, 
		$atts, 
		true
	);
}


function abbr( $contents = '', $atts = '' )
{
	return element( 
		'abbr', 
		$contents, 
		$atts, 
		true
	);
}


function address( $contents = '', $atts = '' )
{
	return element( 
		'address', 
		$contents, 
		$atts, 
		true
	);
}


function area( $contents = '', $atts = '' )
{
	return element( 
		'area', 
		$contents, 
		$atts, 
		false
	);
}


function article( $contents = '', $atts = '' )
{
	return element( 
		'article', 
		$contents, 
		$atts, 
		true
	);
}


function aside( $contents = '', $atts = '' )
{
	return element( 
		'aside', 
		$contents, 
		$atts, 
		true
	);
}


function audio( $contents = '', $atts = '' )
{
	return element( 
		'audio', 
		$contents, 
		$atts, 
		true
	);
}


function b( $contents = '', $atts = '' )
{
	return element( 
		'b', 
		$contents, 
		$atts, 
		true
	);
}


function base( $contents = '', $atts = '' )
{
	return element( 
		'base', 
		$contents, 
		$atts, 
		false
	);
}


function bb( $contents = '', $atts = '' )
{
	return element( 
		'bb', 
		$contents, 
		$atts, 
		true
	);
}


function bdo( $contents = '', $atts = '' )
{
	return element( 
		'bdo', 
		$contents, 
		$atts, 
		true
	);
}


function blockquote( $contents = '', $atts = '' )
{
	return element( 
		'blockquote', 
		$contents, 
		$atts, 
		true
	);
}


function body( $contents = '', $atts = '' )
{
	return element( 
		'body', 
		$contents, 
		$atts, 
		true
	);
}


function br( $contents = '', $atts = '' )
{
	return element( 
		'br', 
		$contents, 
		$atts, 
		false
	);
}


function button( $contents = '', $atts = '' )
{
	return element( 
		'button', 
		$contents, 
		$atts, 
		true
	);
}


function canvas( $contents = '', $atts = '' )
{
	return element( 
		'canvas', 
		$contents, 
		$atts, 
		true
	);
}


function caption( $contents = '', $atts = '' )
{
	return element( 
		'caption', 
		$contents, 
		$atts, 
		true
	);
}


function cite( $contents = '', $atts = '' )
{
	return element( 
		'cite', 
		$contents, 
		$atts, 
		true
	);
}


function code( $contents = '', $atts = '' )
{
	return element( 
		'code', 
		$contents, 
		$atts, 
		true
	);
}


function col( $contents = '', $atts = '' )
{
	return element( 
		'col', 
		$contents, 
		$atts, 
		false
	);
}


function colgroup( $contents = '', $atts = '' )
{
	return element( 
		'colgroup', 
		$contents, 
		$atts, 
		true
	);
}


function command( $contents = '', $atts = '' )
{
	return element( 
		'command', 
		$contents, 
		$atts, 
		false
	);
}


function datagrid( $contents = '', $atts = '' )
{
	return element( 
		'datagrid', 
		$contents, 
		$atts, 
		true
	);
}


function datalist( $contents = '', $atts = '' )
{
	return element( 
		'datalist', 
		$contents, 
		$atts, 
		true
	);
}


function dd( $contents = '', $atts = '' )
{
	return element( 
		'dd', 
		$contents, 
		$atts, 
		true
	);
}


function del( $contents = '', $atts = '' )
{
	return element( 
		'del', 
		$contents, 
		$atts, 
		true
	);
}


function details( $contents = '', $atts = '' )
{
	return element( 
		'details', 
		$contents, 
		$atts, 
		true
	);
}


function dfn( $contents = '', $atts = '' )
{
	return element( 
		'dfn', 
		$contents, 
		$atts, 
		true
	);
}


function dialog( $contents = '', $atts = '' )
{
	return element( 
		'dialog', 
		$contents, 
		$atts, 
		true
	);
}


function div( $contents = '', $atts = '' )
{
	return element( 
		'div', 
		$contents, 
		$atts, 
		true
	);
}


function dl_( $contents = '', $atts = '' )
{
	return element( 
		'dl', 
		$contents, 
		$atts, 
		true
	);
}


function dt( $contents = '', $atts = '' )
{
	return element( 
		'dt', 
		$contents, 
		$atts, 
		true
	);
}


function em( $contents = '', $atts = '' )
{
	return element( 
		'em', 
		$contents, 
		$atts, 
		true
	);
}


function embed( $contents = '', $atts = '' )
{
	return element( 
		'embed', 
		$contents, 
		$atts, 
		false
	);
}


function fieldset( $contents = '', $atts = '' )
{
	return element( 
		'fieldset', 
		$contents, 
		$atts, 
		true
	);
}


function figure( $contents = '', $atts = '' )
{
	return element( 
		'figure', 
		$contents, 
		$atts, 
		true
	);
}


function footer( $contents = '', $atts = '' )
{
	return element( 
		'footer', 
		$contents, 
		$atts, 
		true
	);
}


function form( $contents = '', $atts = '' )
{
	return element( 
		'form', 
		$contents, 
		$atts, 
		true
	);
}


function h1( $contents = '', $atts = '' )
{
	return element( 
		'h1', 
		$contents, 
		$atts, 
		true
	);
}


function h2( $contents = '', $atts = '' )
{
	return element( 
		'h2', 
		$contents, 
		$atts, 
		true
	);
}


function h3( $contents = '', $atts = '' )
{
	return element( 
		'h3', 
		$contents, 
		$atts, 
		true
	);
}


function h4( $contents = '', $atts = '' )
{
	return element( 
		'h4', 
		$contents, 
		$atts, 
		true
	);
}


function h5( $contents = '', $atts = '' )
{
	return element( 
		'h5', 
		$contents, 
		$atts, 
		true
	);
}


function h6( $contents = '', $atts = '' )
{
	return element( 
		'h6', 
		$contents, 
		$atts, 
		true
	);
}


function head( $contents = '', $atts = '' )
{
	return element( 
		'head', 
		$contents, 
		$atts, 
		true
	);
}


function header_( $contents = '', $atts = '' )
{
	return element( 
		'header', 
		$contents, 
		$atts, 
		true
	);
}


function hgroup( $contents = '', $atts = '' )
{
	return element( 
		'hgroup', 
		$contents, 
		$atts, 
		true
	);
}


function hr( $contents = '', $atts = '' )
{
	return element( 
		'hr', 
		$contents, 
		$atts, 
		false
	);
}


function html( $contents = '', $atts = '' )
{
	return element( 
		'html', 
		$contents, 
		$atts, 
		true
	);
}


function i( $contents = '', $atts = '' )
{
	return element( 
		'i', 
		$contents, 
		$atts, 
		true
	);
}


function iframe( $contents = '', $atts = '' )
{
	return element( 
		'iframe', 
		$contents, 
		$atts, 
		true
	);
}


function img_( $contents = '', $atts = '' )
{
	return element( 
		'img', 
		$contents, 
		$atts, 
		false
	);
}


function input( $contents = '', $atts = '' )
{
	return element( 
		'input', 
		$contents, 
		$atts, 
		false
	);
}


function ins( $contents = '', $atts = '' )
{
	return element( 
		'ins', 
		$contents, 
		$atts, 
		true
	);
}


function kbd( $contents = '', $atts = '' )
{
	return element( 
		'kbd', 
		$contents, 
		$atts, 
		true
	);
}


function label( $contents = '', $atts = '' )
{
	return element( 
		'label', 
		$contents, 
		$atts, 
		true
	);
}


function legend( $contents = '', $atts = '' )
{
	return element( 
		'legend', 
		$contents, 
		$atts, 
		true
	);
}


function li( $contents = '', $atts = '' )
{
	return element( 
		'li', 
		$contents, 
		$atts, 
		true
	);
}


function link_( $contents = '', $atts = '' )
{
	return element( 
		'link', 
		$contents, 
		$atts, 
		false
	);
}


function map( $contents = '', $atts = '' )
{
	return element( 
		'map', 
		$contents, 
		$atts, 
		true
	);
}


function mark( $contents = '', $atts = '' )
{
	return element( 
		'mark', 
		$contents, 
		$atts, 
		true
	);
}


function menu( $contents = '', $atts = '' )
{
	return element( 
		'menu', 
		$contents, 
		$atts, 
		true
	);
}


function meta( $contents = '', $atts = '' )
{
	return element( 
		'meta', 
		$contents, 
		$atts, 
		false
	);
}


function meter( $contents = '', $atts = '' )
{
	return element( 
		'meter', 
		$contents, 
		$atts, 
		true
	);
}


function nav( $contents = '', $atts = '' )
{
	return element( 
		'nav', 
		$contents, 
		$atts, 
		true
	);
}


function noscript( $contents = '', $atts = '' )
{
	return element( 
		'noscript', 
		$contents, 
		$atts, 
		true
	);
}


function object( $contents = '', $atts = '' )
{
	return element( 
		'object', 
		$contents, 
		$atts, 
		true
	);
}


function ol( $contents = '', $atts = '' )
{
	return element( 
		'ol', 
		$contents, 
		$atts, 
		true
	);
}


function optgroup( $contents = '', $atts = '' )
{
	return element( 
		'optgroup', 
		$contents, 
		$atts, 
		true
	);
}


function option_( $contents = '', $atts = '' )
{
	return element( 
		'option', 
		$contents, 
		$atts, 
		true
	);
}


function output( $contents = '', $atts = '' )
{
	return element( 
		'output', 
		$contents, 
		$atts, 
		true
	);
}


function p( $contents = '', $atts = '' )
{
	return element( 
		'p', 
		$contents, 
		$atts, 
		true
	);
}


function param( $contents = '', $atts = '' )
{
	return element( 
		'param', 
		$contents, 
		$atts, 
		false
	);
}


function pre( $contents = '', $atts = '' )
{
	return element( 
		'pre', 
		$contents, 
		$atts, 
		true
	);
}


function progress( $contents = '', $atts = '' )
{
	return element( 
		'progress', 
		$contents, 
		$atts, 
		true
	);
}


function q( $contents = '', $atts = '' )
{
	return element( 
		'q', 
		$contents, 
		$atts, 
		true
	);
}


function rp( $contents = '', $atts = '' )
{
	return element( 
		'rp', 
		$contents, 
		$atts, 
		true
	);
}


function rt( $contents = '', $atts = '' )
{
	return element( 
		'rt', 
		$contents, 
		$atts, 
		true
	);
}


function ruby( $contents = '', $atts = '' )
{
	return element( 
		'ruby', 
		$contents, 
		$atts, 
		true
	);
}


function samp( $contents = '', $atts = '' )
{
	return element( 
		'samp', 
		$contents, 
		$atts, 
		true
	);
}


function script( $contents = '', $atts = '' )
{
	return element( 
		'script', 
		$contents, 
		$atts, 
		true
	);
}


function section( $contents = '', $atts = '' )
{
	return element( 
		'section', 
		$contents, 
		$atts, 
		true
	);
}


function select_( $contents = '', $atts = '' )
{
	return element( 
		'select', 
		$contents, 
		$atts, 
		true
	);
}


function small( $contents = '', $atts = '' )
{
	return element( 
		'small', 
		$contents, 
		$atts, 
		true
	);
}


function source( $contents = '', $atts = '' )
{
	return element( 
		'source', 
		$contents, 
		$atts, 
		false
	);
}


function span( $contents = '', $atts = '' )
{
	return element( 
		'span', 
		$contents, 
		$atts, 
		true
	);
}


function strong( $contents = '', $atts = '' )
{
	return element( 
		'strong', 
		$contents, 
		$atts, 
		true
	);
}


function style( $contents = '', $atts = '' )
{
	return element( 
		'style', 
		$contents, 
		$atts, 
		true
	);
}


function sub( $contents = '', $atts = '' )
{
	return element( 
		'sub', 
		$contents, 
		$atts, 
		true
	);
}


function sup( $contents = '', $atts = '' )
{
	return element( 
		'sup', 
		$contents, 
		$atts, 
		true
	);
}


function table( $contents = '', $atts = '' )
{
	return element( 
		'table', 
		$contents, 
		$atts, 
		true
	);
}


function tbody( $contents = '', $atts = '' )
{
	return element( 
		'tbody', 
		$contents, 
		$atts, 
		true
	);
}


function td( $contents = '', $atts = '' )
{
	return element( 
		'td', 
		$contents, 
		$atts, 
		true
	);
}


function textarea( $contents = '', $atts = '' )
{
	return element( 
		'textarea', 
		$contents, 
		$atts, 
		true
	);
}


function tfoot( $contents = '', $atts = '' )
{
	return element( 
		'tfoot', 
		$contents, 
		$atts, 
		true
	);
}


function th( $contents = '', $atts = '' )
{
	return element( 
		'th', 
		$contents, 
		$atts, 
		true
	);
}


function thead( $contents = '', $atts = '' )
{
	return element( 
		'thead', 
		$contents, 
		$atts, 
		true
	);
}


function time_( $contents = '', $atts = '' )
{
	return element( 
		'time', 
		$contents, 
		$atts, 
		true
	);
}


function title( $contents = '', $atts = '' )
{
	return element( 
		'title', 
		$contents, 
		$atts, 
		true
	);
}


function tr( $contents = '', $atts = '' )
{
	return element( 
		'tr', 
		$contents, 
		$atts, 
		true
	);
}


function ul( $contents = '', $atts = '' )
{
	return element( 
		'ul', 
		$contents, 
		$atts, 
		true
	);
}


function var_( $contents = '', $atts = '' )
{
	return element( 
		'var', 
		$contents, 
		$atts, 
		true
	);
}


function video( $contents = '', $atts = '' )
{
	return element( 
		'video', 
		$contents, 
		$atts, 
		true
	);
}

