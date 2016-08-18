# WebSight
a website anti-framework

No inversion of control here!

Create a WebPage and pass it around, add stuff to it, then display it. CSS, JS, HEAD stuff, etc., can be added at any point.

```
$webPage = new WebPage;
$webPage->setTitle( 'My Site' );
$webPage->addToHead( 'head stuff' );
$webPage->addStyleSheet( 'css/style.css' );
$webPage->addToBody( 'main body' );
print $webPage->getOutput();
```

HTML wrappers help you write HTML:
```
$webPage->addToBody( 
	div( 'class=row',
		div( 'class=column',
			p( 'attributes go here',
				'content goes here'
			)
			. ul(
				li( '', 'item 1' )
				. li( '', item 2' )
				. li( '', 'item 3' )
			)
		)
	)
);
```

It guarantees that your tags are closed and nested properly. There's a function corresponding to each HTML5 element. `div()`, `h3()`, `head()`, etc. Since `var` is a PHP keyword, the function for that tag has an underscore: `var_()`.
