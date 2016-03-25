<?php
/***********************************************************************
| Avactis (TM) Shopping Cart software developed by HBWSL.
| http://www.avactis.com
| -----------------------------------------------------------------------
| All source codes & content (c) Copyright 2004-2010, HBWSL.
| unless specifically noted otherwise.
| =============================================
| This source code is released under the Avactis License Agreement.
| The latest version of this license can be found here:
| http://www.avactis.com/license.php
|
| By using this software, you acknowledge having read this license agreement
| and agree to be bound thereby.
|
 ***********************************************************************/
?><?php
_use(dirname(__FILE__) . '/product_search_form_az.php');
/**
 * Catalog module.
 * Showing search short form
 *
 * @author Sergey Kulitsky
 * @package Catalog
 * @access public
 */
class ProductSearchFormShort
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**
     * The viewer constructor
     */
    function ProductSearchFormShort()
    {
        // initializing the template filler
        $this -> mTmplFiller = new TmplFiller();

        $this -> Filter = array();
        if (modApiFunc('Session', 'is_set', 'SearchProductFormFilter'))
            $this -> Filter = modApiFunc('Session', 'get', 'SearchProductFormFilter');

        $this -> _template_dir = 'catalog/product_search_form_short/';
    }

    function output()
    {
        global $application;

        $template_contents = array(
            'OverFlowText'       => $this -> outputOverFlowText(),
            'PatternValue'       => prepareHTMLDisplay($this -> getPatternValue()),
            'isFormActive'       => empty($this -> Filter) ? 'N' : 'Y'
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   $this -> _template_dir,
                   'container.tpl.html',
                   array()
               );
    }

    /**
     * Outputs overfill text if any
     */
    function outputOverFlowText()
    {
        if (isset($this -> Filter['overflow'])
            && $this -> Filter['overflow'])
            return $this -> mTmplFiller -> fill(
                       $this -> _template_dir,
                       'overflow-text.tpl.html',
                       array()
                   );
        return '';
    }

    /**
     * Returns the pattern value
     */
    function getPatternValue()
    {
        return @$this -> Filter['pattern'];
    }


    var $mTmplFiller;
    var $Filter;
    var $_Template_Contents;
    var $_template_dir;
}
?>