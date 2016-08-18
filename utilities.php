<?php

namespace WebSight;

require_once 'HTML-wrappers.php';
require_once 'bootstrap.php';


function sendNoCacheHeaders()
{
	header( "Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0" );
	header( "Pragma: no-cache" );
	header( "Expires: Sat, 26 Jul 1997 05:00:00 GMT" );
}


function cmdLine()
{
	global $argc;

	if (isset( $argc ) and $argc) return true;
	return false;
}


function startStyle()
{
	return "<style type=\"text/css\">\n";
}


function endStyle()
{
	return "</style>\n";
}


function startForm( $hasFiles = false, $atts = '', $action = 'main' )
{
	$html =
		'<form method=post ' . ($hasFiles ? 'enctype="multipart/form-data"' : '') . " action='$action' $atts>" .
		"\n" .
		refererInput() .
		"\n";

	return $html;
}


function endForm()
{
	$html = '</form>' . "\n";

	return $html;
}


function findFiles( $pattern )
{
	$files = array();
	foreach (explode( "\n", `ls -1 $pattern` ) as $i => $line)
	{
		if ($line) $files[] = basename( $line );
	}

	return $files;
}


function truncateString( $string, $max )
{
	if (strlen( $string ) > $max)
	{
		$leave = $max - 3;
		return substr_replace( $string, '...', $leave );
	}

	return $string;
}


function arrVal( $arr, $index )
{
	if (isset( $arr[$index] )) return $arr[$index];
	return null;
}


function mk_array_reverse( $arr )
{
	if (!is_array( $arr )) return;

	$reverse = $arr;
	$j = count( $arr ) - 1;

	for ($i = 0; $i < count( $arr ); $i++)
	{
		assert( 'isset( $arr[$i] )' );
		if (!isset( $arr[$i] )) continue;

		$reverse[$j--] = $arr[$i];
	}

	return $reverse;
}


// returns an array of filenames (with or without the full path)
function readDirectory( $dir, $withPath = false ) 
{
	$filenames = array();

	if (!is_dir( $dir )) return;
	if (!is_readable( $dir )) return;

	$dh = opendir( $dir );

	if ($dh !== false)
	{
		while (($file = readdir( $dh )) !== false)
		{
			if ($file == '.') continue;
			if ($file == '..') continue;

			$filenames[] = ($withPath ? "$dir/" : '') . $file;
		}

		closedir( $dh );

		return $filenames;
	}
}


function copyDirectory( $from_path, $to_path ) 
{ 
	if (!file_exists( $to_path ))
	{
		mkdir( $to_path, 0775 ); 
	}

	$this_path = getcwd(); 
	if (is_dir($from_path)) 
	{ 
		chdir($from_path); 
		$handle=opendir('.'); 
		while (($file = readdir($handle))!==false) 
		{ 
			if (($file != ".") && ($file != "..")) 
			{ 
				if (is_dir($file)) 
				{ 
					rec_copy( "$from_path/$file/", "$to_path/$file/" ); 
					chdir($from_path); 
				} 
				if (is_file($file))
				{ 
					copy( "$from_path/$file", "$to_path/$file" ); 
				} 
			} 
		} 
		closedir($handle); 
	} 

	chdir($this_path); 
}


function formatMoney( $amt, $redNegative = true )
{
	setlocale( LC_MONETARY, 'en_US' );
	if (!is_numeric( $amt )) return null;
	$str = money_format( "%+!n", trim( $amt ) );
	if ($amt < 0 and $redNegative) $str = "<font color=red>$str</font>";
	return $str;
}


function arrayToFile( $lines, $file )
{
	if (!($fh = fopen( $file, 'w' ))) return false;
	foreach ($lines as $line)
	{
		if (!fwrite( $fh, $line )) return false;
	}
	fclose( $fh );

	return true;
}


///////////////////////////////////////////////////////
// CGI/HTML functions
///////////////////////////////////////////////////////

function sp2nbsp( $text )
{
	return preg_replace( "/ /", "&nbsp;", $text );
}


function makeButtonLink( $href, $text, $width = 180 )
{
	print <<<EOF
<input type=button style="width:$width" onClick="location='$href';" value="$text">

EOF;
}


function makeButtonLinkDefaultWidth( $href, $text )
{
	print <<<EOF
<input type=button onClick="location='$href';" value="$text">

EOF;
}


function redirect( $location, $printOnly = false )
{
	if ($printOnly) {
		print a( "Redirect: $location", 'href=' . $location );
		exit;
	}

	//writeLog( "Redirecting to $location" );

	if (headers_sent() === false) {
		header( "HTTP/1.1 301 See Other" );
		header( "Location: $location" );
	}

	$js = '';
	$location = addslashes( $location );
	$js = "location=\"$location\";";

	print <<<EOF

	<script type="text/javascript">
		$js
	</script>

	<a href="$location">Please click here if a new page does not automatically appear within a few seconds.</a>

	</body>
	</html>

EOF;

	exit;
}


function openWindow( $url )
{
	pnl( 
		script(
			"window.open( '$url' );\n"
		)
	);
}


function pC()
{
	printCGIVars();
}


function printCGIVars()
{
	if (!cmdLine()) {
		pnl( '<div style="white-space: pre; text-align: left; font-family: monospace; font-size: medium; ">' );
	}

	print getCGIVars();

	if (!cmdLine()) {
		print "</div><br><br><br>\n";
	}
}

