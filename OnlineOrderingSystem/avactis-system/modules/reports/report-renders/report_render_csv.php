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
 * CReportRenderCSV class
 *
 * @author Alexey Florinsky
 * @version $Id$
 * @package Reports
 */
class CReportRenderCSV
{
    var $__csv_writer = null;
    var $__file_name = '';
    function CReportRenderCSV()
    {
        global $application;
        loadCoreFile('csv_parser.php');
        $cache_folder = $application->getAppIni('PATH_CACHE_DIR');
        $this->__file_name = $cache_folder.'report_'.md5(rand(0,999999999)).'.csv';

        $this->__csv_writer = new CSV_Writer(CSV_WRITER_ENABLE_BUFFERING);
        $this->__csv_writer->setDelimetr(';');
    }

    function output($report_settings)
    {
        global $application;

        $this->__report_settings = $report_settings;

        $this->outputColumnHeaders();
        $this->outputReportRows();

        return $this->__csv_writer->getBuffer();
    }

    function outputColumnHeaders()
    {
        $headers = $this->__report_settings->getColumnHeaders();
        if (empty($headers))
        {
            return;
        }

        $headers = $this->__report_settings->getColumnHeaders();
        $csv_header = array();
        foreach ($this->__report_settings->getColumnList() as $field_key)
        {
            $field_header = isset($headers[$field_key]) ? $headers[$field_key] : '';
            $csv_header[] = $field_header;
        }
        $this->__csv_writer->setLayout($csv_header);
        //                                                  ,
        // CSV                         Stock Chart,
        //       CSV                    .
        //$this->__csv_writer->writeLayout();
    }

    function outputReportRows()
    {
        $headers = $this->__report_settings->getColumnHeaders();
        $field_list = $this->__report_settings->getColumnList();
        $csv_data = array();

        //                      csv                 Stock Chart,
        //                                 -             Stock Chart.
        //                              data source        -   -
        //                            .
        $data = array_reverse($this->__report_settings->getReportData());

        foreach ($data as $row_data)
        {
            $csv_data_item = array();
            foreach ($field_list as $field)
            {
                $value = isset($row_data[$field]) ? $row_data[$field] : '';
                $value_header = $headers[$field];
                $csv_data_item[$value_header] = $value;
            }
            $csv_data[] = $csv_data_item;
        }
        $this->__csv_writer->writeData($csv_data);
    }

    var $__report_settings = null;
}

?>