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
		$this->addToHead( meta( "charset='UTF-8'" ) . NL );
	}


	public function output()
	{
		$this->addToBody( JS::makeJS() );
		$this->addToHead( title( '', $this->title ) );

		print
			'<!DOCTYPE html>' . NL
			. html( 'class="no-js" lang=""',
				head( '', $this->head )
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
		$this->head .= $html;
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
			. NL
		);
	}
}


