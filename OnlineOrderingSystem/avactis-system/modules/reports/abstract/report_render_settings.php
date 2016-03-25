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
 * CReportRenderSettings classe
 *
 * @author Alexey Florinsky
 * @version $Id: report_render_settings.php 5629 2008-09-19 10:12:41Z af $
 * @package Reports
 */
class CReportRenderSettings
{
    function CReportRenderSettings()
    {

    }

    function setTitle($t)
    {
        $this->__title = $t;
    }

    function getTitle()
    {
        return $this->__title;
    }


    // Report Placeholder Width

    function setReportPlaceholderWidth($w)
    {
        $this->__report_placeholder_width = $w;
    }

    function getReportPlaceholderWidth()
    {
        return $this->__report_placeholder_width;
    }


    // report ID

    function setReportID($id)
    {
        $this->__report_id = $id;
    }

    function getReportID()
    {
        return $this->__report_id;
    }



    // Header Line

    function setColumnHeaders($list)
    {
        $this->__column_headers = $list;
    }

    function getColumnHeaders()
    {
        return $this->__column_headers;
    }



    // Units

    function setColumnUnits($list)
    {
        $this->__column_units = $list;
    }

    function getColumnUnits()
    {
        return $this->__column_units;
    }



    // Column List

    function setColumnList($list)
    {
        $this->__column_list = $list;
    }

    function getColumnList()
    {
        return $this->__column_list;
    }


    // Column Styles

    function setColumnStyles($list)
    {
        $this->__column_styles = $list;
    }

    function getColumnStyles()
    {
        return $this->__column_styles;
    }


    // Report Data

    function setReportData($data)
    {
        $this->__report_data = $data;
    }

    function getReportData()
    {
        return $this->__report_data;
    }


    // Total Column Styles

    function setColumnTotalStyles($list)
    {
        $this->__column_total_styles = $list;
    }

    function getColumnTotalStyles()
    {
        return $this->__column_total_styles;
    }


    // Total Column Values

    function setColumnTotalList($list)
    {
        $this->__column_totals = $list;
    }

    function getColumnTotalList()
    {
        return $this->__column_totals;
    }


    // chart Y unit label
    function getChartUnit()
    {
        return $this->__chart_unit;
    }

    function setChartUnit($u)
    {
        return $this->__chart_unit = $u;
    }




    function getColumnNumber()
    {
        return count($this->__column_list);
    }

    function getMinimumReportRowsNumber()
    {
        return $this->__min_report_rows_number;
    }

    var $__column_list = array();
    var $__report_data = array();
    var $__column_styles = array();
    var $__column_total_styles = array();
    var $__column_totals = array();
    var $__min_report_rows_number = 5;
    var $__column_headers = array();
    var $__report_id = null;
    var $__chart_unit = '';
    var $__title = '';
    var $__column_units = array();
}

?>