function getCGIVars()
{
	ob_start();

	foreach (array( 
		'_POST', 
		'_GET', 
		'_COOKIE', 
		'_FILES', 
		'_SESSION', 
		'_SERVER', 
		'_ENV' 
	) as $varname) {
		print "-----------------------------\n$varname =\n-----------------------------\n";

		if ($var = eval( "if (isset( \$$varname )) return \$$varname;" )) {
			pnl( "Size: " . count( $var ) );
			ksort( $var );
			print_r( $var );
		}
	}

	pnl();
	pnl();

/*
	print "------------------<br>_FILES:<br>\n";
	reset( $_FILES );
	while (list($key,$val) = each( $_FILES ))
	{
		if (is_array( $val ))
		{
			while (list($k2,$v2) = each( $val ))
			{
				print " $key" . "[$k2] = $v2<br>\n";
			}
		}
		else
		{  
			print " $key = $val<br>\n";
		}
	}

	printObj( $_FILES );
*/

	if (function_exists( 'apache_request_headers' )) {
		$var = apache_request_headers();
		ksort( $var );
		pnl( "-----------------------------\nApache Request Headers =\n-----------------------------" );
		print_r( $var );
	}

	return ob_get_clean();
}


function printGlobalVars()
{
?>
	<pre>
	<div style="white-space: pre; text-align: left; font-family: monospace; font-size: medium; ">

<?php

	foreach (array( 'GLOBALS' ) as $varname)
	{
		$var = eval( "return \$$varname;" );
		ksort( $var );
		print "-----------------------------\n$varname =\n-----------------------------\n";
		print_r( $var );
	}

	print "</div><br><br><br>\n";
}


function printRequest()
{
?>
	<div style="white-space: pre; text-align: left; font-family: monospace; font-size: medium; ">
<?php

	print_r( $_REQUEST );

	print "</div><br><br><br>\n";
}


function cgiVars2Url()
{
	$url = '';
	foreach (array( '_GET', '_POST' ) as $varname)
	{
		$var = eval( "return \$$varname;" );
		ksort( $var );
		foreach (array_keys( $var ) as $k)
		{
			$url .= "$k=$var[$k]&";
		}
	}

	return $url;
}


function createSelect( $name, $options, $current, $form = "document.form1" )
{
	createSelect2( $name, $options, $current, 0, $form );
}


function createSelect2( $name, $options, $current, $multiple, $form = "document.form1" )
{
	$m = '';
	if ($multiple) 
	{
		$m = 'multiple';
		$current_a = explode( ',', $current );
	}

	print "<select name=\"$name\" $m onFocus=\"formChange2( $form );\">\n";
	$i = 0;
	while ($i < count( $options ))
	{
		$selected = '';

		if ($multiple) 
		{
			if ($current_a) foreach ($current_a as $c)
			{
				if ($options[$i] == $c)
				{
					$selected = 'selected';
					break;
				}
			}
		}
		else
		{
			if ($options[$i] == $current)
				$selected = 'selected';
		}

		print "<option $selected>$options[$i]</option>\n";

		$i++;
	}
	print "</select>\n";
}


function addSelectOption( $value, $text, $currentValue )
{
	if ($value == $currentValue)
	{
		print "<option value=\"$value\" selected>$text</option>\n";
	}
	else
	{
		print "<option value=\"$value\">$text</option>\n";
	}
}


function createRadio( $name, $options, $current, $atts )
{
	$i = 0;
	while ($i < count( $options ))
	{
		$checked = '';
		if ($options[$i] == $current) $checked = 'checked';

		print "<label><input type=radio $checked value=$i name=\"$name\" $atts> $options[$i]</label>\n";

		$i++;
	}
}


function mailto( $emailAddress )
{
	return "<a href=\"mailto:$emailAddress\">$emailAddress</a>";
}



///////////////////////////////////////////////////////
// email functions
///////////////////////////////////////////////////////

/*
function smtpMail( $smtpServer, $username, $password, $to, $subject, $message, $headers = '' )
{
	function smtpCmd( $socket, $cmd, $result )
	{
		$crlfCmd = '';
		foreach (preg_split( "/\n/", $cmd ) as $line)
		{
			$crlfCmd .= "$line\r\n";
		}

		fputs( $socket, "$crlfCmd" );
		$res = fgets( $socket, 256 );
		$rc = substr( $res, 0, 3 );
		if ($rc != "$result")
		{
			print "smtpCmd $crlfCmd failed (result: $rc)!\n";
			return;
		}
	}

	if (!$to)
	{
		print "smtpMail requires 'to' address!\n";
		return;
	}

	$hostname = chop( `hostname` );
	$from = "$_SERVER[USER]@$hostname";

	$socket = fsockopen( $smtpServer, 25, $errno, $errstr, 1 );
	if (!$socket) return "Failed to even make a connection";
	$res = fgets( $socket, 256 );
	if (substr( $res, 0, 3 ) != "220") return "Failed to connect";

	smtpCmd( $socket, "HELO $hostname", 250 );
	smtpCmd( $socket, "AUTH LOGIN", 334 );
	smtpCmd( $socket, base64_encode( $username ), 334 );
	smtpCmd( $socket, base64_encode( $password ), 235 );
	smtpCmd( $socket, "MAIL FROM: $from", 250 );
	smtpCmd( $socket, "RCPT TO: $to", 250 );
	smtpCmd( $socket, "DATA", 354 );

	// Send To:, From:, Subject:, other headers, blank line, message, and finish
	// with a period on its own line (for end of message)
	if ($headers) $headers .= "\n";
	smtpCmd( $socket, "To: $to\nFrom: $from\nSubject: $subject\n$headers\n$message\n.", 250 );
	smtpCmd( $socket, "QUIT", 221 );

	fclose( $socket );
}

*/

///////////////////////////////////////////////////////
// date/time functions
///////////////////////////////////////////////////////


