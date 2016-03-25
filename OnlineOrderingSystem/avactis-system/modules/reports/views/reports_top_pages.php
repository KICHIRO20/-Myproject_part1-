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

class ReportTopViewedPages extends CReportView
{
    function ReportTopViewedPages()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderFlatTable';
        $this->__source_class_name = 'CVisitorTopViewedPageStatistics';
        $this->__render_settings_class_name = 'CReportRenderSettings';
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_TOP_VIEWED_PAGES');
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
                                                            'page_url',
                                                            'view_number',
                                                            'percent',
                                                            'bar',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'page_url' => getMsg('RPTS','PAGE_URL'),
                                                            'view_number' => getMsg('RPTS','PAGE_VIEWS'),
                                                            'percent' => getMsg('RPTS','PERCENT'),
                                                            'bar' => '',
                        ));
                break;

            default:
                $this->__render_settings->setColumnList( array(
                                                            'page_url',
                                                            'view_number',
                                                            'percent',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'page_url' => getMsg('RPTS','PAGE_URL'),
                                                            'view_number' => getMsg('RPTS','PAGE_VIEWS'),
                                                            'percent' => getMsg('RPTS','PERCENT'),
                                                            'percent' => '%',
                        ));
                break;
        }
    }

    function __formatRow($row)
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $row['view_number'] = modApiFunc("Localization", "num_format", $row['view_number']);

                $parsed = @parse_url($row['page_url']);
                if (isset($parsed['path']) and !empty($parsed['path']))
                {
                    $url = $parsed['path'];
                    if (isset($parsed['query']) and !empty($parsed['query']))
                    {
                        $url .= '?'.$parsed['query'];
                    }
                }
                else
                {
                    $url = $row['page_url'];
                }

                //        ,
                $row['page_url'] = $this->__prepareLongUrl($url, 80, 'font-weight: bold; color: #666666;');

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