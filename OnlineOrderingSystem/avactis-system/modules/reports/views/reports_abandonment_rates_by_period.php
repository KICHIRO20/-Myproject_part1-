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

class ReportAbandonmentRatesByDay extends CReportView
{
    function ReportAbandonmentRatesByDay()
    {
        parent::CReportView();

        #$this->__render_class_name = 'CReportRenderFlatTable';
        $this->__render_class_name = 'CReportRenderStockChart';

        $this->__source_class_name = 'COrdersVisitorsCartsStatisticsByDays';
        $this->__render_settings_class_name = 'CReportRenderSettings';
//        $this->__currency = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getMainStoreCurrency"));
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_DAY;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_ABANDONMENT_DAYS');
    }

    function setColumns()
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderchart':
                $this->__render_settings->setColumnList( array(
                                                            'date',
                                                            'order_qty',
                                                            'carts_created_qty',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'date' => getMsg('RPTS','DATE'),
                                                            'order_qty' => getMsg('RPTS','ORDERS_QTY'),
                                                            'carts_created_qty' => getMsg('RPTS','CARTS_QTY'),
                ));
                $this->__render_settings->setChartUnit($this->__currency);
                break;

            default:
                $this->__render_settings->setColumnList( array(
                                                            'date',
                                                            'order_qty',
                                                            'carts_created_qty',
                                                            'abandonment_rate',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'date' => getMsg('RPTS','DATE'),
                                                            'order_qty' => getMsg('RPTS','ORDERS_QTY'),
                                                            'carts_created_qty' => getMsg('RPTS','CARTS_QTY'),
                                                            'abandonment_rate' => getMsg('RPTS','ABANDONMENT_RATE'),
                ));
                $this->__render_settings->setColumnUnits( array(
                                                            'date' => '',
                                                            'order_qty' => getMsg('RPTS','ORDERS_QTY_UNIT'),
                                                            'carts_created_qty' => getMsg('RPTS','CARTS_QTY_UNIT'),
                                                            'abandonment_rate' => getMsg('RPTS','ABANDONMENT_RATE_UNIT'),
                ));
                break;
        }
    }

    function __formatRow($row)
    {
        if ($row['carts_created_qty'] == 0)
        {
            $row['abandonment_rate'] = '0';
        }
        else
        {
            $row['abandonment_rate'] = round(100*(1-$row['order_qty']/$row['carts_created_qty']),2);
        }

        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $row['abandonment_rate'] .= '%';
                $row['order_qty'] = modApiFunc("Localization", "num_format", $row['order_qty']);
                $row['carts_created_qty'] = modApiFunc("Localization", "num_format", $row['carts_created_qty']);
                $row['date'] = $this->__prepareDateToDisplay($row);
                break;
            case 'creportrenderchart':
                $row['date'] = $this->__prepareDateToDisplay($row);
                break;
            case 'creportrendercsv':
                //                           Stock Chart'
                $date = mktime(0,0,0,$row['datetime_month'], $row['datetime_day'], $row['datetime_year']);
                $row['date'] = date('Y-m-d',$date);
                break;
            default:
                $row['abandonment_rate'] .= '%';
                $row['date'] = $this->__prepareDateToDisplay($row);
                break;
        }
        return $row;
    }

    function __prepareDateToDisplay($row)
    {
        $date = mktime(0,0,0,$row['datetime_month'], $row['datetime_day'], $row['datetime_year']);
        return modApiFunc("Localization", "date_format", $date, false); // false - do not apply time shift
    }

    var $__currency = '';
}

?>