// converts YYYY-MM-DD to timestamp
function sqlDateToTimestamp( $sqlDate )
{
	preg_match( "#(\d+)-(\d+)-(\d+)#", $sqlDate, $matches );
	$ts = gmmktime( 23, 59, 59, $matches[2], $matches[3], $matches[1] );
	if ($ts < 0)
	{
		print "Error: date out of range: $sqlDate<br>";
		return 0;
	}
	return $ts;
}


// date format: YYYY-Mon-DD
function getDaysInMonth( $date )
{
	preg_match( "#(....)-(...)-(..)#", $date, $matches );
	$month = $matches[2];
	$day = $matches[3];
	$year = $matches[1];

	$leapYear = 0;
	if (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0)
	{
		$leapYear = 1;
	}

	$days = 0; 
	if ($month == 'Feb')
	{ 
		if ($leapYear) { $days = 29; }
		else { $days = 28; }
	}
	elseif ($month == 'Apr' or $month == 'Jun' or $month == 'Sep' or $month == 'Nov')
	{ 
		$days = 30; 
	}
	else 
	{ 
		$days = 31; 
	}

	return $days;
}


function getDaysInMonth2( $year, $month )
{
	$leapYear = 0;
	if (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0)
	{
		$leapYear = 1;
	}

	$days = 0; 
	if ($month == 2)
	{ 
		if ($leapYear) { $days = 29; }
		else { $days = 28; }
	}
	elseif ($month == 4 or $month == 6 or $month == 9 or $month == 11)
	{ 
		$days = 30; 
	}
	else 
	{ 
		$days = 31; 
	}

	return $days;
}


// "MM/DD/YYYY" -> "YYYY-MM-DD"
function altDateToSQLDate( $altDate )
{
	if (preg_match( "/(\d+)\/(\d+)\/(\d+)/", $altDate, $matches ))
		return "$matches[3]-$matches[1]-$matches[2]";
}


// "YYYY-MM-DD" -> "MM/DD/YYYY"
function sqlDateToAltDate( $sqlDate )
{
	if (preg_match( "/(\d+)\-(\d+)\-(\d+)/", $sqlDate, $matches ))
		return "$matches[2]/$matches[3]/$matches[1]";
	
	return '';
}


// "YYYY-MM-DD" -> "Sunday, Oct 26 2003"
function sqlDateToString( $sqlDate )
{
	if (preg_match( "/\d\d\d\d-\d\d?-\d\d?/", $sqlDate ) and $sqlDate != '0000-00-00')
	{
		$ts = strtotime( $sqlDate );
		return strftime( "%A, %b %d %Y", $ts );
	}

	return "Unknown";
}


// "YYYY-MM-DD" -> "Sunday, Oct 26 2003"
function tsToSQLDate( $ts )
{
	return date( "Y-m-d", $ts );
}


// converts "15:45:00" to "3:45 pm" and "24:00:00" to "Midnight"
function sqlTimeToDisplayTime( $sqlTime )
{
	if ($sqlTime == '00:00:00') $displayTime = 'Midnight';
	elseif ($sqlTime == '24:00:00') $displayTime = 'Midnight';
	elseif ($sqlTime == '12:00:00') $displayTime = 'Noon';
	else
	{
		preg_match( "/(\d\d):(\d\d):(\d\d)/", $sqlTime, $matches );
		$hour = $matches[1];
		$minute = $matches[2];
		$second = $matches[3];
		$ampm = 'am';

		$hour = sprintf( "%d", $hour );
		if ($hour == 12)
		{
			$ampm = 'pm';
		}
		elseif ($hour > 12)
		{
			$hour = $hour - 12;
			$ampm = 'pm';
		}
		elseif ($hour == 0)
		{
			$hour = 12;
		}

		$displayTime = "$hour:$minute $ampm";
	}

	return $displayTime;
}



///////////////////////////////////////////////////////
// other functions
///////////////////////////////////////////////////////

// data should be a query string like "var1=a&var2=b"
// use urlencode() on the values
function postForm( $host, $path, $data, $port = 80 )
{
	$fp = fsockopen( $host, $port );
	$buf = '';

	if ($fp)
	{
		fputs( $fp, "POST $path HTTP/1.1\n" );
		fputs( $fp, "Host: $host\n" );
		fputs( $fp, "Content-type: application/x-www-form-urlencoded\n" );
		fputs( $fp, "Content-length: " . strlen( $data ) . "\n" );
		fputs( $fp, "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3\n" );
		fputs( $fp, "Connection: close\n" );
		fputs( $fp, "\n" );
		fputs( $fp, $data );
		fputs( $fp, "\n" );

		while (!feof( $fp )) $buf .= fgets( $fp,128 );

		fclose( $fp );
	}
	else
	{
		$buf = 'Error';
	}

	return $buf;
}


function isSecure()
{
	if (isset( $_SERVER['HTTPS'] ) and $_SERVER['HTTPS']) return true;
	return false;
}


function urlLink( $url, $atts = '' )
{
	return "<a href=\"$url\" $atts>$url</a>";
}


function mailLink( $email )
{
	return "<a href=\"mailto: $email\">$email</a>";
}


function buttonGo( $text, $url, $atts = '' )
{
	return "<button class='enabled' onclick=\"location='$url'; return false;\" $atts>$text</button>";
}


function buttonPopup( $text, $url, $width = 500, $height = 500 )
{
	return "<button onclick=\"window.open( '$url', '', 'scrollbars=yes,resizable=yes,width=$width,height=$height' ); return false;\">$text</button>";
}


function hrefPopup( $text, $url, $width = 500, $height = 500, $style = '' )
{
	return "<a href=\"\" onclick=\"window.open( '$url', '', 'scrollbars=yes,resizable=yes,width=$width,height=$height' ); return false;\" style=\"$style\">$text</a>";
}


