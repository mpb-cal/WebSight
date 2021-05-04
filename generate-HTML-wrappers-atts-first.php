<?php

/*
save output to HTML-wrappers.php

makes a function for each HTML5 tag, like this:
function tag( $atts = '', $contents = '' )
where name conflicts with built-in function, add _ to name:
function tag_( $atts = '', $contents = '' )

$contents can be an array
*/

//define( 'ELEMENT_NL', '"\\n"' );
define( 'ELEMENT_NL', '""' );

/////////////////////////////////////////////////////
// HTML5 elements from w3c

$HTML5_ELEMENTS = [
  'a',
  'abbr',
  'address',
  'area',
  'article',
  'aside',
  'audio',
  'b',
  'base',
  'bb',
  'bdo',
  'blockquote',
  'body',
  'br',
  'button',
  'canvas',
  'caption',
  'cite',
  'code',
  'col',
  'colgroup',
  'command',
  'datagrid',
  'datalist',
  'dd',
  'del',
  'details',
  'dfn',
  'dialog',
  'div',
  'dl',
  'dt',
  'em',
  'embed',
  'fieldset',
  'figure',
  'footer',
  'form',
  'h1',
  'h2',
  'h3',
  'h4',
  'h5',
  'h6',
  'head',
  'header',
  'hgroup',
  'hr',
  'html',
  'i',
  'iframe',
  'img',
  'input',
  'ins',
  'kbd',
  'label',
  'legend',
  'li',
  'link',
  'map',
  'mark',
  'menu',
  'meta',
  'meter',
  'nav',
  'noscript',
  'object',
  'ol',
  'optgroup',
  'option',
  'output',
  'p',
  'param',
  'pre',
  'progress',
  'q',
  'rp',
  'rt',
  'ruby',
  'samp',
  'script',
  'section',
  'select',
  'small',
  'source',
  'span',
  'strong',
  'style',
  'sub',
  'sup',
  'table',
  'tbody',
  'td',
  'textarea',
  'tfoot',
  'th',
  'thead',
  'time',
  'title',
  'tr',
  'ul',
  'var',
  'video',
];

// void elements (self-closing):
$VOID_ELEMENTS = [
  'area',
  'base',
  'br',
  'col',
  'embed',
  'hr',
  'img',
  'input',
  'link',
  'meta',
  'param',
  'source',
  'track',
  'wbr'
];

$RESERVED_WORDS = [
  'dl',
  'header',
  'link',
  'time',
  'var',
];


print "<?php\n\n// Do Not Edit This File. Edit " . __FILE__ . " Instead.\n\n";

////////////////////
// create import statement

$list = '';
foreach ($HTML5_ELEMENTS as $element) {
	$funcName = $element;

	if (
		function_exists($element) or
		in_array($element, $RESERVED_WORDS)
	) {
		$funcName .= '_';
	}

  $list .= "$funcName,";
}

$list = preg_replace( "/,$/", '', $list );

print "// paste the following if you want to:\n// use function WebSight\\{" . $list . "};\n\n";


print
'

namespace WebSight;

function element($name, $atts = \'\', $contents = \'\', $end_tag = true)
{
	if (is_array($contents)) {
		$e = \'\';
		foreach ($contents as $c) {
			$e .= element($name, $atts, $c, $end_tag);
		}
		return $e;
	}

	if ($atts) {
		$atts = " $atts";
	}

	if ($contents) {
		$contents = ' . ELEMENT_NL . ' . $contents . ' . ELEMENT_NL . ';
	}

  if ($end_tag) {
    return "<$name$atts>$contents</$name>" . ' . ELEMENT_NL . ';
  } else {
    return "<$name$atts />" . ' . ELEMENT_NL . ';
  }
}


';

foreach ($HTML5_ELEMENTS as $element) {
	$funcName = $element;

	if (
		function_exists( $element ) or
		in_array($element, $RESERVED_WORDS)
	) {
		$funcName .= '_';
	}

  $end_tag = 'true';
  if (in_array($element, $VOID_ELEMENTS)) {
    $end_tag = 'false';
  }

	print
"
function $funcName(\$atts = '', \$contents = '')
{
	return element( 
		'$element', 
		\$atts, 
		\$contents, 
		$end_tag
	);
}

";
}


