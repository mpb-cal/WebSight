# WebSight
a website anti-framework

No inversion of control here!

Create a WebPage and pass it around, add stuff to it, then tell it to display itself.

HTML wrappers:
	print
		div( 'class=row',
			div( 'class=column',
				p( 'attributes go here',
					'content goes here'
				)
			)
		)
	;


