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

loadModuleFile('reports/abstract/report_view.php');

class ReportVisitorOS extends CReportView
{
    function ReportVisitorOS()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderFlatTable';
        $this->__source_class_name = 'CVisitorOSStatistics';
        $this->__render_settings_class_name = 'CReportRenderSettings';
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_OS');
    }

    function isDatetimePeriodSelectorApplicable()
    {
        return false;
    }

    function setColumns()
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $this->__render_settings->setColumnList( array(
                                                            'os_name',
                                                            'number',
                                                            'percent',
                                                            'bar'
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'os_name' => getMsg('RPTS','OS'),
                                                            'percent' => getMsg('RPTS','PERCENT'),
                                                            'number' => getMsg('RPTS','OS_QTY'),
                                                            'bar' => '',
                        ));
                break;

            default:
                $this->__render_settings->setColumnList( array(
                                                            'os_name',
                                                            'number',
                                                            'percent',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'os_name' => getMsg('RPTS','OS'),
                                                            'percent' => getMsg('RPTS','PERCENT'),
                                                            'number' => getMsg('RPTS','OS_QTY'),
                ));
                break;

        }
    }

    function __formatRow($row)
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $row['number'] = modApiFunc("Localization", "num_format", $row['number']);
                $row['bar'] = $this->__getBar($row['percent_pixel']);
                $row['percent'] = modApiFunc("Localization", "num_format",$row['percent']).'%';
                break;

            default: // simple html table, binary excel or chart
                // Let's stay as is.
                break;

        }
        return $row;
    }
}

?>