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
 * Paginator module.
 *
 * @package Paginator
 * @access  public
 */
class Paginator
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Paginator constructor.
     *
     * @ finish the functions on this page
     */
    function Paginator()
    {
        global $application;

        $session = &$application->getInstance('Session');

        if ($session-> is_Set('Paginators'))
        {
            $this->Paginators = $session->get('Paginators');
        }
        else
        {
            $this->Paginators = array();
        }

        global $zone;
        if ($zone == "AdminZone")
        {
            $rows_per_page = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_PAGINATOR_DEFAULT_ROWS_PER_PAGE_AZ);
            $pages_per_line = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_PAGINATOR_PAGES_PER_LINE_AZ);
            $rows_per_page_array = unserialize(modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_AZ));
        }
        else
        {
            $rows_per_page = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_PAGINATOR_DEFAULT_ROWS_PER_PAGE_CZ);
            $pages_per_line = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_PAGINATOR_PAGES_PER_LINE_CZ);
            $rows_per_page_array = unserialize(modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_CZ));
        }

        define('ROWS_PER_PAGE', $rows_per_page);
        define('MIN_ROWS_PER_PAGE', $rows_per_page_array[0]);
        define('PAGES_PER_LINE', $pages_per_line);
        $this->rows_per_page = $rows_per_page_array;
    }

    /**
     * Installs the module.
     *
     * The install() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Paginator::getTables() instead of $this->getTables().
     *
     * @ finish the functions on this page
     */
    function Install()
    {
    }

    /**
     * Uninstalls the module.
     *
     * The uninstall() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Paginator::getTables() instead of $this->getTables().
     *
     * @ finish the functions on this page
     */
    function UnInstall()
    {
    }

    /**
     * Adds a paginator name and sets its current page to the paginator
     * array.
     *
     * @return
     * @param string $pag_name paginator name
     * @param string $page_num paginator page number
     */
    function setPaginatorPage($pag_name, $page_num)
    {
        if (!is_numeric($page_num) || $page_num <= 0)
        {
            return;
        }

        $paginator = array(
                           'ID' => $pag_name,
                           'PAGE_NUM' => $page_num,
                           'ROWS_PER_PAGE' => ROWS_PER_PAGE
//                           'PAGES_PER_LINE' => PAGES_PER_LINE,
//                           'TOTAL_ROWS' => TOTAL_ROWS
                           );

        $i = $this->issetPaginator($pag_name);

        if (is_int($i))
        {
            $this->Paginators[$i]['PAGE_NUM'] = $page_num;
        }
        else
        {
            array_push($this->Paginators, $paginator);
        }

        $this->savePaginators();
    }

    /**
     * Adds a paginator name and sets its current page to the last one.
     * It depends on rows outputted on the page.
     *
     *
     * @return
     * @param string $pag_name paginator name
     * @param integer $items_quan record count
     */
    function setPaginatorPageToLast($pag_name, $items_quan)
    {
        $rows_per_page = $this->getPaginatorRowsPerPage($pag_name);
        if ($rows_per_page === false)
        {
            $rows_per_page = ROWS_PER_PAGE;
        }
        $page_num = max((int)ceil($items_quan/$rows_per_page), 1);

        $paginator = array(
                           'ID' => $pag_name,
                           'PAGE_NUM' => $page_num,
                           'ROWS_PER_PAGE' => $rows_per_page
//                           'PAGES_PER_LINE' => PAGES_PER_LINE,
//                           'TOTAL_ROWS' => TOTAL_ROWS
                           );
        $i = $this->issetPaginator($pag_name);
        if (is_int($i))
        {
            $this->Paginators[$i]['PAGE_NUM'] = $page_num;
        }
        else
        {
            array_push($this->Paginators, $paginator);
        }
        $this->savePaginators();
    }

    /**
     * Adds a paginator name and sets rows outputted per page.
     *
     * @return
     * @param string $pag_name paginator name
     * @param integer $items_quan record count
     */
    function setPaginatorRows($pag_name, $rows_per_page)
    {
        $paginator = array(
                           'ID' => $pag_name,
                           'PAGE_NUM' => 0,
                           'ROWS_PER_PAGE' => $rows_per_page
//                           'PAGES_PER_LINE' => PAGES_PER_LINE,
//                           'TOTAL_ROWS' => TOTAL_ROWS
                           );
        $i = $this->issetPaginator($pag_name);
        if (is_int($i))
        {
            $this->Paginators[$i]['ROWS_PER_PAGE'] = $rows_per_page;
        }
        else
        {
            array_push($this->Paginators, $paginator);
        }
        $this->savePaginators();
    }

    /**
     * Check by a paginator name if it exists in the paginator array.
     *
     * @return mixed index of Paginator in Paginator array or false if no such Paginator exists in array
     * @param string $pag_name Paginator name
     */
    function issetPaginator($pag_name)
    {
        $i=0;
        foreach ($this->Paginators as $val)
        {
            if ($pag_name == $val['ID'])
            {
                return $i;
            }
            $i++;
        }
        return false;
    }

    /**
     * Gets the current page of the paginator with the name $pag_name.
     *
     * @return mixed number of page or false if no such Paginator exists in array
     * @param string $pag_name Paginator name
     */
    function getPaginatorPage($pag_name)
    {
        $i = $this->issetPaginator($pag_name);
        if (is_int($i))
        {
            return $this->Paginators[$i]['PAGE_NUM'];
        }
        return false;
    }

    /**
     * Gets rows outputted per page for the current paginator with
     * the name $pag_name.
     *
     * @return mixed number of page or false if no such Paginator exists in array
     * @param string $pag_name Paginator name
     */
    function getPaginatorRowsPerPage($pag_name)
    {
        $i = $this->issetPaginator($pag_name);
        if (is_int($i))
        {
            return $this->Paginators[$i]['ROWS_PER_PAGE'];
        }
        return ROWS_PER_PAGE;
    }

    /**
     * Gets an array of paginators.
     *
     * @return array Array of Paginators
     */
    function getPaginators()
    {
        return $this->Paginators;
    }

    /**
     * Saves the array of paginanators in the session.
     *
     * @return
     */
    function savePaginators()
    {
        global $application;
        $session = &$application->getInstance('Session');
        $session->set('Paginators', $this->Paginators);
    }

    /**
     *
     *
     * @return
     */
    function clearPaginatorsSession()
    {
        global $application;
        $session = &$application->getInstance('Session');
        $session->un_Set('Paginators');
        $this->Paginators = array();
    }

    /**
     * Sets up the current paginator.
     *
     * @return
     * @param string $pag_name Paginator name
     */
    function setCurrentPaginatorName($pag_name)
    {
        if (!is_int($this->issetPaginator($pag_name)))
        {
            $this->setPaginatorPage($pag_name, 1);
        }
        $this->CurrentPaginator = $pag_name;
    }

    /**
     * Resets a pagiantor with the name $pag_name.
     *
     * @return
     * @param string $pag_name Paginator name
     */
    function resetPaginator($pag_name)
    {
        $this->setPaginatorPage($pag_name, 1);
//        $this->savePaginators();
    }

    /**
     * Resets the number for the current page for all paginators.
     *
     * @return
     */
    function resetPaginators()
    {
        foreach ($this->Paginators as $paginator)
        {
            $this->setPaginatorPage($paginator["ID"], 1);
        }
//        $this->savePaginators();
    }


    /**
     * Gets a name of the current paginator.
     *
     * @return string name of the current paginator
     */
    function getCurrentPaginatorName()
    {
        return $this->CurrentPaginator;
    }

    /**
     * Gets total rows in the database for the current paginator.
     *
     * @return int Number of raws in DB
     */
    function getCurrentPaginatorTotalRows()
    {
        return $this->CurrentPaginatorTotalRows;
    }

    /**
     * Gets an offset.
     *
     * @return int Number of raws in DB
     */
    function getCurrentPaginatorOffset()
    {
        return $this->Offset;
    }

    /**
     * Adds the SQL of the LIMIT operator to the SELECT query.
     *
     * @return
     * @param object $query a reference to the object DB_Select
     * @param integer $fake_total_rows -                         NULL,                                     ,      SQL query
     */
    function setQuery($query, $fake_total_rows=NULL)
    {
        if ($fake_total_rows === NULL)
        {
            global $application;
            if (phpversion()<5)
            {
                $count_query = $query;
            }
            else
            {
                eval('$count_query = clone $query;');
            }

            $result = $application->db->getDB_Result_num_rows($count_query);
            $_total_rows = $result;
        }
        else
        {
            $_total_rows = $fake_total_rows;
        }

        $limits = $this->getQueryLimits($_total_rows);

        if ($limits != NULL && is_array($limits))
        {
            list($offset,$count) = $limits;

            if (!($fake_total_rows === NULL) && is_array($query))
            {
                $result = array();
                for ($i=$offset; $i<($offset+$count); $i++)
                {
                    if (isset($query[$i]))
                    {
                        $result[] = $query[$i];
                    }
                }
                $query = $result;
            }
            else
            {
                $query->SelectLimit($offset,$count);
            }
        }
        return $query;
    }

    function getQueryLimits($total_rows_in_query)
    {
        $this->CurrentPaginatorTotalRows = $total_rows_in_query;
        $page = $this->getPaginatorPage($this->getCurrentPaginatorName());
        if ($page!=NULL)
        {
            $count = $this->getPaginatorRowsPerPage($this->getCurrentPaginatorName());
            $offset = ($page-1)*$count;
            if ($offset>=$this->CurrentPaginatorTotalRows && $offset > 0)
            {
                $this->setPaginatorPageToLast($this->getCurrentPaginatorName(), $this->CurrentPaginatorTotalRows);
                $offset = ($this->getPaginatorPage($this->getCurrentPaginatorName()) - 1) * $count;
            }
            elseif ($this->CurrentPaginatorTotalRows == 0)
            {
                $offset = 0;
            }
            $this->Offset = $offset;
            return array($offset,$count);
        }
        return null;
    }

    /**
     * Gets the array of pissible values for rows per page.
     *
     * @ Then this method should take values from the system configurator
     *
     * @return array the array of pissible values for rows per page
     */
    function getRowsPerPage()
    {
        return $this->rows_per_page;
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * A list of paginators.
     */
    var $Paginators;

    /**
     * Current paginator.
     */
    var $CurrentPaginator;

    /**
     * Total raws in DB.
     */
    var $CurrentPaginatorTotalRows;

    /**
     * The array of values for rows per page.
     */
    var $rows_per_page;

    var $Offset;

    /**#@-*/
}
?>