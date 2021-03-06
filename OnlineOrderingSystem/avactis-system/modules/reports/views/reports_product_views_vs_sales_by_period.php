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

class ReportProductSalesVsViewsByDay extends CReportView
{
    function ReportProductSalesVsViewsByDay()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderStockChart';
        $this->__source_class_name = 'CProductSoldExtendedStatisticsByDays';
        $this->__render_settings_class_name = 'CReportRenderSettings';
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_DAY;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_PRODUCT_VIEWS');
    }

    function setColumns()
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            default:
                $this->__render_settings->setColumnList( array(
                                                            'date',
                                                            'items_sold',
                                                            'product_views',
                                                            'cr_views_sold',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'date' => getMsg('RPTS','DATE'),
                                                            'items_sold' => getMsg('RPTS','ITEMS_SOLD'),
                                                            'product_views' => getMsg('RPTS','PRODUCT_VIEWS'),
                                                            'cr_views_sold' => getMsg('RPTS','RATE_VIEWS_VS_ITEMS_SOLD'),
                ));

                $this->__render_settings->setColumnUnits( array(
                                                            'date' => '',
                                                            'items_sold' => getMsg('RPTS','ITEMS_SOLD_UNIT'),
                                                            'product_views' => getMsg('RPTS','PRODUCT_VIEWS_UNIT'),
                                                            'cr_views_sold' => getMsg('RPTS','RATE_VIEWS_VS_ITEMS_SOLD_UNIT'),
                ));
                break;
        }
    }

    function __formatRow($row)
    {
    	$row['date'] = $this->__prepareDateToDisplay($row);
        if ($row['items_sold'] == 0 || $row['product_views'] == 0)
        {
            $row['cr_views_sold'] = '0';
        }
        else
        {
            $row['cr_views_sold'] = round(100*$row['items_sold']/$row['product_views'], 2);
        }

        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $row['items_sold'] = modApiFunc("Localization", "num_format", $row['items_sold']);
                $row['product_views'] = modApiFunc("Localization", "num_format", $row['product_views']);
                $row['cr_views_sold'] .= '%';
                break;

            case 'creportrendercsv':
                //                           Stock Chart'
                $date = mktime(0,0,0,$row['datetime_month'], $row['datetime_day'], $row['datetime_year']);
                $row['date'] = date('Y-m-d',$date);
                break;

            default: // simple html table, binary excel or chart
                // Let's stay as is.
                break;

        }
        return $row;
    }

    function __prepareDateToDisplay($row)
    {
        $date = mktime(0,0,0,$row['datetime_month'], $row['datetime_day'], $row['datetime_year']);
        return modApiFunc("Localization", "date_format", $date, false); // false - do not apply time shift
    }
}


?>