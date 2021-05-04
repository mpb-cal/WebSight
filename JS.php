<?php

namespace WebSight;

require_once __DIR__ . '/utilities.php';

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


	static public function addBabelFile( $jsFile )
	{
		self::$jsFiles .= self::makeBabelFile( $jsFile );
	}


	static public function addJSModule( $jsFile )
	{
		self::$jsFiles .= self::makeJSModule( $jsFile );
	}


	static public function makeJS()
	{
		return
			NL . NL
			. self::makeJSFiles()
			. NL . script( '', self::$js )
			. (self::$documentReady ?
          NL . script( '', NL . "jQuery(function($) {" . NL . self::$documentReady . NL . "});" . NL )
        :
          ''
      )
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


	static private function makeJSModule( $filename )
	{
		return
			script( "src='$filename' type='module'" ) . NL
		;
	}


	static private function makeBabelFile( $filename )
	{
		return
			script( "src='$filename' type='text/babel'" ) . NL
		;
	}


}


