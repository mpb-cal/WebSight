# WebSight
A website anti-framework

Create a WebPage and pass it around, add stuff to it, then display it. CSS, JS, HEAD stuff, etc., can be added at any point.

To install:

`composer require "mpb-cal/web-sight >=1"`

To use:

```
namespace WebSight;

require_once __DIR__ . '/vendor/mpb-cal/web-sight/WebPage.php';`

$webPage = new WebPage;
$webPage->setTitle( 'My Site' );
$webPage->addToHead( 'head stuff' );
$webPage->addStyleSheet( 'css/style.css' );
$webPage->addToBody( 'main body' );
print $webPage->getOutput();
```

HTML wrappers help you write HTML. It guarantees that your tags are closed and nested properly, without having to write the tag name twice. There's a function corresponding to each HTML5 element, e.g. `div()`, `h3()`, `head()`, etc. Since some tag names (e.g. `var`) are PHP keywords, the functions for those tags have underscores, like this: `var_()`. This applies to `dl_, header_, link_, and time_`.

```
$webPage->addToBody( 
	div( 'class=row id=mainRow',
		div( 'class=column',
			p( 'attributes go here',
				'content goes here'
			)
			. ul(
				li( '', 'item 1' )   // don't forget the '' even if there are no properties!
				. li( '', item 2' )
				. li( '', 'item 3' )
			)
			. a( 'href="http://www.example.com"', 'Click Here' )
		)
	)
);
```

renders as:

```
<div class=row id=mainRow>
	<div class=column>
		<p attributes>
			content
		</p>
		<ul>
			<li>item 1</li>
			<li>item 2</li>
			<li>item 3</li>
		</ul>
		<a href="http://www.example.com">Click Here</a>
	</div>
</div>
	