function makeOption( $value, $current )
{
	$selected = '';
	if ($value == $current) $selected = 'selected';

	$value = esc( $value );
	return "<option $selected value=\"$value\">$value</option>";
}


function option2( $value, $current, $text, $atts = '' )
{
	$selected = '';
	if ($value == $current) $selected = 'selected';

	$value = esc( $value );
	$text = esc( $text );
	return "<option $selected value=\"$value\" $atts>$text</option>";
}


function radio( $name, $value, $current, $atts = '', $label = '' )
{
	$checked = '';
	if ($value == $current) $checked = 'checked';

	$value = esc( $value );
	return
		label( 
			input( '', "type=radio $checked name='$name' value='$value' $atts" )
			. NL
			. $label
			, "class='control-label'"
		)
	;
}


// options = array( value, value, value )
function makeSelect( $name, $options = array(), $current = '', $atts = '' )
{
	$text = "<select name=\"$name\" id=\"$name\" $atts>\n";

	if ($options) foreach ($options as $o)
	{
		$text .= makeOption( $o, $current );
	}

	$text .= "</select>\n";

	return $text;
}


// options = array( array( value, text, atts ), array( value, text, atts ), array( value, text, atts ) )
function select2( $name, $options = array(), $current = '', $atts = '' )
{
	$text = "<select name=\"$name\" id=\"$name\" $atts>\n";

	if ($options) foreach ($options as $o)
	{
		$atts = '';
		if (isset( $o[2] )) $atts = $o[2];

		$text .= option2( $o[0], $current, $o[1], $atts );
	}

	$text .= "</select>\n";

	return $text;
}


// options = array( array( value, text, atts ), array( value, text, atts ), array( value, text, atts ) )
function selectWithOther2( $name, $options, $current, $atts = '', $otherAtts = '' )
{
	$otherValue = '';
	$foundInList = false;
	if ($options) foreach ($options as $option)
	{
		if ($current == $option[0]) 
		{
			$foundInList = true;
		}
	}
	if (!$foundInList)
	{
		$otherValue = $current; 
		$current = 'Other:'; 
	}

	$options[] = array( 'Other:', 'Other:' );
	$otherName = "{$name}_other";

	$js = <<<EOF

	$('#$name').change( function( event )
		{
			$('#$otherName').prop( 'disabled', true );
			if ($(this).val() == 'Other:') $('#$otherName').prop( 'disabled', false );
		}
	);

	$('#$name').change();

EOF;

	addToDocumentReady( $js );

	return
		span(
			select2( $name, $options, $current, $atts ) .
			input( 
				'', 
				"name='$otherName' id='$otherName' value='$otherValue' disabled $otherAtts" 
			)
		)
	;
}


// options = array( value, value, value )
function selectWithOther( $name, $options, $value, $atts = '' )
{
	$text = '';

	$otherValue = '';
	if ($value and !in_array( $value, $options )) { $otherValue = $value; $value = 'Other:'; }

	$text .= "<select name='$name' id='$name' $atts>";
	if ($options) foreach ($options as $o)
		$text .= makeOption( $o, $value );
	$text .= makeOption( 'Other:', $value );
	$text .= "</select>";
	$otherName = "{$name}_other";
	$text .= "<input name='$otherName' id='$otherName' value='$otherValue' disabled>";

	$js = <<<EOF

	$('#$name').change( function( event )
		{
			$('#$otherName').prop( 'disabled', true );
			if ($(this).val() == 'Other:') $('#$otherName').prop( 'disabled', false );
		}
	);

	$('#$name').change();

EOF;

	addToDocumentReady( $js );

	return $text;
}


function makeSelectWithOther( $options, $name, $otherName, $selectValue, $otherValue, $extraOnChange = '' )
{
	$text = '';

	$text .= "<select $extraOnChange\"  name=\"$name\" id=\"$name\">";
	if ($options) foreach ($options as $o)
		$text .= makeOption( $o, $selectValue );
	$text .= makeOption( 'Other:', $selectValue );
	$text .= "</select>";
	$text .= "<input name=\"$otherName\" id=\"$otherName\" value=\"$otherValue\" size=30 disabled>";

	$js = <<<EOF

	$('#$name').change( function( event )
		{
			$('#$otherName').prop( 'disabled', true );
			if ($(this).val() == 'Other:') $('#$otherName').prop( 'disabled', false );
		}
	);

	$('#$name').change();

EOF;

	addToDocumentReady( $js );

	return $text;
}


function checkbox( $name, $isChecked = 0, $label = '', $atts = '' )
{
	return
		label( '',
			input(
				"type=checkbox name='$name' id='$name' $atts " . ($isChecked ? 'checked' : '')
			)
			. NL
			. $label,
			"for='$name' class='control-label'"
		)
	;
}


function makeSQLDate( $anydate )
{
	return date( 'Y-m-d', strtotime( $anydate ) );
}


function startPlaintextOutput( $attachmentName = '', $contentType = 'text/plain' )
{
	if (headers_sent()) {
		return;
	}

	header( 'Pragma: public' );
	header( 'Expires: 0' );
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Cache-Control: private', false );

	header( "Content-type: $contentType" );

	if ($attachmentName) {
		header( "Content-Disposition: attachment; filename=\"$attachmentName\"" );
	}
}


function getMIMEType( $file )
{
	if (($finfo = finfo_open( FILEINFO_MIME_TYPE )) === false) return false;
	if (($fileInfo = finfo_file( $finfo, $file )) === false) return false;

	return $fileInfo;
}


function outputFile( $file, $asAttachment = true )
{
	if (headers_sent()) {
		return false;
	}

	if (!file_exists( $file )) {
		return false;
	}

	if ($asAttachment) {
		//startPlaintextOutput( basename( $file ), 'text/plain' );
		startPlaintextOutput( basename( $file ), getMIMEType( $file ) );
	} else {
		startPlaintextOutput( '', getMIMEType( $file ) );
	}

	header( 'Content-Length: ' . filesize( $file ) ); 

	readfile( $file );

	return true;
}


