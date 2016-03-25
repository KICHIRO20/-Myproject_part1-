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
 * Paginator Line
 *
 * @author Alexander Girin
 * @package Paginator
 * @access  public
 */
class PaginatorLine
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
    	    'layout-file'        => 'paginator-line-config.ini'
    	   ,'files' => array(
    	        'Container'      => TEMPLATE_FILE_SIMPLE
    	       ,'Empty'          => TEMPLATE_FILE_SIMPLE
    	       ,'Prev'           => TEMPLATE_FILE_SIMPLE
    	       ,'Next'           => TEMPLATE_FILE_SIMPLE
    	       ,'ItemSelected'   => TEMPLATE_FILE_SIMPLE
    	       ,'Item'           => TEMPLATE_FILE_SIMPLE
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
    function PaginatorLine()
    {
        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("PaginatorLine"))
        {
            $this->NoView = true;
        }
    }

/*    function getViewCacheKey($pag_name, $viewname, $cat_id=-1)
    {
        $rows_per_page = modAPIFunc('Paginator', 'getPaginatorRowsPerPage', $pag_name);
        $pages = ceil(modAPIFunc('Paginator', 'getCurrentPaginatorTotalRows')/$rows_per_page);
        $currentpage = modAPIFunc('Paginator', 'getPaginatorPage', $pag_name);
        $currentrows = modAPIFunc('Paginator', 'getPaginatorRowsPerPage', $pag_name);
        return md5($rows_per_page . $pages . $currentpage . $currentrows);
    }//*/

    /**
     * Returns the page links line.
     *
     * @return string paginator view in the type of paginal references
     * @ finish the functions on this page
     */
    function output($pag_name, $viewname, $cat_id=-1, $prod_id=-1)
    {
        global $application;

        $viewname = CURRENT_REQUEST_URL;


        #Define whether to output the view or not
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "PaginatorLine", "Errors");
            return "";
        }

        $this->templateFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate('PaginatorLine');
        $this->templateFiller->setTemplate($this->template);

        $retval='';
        //Output a paginator view with the page number,
        //form the link of the type ?action=setpage&pgname=$pag_name&pgnum=$i
        $rows_per_page = modAPIFunc('Paginator', 'getPaginatorRowsPerPage', $pag_name);
        if (!$rows_per_page)
        {
            $rows_per_page = ROWS_PER_PAGE;
        }
        $pages = ceil(modAPIFunc('Paginator', 'getCurrentPaginatorTotalRows')/$rows_per_page);
        $lines = ceil($pages/PAGES_PER_LINE);
        $currentpage = modAPIFunc('Paginator', 'getPaginatorPage', $pag_name);
        $currentrows = modAPIFunc('Paginator', 'getPaginatorRowsPerPage', $pag_name);
        $currentline = ceil($currentpage/PAGES_PER_LINE);
        //$firstpageinline = ($currentline - 1)*PAGES_PER_LINE+1;
        $lastpageinline = ($currentline)*PAGES_PER_LINE;

        if ($lastpageinline > $pages)
        {
            $lastpageinline = $pages;
        }
        $firstpageinline = $lastpageinline - PAGES_PER_LINE + 1;
        if ($firstpageinline <= 0)
        {
            $firstpageinline = 1;
        }

        $this->_Data = array(
                               'Local_Items' => ''
                              ,'Local_From' => ''
                              ,'Local_To' => ''
                              ,'Local_Of' => ''
                              ,'Local_CurrentPage' => ''
                              ,'Local_PagesQty' => ''
                              ,'Local_ViewAll' => ''
                              );

        $request = new Request();
        $request->setView  ( $viewname );
        $request->setAction( 'Paginator_SetPage' );
        $request->setKey   ( 'pgname', $pag_name );
        $request->setKey   ( 'pgnum', $currentpage-1 );
        $request->setCategoryID($cat_id);
        $request->setProductID($prod_id);
        $this->_Data = array_merge($this->_Data, array(
                        'Local_PageLink' => $request->getURL()
                       ,'Local_PageNumber'      => ''//'PREV'
                       ));

        $retval = '';
        $paginator_line = '';
        if ($currentpage != 1)
        {
            $application->registerAttributes($this->_Data);
            $paginator_line.= $this->templateFiller->fill("Prev");
        }

        if ($firstpageinline != 1)
        {
            $paginator_line.= CZ_getMsg("PAGINATOR_DOTS");
        }
        for ($i=$firstpageinline; $i<=$lastpageinline; $i++)
        {
            $request = new Request();
            $request->setView  ( $viewname );
            $request->setAction( 'Paginator_SetPage' );
            $request->setKey   ( 'pgname', $pag_name );
            $request->setKey   ( 'pgnum', $i );
            $request->setCategoryID($cat_id);
            $this->_Data = array_merge($this->_Data, array(
                            'Local_PageLink' => $request->getURL()
                           ,'Local_PageNumber'      => $i
                            ));
            $application->registerAttributes($this->_Data);
            if ($i == $currentpage)
            {
                $paginator_line.= $this->templateFiller->fill("ItemSelected");
            }
            else
            {
                $paginator_line.= $this->templateFiller->fill("Item");
            }
        }
        if ($lastpageinline != $pages)
        {
            $paginator_line.= CZ_getMsg("PAGINATOR_DOTS");
        }
        $request = new Request();
        $request->setView  ( $viewname );
        $request->setAction( 'Paginator_SetPage' );
        $request->setKey   ( 'pgname', $pag_name );
        $request->setKey   ( 'pgnum', $currentpage+1 );
        $request->setCategoryID($cat_id);
        $this->_Data = array_merge($this->_Data, array(
                        'Local_PageLink' => $request->getURL()
                       ,'Local_PageNumber' => ''//'NEXT'
                       ));

        if ($currentpage != $pages && $pages!=0)
        {
            $paginator_line .= $this->templateFiller->fill("Next");
        }

        $request = new Request();
        $request->setView  ( $viewname );
        $request->setAction( 'Paginator_SetRowsPerPage' );
        $request->setKey   ( 'pgname', $pag_name );
        $request->setKey   ( 'rows', modAPIFunc('Paginator', 'getCurrentPaginatorTotalRows') );
        $request->setCategoryID($cat_id);

        $to = $currentpage*$rows_per_page;
        $total_rows = modAPIFunc('Paginator', 'getCurrentPaginatorTotalRows');
        if ($to > $total_rows)
        {
            $to = $total_rows;
        }
        $this->_Data = array_merge($this->_Data, array(
                                                       'Local_Items' => ($pages > 1 ? $paginator_line: "")
                                                      ,'Local_From' => ($currentpage-1)*$rows_per_page+1
                                                      ,'Local_To' => $to
                                                      ,'Local_Of' => $total_rows
                                                      ,'Local_CurrentPage' => $currentpage
                                                      ,'Local_PagesQty' => $pages
                                                      ,'Local_ViewAll' => $request->getURL()
                                                      ));
        $application->registerAttributes($this->_Data);
        if ($pages > 1)
        {
            $retval = $this->templateFiller->fill("Container");
        }
        else
        {
            $retval = $this->templateFiller->fill("Empty");
        }
//        $retval = $this->templateFiller->fill("Container");

        return $retval;
    }

    function getTag($tag)
    {
        if (is_array($this->_Data) && array_key_exists($tag, $this->_Data))
        {
        	return $this->_Data[$tag];
        }
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

    var $_Data;

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