<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class Form_Input_Element extends XHTML_Element
{
	public function __construct ($type_attribute, $name_attribute,
		$value_attribute = NULL)
	{
		parent::__set ('name', @$name_attribute);
		parent::__set ('id', @$name_attribute);

		switch ( $type_attribute )
		{
			case 'textarea':
			case 'select':
				parent::__construct ($type_attribute);
				if ($value_attribute !== NULL)
					parent::setContent (@$value_attribute);
				
				break;
			default:
				parent::__construct ('input');
				parent::__set ('type', $type_attribute);
				if ($value_attribute !== NULL)
					parent::__set ('value', @$value_attribute);
		}
	}
}

class Hidden extends Form_Input_Element
{
	public function __construct ($name_attribute, $value_attribute = NULL)
	{
		parent::__construct ('hidden', $name_attribute, $value_attribute);
	}
}

class Button extends Form_Input_Element
{
	public function __construct ($name_attribute, $value_attribute = NULL)
	{
		parent::__construct ('button', $name_attribute, $value_attribute);
	}
}

class Reset extends Form_Input_Element
{
	public function __construct ($name_attribute, $value_attribute = NULL)
	{
		parent::__construct ('reset', $name_attribute, $value_attribute);
	}
}

class Submit extends Form_Input_Element
{
	public function __construct ($name_attribute, $value_attribute = NULL)
	{
		parent::__construct ('submit', $name_attribute, $value_attribute);
	}
}

class Password extends Form_Input_Element
{
	public function __construct ($name_attribute, $value_attribute = NULL)
	{
		parent::__construct ('password', $name_attribute, $value_attribute);
	}
}

class Text extends Form_Input_Element
{
	public function __construct ($name_attribute, $value_attribute = NULL, $maxlength = NULL)
	{		
		parent::__construct ('text', $name_attribute, @$value_attribute);
		
		if ( is_int ($maxlength) )
			parent::__set ('maxlength', @$maxlength);
	}

	public function Enable_Chars_Remaining ()
	{
		parent::__set ('onkeyup', "if (this.value.length > " . parent::__get ('maxlength') . ") { this.value = this.value.substr(0, " . parent::__get ('maxlength') . "); } else { document.getElementById('" . parent::__get ('name')  . "_CharsRemaining').value = " . parent::__get ('maxlength') . " - this.value.length; }");
	}
}

class TextArea extends Form_Input_Element
{
	public function __construct ($name_attribute, $content = NULL, $maxlength = NULL)
	{
		parent::__construct ('textarea', $name_attribute, @$content);
		
		if ( is_int ($maxlength) )
			parent::__set ('maxlength', @$maxlength);
	}
	
	public function Enable_Chars_Remaining ()
	{
		parent::__set ('onkeyup', "if (this.value.length > " . parent::__get ('maxlength') . ") { this.value = this.value.substr(0, " . parent::__get ('maxlength') . "); } else { document.getElementById('" . parent::__get ('name')  . "_CharsRemaining').value = " . parent::__get ('maxlength') . " - this.value.length; }");
	}
}

class Chars_Remaining extends Text
{
	public function __construct ($text_object, $size_attribute = 5)
	{
		if ($text_object instanceof Text)
			parent::__construct ($text_object->__get ('name') . '_CharsRemaining',
				$text_object->maxlength - strlen($text_object->value));
		elseif ($text_object instanceof TextArea)
			parent::__construct ($text_object->__get ('name') . '_CharsRemaining',
				$text_object->maxlength - strlen($text_object->getContent()));
		
		parent::__set ('size', @$size_attribute);
		parent::__set ('disabled', 'disabled');
	}
}

class Checkbox extends Form_Input_Element
{
	public function __construct ($name_attribute, $value_attribute, $compare_value = NULL)
	{
		parent::__construct ('checkbox', $name_attribute, $value_attribute);
		
		if ($value_attribute === $compare_value)
			parent::__set ('checked', 'checked');
	}
}

class Radio extends Form_Input_Element
{
	public function __construct ($name_attribute, $value_attribute, $compare_value = NULL)
	{
		parent::__construct ('radio', $name_attribute, $value_attribute);
		
		if ($value_attribute === $compare_value)
			parent::__set ('checked', 'checked');
	}
}

class Select extends Form_Input_Element
{
	public function __construct ($name_attribute, $options, $compare_value = NULL, $selected_attributes = NULL)
	{
		$options_str = '';
		foreach ($options as $option)
		{
			if ($option->value === $compare_value)
			{
				$option->selected = 'selected';
				
				if ( is_array ($selected_attributes) )
				{
					foreach ($selected_attributes as $selected_attribute)
						$option->{$selected_attribute->attribute_name} =
							$selected_attribute->value;
				}
			}
			
			$options_str .= $option;
		}
		
		parent::__construct ('select', $name_attribute, $options_str);
	}
}

class Option extends XHTML_Element
{
	public function __construct ($value_attribute, $content)
	{
		parent::__construct ('option', $content);
		parent::__set ('value', @$value_attribute);
	}
}

class SelectedAttribute
{
	public $attribute_name;
	public $value;
	
	public function __construct ($attribute_name, $value)
	{
		$this->attribute_name = $attribute_name;
		$this->value = $value;
	}
}

class Label extends XHTML_Element
{
	public function __construct ($form_object, $content)
	{
		parent::__construct ('label', $content);
		parent::__set ('for', $form_object->__get ('name') );
	}
}

?>