function isIE()
{
	if (isset( $_SERVER['HTTP_USER_AGENT'] ))
		if (preg_match( "/MSIE/", $_SERVER['HTTP_USER_AGENT'] )) { return 1; }

	return 0;
}


function isIE7()
{
	if (isset( $_SERVER['HTTP_USER_AGENT'] ))
		if (preg_match( "/MSIE (\d)/", $_SERVER['HTTP_USER_AGENT'], $m ) and $m[1] < 8) { return 1; }

	return 0;
}


function isFirefox()
{
	if (isset( $_SERVER['HTTP_USER_AGENT'] ))
		if (preg_match( "/Firefox/", $_SERVER['HTTP_USER_AGENT'] )) { return 1; }

	return 0;
}


function isSafari()
{
	if (isset( $_SERVER['HTTP_USER_AGENT'] ))
		if (preg_match( "/Safari/", $_SERVER['HTTP_USER_AGENT'] )) { return 1; }

	return 0;
}


function registerVars( $varNames )
{
	foreach ($varNames as $varName)
	{
		$GLOBALS[$varName] = '';

		if (isset( $_REQUEST[$varName] ))
		{
			$GLOBALS[$varName] = $_REQUEST[$varName];

			if (is_array( $_REQUEST[$varName] ))
				foreach ($GLOBALS[$varName] as &$e) $e = addslashes( $e );
			else
				$GLOBALS[$varName] = addslashes( $GLOBALS[$varName] );
		}
		elseif (isset( $_FILES[$varName] ))
		{
			$GLOBALS[$varName] = $_FILES[$varName];
			$GLOBALS["$paramName_name"] = $_FILES[$varName]['name'];
			$GLOBALS["$paramName_type"] = $_FILES[$varName]['type'];
			$GLOBALS["$paramName_tmp_name"] = $_FILES[$varName]['tmp_name'];
			$GLOBALS["$paramName_error"] = $_FILES[$varName]['error'];
		}
	}
}


///////////////////////////////////////////////////////
// CGI/HTML functions
///////////////////////////////////////////////////////

function redirect2( $location )
{
	if ($location)
	{
		if (!headers_sent()) header( "Location: $location" );

		redirect( $location );
	}

	exit;
}



function makeSortableTable( $tableID, $headers, $dataRows, $sortColumn )
{
	print <<<EOF

<table id="$tableID" border="1" cellpadding=3>

	<tr>

EOF;

	$i = 0;
	foreach ($headers as $h)
	{
		print "<th><a href=\"javascript:sortTable( $i )\" " .
			"onmouseover=\"window.status = 'Sort Table by $h'; return true;\" " .
			"onmouseout=\"window.status = ''; return true;\" " .
			">$h</a>\n";
		$i++;
	}

	print <<<EOF

	</tr>

</table>

<script type="text/javascript">

	function loadTable()
	{
		table = document.getElementById( '$tableID' )

		for (i=table.rows.length - 1; i>0; i--)
		{
			table.deleteRow( i );
		}

		for (i=0; i<dataRows.length; i++)
		{
			newRow = table.insertRow( table.rows.length );

			for (j=0; j<dataRows[i].length; j++)
			{
				newCell = newRow.insertCell( newRow.cells.length );
				newCell.innerHTML = dataRows[i][j];
			}
		}
	}

	var cmpReturn = 1;

	function cmpData(a, b)
	{
		if (a[compareIndex] < b[compareIndex])
			return -cmpReturn
		if (a[compareIndex] > b[compareIndex])
			return cmpReturn
		return 0
	}

	function sortTable( index )
	{
		if (index == compareIndex)
			cmpReturn = -cmpReturn;
		else
			cmpReturn = 1;

		compareIndex = index
		dataRows.sort( cmpData );

EOF;

		$i = 0;
		reset( $headers );
		foreach ($headers as $h)
		{
			print "
			//if (index == $i)
				//setTableCellText( '$tableID', $i, 0, '<th>$h' )
			//else
				//setTableCellText( '$tableID', $i, 0, '<th><a href=\"javascript:sortTable( $i )\">$h</a>' )
			";

			$i++;
		}

print <<<EOF

		loadTable();
	}


	dataRows = new Array();

EOF;

	foreach ($dataRows as $r)
	{
		print "dataRows.push( new Array( ";

		$text = '';
		foreach ($r as $d)
		{
			$text .= "'$d', ";
		}
		$text = preg_replace( "/, $/", " ", $text );
		print $text;

		print " ) );\n";
	}

print <<<EOF

	var compareIndex = -1;

	sortTable( $sortColumn );

</script>

EOF;
}


function imgSize( $src )
{
	$is = getimagesize( $src );
	return "width=$is[0] height=$is[1] ";
}


///////////////////////////////////////////////////////
// date/time functions
///////////////////////////////////////////////////////


function tsToAltDate( $ts )
{
	return date( "n/j/Y", $ts );
}


///////////////////////////
// WEBRPC
///////////////////////////

// call early in the BODY
function webrpc_init()
{
	?>
	<iframe id=webrpcIFrame style="width: 0px; height: 0px; border: 0px solid black; ">
	</iframe>
	<?php

	$js = <<<EOF
	function webrpc_call( url )
	{
		document.getElementById( 'webrpcIFrame' ).src = url;
	}

EOF;

	addToJS( $js );
}


function webrpcFieldParameter( $fieldId )
{
	return "' + encodeURIComponent( $('#$fieldId').val() ) + '";
}


