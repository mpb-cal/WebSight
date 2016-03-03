<?php

namespace WebSight;

class WebPage
{
	private $html = null;

	public function __construct()
	{
		$this->html =
			'<!DOCTYPE html>' . NL
		;
	}


	public function output()
	{
		print
			$this->html
		;
	}


	public function add( $html = '' )
	{
		$this->html .= $html;
	}
}


