<?php

namespace WebSight;

use Symfony\Component\HttpFoundation\Request;

/*

// during initialization

$myForm = new Form(
	$session,
	$request,
	'myFormName',
	[
		'fname' => [ 'First Name', INPUT_TYPE_TEXT, true ],	// title, type, isRequired
		'lname' => [ 'Last Name', INPUT_TYPE_TEXT, true ]
	],
	'url',	// redirect after post
	'url'		// redirect if invalid
);

if ($myForm->wasSubmitted()) {
	// do stuff with $myForm->getFieldSubmittedValue( 'fname' )
}

// on form page:

	// if pre-filling:
	$myForm->setValues( array(
		'fname' => 'Bob',
		'lname' => 'Loblaw'
	) );

	print $myForm->draw();

*/

require_once 'utilities.php';
require_once 'MissingFields.php';

$i = 1;
define( 'INPUT_TYPE_TEXT', $i++ );
define( 'INPUT_TYPE_PASSWORD', $i++ );
define( 'INPUT_TYPE_EMAIL', $i++ );
define( 'INPUT_TYPE_CHECKBOX', $i++ );
define( 'INPUT_TYPE_RADIO', $i++ );
define( 'INPUT_TYPE_TEXTAREA', $i++ );
define( 'INPUT_TYPE_STATE', $i++ );
define( 'INPUT_TYPE_SHIPPING_METHOD', $i++ );
define( 'INPUT_TYPE_HIDDEN', $i++ );
define( 'INPUT_TYPE_SELECT', $i++ );
define( 'INPUT_TYPE_SELECT_NO_OTHER', $i++ );
define( 'INPUT_TYPE_STATIC', $i++ );
define( 'INPUT_TYPE_STATIC_HTML', $i++ );
define( 'INPUT_TYPE_FILE', $i++ );
define( 'INPUT_TYPE_DATE', $i++ );
define( 'INPUT_TYPE_DATE_RANGE', $i++ );

define( 'REQUIRED', span( 'class=', '*' ) );
define( 'VALUE_OTHER', 'Other:' );
define( 'OTHER_SUFFIX', '_other' );
define( 'FIELD_TITLE_WIDTH', '40%' );
// mpb! fix state input for this
define( 'SHOW_CURRENT_VALUE', false );

$COL_SIZE = 'sm';
$GRID_COLUMNS = 12;
if (SHOW_CURRENT_VALUE)
{
	$LABEL_COLS = 2;
}
else
{
	$LABEL_COLS = 3;
}
$INPUT_COLS = 7;
$CURRENT_COLS = 3;
$SUBMIT_COLS = 4;
$RESET_COLS = 1;


class Form
{
	private $m_name = '';
	private $m_fields = '';
	private $m_showSubmitButton = true;
	private $m_session = null;
	private $m_request = null;
	private $m_postRedirect = '';
	private $m_invalidRedirect = '';

	function __construct( 
		Session $session,
		Request $request,
		$name,				// used to name the HTML elements
		$fields,				// each field is 'fieldName' => array( 'Title', INPUT_TYPE, isRequired )
		$postRedirect,		// URL to redirect to after the form is submitted
		$invalidRedirect	// URL to redirect to if form is invalid or incomplete
	)
	{
		global $STATE_TERRITORY_NAME;

		$this->m_session = $session;
		$this->m_request = $request;
		$this->m_name = $name;
		$this->m_fields = array();
		$this->m_postRedirect = $postRedirect;
		$this->m_invalidRedirect = $invalidRedirect;

		////////////////////////////
		// give keys to fields array

		foreach ($fields as $name => $field)
		{
			assert( '$name !== ""' );
			assert( '!preg_match( "/\s/", $name )' );
			list( $title, $inputType, $required ) = $field;

			$this->m_fields[$name] = array(
				'title' => $title,
				'inputType' => $inputType,
				'required' => $required,
				'value' => ''
			);

			if ($inputType == INPUT_TYPE_DATE_RANGE)
			{
				$this->m_fields[$name]['value'] = array( '', '' );
			}

			if ($inputType == INPUT_TYPE_STATE)
			{
				$options = array();
				//$options[] = array( '', '' );

				foreach ($STATE_TERRITORY_NAME as $i => $state)
				{
					$options[] = array( $i, $state );
				}

				$this->setSelectOptions( $name, $options );
			}
		}

		$this->handleSubmission();
	}