function webrpcCheckboxParameter( $fieldId )
{
	return "' + ($('#$fieldId').prop( 'checked' ) ? 1 : 0)+ '";
}


function webrpcRadioButtonParameter( $name )
{
	return "' + $('input[name=$name]:checked').val() + '";
}


function webrpcHref( $function, $parameters = array() )
{
	$href = "?p_page=webrpc&p_f=$function&";

	if ($parameters) for ($i=0; $i<count( $parameters ); $i++)
		$href .= 'p_p[' . ($i + 1) . ']=' . $parameters[$i] . '&';

	return $href;
}


/*
function webrpcLink( $function, $parameters, $text, $waitMessage = 'Wait...', $atts = '' )
{
	$href = webrpcHref( $function, $parameters );

	return "<a $atts href=\"javascript: this.innerHTML = '$waitMessage'; webrpc_call( '$href' ); return false; \">$text</a>";
}


function webrpcButton( $function, $parameters, $text, $waitMessage = 'Wait...', $atts = '' )
{
	$href = webrpcHref( $function, $parameters );

	return "<button $atts onClick=\"this.innerHTML = '$waitMessage'; webrpc_call( '$href' ); return false; \">$text</button>";
}

*/

function webrpc_html2js( $html )
{
	$html = addslashes( $html );
	$html = preg_replace( "/\r/", "", $html );
	$html = preg_replace( "/\n/", "", $html );

	return $html;
}



function objText( $o )
{
	ob_start();
	print_r( $o );
	return ob_get_clean();
}


function printObj( $o )
{
	if (cmdLine()) {
		print objText( $o );
		return;
	}

	//if (DEBUG) {
		print
			pre(
				'style="white-space: pre-wrap; "'
				, objText( $o )
			)
		;
	//}
}


function po( $o )
{
	printObj( $o );
}


function makeStripedTable( $rows )
{
	print '<table style="border: none; border-collapse: collapse; ">';

	foreach ($rows as $i => $tr)
	{
		print '<tr>';

		$i % 2 == 0 ? $style = "background-color: #F4F4F5; " : $style = "background-color: #E9EAF1; ";

		foreach ($tr as $td)
		{
			print "<td valign=top style=\"padding: 6px; $style\">$td";
		}

		print '</tr>';
	}

	print '</table>';
}


function tabbedSheets( $tabs )
{
	$text = '';

	foreach ($tabs as $tab)
	{
		$text .= "<a href=\"\" style=\"\">$tab[0]</a> &middot; \n";
	}

	$text = preg_replace( "/ &middot; $/", '', $text );

	$text .= '
	<table style="border: 1px solid black; width: 100%; height: 300px; ">
		<tr>
			<td>
	</table>
	';

	return $text;
}

function refererValue()
{
	if (isset( $_SERVER['REQUEST_URI'] ))
		return base64_encode( $_SERVER['REQUEST_URI'] );
}


function refererHref()
{
	$referer = sessionVar( 'referer' );
	if (!$referer) $referer = postVar( 'referer' );
	if (!$referer) $referer = getVar( 'referer' );
	return base64_decode( $referer );
}


function refererInput()
{
	return '<input type=hidden name="p_referer" value="' . refererValue() . '">';
}


function refererCGIString()
{
	return 'p_referer=' . refererValue();
}


function setSessionVarLink( $varName, $value, $text )
{
	return "<a class='btn btn-info' href=\"" . ufLink( 'setSessionVar', array( $varName, $value ) ) . "\">$text</a>";
}


/*
function saveCheckboxValue( $name )
{
	if (isset( $GLOBALS["p_$name"] )) $_SESSION[$name] = 1;
	else $_SESSION[$name] = 0;
}


function saveCheckboxArrayValue( $name, $length )
{
	for ($i=0; $i<$length; $i++)
	{
		if (isset( $GLOBALS["p_${name}[$i]"] )) $_SESSION[${name}[$i]] = 1;
		else $_SESSION["${name}[$i]"] = 0;
	}
}


function requestVarNoPrefix( $name )
{
	if (isset( $_REQUEST[$name] )) return $_REQUEST[$name];

	return '';
}


function requestVar( $name )
{
	return requestVarNoPrefix( "p_$name" );
}


function getVarNoPrefix( $name )
{
	if (isset( $_GET[$name] )) return $_GET[$name];

	return '';
}


function getVar( $name )
{
	return getVarNoPrefix( "p_$name" );
}


function postVarNoPrefix( $name )
{
	if (isset( $_POST[$name] )) return $_POST[$name];

	return '';
}


function postVar( $name )
{
	return postVarNoPrefix( "p_$name" );
}


function postArr( $name, $index )
{
	$name = "p_$name";

	if (isset( $_POST[$name] ) and isset( $_POST[$name][$index] ))
	{
		return $_POST[$name][$index];
	}

	return '';
}


// do not use with arrays
function sessionVar( $name )
{
	if (isset( $_SESSION[$name] ))
	{
		return $_SESSION[$name];
	}

	return '';
}


function sessionArr( $name, $index )
{
	if (isset( $_SESSION[$name] ) and isset( $_SESSION[$name][$index] ))
	{
		return $_SESSION[$name][$index];
	}

	return '';
}


// using with arrays causes error
function setSessionVar( $name, $value )
{
	$_SESSION[$name] = $value;
}


function setSessionArr( $name, $index, $value )
{
	if (!isset( $_SESSION[$name] ))
		$_SESSION[$name] = array();

	$_SESSION[$name][$index] = $value;
}


// do not use with arrays
function sessionValue( $name )
{
	return 'value="' . esc( sessionVar( $name ) ) . '"';
}


function unsetSessionVar( $name )
{
	unset( $_SESSION[$name] );
}
*/


