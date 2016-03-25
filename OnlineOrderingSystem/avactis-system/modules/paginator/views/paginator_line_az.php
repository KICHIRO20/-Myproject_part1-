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
     * The view constructor.
     *
     * @ finish the functions on this page
     */
    function PaginatorLine()
    {
    }

    /**
     * Returns the page links line.
     *
     * @return string paginator view in the type of paginal references
     * @ finish the functions on this page
     */
    function output($pag_name, $viewname, $add_keys = null)
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');

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
        $firstpageinline = ($currentline - 1)*PAGES_PER_LINE+1;
        $lastpageinline = ($currentline)*PAGES_PER_LINE;

        if ($lastpageinline > $pages)
        {
            $lastpageinline = $pages;
        }

        $request = new Request();
        $request->setView  ( $viewname );
        $request->setAction( 'Paginator_SetPage' );
        $this->addKeysToRequest($request, $add_keys);
        $request->setKey   ( 'pgname', $pag_name );
        $request->setKey   ( 'pgnum', $firstpageinline-1 );
        $this->prefix = array(
                        'Pre_Item_Link' => $request->getURL()
                       ,'Pre_Item'      => $obj->getMessage('PGNTR_PREV')
                       );

        $request = new Request();
        $request->setView  ( $viewname );
        $request->setAction( 'Paginator_SetPage' );
        $this->addKeysToRequest($request, $add_keys);
        $request->setKey   ( 'pgname', $pag_name );
        $request->setKey   ( 'pgnum', $lastpageinline+1 );
        $this->postfix = array(
                        'Post_Item_Link' => $request->getURL()
                       ,'Post_Item'      => $obj->getMessage('PGNTR_NEXT')
                       );
        $application->registerAttributes($this->prefix);
        $application->registerAttributes($this->postfix);

        $retval = '';
        $paginator_line = '';
        if ($currentline != 1)
        {
            $paginator_line.= modApiFunc('TmplFiller', 'fill', "paginator/", "item_prev.tpl.html", array());
        }
        for ($i=$firstpageinline; $i<=$lastpageinline; $i++)
        {
            $request = new Request();
            $request->setView  ( $viewname );
            $request->setAction( 'Paginator_SetPage' );
            $this->addKeysToRequest($request, $add_keys);
            $request->setKey   ( 'pgname', $pag_name );
            $request->setKey   ( 'pgnum', $i );
            $this->page = array(
                            'Item_Link' => $request->getURL()
                           ,'Item'      => $i
                            );
            $application->registerAttributes($this->page);
            if ($i == $currentpage)
            {
                $paginator_line.= modApiFunc('TmplFiller', 'fill', "paginator/", "selected_item.tpl.html", array());
            }
            else
            {
                $paginator_line.= modApiFunc('TmplFiller', 'fill', "paginator/", "item.tpl.html", array());
            }
        }
        if ($currentline != $lines&&$lines!=0)
        {
            $paginator_line .= modApiFunc('TmplFiller', 'fill', "paginator/", "item_next.tpl.html", array());
        }

        $this->paginator = $paginator_line;
        if ($pages > 1)
        {
            $retval = modApiFunc('TmplFiller', 'fill', "paginator/", "container.tpl.html", array());
        }
        return $retval;
    }

    function addKeysToRequest(&$request, &$add_keys)
    {
        if (is_array($add_keys)) {
            foreach ($add_keys as $k => $v) {
                $request->setKey($k, $v);
            }
        }
    }

    function getTag($tag)
    {
        $value = null;
        switch ($tag)
        {
            case "Items":
                  $value = $this->paginator;
                  break;
            case "Item":
                  $value = $this->page["Item"];
                  break;
            case "Item_Link":
                  $value = $this->page["Item_Link"];
                  break;
            case "Pre_Item":
                  $value = $this->prefix["Pre_Item"];
                  break;
            case "Pre_Item_Link":
                  $value = $this->prefix["Pre_Item_Link"];
                  break;
            case "Post_Item":
                  $value = $this->postfix["Post_Item"];
                  break;
            case "Post_Item_Link":
                  $value = $this->postfix["Post_Item_Link"];
                  break;
            default:

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
     * Pointer to the module object.
     */
    var $pPaginator;

    /**#@-*/

}
?>