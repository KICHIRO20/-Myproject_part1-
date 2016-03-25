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
 * CReportRenderFlatTable class
 *
 * @author Alexey Florinsky
 * @version $Id: report_render_flat_table.php 5013 2008-04-09 14:04:24Z af $
 * @package Reports
 */
class CReportRenderFlatTable
{

    function CReportRenderFlatTable()
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
                                        'ReportTotalRow' => $this->outputTotalRow(),
                                     );
        return $this->_TmplFiller->fill("flat-table/", "container-report.tpl.html", $container_tag_values);
    }

    function outputTotalRow()
    {
        $total_column_list = $this->__report_settings->getColumnTotalList();
        if ( empty( $total_column_list) or $this->flag_report_is_empty == true)
        {
            return '';
        }
        $row_html = $this->outputRowCells($total_column_list, $this->__report_settings->getColumnTotalStyles(), 'total_item.tpl.html');
        return $this->_TmplFiller->fill("flat-table/", "total_row.tpl.html",array('Row'=>$row_html ));
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
            $html .= $this->_TmplFiller->fill( "flat-table/", "header_item.tpl.html", array('ColumnName'=>$field_header) );
        }
        $html = $this->_TmplFiller->fill("flat-table/", "header_row.tpl.html",array('ColumnItems'=>$html));
        return $html;
    }

    function outputReportRows()
    {
        $html = '';
        $rows_counter = 0;

        $backgroundColors = array('#EEF2F8','#FFFFFF');
        reset($backgroundColors);
        $currentColor = current($backgroundColors);
        $row_style = 'background-color: '.$currentColor;

        foreach ($this->__report_settings->getReportData() as $row_data)
        {
            $row_html = $this->outputRowCells($row_data, $this->__report_settings->getColumnStyles(), 'data_item.tpl.html');
            $html .= $this->_TmplFiller->fill("flat-table/", "data_row.tpl.html",array('Row'=>$row_html, 'RowStyle'=>$row_style));
            ++$rows_counter;

            $currentColor = next($backgroundColors);
            if ($currentColor === false)
            {
                reset($backgroundColors);
                $currentColor = current($backgroundColors);
            }
            $row_style = 'background-color: '.$currentColor;
        }

        if ($rows_counter == 0)
        {
            $this->flag_report_is_empty = true;
            $html .= $this->_TmplFiller->fill("flat-table/", "empty_row.tpl.html",array('ColumnNumber'=>$this->__report_settings->getColumnNumber()));
        }

        if ($rows_counter < $this->__report_settings->getMinimumReportRowsNumber())
        {
            $row_data = array();
            foreach ($this->__report_settings->getColumnList() as $column)
            {
                $row_data[$column] = '&nbsp;';
            }
            for ($i = $rows_counter; $i<$this->__report_settings->getMinimumReportRowsNumber(); $i++)
            {
                $row_html = $this->outputRowCells($row_data, $this->__report_settings->getColumnStyles(), 'data_item.tpl.html');
                $html .= $this->_TmplFiller->fill("flat-table/", "data_row.tpl.html",array('Row'=>$row_html, 'RowStyle'=>$row_style));
				$currentColor = next($backgroundColors);
				if ($currentColor === false)
				{
					reset($backgroundColors);
					$currentColor = current($backgroundColors);
				}
				$row_style = 'background-color: '.$currentColor;
            }
        }
        return $html;
    }

    function outputRowCells($row_data, $styles, $template)
    {
        $html = '';
        $field_list = $this->__report_settings->getColumnList();
        foreach ($field_list as $field)
        {
            $cell_data = array(
                                'Value' => isset($row_data[$field]) ? $row_data[$field] : '',
                                'Style' => isset($styles[$field]) ? $styles[$field] : '',
                              );
            $html .= $this->_TmplFiller->fill("flat-table/", $template, $cell_data);
        }
        return $html;
    }

    var $_TmplFiller;
    var $__report_settings = null;
    var $flag_report_is_empty = false;
}

?>