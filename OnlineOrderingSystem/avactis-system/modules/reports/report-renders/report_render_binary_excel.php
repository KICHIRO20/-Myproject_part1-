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
 * CReportRenderBinaryExcel class
 *
 * @author Alexey Florinsky
 * @version $Id$
 * @package Reports
 */
class CReportRenderBinaryExcel
{
    var $__excel = null;
    function CReportRenderBinaryExcel()
    {
        loadCoreFile('simple_excel_writer.php');
        $this->__excel = new CSimpleBinaryExcel();
    }

    function output($report_settings)
    {
        global $application;

        $this->__report_settings = $report_settings;
        $this->__excel->begin();
        $this->outputColumnHeaders();
        $this->outputReportRows();
        $this->__excel->end();

        return $this->__excel->getContent();
    }

    function outputColumnHeaders()
    {
        $headers = $this->__report_settings->getColumnHeaders();
        if (empty($headers))
        {
            return;
        }

        $headers = $this->__report_settings->getColumnHeaders();
        $x = 0;
        $y = 0;
        foreach ($this->__report_settings->getColumnList() as $field_key)
        {
            $field_header = isset($headers[$field_key]) ? $headers[$field_key] : '';
            $this->__excel->writeLabel($y, $x, $field_header);
            $x++;
        }
    }

    function outputReportRows()
    {
        $x = 0;
        $y = 1;

        $field_list = $this->__report_settings->getColumnList();
        foreach ($this->__report_settings->getReportData() as $row_data)
        {
            $x = 0;
            foreach ($field_list as $field)
            {
                $value = isset($row_data[$field]) ? $row_data[$field] : '';
                if (Validator::isValidInt($value) or Validator::isValidFloat($value))
                {
                    $this->__excel->writeNumber($y, $x, $value);
                }
                else
                {
                    $this->__excel->writeLabel($y, $x, $value);
                }
                $x++;
            }
            $y++;
        }
    }

    var $__report_settings = null;
}

?>