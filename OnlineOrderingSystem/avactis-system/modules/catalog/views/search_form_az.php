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
 * View-class to create a search form of the products in the Admin Zone.
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


    function SearchForm()
    {
        $this->container_filename = "search-form-container.tpl.html";
    }

    /**
     * Outputs the search form.
     *
     * @return string.
     */
    function output()
    {
        global $application;

        # Register Additional tags
        $application->registerAttributes(
            array('Local_SearchFormAction',
                  'Local_SearchFormName',
                  'Local_ActionName',
                  'Local_ActionValue',
                  'Local_SearchTextFieldName',
                  'Local_SearchText'
            ));

        $result = modApiFunc('TmplFiller', 'fill', "catalog/search_form/", $this->container_filename, array());

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
                $value=$_request->getURL();
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