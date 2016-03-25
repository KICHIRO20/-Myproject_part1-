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

class ReportOrdersPerVisitsRatesByDay extends CReportView
{
    function ReportOrdersPerVisitsRatesByDay()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderStockChart';
        $this->__source_class_name = 'COrdersVisitorsCartsStatisticsByDays';
        $this->__render_settings_class_name = 'CReportRenderSettings';
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_DAY;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_ORDERS_PER_VISITS');
    }

    function setColumns()
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderchart':
                $this->__render_settings->setColumnList( array(
                                                            'date',
                                                            'order_qty',
                                                            'seance_number',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'date' => getMsg('RPTS','DATE'),
                                                            'order_qty' => getMsg('RPTS','ORDERS_QTY'),
                                                            'seance_number' => getMsg('RPTS','VISITOR_SEANCES_QTY'),
                ));
                $this->__render_settings->setChartUnit($this->__currency);
                break;

            default:
                $this->__render_settings->setColumnList( array(
                                                            'date',
                                                            'order_qty',
                                                            'seance_number',
                                                            'orders_per_visits_rate',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'date' => getMsg('RPTS','DATE'),
                                                            'order_qty' => getMsg('RPTS','ORDERS_QTY'),
                                                            'seance_number' => getMsg('RPTS','VISITOR_SEANCES_QTY'),
                                                            'orders_per_visits_rate' => getMsg('RPTS','ORDERS_PER_VISIT'),
                ));
                $this->__render_settings->setColumnUnits( array(
                                                            'date' => '',
                                                            'order_qty' => getMsg('RPTS','ORDERS_QTY_UNIT'),
                                                            'seance_number' => getMsg('RPTS','VISITOR_SEANCES_QTY_UNIT'),
                                                            'orders_per_visits_rate' => getMsg('RPTS','ORDERS_PER_VISIT_UNIT'),
                ));
                $this->__render_settings->setChartUnit($this->__currency);
                break;
        }
    }

    function __formatRow($row)
    {
        $row['date'] = $this->__prepareDateToDisplay($row);

        if ($row['seance_number'] == 0)
        {
            $row['orders_per_visits_rate'] = '0';
        }
        else
        {
            $row['orders_per_visits_rate'] = round(100*($row['order_qty']/$row['seance_number']),2);
        }

        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $row['orders_per_visits_rate'] .= '%';
                $row['order_qty'] = modApiFunc("Localization", "num_format", $row['order_qty']);
                $row['seance_number'] = modApiFunc("Localization", "num_format", $row['seance_number']);
                break;
            case 'creportrenderchart':
                break;
            case 'creportrendercsv':
                //                           Stock Chart'
                $date = mktime(0,0,0,$row['datetime_month'], $row['datetime_day'], $row['datetime_year']);
                $row['date'] = date('Y-m-d',$date);
                break;
            default:
                $row['orders_per_visits_rate'] .= '%';
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


class ReportOrdersPerVisitorsRatesByDay extends CReportView
{
    function ReportOrdersPerVisitorsRatesByDay()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderStockChart';
        $this->__source_class_name = 'COrdersVisitorsCartsStatisticsByDays';
        $this->__render_settings_class_name = 'CReportRenderSettings';
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_DAY;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_ORDERS_PER_VISITORS');
    }

    function setColumns()
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderchart':
                $this->__render_settings->setColumnList( array(
                                                            'date',
                                                            'order_qty',
                                                            'visitor_number',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'date' => getMsg('RPTS','DATE'),
                                                            'order_qty' => getMsg('RPTS','ORDERS_QTY'),
                                                            'visitor_number' => getMsg('RPTS','VISITORS_QTY'),
                ));
                $this->__render_settings->setChartUnit($this->__currency);
                break;

            default:
                $this->__render_settings->setColumnList( array(
                                                            'date',
                                                            'order_qty',
                                                            'visitor_number',
                                                            'orders_per_visitors_rate',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'date' => getMsg('RPTS','DATE'),
                                                            'order_qty' => getMsg('RPTS','ORDERS_QTY'),
                                                            'visitor_number' => getMsg('RPTS','VISITORS_QTY'),
                                                            'orders_per_visitors_rate' => getMsg('RPTS','ORDERS_PER_VISITORS'),
                ));
                $this->__render_settings->setColumnUnits( array(
                                                            'date' => '',
                                                            'order_qty' => '',
                                                            'visitor_number' => '',
                                                            'orders_per_visitors_rate' => ' %',
                ));
                $this->__render_settings->setChartUnit($this->__currency);
                break;
        }
    }

    function __formatRow($row)
    {
        $row['date'] = $this->__prepareDateToDisplay($row);

        if ($row['visitor_number'] == 0)
        {
            $row['orders_per_visitors_rate'] = '0';
        }
        else
        {
            $row['orders_per_visitors_rate'] = round(100*($row['order_qty']/$row['visitor_number']),2);
        }

        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $row['orders_per_visitors_rate'] .= '%';
                $row['order_qty'] = modApiFunc("Localization", "num_format", $row['order_qty']);
                $row['visitor_number'] = modApiFunc("Localization", "num_format", $row['visitor_number']);
                break;
            case 'creportrenderchart':
                break;
            case 'creportrendercsv':
                //                           Stock Chart'
                $date = mktime(0,0,0,$row['datetime_month'], $row['datetime_day'], $row['datetime_year']);
                $row['date'] = date('Y-m-d',$date);
                break;
            default:
                $row['orders_per_visitors_rate'] .= '%';
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


class ReportSalesPerVisitorsRatesByDay extends CReportView
{
    function ReportSalesPerVisitorsRatesByDay()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderStockChart';
        $this->__source_class_name = 'COrdersVisitorsCartsStatisticsByDays';
        $this->__render_settings_class_name = 'CReportRenderSettings';
        $this->__currency = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getMainStoreCurrency"));
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_DAY;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_SALES_PER_VISITORS').', '.$this->__currency;
    }

    function setColumns()
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderchart':
                $this->__render_settings->setColumnList( array(
                                                            'date',
                                                            'order_total_sum',
                                                            'visitor_number',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'date' => getMsg('RPTS','DATE'),
                                                            'order_total_sum' => getMsg('RPTS','SALES').', '.$this->__currency,
                                                            'visitor_number' => getMsg('RPTS','VISITORS_QTY'),
                ));
                $this->__render_settings->setChartUnit($this->__currency);
                break;

            default:
                $this->__render_settings->setColumnList( array(
                                                            'date',
                                                            'order_total_sum',
                                                            'visitor_number',
                                                            'sales_per_visitors_rate',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'date' => getMsg('RPTS','DATE'),
                                                            'order_total_sum' => getMsg('RPTS','SALES').', '.$this->__currency,
                                                            'visitor_number' => getMsg('RPTS','VISITORS_QTY'),
                                                            'sales_per_visitors_rate' => getMsg('RPTS','SALES_PER_VISITORS').', '.$this->__currency,
                ));
                $this->__render_settings->setColumnUnits( array(
                                                            'date' => '',
                                                            'order_total_sum' => '',
                                                            'visitor_number' => '',
                                                            'sales_per_visitors_rate' => ''
                ));
                $this->__render_settings->setChartUnit($this->__currency);
                break;
        }
    }

    function __formatRow($row)
    {
        $row['date'] = $this->__prepareDateToDisplay($row);

        if ($row['visitor_number'] == 0)
        {
            $row['sales_per_visitors_rate'] = '0';
        }
        else
        {
            $row['sales_per_visitors_rate'] = round(($row['order_total_sum']/$row['visitor_number']),2);
        }

        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $row['order_total_sum'] = modApiFunc("Localization", "num_format", $row['order_total_sum']);
                $row['visitor_number'] = modApiFunc("Localization", "num_format", $row['visitor_number']);
                break;
            case 'creportrenderchart':
                break;
            case 'creportrendercsv':
                //                           Stock Chart'
                $date = mktime(0,0,0,$row['datetime_month'], $row['datetime_day'], $row['datetime_year']);
                $row['date'] = date('Y-m-d',$date);
                break;
            default:
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

class ReportSalesPerVisitsRatesByDay extends CReportView
{
    function ReportSalesPerVisitsRatesByDay()
    {
        parent::CReportView();
        $this->__render_class_name = 'CReportRenderStockChart';
        $this->__source_class_name = 'COrdersVisitorsCartsStatisticsByDays';
        $this->__render_settings_class_name = 'CReportRenderSettings';
        $this->__currency = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getMainStoreCurrency"));
    }

    function getDatetimePeriodSelectorMinDiscontinuity()
    {
        return DATETIME_PERIOD_DISCONTINUITY_DAY;
    }

    function getReportName()
    {
        return getMsg('RPTS','REPORT_SALES_PER_VISITS').', '.$this->__currency;
    }

    function setColumns()
    {
        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderchart':
                $this->__render_settings->setColumnList( array(
                                                            'date',
                                                            'order_total_sum',
                                                            'seance_number',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'date' => getMsg('RPTS','DATE'),
                                                            'order_total_sum' => getMsg('RPTS','SALES').', '.$this->__currency,
                                                            'seance_number' => getMsg('RPTS','VISITS_QTY'),
                ));
                $this->__render_settings->setChartUnit($this->__currency);
                break;

            default:
                $this->__render_settings->setColumnList( array(
                                                            'date',
                                                            'order_total_sum',
                                                            'seance_number',
                                                            'sales_per_visits_rate',
                ));

                $this->__render_settings->setColumnHeaders( array(
                                                            'date' => getMsg('RPTS','DATE'),
                                                            'order_total_sum' => getMsg('RPTS','SALES').', '.$this->__currency,
                                                            'seance_number' => getMsg('RPTS','VISITS_QTY'),
                                                            'sales_per_visits_rate' => getMsg('RPTS','SALES_PER_VISITS').', '.$this->__currency,
                ));
                $this->__render_settings->setColumnUnits( array(
                                                            'date' => '',
                                                            'order_total_sum' => '',
                                                            'seance_number' => '',
                                                            'sales_per_visits_rate' => ''
                ));
                $this->__render_settings->setChartUnit($this->__currency);
                break;
        }
    }

    function __formatRow($row)
    {
        $row['date'] = $this->__prepareDateToDisplay($row);

        if ($row['seance_number'] == 0)
        {
            $row['sales_per_visits_rate'] = '0';
        }
        else
        {
            $row['sales_per_visits_rate'] = round(($row['order_total_sum']/$row['seance_number']),2);
        }

        switch(_ml_strtolower($this->__render_class_name))
        {
            case 'creportrenderflattable':
                $row['order_total_sum'] = modApiFunc("Localization", "num_format", $row['order_total_sum']);
                $row['seance_number'] = modApiFunc("Localization", "num_format", $row['seance_number']);
                break;
            case 'creportrenderchart':
                break;
            case 'creportrendercsv':
                //                           Stock Chart'
                $date = mktime(0,0,0,$row['datetime_month'], $row['datetime_day'], $row['datetime_year']);
                $row['date'] = date('Y-m-d',$date);
                break;
            default:
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