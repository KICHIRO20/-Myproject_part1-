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

class ReportTopVisitorReferers extends CReportView
{
    function ReportTopVisitorReferers()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderFlatTable';
        $this->__source_class_name = 'CVisitorTopRefererStatistics';
        $this->__render_settings_class_name = 'CReportRenderSettings';
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_TOP_REFERERS');
    }

    function isDatetimePeriodSelectorApplicable()
    {
        return true;
    }

    function setColumns()
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $this->__render_settings->setColumnList( array(
                                                            'referer',
                                                            'visit_number',
                                                            'bar',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'referer' => getMsg('RPTS','REFERER'),
                                                            'visit_number' => getMsg('RPTS','REFERER_VISITS'),
                                                            'bar' => getMsg('RPTS','PERCENT'),
                        ));
                break;

            default:
                $this->__render_settings->setColumnList( array(
                                                            'referer',
                                                            'visit_number',
                                                            'percent',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'referer' => getMsg('RPTS','REFERER'),
                                                            'visit_number' => getMsg('RPTS','REFERER_VISITS'),
                                                            'percent' => getMsg('RPTS','PERCENT'),
                ));
                break;
        }
    }

    function __formatRow($row)
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $row['visit_number'] = modApiFunc("Localization", "num_format", $row['visit_number']);
                if (empty($row['referer']))
                {
                     $row['referer'] = getMsg('RPTS','DIRECT_ACCESS');
                }
                $row['bar'] = $this->__getBar($row['percent_pixel']);
                $row['percent'] = modApiFunc("Localization", "num_format",$row['percent']).'%';
                break;

            default: // simple html table, binary excel or chart
                // Let's stay as is.
                if (empty($row['referer']))
                {
                     $row['referer'] = getMsg('RPTS','DIRECT_ACCESS');
                }
                break;

        }
        return $row;
    }
}


class ReportTopVisitorFullReferers extends ReportTopVisitorReferers
{
    function ReportTopVisitorFullReferers()
    {
        parent::ReportTopVisitorReferers();
    }

    function getReportName()
    {
        return getMsg('RPTS', 'REPORT_TOP_FULL_REFERERS');
    }

    function __formatRow($row)
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $row['visit_number'] = modApiFunc("Localization", "num_format", $row['visit_number']);
                if (empty($row['referer']))
                {
                     $row['referer'] = getMsg('RPTS', 'DIRECT_ACCESS');
                }
                else
                {
                    //        ,                                                   URL
                    $row['referer'] = $this->__prepareLongUrl($row['referer'], 80, 'font-weight: bold; color: #666666;');
                }

                $row['bar'] = $this->__getBar($row['percent_pixel']);
                $row['percent'] = modApiFunc("Localization", "num_format",$row['percent']).'%';
                break;

            default: // simple html table, binary excel or chart
                if (empty($row['referer']))
                {
                     $row['referer'] = getMsg('RPTS', 'DIRECT_ACCESS');
                }
                break;

        }
        return $row;
    }

    function prepareData()
    {
        $this->__source->__params['select_full_referer_url'] = true;
        parent::prepareData();
    }

}


?>