	private function handleSubmission()
	{
		if (!$this->wasSubmitted()) {
			$this->loadFromSession();
			return;
		}

		// save submitted values
		foreach ($this->m_fields as $name => $field) {
			$this->m_fields[$name]['value'] = $this->getFieldSubmittedValue( $name );
		}

		// save form values to the session
		foreach ($this->m_fields as $name => $field) {
			$this->m_session->set_arr( 
				$this->getSessionKey(), 
				$name, 
				$this->getFieldSubmittedValue( $name )
			);
		}

		if (SHOW_CURRENT_VALUE)
		{
			$names = array();
			$cbNames = array();

			foreach ($this->m_fields as $name => $field)
			{
				if ($field['inputType'] == INPUT_TYPE_CHECKBOX) 
				{
					$cbNames[] = $this->getInputName( $name );
				}
				else
				{
					$names[] = $this->getInputName( $name );
				}
			}

			savePostInputAsSessionVars( $names );
			saveCheckboxInputAsSessionVars( $cbNames );
		}

		$this->checkForRequiredFields();

		redirect( $this->m_postRedirect );
	}


	private function checkForRequiredFields()
	{
		$error = '';

		foreach ($this->m_fields as $name => $field) {
			if ($field['required']) {
				$inputName = $this->getInputName( $name );
				if ($this->m_request->request->get( $inputName ) === '') {
					$error = "One or more required fields are missing.";
					MissingFields::addMissing( $inputName );
				}
			}
		}

		if ($error) {
			Flash::userMessage( $error );
			redirect( $this->m_invalidRedirect );
		}
	}


	private function getDateRangeInputDiv( $id, $controlName, $value )
	{
		global $COL_SIZE;

		$fromId = "{$id}_from";
		$toId = "{$id}_to";
		$fromName = "{$controlName}_from";
		$toName = "{$controlName}_to";
		$fromValue = $value[0];
		$toValue = $value[1];

		return
			div( "class='col-$COL_SIZE-2'",
				input( 
					'',
					"class='datepicker form-control input-sm' name='$fromName' id='$fromId' value='$fromValue'" 
				)
			) .
			div( "class='col-$COL_SIZE-1'",
				p( 'to', "class='form-control-static input-sm centerAlign'" )
			) .
			div( "class='col-$COL_SIZE-2'",
				input( 
					'',
					"class='datepicker form-control input-sm' name='$toName' id='$toId' value='$toValue'" 
				)
			) .
			div( "class='col-$COL_SIZE-2'",
				makeDateRangeSelect( $fromId, $toId, '', 'form-control input-sm' )
			);
	}


