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
class SearchResult
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */


    function SearchResult()
    {
        loadCoreFile('cstring.php');
        $this->cstring = new CString();
    }

    /**
     * Outputs the search result.
     *
     * @return string.
     */
    function output()
    {
        global $application;

       /*
        If the search id doesn't exist in the session, then output the empty
        template.
        */
        if(modApiFunc('Session', 'is_Set', 'search_result_id'))
        {
            $this->search_id = $search_id = modApiFunc('Session', 'get', 'search_result_id');
            modAPIFunc('paginator', 'setCurrentPaginatorName', "Catalog_SearchResult_$search_id");
            $this->prodlist = modApiFunc('CatalogSearch', 'getProdsListInSearchResult', $search_id, true);

            # If the search result is empty, then output a special template created
            #for such case
            if (NULL != $this->prodlist)
            {
                $html = modApiFunc('TmplFiller', 'fill', "catalog/search_result/","search-result-container.tpl.html", array());
            }
            else
            {
                $html = modApiFunc('TmplFiller', 'fill', "catalog/search_result/","search-result-container-notmatch.tpl.html", array());
            }
        }
        else
        {
            $html = modApiFunc('TmplFiller', 'fill', "catalog/search_result/","search-result-container-empty.tpl.html", array());
        }
        return $html;
    }

    /**
     * Creates the HTML code to output the search results.
     *
     * @return unknown
     */
    function getHTMLProductsList()
    {
        global $application;

        if(!modApiFunc('Session', 'is_Set', 'search_result_id') || NULL == $this->prodlist)
        {
            return '';
        }

        $html = "";
        $col =1;
        $columns = 1;
        $this->_Product_Info = null;

        $pl = $this->prodlist;
        $this->prodlist = null;

        reset($pl);
        $counter = modApiFunc('paginator', 'getCurrentPaginatorOffset');

        while(!empty($pl))
        {
            if ($col == 1)
            {
                $html .= '<tr><td>';
                $col++;
            }
            else
            {
                $html .= '<td>';
                $col++;
            }
            if ($col > $columns)
            {
                $col = 1;
            }

            $productInfo_array = array_shift($pl);

            $this->_Product_Info = new CProductInfo($productInfo_array['product_id']);
            $this->_Product_Info->setAdditionalProductTag('Local_ProductNumber', ++$counter);

            # Redefine InfoLink tag, because CProductInfo generate this link for Customer Zone
            $request = new Request();
            $request->setView  ( 'Catalog_ProdInfo' );
            $request->setAction( 'SetCurrentProduct' );
            $request->setKey   ( 'prod_id', $this->_Product_Info->getProductTagValue('ID') );
            $this->_Product_Info->setAdditionalProductTag('ProductInfoLink', $request->getURL());

            # Register Additional tags
            $application->registerAttributes( $this->_Product_Info->getAdditionalProductTagList() );

            $html .= modApiFunc('TmplFiller', 'fill', "catalog/search_result/","search-result-item-general.tpl.html", array());

            $this->_Product_Info->_destruct();
            $this->_Product_Info = null;
            unset($productInfo_array);
        };

        return $html;

    }

    /**
     * Highlights the search words in the text $value.
     *
     * @param string $value The text, whose words need to be highlighted
     * @return string HTML code with the highlighted words
     */
    function highlightIndexWords($value)
    {
        global $application;

        $words = modApiFunc('CatalogSearch','getSearchIndexWordsInSearchResult',$this->search_id);

        $highlightMask = $application->getBlockOption($this->template, "HighLightMask");
        if ($highlightMask == null || $highlightMask == '')
        {
            return $value;
        }

        $b = '@@@@@@';
        $e = '######';

        list($highlightMask_b, $highlightMask_e) = explode('{WORD}', $highlightMask);

        $search  = array();
        foreach($words as $word)
        {
            array_push($search, "'(".$word.")'i");
        }

        return str_replace(array($b, $e), array($highlightMask_b, $highlightMask_e), preg_replace($search, $b.'\\1'.$e, $value));
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
            case 'PaginatorLine':
                $obj = &$application->getInstance('PaginatorLine');
                $value = $obj->output('Catalog_SearchResult_'.$this->search_id, "SearchResult");
                break;

            case 'Local_Items':
                $value = $this->getHTMLProductsList();
                break;

            case 'PaginatorRows':
                $obj = &$application->getInstance('PaginatorRows');
                $value = $obj->output('Catalog_SearchResult_'.$this->search_id, "SearchResult");
                break;

            case 'Local_ProductNumber':
        	    if (is_object($this->_Product_Info))
                {
                    $value = $this->_Product_Info->getProductTagValue('Local_ProductNumber');
                }
                else
                {
                    $value = '';
                }
                break;

            case 'ProductName':
        	    if (is_object($this->_Product_Info))
        	    {
        	        $value = $this->_Product_Info->getProductTagValue('Name');
                    $value = $this->highlightIndexWords($value);
        	    }
                break;

            case 'ProductShortDescription':
        	    if (is_object($this->_Product_Info))
        	    {
                    $value = $this->_Product_Info->getProductTagValue('ShortDescription');
                    $value = $this->cstring->mergeWhiteSpace($this->cstring->stripHTML($value));
                    $value = $this->highlightIndexWords($value);
        	    }
                break;

            case 'ProductDetailedDescription':
        	    if (is_object($this->_Product_Info))
        	    {
        	        $value = $this->_Product_Info->getProductTagValue('DetailedDescription');
                    $value = $this->cstring->mergeWhiteSpace($this->cstring->stripHTML($value));
                    $value = $this->highlightIndexWords($value);
        	    }
                break;

            case 'ProductInfoLink':
      	        # return redefined tag value
                $value = $this->_Product_Info->getProductTagValue('ProductInfoLink');
                break;

    	    default:
    	        list($entity, $tag) = getTagName($tag);
        	    if ($entity == 'product' && is_object($this->_Product_Info))
        	    {
        	        $value = $this->_Product_Info->getProductTagValue($tag);
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

    /**
     * Current product info.
     *
     * @var array
     */
    var $_ProductInfo;

    var $NoView;

    var $prodlist;

    var $search_id;

    var $templateFiller;
    var $template;

    var $cstring;

    /**#@-*/
}
?>