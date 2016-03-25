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
 * View-class to create search form of the products.
 *
 * @package Catalog
 * @access  public
 * @author Alexey Florinsky
 */
class SearchForm
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Gets a template format for the given view.
     */
    function getTemplateFormat()
    {
    	$format = array(
    	    'layout-file'        => 'search-form-config.ini'
    	   ,'files' => array(
               'Container'       => TEMPLATE_FILE_SIMPLE
    	    )
    	   ,'options' => array(
    	    )
    	);
    	return $format;
    }

    function SearchForm()
    {
        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("SearchForm"))
        {
            $this->NoView = true;
        }
    }

    /**
     * Outputs the search form.
     *
     * @return string.
     */
    function output()
    {
        global $application;

        #define whether to output the view or not
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "SearchForm", "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, "SearchForm", "Warnings");
        }

        # Register Additional tags
        $application->registerAttributes(
            array('Local_SearchFormAction',
                  'Local_SearchFormName',
                  'Local_ActionName',
                  'Local_ActionValue',
                  'Local_SearchTextFieldName',
                  'Local_SearchText'
            ));

        $templateFiller = new TemplateFiller();
        # define the template for the given view.
        $template = $application->getBlockTemplate('SearchForm');
        $templateFiller->setTemplate($template);
        $result = $templateFiller->fill("Container");

        return $result;
    }

    /**
     * Processes tags in the templates for the given view.
     *
     * @return string tag value, if tag has been processed. NULL, otherwise.
     */
    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case 'Local_SearchFormAction':
                $_request = new Request();
                $_request->setView  ( 'SearchResult' );
                $value=$_request->getURL($application->protocol);
                break;

            case 'Local_SearchFormName':
                $value='CatalogSearchForm';
                break;

            case 'Local_ActionName':
                $value='asc_action';
                break;

            case 'Local_ActionValue':
                $value='SearchProducts';
                break;

            case 'Local_SearchTextFieldName':
                $value='search_pattern';
                break;

            case 'Local_SearchText':
                if(modApiFunc('Session', 'is_Set', 'search_result_id'))
                {
                    $search_id = modApiFunc('Session', 'get', 'search_result_id');
                    $value=modApiFunc('CatalogSearch','getSearchPatternInSearchResult',$search_id);
                    $value=prepareHTMLDisplay($value);
                }
                else
                {
                    $value = '';
                }
                break;


        }
        return $value;
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**#@-*/
}
?>