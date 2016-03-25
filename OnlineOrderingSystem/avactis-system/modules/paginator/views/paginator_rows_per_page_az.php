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
class PaginatorRows
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
    function PaginatorRows()
    {
    }

    /**
     * Returns the Select box view with possible variants of rows
     * per page.
     *
     * @return Select box with possible page values outputted on the page.
     * @ finish the functions on this page
     */
    function output($pag_name, $viewname, $items_name='PGNTR_REC_ITEMS', $add_keys = null)
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        $retval='';
        $rows_per_page = modAPIFunc('Paginator', 'getPaginatorRowsPerPage', $pag_name);
        if (!$rows_per_page)
        {
            $rows_per_page = ROWS_PER_PAGE;
        }
        $currentrows = $rows_per_page;

        $rows_per_page = modApiFunc('Paginator', 'getRowsPerPage');
        $Row_Options = '';
        for ($i=0; $i<sizeof($rows_per_page); $i++)
        {
            if ($rows_per_page[$i] == $currentrows)
            {
                $Row_Options.= '<option selected="selected" value="'.$rows_per_page[$i].'">'.$rows_per_page[$i].'</option>';
            }
            else
            {
                $Row_Options.= '<option value="'.$rows_per_page[$i].'">'.$rows_per_page[$i].'</option>';
            }
        }

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $url = $request->getURL();

        $this->paginator_rows = array(
                                'ViewName' => $url
                               ,'AdditionalKeyList' => $this->getAdditionalKeyList($add_keys)
                               ,'Items_Name' => $obj->getMessage($items_name)
                               ,'Row_Options' => $Row_Options
                               ,'pgname' => $pag_name);
        $application->registerAttributes($this->paginator_rows);
        if(modAPIFunc('Paginator', 'getCurrentPaginatorTotalRows')>MIN_ROWS_PER_PAGE)
        {
            $retval = modApiFunc('TmplFiller', 'fill', "paginator/", "container_rows.tpl.html", array());
        }
        return $retval;
    }

    /**
     * @ describe the function AddCategory->getTag.
     */
    function getTag($tag)
    {
        $value = null;
        if (array_key_exists($tag, $this->paginator_rows))
        {
            $value = $this->paginator_rows[$tag];
        }
        return $value;
    }


    function getAdditionalKeyList($add_keys)
    {
        if (!is_array($add_keys) || empty($add_keys))
            return '';

        $retval = array();
        foreach($add_keys as $k=>$v)
            $retval[] = urlencode($k) . '=' . urlencode($v);

        return implode('&', $retval) . '&';
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