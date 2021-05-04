<?php

namespace WebSight;

require_once __DIR__ . '/JS.php';

define( 'NL', "\n" );

class WebPage
{
	private $body = '';
	private $bodyAtts = '';
	private $head = '';
	private $title = '';

	public function __construct()
	{
		$this->addToHead( meta( "charset='UTF-8'" ) );
	}


	public function getOutput()
	{
		$this->addToBody( JS::makeJS() );
		if ($this->title) {
			$this->addToHead( title( '', $this->title ) );
		}

		return
			'<!DOCTYPE html>' . NL
			. html( 'class="" lang=""',	// no-js replaced with js by modernizr?
        NL
				. head( '', NL . $this->head )
				. body( $this->bodyAtts, $this->body )
			)
		;
	}


	public function addToBody( $html = '' )
	{
		$this->body .= $html;
	}


	public function addBodyAtts( $atts = '' )
	{
		$this->bodyAtts .= $atts;
	}


	public function addToHead( $html = '' )
	{
		$this->head .= $html . NL;
	}


	public function addMeta( $name = '', $content = '' )
	{
		$this->addToHead( meta( "name='$name' content='$content'" ) );
	}


	public function setTitle( $title = '' )
	{
		$this->title = $title;
	}


	public function addStyleSheet( $href = '' )
	{
		$this->addToHead(
			link_(
				"rel='stylesheet' href='$href' type='text/css' media='all'"
			)
		);
	}


	public function setNoCache()
	{
		foreach ([
			["cache-control", "no-cache"],	// no-store?
			["expires", "0"],
			["pragma", "no-cache"],
		] as $meta) {
			$this->addToHead( meta( "http-equiv='$meta[0]' content='$meta[1]'" ) );
		}
	}
}