// use to escape output
function esc( $str )
{
	return htmlentities( $str, ENT_QUOTES, 'UTF-8' );
}


/*
function img( $src, $atts = '' )
{
	return awsImg( $src, $atts );

	if (file_exists( $src )) {
		$wh = '';

		if (!preg_match( "/alt=/", $atts )) {
			$atts .= ' alt="" ';
		}

		return "<img src=\"$src\" $wh $atts>";
	}
}
*/


function sortByIndex( &$array, $index )
{
	usort( $array, function ( $a, $b ) use ($index) {
		$val_a = $a[$index];
		$val_b = $b[$index];
		if ($val_a == $val_b) return 0;
		return ($val_a < $val_b) ? -1 : 1;
	});
}


function sortByIndexes( &$array, $indexes )
{
	usort( $array, function ( $a, $b ) use ($indexes) {
		$i = 0;
		do {
			$val_a = $a[$indexes[$i]];
			$val_b = $b[$indexes[$i]];
			$i++;
		} while ($val_a == $val_b and $i < count( $indexes ));

		if ($val_a == $val_b) return 0;
		return ($val_a < $val_b) ? -1 : 1;
	});
}


function sqlTable( $sql, $params = array() )
{
	$rows = selectRows( $sql, $params );
	$headers = '';
	foreach (array_keys( $rows[0] ) as $k) $headers .= th( $k );
	$rowText = '';
	foreach ($rows as $row) $rowText .= sqlTableRow( $row );

	print HTML::table2( "class='table table-bordered'",
		tr( $headers ) .
		$rowText
	);
}


function sqlTableRow( $row )
{
	$cells = '';
	foreach ($row as $f) $cells .= td( $f );
	return tr( $cells );
}


// call with local or S3 images
function imageElement( $element, $img, $atts = '', $otherStyles = '' )
{
	//if (file_exists( $img ))
	{
		$is = getimagesize( $img );
		return "<$element $atts style=\"padding: 0; width: $is[0]px; height: $is[1]px; background-image: url( '$img' ); background-repeat: no-repeat; background-position: center; background-color: transparent; $otherStyles\">\n";
	}
}


function imageElementLink( $element, $img, $href, $text, $atts = '', $elementStyles = '', $textStyles = '' )
{
	$html = imageElement( $element, $img, $atts, $elementStyles );

	$is = getimagesize( $img );

	$html .= "<a href=\"$href\" style=\"margin: auto; border: 0px solid fuchsia; text-align: left; display: block; width: $is[0]px; height: $is[1]px; \"><p style=\"border: 0px solid red; $textStyles\">$text</p></a>";

	return $html;
}


function xtag()
{
	$args = func_get_args();
	$tag = $args[0];
	if (preg_match( "/^([\w:]+)/", $tag, $m ))
		$closing_tag = $m[1];

	$args2 = array_slice( $args, 1 );

	$rc = "\n<$tag>";
	if (isset( $args2 ) and $args2) foreach ($args2 as $arg) $rc .= $arg;
	$rc .= "</$closing_tag>\n";
	return $rc;
}


function remNL( &$s )
{ 
	$s = strtr( $s, "\n", " " ); 
}


function csvRow( $fields = array() )
{
	assert( 'is_array( $fields )' );

	if (!$fields) return;

	array_walk( $fields, 'WebSight\\remNL' );

	$fp = fopen( 'php://output', 'a' );
	fputcsv( $fp, $fields );
}


// cells: array
function tableRowArr( $cells = array() )
{
	assert( 'is_array( $cells )' );

	return
		tr(
			array_reduce( $cells, function ($c,$i) {
				return $c . td( nl2br( $i ) );
			})
		)
	;
}


// cells: comma separated parameters
function tableRow()
{
	return tableRowArr( func_get_args() );
}


/*
function saveInputAsSessionVars( $varNames )
{
	foreach ($varNames as $varName)
	{
		if (!isset( $_SESSION[$varName] )) 
		{
			$_SESSION[$varName] = '';
		}

		$paramName = "p_$varName";

		if (isset( $_REQUEST[$paramName] ))
		{
			$_SESSION[$varName] = $_REQUEST[$paramName];
		}
		else
		{
			// reset these session variables if no input parameter

			if ($varName == 'page')
			{
				$_SESSION[$varName] = '';
			}
		}
	}
}


function savePostInputAsSessionVars( $varNames )
{
	foreach ($varNames as $varName)
	{
		if (!isset( $_SESSION[$varName] )) 
		{
			$_SESSION[$varName] = '';
		}

		$paramName = "p_$varName";

		if (isset( $_POST[$paramName] ))
		{
			$_SESSION[$varName] = $_POST[$paramName];
		}
	}
}


function saveGETInputAsGlobalVars( $varNames )
{
	foreach ($varNames as $varName)
	{
		$paramName = "p_$varName";

		if (isset( $_GET[$paramName] ))
			$GLOBALS[$paramName] = $_GET[$paramName];
		else
			$GLOBALS[$paramName] = '';
	}
}


function saveCheckboxInputAsSessionVars( $varNames )
{
	foreach ($varNames as $varName)
	{
		if (isset( $_POST["p_$varName"] )) $_SESSION[$varName] = 1;
		else $_SESSION[$varName] = 0;
	}
}
*/


function getArrayValue( $arr, $key ) 
{
	return $arr[$key];
}


// use id=XXX in element
function focusElement( $id )
{
	$id = preg_replace( "/\[/", '\\\\\[', $id );
	$id = preg_replace( "/\]/", '\\\\\]', $id );

	$js = <<<EOF

	$( '#$id' ).focus();
	$( '#$id' ).select();

EOF;

	addToDocumentReady( $js );
}


function getEmailAddressRegex()
{
	return "([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})";
}