	private function drawField( $field, $name )
	{
		global $COL_SIZE, $LABEL_COLS, $INPUT_COLS, $CURRENT_COLS;

		$inputName = $this->getInputName( $name );
		$id = $inputName;
		$controlName = $inputName;

		if (SHOW_CURRENT_VALUE)
		{
			$currentValue = htmlspecialchars( $this->getFieldValue( $name ), ENT_QUOTES );
			$newValue = $this->getFieldNewValue( $name );
		}
		else
		{
			$newValue = $this->getFieldValue( $name );
		}

		if (
			$field['inputType'] != INPUT_TYPE_STATIC_HTML and
			$field['inputType'] != INPUT_TYPE_DATE_RANGE
		)
		{
			$newValue = htmlspecialchars( $newValue, ENT_QUOTES );
		}

		$label = $field['title'];
		if ($field['required'])
			$label .= ' ' . REQUIRED;

		$formGroupClass = 'form-group';
		$inputDivClasses = '';

		if (
			MissingFields::isMissing( $inputName ) or
			MissingFields::isMissing( $inputName . OTHER_SUFFIX )
		)
		{
			$formGroupClass .= ' has-error';
		}

		if ($field['inputType'] == INPUT_TYPE_EMAIL)
		{
			$input = input( "type='email' class='form-control input-sm' name='$controlName' id='$id' value='$newValue'" );
		}
		elseif ($field['inputType'] == INPUT_TYPE_DATE)
		{
			$input = input( 
				'',
				"class='datepicker form-control input-sm' name='$controlName' id='$id' value='$newValue'" 
			);
		}
		elseif ($field['inputType'] == INPUT_TYPE_DATE_RANGE)
		{
		}
		elseif ($field['inputType'] == INPUT_TYPE_CHECKBOX)
		{
			$formGroupClass .= ' checkbox';
			$inputDivClasses .= ' checkbox';

			$input = input( "type='checkbox' class='' name='$controlName' id='$id'" . ($newValue ? ' checked' : '') );

			if (SHOW_CURRENT_VALUE)
			{
				if ($currentValue) $currentValue = 'checked';
			}
		}
		elseif ($field['inputType'] == INPUT_TYPE_RADIO)
		{
			$formGroupClass .= ' radio';
			$inputDivClasses .= ' radio';

			//$input = input( "type='radio' class='' name='$controlName' id='$id'" . ($newValue ? ' checked' : '') );
			$input = radio(
				$controlName,
				$newValue,
				$newValue,
				" id='$id'"
			);

			if (SHOW_CURRENT_VALUE)
			{
				if ($currentValue) $currentValue = 'checked';
			}
		}
		elseif ($field['inputType'] == INPUT_TYPE_TEXTAREA)
		{
			$input = textarea( "class='form-control input-sm' name='$controlName' id='$id'",
				$newValue
			);
		}
		elseif ($field['inputType'] == INPUT_TYPE_SHIPPING_METHOD)
		{
			$input = shippingMethodWidget( 
				true, 
				$newValue == ShippingMethod::OTHER ? 'other' : 'company',
				$inputName . '_shipToMethodType',
				$inputName . '_shippingCompanyType',
				$inputName . '_shippingCompany',
				$inputName . '_shippingServiceLevel',
				$inputName . '_otherShipToMethod'
			);
		}
		elseif (
			$field['inputType'] == INPUT_TYPE_SELECT or 
			$field['inputType'] == INPUT_TYPE_STATE
		)
		{
			$input = selectWithOther2( 
				$controlName, 
				$this->getSelectOptions( $name ), 
				$newValue, 
				"class='form-control input-sm'",
				"class='form-control input-sm'" 
			);
		}
		elseif ($field['inputType'] == INPUT_TYPE_SELECT_NO_OTHER)
		{
			$input = select2( 
				$controlName, 
				$this->getSelectOptions( $name ), 
				$newValue, 
				"class='form-control input-sm'"
			);
		}
		elseif ($field['inputType'] == INPUT_TYPE_HIDDEN)
		{
			return input( "type='hidden' class='form-control input-sm' name='$controlName' id='$id' value='$newValue'" );
		}
		elseif (
			$field['inputType'] == INPUT_TYPE_STATIC or
			$field['inputType'] == INPUT_TYPE_STATIC_HTML
		)
		{
			$input = p( "class='form-control-static' id='$id'", $newValue );
		}
		elseif ($field['inputType'] == INPUT_TYPE_PASSWORD)
		{
			$input = input( "type='password' class='form-control input-sm' name='$controlName' id='$id'" );
		}
		elseif ($field['inputType'] == INPUT_TYPE_FILE)
		{
			$input = input( "type='file' class='' name='$controlName' id='$id'" );
		}
		else
		{
			$input = input( "type='text' class='form-control input-sm' name='$controlName' id='$id' value='$newValue'" );
		}

		if ($field['inputType'] == INPUT_TYPE_DATE_RANGE)
		{
			$inputDiv = $this->getDateRangeInputDiv( $id, $controlName, $newValue );
		}
		else
		{
			$inputDiv = div( "class='col-$COL_SIZE-$INPUT_COLS $inputDivClasses'",
				$input
			);
		}

		return
			div( "class='$formGroupClass'",
				label( "class='control-label col-$COL_SIZE-$LABEL_COLS' for='$id'",
					$label
				) .
				(SHOW_CURRENT_VALUE ?
					div( "class='col-$COL_SIZE-$CURRENT_COLS'",
						p( $currentValue, "class='form-control-static input-sm'" )
					)
				:
					''
				) .
				$inputDiv
			);
	}


	private function hasRequiredFields()
	{
		foreach ($this->m_fields as $field)
		{
			if ($field['required']) return true;
		}

		return false;
	}


	public function drawFields()
	{
		$fieldRows = '';
		foreach ($this->m_fields as $name => $field) {
			$fieldRows .= $this->drawField( $field, $name );
		}
		return $fieldRows;
	}


