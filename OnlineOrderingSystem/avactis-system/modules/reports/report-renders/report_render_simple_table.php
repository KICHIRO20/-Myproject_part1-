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
 * CReportRenderSimpleHTMLTable class
 *
 * @author Alexey Florinsky
 * @version $Id$
 * @package Reports
 */
class CReportRenderSimpleHTMLTable
{

    function CReportRenderSimpleHTMLTable()
    {
        //                        ,                      .
        $this->_TmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/render/');
    }

    function output($report_settings)
    {
        global $application;

        $this->__report_settings = $report_settings;

        $container_tag_values = array(
                                        'ReportID' => $this->__report_settings->getReportID(),
                                        'ColumnHeaders' => $this->outputColumnHeaders(),
                                        'ReportRows' => $this->outputReportRows(),
                                        'ColumnNumber' => $this->__report_settings->getColumnNumber(),
                                     );
        return $this->_TmplFiller->fill("simple-html-table/", "container-report.tpl.html", $container_tag_values);
    }

    function outputColumnHeaders()
    {
        $html = '';

        $headers = $this->__report_settings->getColumnHeaders();
        if (empty($headers))
        {
            return $html;
        }

        $headers = $this->__report_settings->getColumnHeaders();
        foreach ($this->__report_settings->getColumnList() as $field_key)
        {
            $field_header = isset($headers[$field_key]) ? $headers[$field_key] : '';
            $html .= $this->_TmplFiller->fill( "simple-html-table/", "header_item.tpl.html", array('ColumnName'=>$field_header) );
        }
        $html = $this->_TmplFiller->fill("simple-html-table/", "header_row.tpl.html",array('ColumnItems'=>$html));
        return $html;
    }

    function outputReportRows()
    {
        $html = '';
        $rows_counter = 0;

        foreach ($this->__report_settings->getReportData() as $row_data)
        {
            $row_html = $this->outputRowCells($row_data, 'data_item.tpl.html');
            $html .= $this->_TmplFiller->fill("simple-html-table/", "data_row.tpl.html",array('Row'=>$row_html));
            ++$rows_counter;
        }

        if ($rows_counter == 0)
        {
            $this->flag_report_is_empty = true;
            $html .= $this->_TmplFiller->fill("simple-html-table/", "empty_row.tpl.html",array('ColumnNumber'=>$this->__report_settings->getColumnNumber()));
        }
        return $html;
    }

    function outputRowCells($row_data, $template)
    {
        $html = '';
        $field_list = $this->__report_settings->getColumnList();
        foreach ($field_list as $field)
        {
            $cell_data = array(
                                'Value' => isset($row_data[$field]) ? $row_data[$field] : '',
                              );
            $html .= $this->_TmplFiller->fill("simple-html-table/", $template, $cell_data);
        }
        return $html;
    }

    var $_TmplFiller;
    var $__report_settings = null;
    var $flag_report_is_empty = false;
}

?>