function isValidEmailAddress( $address )
{
	return (filter_var( $address, FILTER_VALIDATE_EMAIL ) ? true : false);

/*
	// Create the syntactical validation regular expression
	$regexp = "/^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i";

	// Presume that the email is invalid
	$valid = 0;

	// Validate the syntax
	//if (eregi($regexp, $address))
	if (preg_match( $regexp, $address ))
	{
		list($username,$domaintld) = preg_split("/@/",$address);
		// Validate the domain
		//if (getmxrr($domaintld,$mxrecords))
			$valid = 1;
	} else {
		$valid = 0;
	}

	return $valid;
*/
}


function isValidZipCode( $zip )
{
	return 
		isUSAZipCode( $zip ) or
		isCanadaZipCode( $zip );
}


function isCanadaZipCode( $zip )
{
	return 
		preg_match( "/^[A-Z]\d[A-Z]\d[A-Z]\d$/", strtoupper( $zip ) );
}


function isCaliforniaZipCode( $zip )
{
	if (preg_match( "/^(\d\d\d)/", $zip, $m ))
	{
		if ($m[1] >= 900 and $m[1] <= 961) return true;
	}

	return false;
}


function isPennsylvaniaZipCode( $zip )
{
	if (
		substr( $zip, 0, 5 ) >= 15001 and 
		substr( $zip, 0, 5 ) <= 19640
	) {
		return true;
	}

	return false;
}


function isUSAZipCode( $zip )
{
	return 
		preg_match( "/^\d\d\d\d\d(-\d\d\d\d)?$/", $zip );
}


/*
function requireFields( $fields )
{
	$error = '';

	foreach ($fields as $f)
	{
		if (!isset( $_SESSION[$f] ) or strlen( $_SESSION[$f] ) == 0)
		{
			$error = "One or more required fields are missing.";
			MissingFields::addMissing( $f );
		}
	}

	if ($error)
	{
		Flash::userMessage( $error );
	}
}
*/


/*
function requirePostFields( $fields )
{
	$error = '';

	foreach ($fields as $f)
	{
		$p = "p_$f";

		if (!isset( $_POST[$p] ) or strlen( $_POST[$p] ) == 0)
		{
			$error = "One or more required fields are missing.";
			MissingFields::addMissing( $f );
		}
	}

	if ($error)
	{
		Flash::userMessage( $error );
	}
}
*/


// for db rows
function toJavascriptArray( $array, $arrayName )
{
	pnl( "var $arrayName = [];" );
	pnl();
	pnl( 'var i = 0;' );

	foreach ($array as $row)
	{
		pnl( $arrayName . "[i] = {};" );

		foreach (array_keys( $row ) as $key)
		{
			if ($key == 'history') continue;
			if ($key == 'notes') continue;
			if ($key == 'comments') continue;

			pnl( $arrayName . "[i].$key = '" . addslashes( $row[$key] ) . "';" );
		}

		pnl( 'i++;' );
		pnl();
	}

	pnl();
}
 

function FriendlyErrorType($type)
{
	switch($type)
	{
		case E_ERROR: // 1 //
			return 'E_ERROR';
		case E_WARNING: // 2 //
			return 'E_WARNING';
		case E_PARSE: // 4 //
			return 'E_PARSE';
		case E_NOTICE: // 8 //
			return 'E_NOTICE';
		case E_CORE_ERROR: // 16 //
			return 'E_CORE_ERROR';
		case E_CORE_WARNING: // 32 //
			return 'E_CORE_WARNING';
		case E_CORE_ERROR: // 64 //
			return 'E_COMPILE_ERROR';
		case E_CORE_WARNING: // 128 //
			return 'E_COMPILE_WARNING';
		case E_USER_ERROR: // 256 //
			return 'E_USER_ERROR';
		case E_USER_WARNING: // 512 //
			return 'E_USER_WARNING';
		case E_USER_NOTICE: // 1024 //
			return 'E_USER_NOTICE';
		case E_STRICT: // 2048 //
			return 'E_STRICT';
		case E_RECOVERABLE_ERROR: // 4096 //
			return 'E_RECOVERABLE_ERROR';
		case E_DEPRECATED: // 8192 //
			return 'E_DEPRECATED';
		case E_USER_DEPRECATED: // 16384 //
			return 'E_USER_DEPRECATED';
	}

	return "";
} 


function dbRows2IndexedArray( $rows, $indexColumn )
{
	$arr = array();
	foreach ($rows as $row)
	{
		$arr[$row[$indexColumn]] = $row;
	}
	return $arr;
}


function nl()
{
	$rc = "\n";
	return $rc;
}


function pbr( $text = '' )
{
	print "$text<br>\n";
}


function pnl( $text = '' )
{
	print "$text\n";
}


function flash( $src, $w, $h )
{
	$src = esc( $src );

	return "
	<object type=\"application/x-shockwave-flash\" data=\"$src\" width=\"$w\" height=\"$h\">
		<param name=\"movie\" value=\"$src\">
		<param name=\"quality\" value=\"high\">
		<param name=\"allowFullScreen\" value=\"true\">
	</object>
	";
}


function getPercentage( $num, $den )
{
	$percentage = '-';
	if ($den) {
		$percentage = intval( 100.0 * $num / $den );
	}
	return $percentage;
}


if (!function_exists('array_column')) {
    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();
        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }
        if (!is_array($params[0])) {
            trigger_error(
                'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given',
                E_USER_WARNING
            );
            return null;
        }
        if (!is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;
        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }
        $resultArray = array();
        foreach ($paramsInput as $row) {
            $key = $value = null;
            $keySet = $valueSet = false;
            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }
            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }
            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }
        }
        return $resultArray;
    }
}