	public function draw()
	{
		global $COL_SIZE, $LABEL_COLS, $SUBMIT_COLS, $RESET_COLS, $CURRENT_COLS;

		JS::addToDocumentReady( '
			$("button.reset").click( function() {
				$(this).closest( "form" ).find( ":input" ).not(":button, :submit, :reset, :hidden").
					removeAttr("checked").removeAttr("selected").
						not(":checkbox, :radio, select").val("");
				return false;
			});
		' );

		return
			form( 'class="form-horizontal" action="" method=post enctype="multipart/form-data"',
				$this->drawFields() .
				div( "class='form-group submitRow'",
					($this->hasRequiredFields() ?
						label( "class='control-label col-$COL_SIZE-2 col-$COL_SIZE-offset-$LABEL_COLS'",
							REQUIRED . " Required Field")
						:
						''
					) .
					($this->m_showSubmitButton ?
						div(
							"class='col-$COL_SIZE-$SUBMIT_COLS" .
								($this->hasRequiredFields() ? '' : " col-$COL_SIZE-offset-" . ($LABEL_COLS+1)) .
								"'", 
							button(
								"type=submit class='btn btn-primary btn-block' name='" . 
									$this->getSubmitName() . "'",
								"Submit" 
							)
						)
						. div(
							"class='col-$COL_SIZE-$RESET_COLS'",
							button(
								"class='reset btn btn-info btn-block'",
								"Clear" 
							)
						)
						:
						''
					)
				)
			)
		;
	}


	public function drawText()
	{
		ob_start();

		foreach ($this->m_fields as $name => $field)
		{
			$value = $this->getFieldValue( $name );

			pnl( "$field[title]: $value" );
		}

		return ob_get_clean();
	}


	public function wasSubmitted()
	{
/*
		po( $this->m_request->request );
		po( $this->getSubmitName() );
		exit;
*/
		if ($this->m_request->request->has( $this->getSubmitName() )) return true;
		return false;
	}


	public function getInputName( $name )
	{
		return "{$this->m_name}_$name";
	}


	private function getField( $name )
	{
		if (isset( $this->m_fields[$name] ))
		{
			return $this->m_fields[$name];
		}
	}


	public function getFieldValue( $name )
	{
		$field = $this->getField( $name );
		return $field['value'];
	}


	//if (SHOW_CURRENT_VALUE)
	public function getFieldNewValue( $name )
	{
		return $this->m_session->get( $this->getInputName( $name ) );
	}


	public function getFieldSubmittedValue( $name )
	{
		$value = $this->m_request->request->get( $this->getInputName( $name ) );
		$field = $this->getField( $name );

		if ($field['inputType'] == INPUT_TYPE_CHECKBOX)
		{
			if ($value)
			{
				return 1;
			}
		}

		if ($field['inputType'] == INPUT_TYPE_STATE)
		{
			if ($value == VALUE_OTHER)
			{
				$value = $this->m_request->request->get( $this->getInputName( $name . OTHER_SUFFIX ) );
			}
		}

		if ($field['inputType'] == INPUT_TYPE_DATE_RANGE)
		{
			$value = array(
				$this->m_request->request->get( $this->getInputName( $name. '_from' ) ),
				$this->m_request->request->get( $this->getInputName( $name. '_to' ) ),
			);
		}

		return $value;
	}


	public function getSubmitName()
	{
		return "submit_{$this->m_name}";
	}


	private function getSessionKey()
	{
		return "Form_{$this->m_name}";
	}


	private function loadFromSession()
	{
		$names = [];
		foreach ($this->m_fields as $name => $field) {
			$names[] = $name;
		}

		$this->loadValuesFromSession( $names );
	}


	public function loadValuesFromSession( $fields )
	{
		foreach ($fields as $field) {
			$this->setValues( array(
				$field => $this->m_session->get_arr( $this->getSessionKey(), $field )
			) );
		}
	}


	// each option is array( value, text, atts )
	// or just value if valueOnly
	public function setSelectOptions( $name, $options = array(), $valueOnly = false )
	{
		assert( 'isset( $this->m_fields[$name] )' );

		if ($valueOnly)
		{
			foreach ($options as &$o)
			{
				$o = array( $o, $o );
			}
		}

		$this->m_fields[$name]['selectOptions'] = $options;
	}


	public function getSelectOptions( $name )
	{
		if (isset( $this->m_fields[$name]['selectOptions'] )) {
			return $this->m_fields[$name]['selectOptions'];
		}
	}


	public function setValues( $values )
	{
		foreach ($values as $name => $value)
		{
			assert( 'isset( $this->m_fields[$name] )' );
			$this->m_fields[$name]['value'] = $value;
		}
	}

	public function setShowSubmitButton( $show )
	{
		$this->m_showSubmitButton = $show;
	}


	public static function addCommentsFields( &$fields )
	{
		$fields['comments'] = array( 'Comments', INPUT_TYPE_STATIC_HTML, false );
		$fields['comment'] = array( 'Add Comment', INPUT_TYPE_TEXT, false );
		$fields['commentFile'] = array( 'Attach File To Comment', INPUT_TYPE_FILE, false );
	}
};




