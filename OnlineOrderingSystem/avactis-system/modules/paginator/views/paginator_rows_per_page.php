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
/**
 * Paginator module view.
 * Paginator Rows Per Page
 *
 * @package Paginator
 * @access  public
 */
class PaginatorDropdown
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Declares a template format for the given view.
     */
    function getTemplateFormat()
    {
    	$format = array(
    	    'layout-file'        => 'paginator-rows-config.ini'
    	   ,'files' => array(
    	        'Container'      => TEMPLATE_FILE_SIMPLE
    	       ,'Empty'          => TEMPLATE_FILE_SIMPLE
    	    )
    	   ,'options' => array(
    	    )
    	);
    	return $format;
    }

    /**
     * The view constructor.
     *
     * @ finish the functions on this page
     */
    function PaginatorDropdown()
    {
        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("PaginatorRows"))
        {
            $this->NoView = true;
        }
    }

/*    function getViewCacheKey($pag_name, $viewname, $items_name='products')
    {
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $url = $request->getURL();

        return md5(modAPIFunc('Paginator', 'getPaginatorRowsPerPage', $pag_name) . $url . $items_name);
    }//*/

    /**
     * Returns the Select box view with possible variants of rows
     * per the page.
     *
     * @return Select box with possible page values outputted on the page.
     * @ finish the functions on this page
     */
    function output($pag_name, $viewname, $items_name='products')
    {
        global $application;

        #Define whether to output the view or not
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "PaginatorRows", "Errors");
            return "";
        }

        $this->templateFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate('PaginatorDropdown');
        $this->templateFiller->setTemplate($this->template);

        $retval = '';
        $rows_per_page = modAPIFunc('Paginator', 'getPaginatorRowsPerPage', $pag_name);
        if (!$rows_per_page)
        {
            $rows_per_page = ROWS_PER_PAGE;
        }
        $currentrows = $rows_per_page;

        $rows_per_page = modApiFunc('Paginator', 'getRowsPerPage');
        $_rows_per_page = array();
        foreach ($rows_per_page as $value)
        {
            $_rows_per_page[$value] = $value;
        }

        $Row_Options = '';
        $selected_flag = false;
        foreach ($_rows_per_page as $option_value => $option_text)
        {
            if ($option_value == $currentrows)
            {
                $Row_Options.= '<option selected="selected" value="'.$option_value.'">'.$option_text.'</option>';
                $selected_flag = true;
            }
            else
            {
                $Row_Options.= '<option value="'.$option_value.'">'.$option_text.'</option>';
            }
        }
        $Row_Options.= '<option '.($selected_flag ? '' : 'selected="selected"').'value="'.modAPIFunc('Paginator', 'getCurrentPaginatorTotalRows').'">'.CZ_getMsg("PAGINATOR_VIEW_ALL").'</option>';

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $url = $request->getURL();

        $this->_Page = array('Local_FormAction' => $url
                               ,'Local_FormId' => 'Paginator'
                               ,'Local_FormActionFieldName' => 'asc_action'
                               ,'Local_FormActionFieldValue' => 'Paginator_SetRowsPerPage'
                               ,'Local_FormPaginatorFieldName' => 'pgname'
                               ,'Local_FormPaginatorFieldValue' => $pag_name
                               ,'Local_ItemsName' => $items_name
                               ,'Local_FormSelectFieldName' => 'rows'
                               ,'Local_FormSelectOptions' => $Row_Options);
        $application->registerAttributes($this->_Page);
        if(modAPIFunc('Paginator', 'getCurrentPaginatorTotalRows')>MIN_ROWS_PER_PAGE)
        {
            $retval = $this->templateFiller->fill("Container");
        }
        else
        {
            $retval = $this->templateFiller->fill("Empty");
        }
        return $retval;
    }

    function getTag($tag)
    {
        if (isset($this->_Page[$tag]))
            return $this->_Page[$tag];

        return null;
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Pointer to the module object.
     */
    var $pPaginator;

    var $_Page;

    /**
     * A reference to the TemplateFiller object.
     *
     * @var TemplateFiller
     */
    var $templateFiller;

    /**
     * A current selected template.
     *
     * @var array
     */
    var $template;

    /**#@-*/

}
?>