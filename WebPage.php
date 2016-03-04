<?php

namespace WebSight;

class WebPage
{
	private $body = '';
	private $bodyAtts = '';
	private $head = '';

	public function __construct()
	{
		$this->addToHead( meta( '', "charset='UTF-8'" ) . NL );
	}


	public function output()
	{
		$this->addToBody( JS::makeJS() );

		print
			'<!DOCTYPE html>' . NL
			. html(
				head( $this->head )
				. body( $this->body, $this->bodyAtts )
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
		$this->addToHead( meta( '', "name='$name' content='$content'" ) );
	}


	public function setTitle( $title = '' )
	{
		$this->addToHead( title( $title ) );
	}


	public function addStyleSheet( $href = '' )
	{
		$this->addToHead(
			link_(
				'',
				"rel='stylesheet' href='$href' type='text/css' media='all'"
			)
			. NL
		);
	}
}


