<?php

namespace WebSight;


class JS
{
	private static $documentReady = '';
	private static $js = '';
	private static $jsFiles = '';

	static public function addToJS( $js )
	{
		self::$js .= $js;
	}


	static public function addToDocumentReady( $js )
	{
		self::$documentReady .= $js;
	}


	static public function addJSFile( $jsFile )
	{
		self::$jsFiles .= self::makeJSFile( $jsFile );
	}


	static public function makeJS()
	{
		return
			NL . NL
			. self::makeJSFiles()
			. NL . script( '', self::$js )
			. NL . script( '', NL . "jQuery(function($) {" . NL . self::$documentReady . NL . "});" . NL )
			. NL . NL
		;
	}


	static private function makeJSFiles()
	{
		return
			self::$jsFiles
		;
	}


	static private function makeJSFile( $filename )
	{
		return
			script( "src='$filename'" ) . NL
		;
	}


}


