<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

define ('XHTML_10_STRICT',			'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">');
define ('XHTML_10_TRANSITIONAL',	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
define ('XHTML_10_FRAMESET',		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">');

class Document
{
	/**
	 * Doctype
	 * @var const XHTML_10_STRICT, XHTML_10_TRANSITIONAL, XHTML_10_FRAMESET
	 */
	public $doctype;

	/**
	 * HTML element
	 * @var XHTML_Element
	 */
	public $html;

	/**
	 * Head element
	 * @var XHTML_Element
	 */
	public $head;

	/**
	 * Title element
	 * @var XHTML_Element
	 */
    public $title;

	/**
	 * Body element
	 * @var XHTML_Element
	 */
	public $body;

	/**
	 * Beautifies XHTML output for debugging.
	 * @var bool
	 */
	public $beautified = FALSE;

	/**
	 * Intializes the XHTML_Doctype class, defaulting to a doctype of
	 * XHTML_10_TRANSITIONAL, unless specified.
	 * @param const $doctype 
	 */
	public function __construct ($doctype = XHTML_10_TRANSITIONAL)
	{
		$this->set_doctype($doctype);
		$this->html = new XHTML_Element ('html');
		$this->head = new XHTML_Element ('head');
		$this->title = new XHTML_Element ('title');
		$this->body = new XHTML_Element ('body');
	}
	
	/**
	 * Sets the document type.
	 * @param const $doctype
	 */
	public function set_doctype ($doctype = XHTML_10_TRANSITIONAL)
	{
		$this->doctype = $doctype;
	}

	/**
	 * Sets the document's language.
	 * @param string $language
	 */
	public function set_language ($language = 'en')
	{
	    $this->html->set ('xml:lang', $language);
	    $this->html->set ('lang', $language);
	}

	/**
	 * Sets the document's content type and charset.
	 * @param string $content_type
	 * @param string $charset
	 */
	public function set_content_type ($content_type = 'text/html', $charset = 'utf-8')
	{
	    $meta = new XHTML_Element ('meta');
	    $meta->set ('http-equiv', 'Content-Type');
	    $meta->set ('content', $content_type .'; charset='. $charset);
	    
	    $this->head->appendContent ($meta);
	}

	/**
	 * Returns the document's contents.
	 * @return string
	 */
	public function __toString ()
	{
		$this->head->appendContent ($this->title);
		
		$this->html->setContent (
			$this->head .
			$this->body
		);
		
		if ($this->beautified === TRUE)
			return str_replace('><', ">\n<", $this->doctype . $this->html);
		
		return $this->doctype . $this->html;
	}

	/**
	 * Outputs the document's contents.
	 */
	public function render ()
	{
		echo $this->__toString ();
	}
}

?>