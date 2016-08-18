# WebSight
a website anti-framework

No inversion of control here!

Create a WebPage and pass it around, add stuff to it, then tell it to display itself.

HTML wrappers help you write HTML:
```
	print
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
	;
```

It guarantees that your tags are closed and nested properly. There's a function corresponding to each HTML5 element. `div()`, `h3()`, `head()`, etc. Since `var` is a PHP keyword, the function for that tag has an underscore: `var_()`.
