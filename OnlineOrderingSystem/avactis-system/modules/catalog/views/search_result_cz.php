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
 * View-class to output a search result.
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

    /**
     * Gets a template format for the given view.
     */
    function getTemplateFormat()
    {
    	$format = array(
    	    'layout-file'        => 'search-result-config.ini'
    	   ,'files' => array(
    	        'Container'      => TEMPLATE_FILE_SIMPLE
    	       ,'ContainerEmpty' => TEMPLATE_FILE_SIMPLE
               ,'ContainerNotMatch'=> TEMPLATE_FILE_SIMPLE
    	       ,'Item'           => TEMPLATE_FILE_PRODUCT_TYPE
               ,'Separator'       => TEMPLATE_FILE_SIMPLE
    	    )
    	   ,'options' => array(
                'HighLightMask'  => TEMPLATE_OPTION_OPTIONAL
    	       ,'Columns'        => TEMPLATE_OPTION_OPTIONAL
    	    )
    	);
    	return $format;
    }

    function SearchResult()
    {
        global $application;
        loadCoreFile('cstring.php');
        $this->cstring = new CString();

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("SearchResult"))
        {
            $this->NoView = true;
        }
    }

    /**
     * Outputs the search result.
     *
     * @return string.
     */
    function output()
    {
        global $application;

        #define whether to output the view or not
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "SearchResult", "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, "SearchResult", "Warnings");
        }

        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('SearchResult');
        $this->templateFiller->setTemplate($this->template);

       /*
        If the search id doesn't exist in the session, then putput the empty
        template.
        */
        if(modApiFunc('Session', 'is_Set', 'search_result_id'))
        {
            $this->search_id = $search_id = modApiFunc('Session', 'get', 'search_result_id');
            modAPIFunc('paginator', 'setCurrentPaginatorName', "Catalog_SearchResult_$search_id");
            $this->prodlist = modApiFunc('CatalogSearch', 'getProdsListInSearchResult', $search_id, true);

            #If the search result is empty, then output a special template created
            #for such case
            if (NULL != $this->prodlist)
            {
                $html = $this->templateFiller->fill("Container");
            }
            else
            {
                $html = $this->templateFiller->fill("ContainerNotMatch");
            }
        }
        else
        {
            $html = $this->templateFiller->fill("ContainerEmpty");
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

        #  the fields should be initialized here somehow.
        # perhaps when the method is invoked, $this is not the same object
        $this->templateFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate('SearchResult');
        $this->templateFiller->setTemplate($this->template);

        $html = "";
        $col =0;

        $columns = intval($application->getBlockOption($this->template, "Columns"));
        $this->_Product_Info = null;

        $pl = $this->prodlist;
        $this->prodlist = null;

        reset($pl);
        $counter = modApiFunc('paginator', 'getCurrentPaginatorOffset');

        $disable_trtd = $application->getAppIni('PRODUCT_LIST_DISABLE_TR_TD');
        if ($disable_trtd != null && strtolower($disable_trtd) === 'yes')
            $disable_trtd = true;
        else
            $disable_trtd = false;

        while(!empty($pl))
        {
            if ($col == 1)
            {
                if ($disable_trtd == false) $html .= '<tr><td>';
                $col++;
            }
            else
            {
                if ($disable_trtd == false) $html .= '<td>';
                $col++;
            }

            if ($col > $columns)
            {
                $col = 1;
                if ($disable_trtd == true) $html .= $this->templateFiller->fill('Separator');
            }

            $productInfo_array = array_shift($pl);

            $this->_Product_Info = new CProductInfo($productInfo_array['product_id']);

            $this->_Product_Info->setAdditionalProductTag('Local_ProductNumber', ++$counter);
            $application->registerAttributes(array('Local_ProductNumber'));

            modApiFunc("tag_param_stack", "push", __CLASS__, array(array("key" => TAG_PARAM_PROD_ID, "value" => $productInfo_array['product_id'])));
            $html .= $this->templateFiller->fill("Item", $this->_Product_Info->getProductTagValue('TypeId'));
            modApiFunc("tag_param_stack", "pop", __CLASS__);

            $this->_Product_Info->_destruct();
            $this->_Product_Info = null;
            unset($productInfo_array);
        }

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

            case 'PaginatorDropdown':
                $obj = &$application->getInstance('PaginatorDropdown');
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