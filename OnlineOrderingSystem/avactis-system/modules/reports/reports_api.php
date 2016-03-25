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
 * Reports class
 *
 * Common API class fro all reports.
 *
 * @author Alexey Florinsky
 * @version $Id: reports_api.php 8126 2010-03-15 10:59:05Z melkor $
 * @package Reports
 */
class Reports
{
    var $report_periods_collector = null;
    var $report_periods_source = null;

    function Reports()
    {
    }

    function getMaxOrderID()
    {
        $res = execQuery('SELECT_MAX_ORDER_ID');
        return $res[0]['max_order_id'];
    }

    function getReportPeriodLabel($report_class_name)
    {
        $report_class_name = _ml_strtolower($report_class_name);

        $params = array('report_class_name' => $report_class_name);
        $row = execQuery('SELECT_REPORT_PERIOD', $params);

        if (empty($row) or !isset($row[0]) or !isset($row[0]['report_period_label']))
        {
            return DATETIME_PERIOD_UNDEFINED;
        }
        else
        {
            return $row[0]['report_period_label'];
        }
    }

    function getReportPeriodTimestamps($report_class_name)
    {
        $report_class_name = _ml_strtolower($report_class_name);
        $label = $this->getReportPeriodLabel($report_class_name);
        return $this->getTimestampPeriodByDatetimeLabel($label);
    }

    function setReportPeriodLabel($report_class_name, $period_label)
    {
        //         ,             $period_lable
        $labels = $this->getDatetimePeriodLabels(DATETIME_PERIOD_DISCONTINUITY_DAY);
        $correct_period = false;
        foreach ($labels as $l)
        {
            if (in_array($period_label, $l))
            {
                $correct_period = true;
                break;
            }
        }

        if ($correct_period)
        {
            $params = array('report_class_name' => _ml_strtolower($report_class_name), 'report_period_label' => $period_label);
            execQuery('REPLACE_REPORT_PERIOD', $params);
        }
    }


    function getDatetimePeriodLabels($min_discontinuity = DATETIME_PERIOD_DISCONTINUITY_DAY)
    {
        //                                                  ,      ,                                   ,
        //                                        .
        $periods = array(
            DATETIME_PERIOD_DISCONTINUITY_DAY   => array(
                                                            DATETIME_PERIOD_DAY_THIS,
                                                            DATETIME_PERIOD_DAY_PREVIOUS,
                                                            DATETIME_PERIOD_DAY_LAST_10,
                                                            DATETIME_PERIOD_DAY_LAST_14,
                                                            DATETIME_PERIOD_DAY_LAST_30,
                                                        ),
            DATETIME_PERIOD_DISCONTINUITY_WEEK  => array(
                                                            DATETIME_PERIOD_WEEK_THIS,
                                                            DATETIME_PERIOD_WEEK_PREVIOUS,
                                                            DATETIME_PERIOD_WEEK_LAST_2,
                                                            DATETIME_PERIOD_WEEK_LAST_4,
                                                            DATETIME_PERIOD_WEEK_LAST_10,
                                                            DATETIME_PERIOD_WEEK_LAST_30,
                                                        ),
            DATETIME_PERIOD_DISCONTINUITY_MONTH => array(
                                                            DATETIME_PERIOD_MONTH_THIS,
                                                            DATETIME_PERIOD_MONTH_PREVIOUS,
                                                            DATETIME_PERIOD_MONTH_LAST_2,
                                                            DATETIME_PERIOD_MONTH_LAST_4,
                                                            DATETIME_PERIOD_MONTH_LAST_6,
                                                            DATETIME_PERIOD_MONTH_LAST_12,
                                                         ),
            DATETIME_PERIOD_DISCONTINUITY_YEAR  => array(
                                                            DATETIME_PERIOD_YEAR_THIS,
                                                            DATETIME_PERIOD_YEAR_PREVIOUS,
                                                            DATETIME_PERIOD_YEAR_LAST_2,
                                                            DATETIME_PERIOD_YEAR_LAST_4,
                                                            DATETIME_PERIOD_YEAR_LAST_6,
                                                        ),
            DATETIME_PERIOD_DISCONTINUITY_UNDEFINED => array(DATETIME_PERIOD_UNDEFINED),
        );

        foreach ($periods as $key=>$value)
        {
            if ($key != $min_discontinuity)
            {
                unset($periods[$key]);
            }
            else
            {
                break;
            }
        }
        return $periods;
    }

    function getTimestampPeriodByDatetimeLabel($label)
    {
        $store_time = new CStoreDatetime();
        $time_periods = new CDatetimePeriods($store_time);

        if (modApiFunc('Settings','getParamValue','REPORTS_AND_STATISTICS','WEEK_FIRST_DAY') === 'MONDAY')
        {
            $week_first_day = CDATETIME_WEEK_FIRST_DAY_MONDAY;
        }
        else
        {
            $week_first_day = CDATETIME_WEEK_FIRST_DAY_SUNDAY;
        }

        switch ($label)
        {
            case DATETIME_PERIOD_DAY_THIS:
                return $time_periods->getLastDays(1, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_DAY_PREVIOUS:
                return $time_periods->getLastDays(1, CDATETIMEPERIODS_NOT_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_DAY_LAST_10:
                return $time_periods->getLastDays(10, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_DAY_LAST_14:
                return $time_periods->getLastDays(14, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_DAY_LAST_30:
                return $time_periods->getLastDays(30, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_WEEK_THIS:
                return $time_periods->getLastWeeks(1, $week_first_day, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_WEEK_PREVIOUS:
                return $time_periods->getLastWeeks(1, $week_first_day, CDATETIMEPERIODS_NOT_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_WEEK_LAST_2:
                return $time_periods->getLastWeeks(2, $week_first_day, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_WEEK_LAST_4:
                return $time_periods->getLastWeeks(4, $week_first_day, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_WEEK_LAST_10:
                return $time_periods->getLastWeeks(10, $week_first_day, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_WEEK_LAST_30:
                return $time_periods->getLastWeeks(30, $week_first_day, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_MONTH_THIS:
                return $time_periods->getLastMonths(1, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_MONTH_PREVIOUS:
                return $time_periods->getLastMonths(1, CDATETIMEPERIODS_NOT_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_MONTH_LAST_2:
                return $time_periods->getLastMonths(2, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_MONTH_LAST_4:
                return $time_periods->getLastMonths(4, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_MONTH_LAST_6:
                return $time_periods->getLastMonths(6, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_MONTH_LAST_10:
                return $time_periods->getLastMonths(10, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_MONTH_LAST_12:
                return $time_periods->getLastMonths(12, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_YEAR_THIS:
                return $time_periods->getLastYears(1, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_YEAR_PREVIOUS:
                return $time_periods->getLastYears(1, CDATETIMEPERIODS_NOT_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_YEAR_LAST_2:
                return $time_periods->getLastYears(2, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_YEAR_LAST_4:
                return $time_periods->getLastYears(4, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;

            case DATETIME_PERIOD_YEAR_LAST_6:
                return $time_periods->getLastYears(6, CDATETIMEPERIODS_INCLUDING_CURRENT_ONE);
                break;
        }
        return null;
    }

    function getReportGroups()
    {
        $report_grp=array(

                      #--------------------------------------------------------------------------------#
                      ### PRODUCTS
                      5 => array (
                                'GROUP_NAME' => getMsg('RPTS', 'REPORT_GROUP_SALES_CHART'),
                                'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_GROUP_SALES_CHART_DESCR'),
                                'REPORTS' => array(
                                                    'ChartOrdersByDay'                => REPORT_PLACEHOLDER_FULL,
                                                    'ChartOrdersByMonth'              => REPORT_PLACEHOLDER_FULL,
                                                    'ChartOrdersByYear'               => REPORT_PLACEHOLDER_FULL,
                                                  ),
                            ),
                      6 => array (
                                'GROUP_NAME' => getMsg('RPTS', 'REPORT_TAXES'),
                                'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_TAXES_DESCR'),
                                'REPORTS' => array(
                                                    'ChartTaxByDay' => REPORT_PLACEHOLDER_FULL,
                                                    'ChartTaxByMonth' => REPORT_PLACEHOLDER_FULL,
                                                    'ChartTaxByYear' => REPORT_PLACEHOLDER_FULL,
                                                ),
                            ),

                      7 => array (
                                'GROUP_NAME' => getMsg('RPTS', 'REPORT_GROUP_ORDERS_PER_VISITOR'),
                                'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_GROUP_ORDERS_PER_VISITOR_DESCR'),
                                'REPORTS' => array(
                                                    'ReportOrdersPerVisitorsRatesByDay'   => REPORT_PLACEHOLDER_FULL,
                                                    'ReportOrdersPerVisitsRatesByDay' => REPORT_PLACEHOLDER_FULL,
                                                    'ReportSalesPerVisitorsRatesByDay' => REPORT_PLACEHOLDER_FULL,
                                                    'ReportSalesPerVisitsRatesByDay' => REPORT_PLACEHOLDER_FULL,
                                                    'ReportProductSalesVsViewsByDay'               => REPORT_PLACEHOLDER_FULL,
                                                  ),
                            ),
                      8 => array (
                                'GROUP_NAME' => getMsg('RPTS', 'REPORT_GROUP_ABANDONMENT_RATES'),
                                'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_GROUP_ABANDONMENT_RATES_DESCR'),
                                'REPORTS' => array(
                                                    'ReportAbandonmentRatesByDay'   => REPORT_PLACEHOLDER_FULL,
                                                    'ReportProductAddedCartVsSalesByDay' => REPORT_PLACEHOLDER_FULL,
                                                  ),
                            ),
                      9 => array (
                                'GROUP_NAME' => getMsg('RPTS', 'REPORT_GROUP_TOP_10'),
                                'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_GROUP_TOP_10_DESCR'),
                                'REPORTS' => array(
                                                    'ReportTop10SellersByItems'             => REPORT_PLACEHOLDER_FULL,
                                                    'ReportTop10ByViews'                    => REPORT_PLACEHOLDER_FULL,
                                                    'ReportTop10AddedToCartQuantity'        => REPORT_PLACEHOLDER_FULL,
                                                    'ReportTop10DeletedFromCartQuantity'    => REPORT_PLACEHOLDER_FULL,
                                                ),
                            ),
                      10 => array (
                                'GROUP_NAME' => getMsg('RPTS', 'REPORT_GROUP_PRODUCT_ITEMS_SOLD'),
                                'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_GROUP_PRODUCT_ITEMS_SOLD_DESCR'),
                                'REPORTS' => array(
                                                    'ChartProductSalesByDay'                => REPORT_PLACEHOLDER_FULL,
                                                    'ChartProductSalesByMonth'              => REPORT_PLACEHOLDER_FULL,
                                                    'ChartProductSalesByYear'               => REPORT_PLACEHOLDER_FULL,
                                                  ),
                            ),
                      20 => array (
                                'GROUP_NAME' => getMsg('RPTS', 'REPORT_GROUP_PRODUCT_VIEWS'),
                                'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_GROUP_PRODUCT_VIEWS_DESCR'),
                                'REPORTS' => array(
                                                     'ChartProductViewsByDay'   => REPORT_PLACEHOLDER_FULL,
                                                     'ChartProductViewsByMonth' => REPORT_PLACEHOLDER_FULL,
                                                     'ChartProductViewsByYear'  => REPORT_PLACEHOLDER_FULL,
                                                ),
                            ),
//                      40 => array (
//                                'GROUP_NAME' => getMsg('RPTS', 'REPORT_GROUP_CART_VS_SALES'),
//                                'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_GROUP_CART_VS_SALES_DESCR'),
//                                'REPORTS' => array(
//                                                    'ReportProductAddedCartVsSalesByDay'               => REPORT_PLACEHOLDER_FULL,
//                                                  ),
//                            ),

//                      60 => array (
//                                'GROUP_NAME' => getMsg('RPTS', 'REPORT_GROUP_PRODUCT_SUMMARY'),
//                                'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_GROUP_PRODUCT_SUMMARY_DESCR'),
//                                'REPORTS' => array(
//                                                    'ReportProductSummaryStatisticsByDays'         => REPORT_PLACEHOLDER_FULL,
//                                                    'ReportProductSummaryStatisticsByMonths'       => REPORT_PLACEHOLDER_FULL,
//                                                    'ReportProductSummaryStatisticsByYears'        => REPORT_PLACEHOLDER_FULL,
//                                                ),
//                            ),
        		70 => array (
        				'GROUP_NAME' => getMsg('RPTS', 'REPORT_GROUP_ALL'),
        				'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_GROUP_ALL_DESCR'),
        				'REPORTS' => array(
        						'ReportAllSellersByItems'             => REPORT_PLACEHOLDER_FULL,
        						'ReportAllByViews'                    => REPORT_PLACEHOLDER_FULL,
        						'ReportAllAddedToCartQuantity'        => REPORT_PLACEHOLDER_FULL,
        						'ReportAllDeletedFromCartQuantity'    => REPORT_PLACEHOLDER_FULL,
        				),
        		),

                      #--------------------------------------------------------------------------------#
                      ### VISITORS

                      200 => array (
                                'GROUP_NAME' => getMsg('RPTS', 'REPORT_GROUP_RECENT_100_VISITORS'),
                                'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_GROUP_RECENT_100_VISITORS_DESCR'),
                                'REPORTS' => array(
                                                    'ReportRecentVisitorStatistics'  => REPORT_PLACEHOLDER_FULL,
                                                  ),
                            ),
                      230 => array (
                                'GROUP_NAME' => getMsg('RPTS', 'REPORT_TOP_10_CUSTOMERS'),
                                'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_TOP_10_CUSTOMERS_DESCR'),
                                'REPORTS' => array(
                                                    'ReportTopTenCustomerStatistics'  => REPORT_PLACEHOLDER_FULL,
                                                  ),
                            ),
                      400 => array (
                                'GROUP_NAME' => getMsg('RPTS', 'REPORT_GROUP_REFERERS'),
                                'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_GROUP_REFERERS_DESCR'),
                                'REPORTS' => array(
                                                    'ReportTopVisitorReferers'      => REPORT_PLACEHOLDER_FULL,
                                                    'ReportTopVisitorFullReferers'  => REPORT_PLACEHOLDER_FULL,
                                                  ),
                            ),
                      500 => array (
                                'GROUP_NAME' => getMsg('RPTS', 'REPORT_GROUP_TOP_PAGES'),
                                'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_GROUP_TOP_PAGES_DESCR'),
                                'REPORTS' => array(
                                                    'ReportTopViewedPages'          => REPORT_PLACEHOLDER_FULL,
                                                    //'ReportTopEntryPages'          => REPORT_PLACEHOLDER_FULL,
                                                    //'ReportTopExitPages'          => REPORT_PLACEHOLDER_FULL,
                                                    ),
                            ),
                      600 => array (
                                'GROUP_NAME' => getMsg('RPTS', 'REPORT_GROUP_FULL_VISITS_STAT'),
                                'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_GROUP_FULL_VISITS_STAT_DESCR'),
                                'REPORTS' => array(
                                                    'ReportVisitorStatisticsByDay'  => REPORT_PLACEHOLDER_FULL,
                                                    'ReportVisitsStatisticsByDay'  => REPORT_PLACEHOLDER_FULL,
                                ),
                            ),
                      700 => array (
                                'GROUP_NAME' => getMsg('RPTS', 'REPORT_GROUP_BROWSERS_OS'),
                                'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_GROUP_BROWSERS_OS_DESCR'),
                                'REPORTS' => array(
                                                    'ReportVisitorBrowsers'         => REPORT_PLACEHOLDER_FULL,
                                                    'ReportVisitorOS'               => REPORT_PLACEHOLDER_FULL,
                                                  ),
                            ),
                      800 => array (
                                'GROUP_NAME' => getMsg('RPTS', 'REPORT_GROUP_RECENT_100_CRAWLERS'),
                                'GROUP_DESCRIPTION' => getMsg('RPTS', 'REPORT_GROUP_RECENT_100_CRAWLERS_DESCR'),
                                'REPORTS' => array(
                                                    'ReportRecentCrawlersStatistics'  => REPORT_PLACEHOLDER_FULL,
                                                  ),
                            ),
		      );
	/**Hook to add new reports from any extensions **/
	$report_grp=apply_filters("az_reports_addlinks",$report_grp);

	return $report_grp;
    }

    function install()
    {
    	include_once(dirname(__FILE__)."/_includes/install.inc");
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $tbl_info = 'report_periods';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'report_class_name'     => $tbl_info.'.report_class_name',
                'report_period_label'   => $tbl_info.'.report_period_label',
            );
        $tables[$tbl_info]['types'] = array
            (
                'report_class_name'     => DBQUERY_FIELD_TYPE_CHAR255,
                'report_period_label'   => DBQUERY_FIELD_TYPE_CHAR255,
            );
        $tables[$tbl_info]['primary'] = array
            (
                'report_class_name',
            );
        $tables[$tbl_info]['indexes'] = array
            (
            );

        $tbl_info = 'reports_product_stat';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'datetime'             => $tbl_info.'.datetime',
                'product_id'           => $tbl_info.'.product_id',
                'views'                => $tbl_info.'.views',
                'sale_items'           => $tbl_info.'.sale_items',
                'added_to_cart_times'    => $tbl_info.'.added_to_cart_times',
                'deleted_from_cart_times'=> $tbl_info.'.deleted_from_cart_times',
                'added_to_cart_qty'      => $tbl_info.'.added_to_cart_qty',
                'deleted_from_cart_qty'  => $tbl_info.'.deleted_from_cart_qty',
            );
        $tables[$tbl_info]['types'] = array
            (
                'datetime'             => DBQUERY_FIELD_TYPE_DATETIME,
                'product_id'           => DBQUERY_FIELD_TYPE_INT,
                'views'                => DBQUERY_FIELD_TYPE_INT. ' NOT NULL default 0',
                'sale_items'           => DBQUERY_FIELD_TYPE_INT. ' NOT NULL default 0',
                'added_to_cart_times'  => DBQUERY_FIELD_TYPE_INT. ' NOT NULL default 0',
                'deleted_from_cart_times'=> DBQUERY_FIELD_TYPE_INT. ' NOT NULL default 0',
                'added_to_cart_qty'     => DBQUERY_FIELD_TYPE_INT. ' NOT NULL default 0',
                'deleted_from_cart_qty' => DBQUERY_FIELD_TYPE_INT. ' NOT NULL default 0',
            );
        $tables[$tbl_info]['primary'] = array
            (
                'datetime', 'product_id'
            );
        $tables[$tbl_info]['indexes'] = array
            (
            );


        $tbl_info = 'reports_product_info';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'product_id'           => $tbl_info.'.product_id',
                'product_name'         => $tbl_info.'.product_name',
            );
        $tables[$tbl_info]['types'] = array
            (
                'product_id'           => DBQUERY_FIELD_TYPE_INT,
                'product_name'         => DBQUERY_FIELD_TYPE_CHAR255,
            );
        $tables[$tbl_info]['primary'] = array
            (
                'product_id'
            );
        $tables[$tbl_info]['indexes'] = array
            (
            );

        $tbl_info = 'reports_visitor_info';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'visitor_id'         => $tbl_info.'.visitor_id',
                'visitor_session_id' => $tbl_info.'.visitor_session_id',
                'visitor_os'         => $tbl_info.'.visitor_os',
                'visitor_browser'    => $tbl_info.'.visitor_browser',
                'visitor_agent'      => $tbl_info.'.visitor_agent',
                'visitor_type'       => $tbl_info.'.visitor_type',
                'visitor_register_time'=> $tbl_info.'.visitor_register_time',
            );
        $tables[$tbl_info]['types'] = array
            (
                'visitor_id'         => DBQUERY_FIELD_TYPE_INT.' NOT NULL auto_increment',
                'visitor_session_id' => DBQUERY_FIELD_TYPE_CHAR200,
                'visitor_os'         => DBQUERY_FIELD_TYPE_CHAR100,
                'visitor_browser'    => DBQUERY_FIELD_TYPE_CHAR100,
                'visitor_agent'      => DBQUERY_FIELD_TYPE_TEXT,
                'visitor_type'       => DBQUERY_FIELD_TYPE_CHAR5,
                'visitor_register_time' => DBQUERY_FIELD_TYPE_DATETIME,
            );
        $tables[$tbl_info]['primary'] = array
            (
                'visitor_id', 'visitor_session_id'
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'IDX_register_time' => 'visitor_register_time',
                'IDX_visitor_id'    => 'visitor_id',
                'IDX_visitor_type'  => 'visitor_type',
                'IDX_visitor_session_id' => 'visitor_session_id'
            );

        $tbl_info = 'reports_visitor_seances';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'visitor_id'         => $tbl_info.'.visitor_id',
                'seance_id'          => $tbl_info.'.seance_id',
                'prev_seance_id'     => $tbl_info.'.prev_seance_id',
                'visit_number'       => $tbl_info.'.visit_number',
                'creation_time'      => $tbl_info.'.creation_time',
                'entry_page'         => $tbl_info.'.entry_page',
                'referer_host'       => $tbl_info.'.referer_host',
                'referer'            => $tbl_info.'.referer',
                'remote_ip'          => $tbl_info.'.remote_ip',
                'remote_host'        => $tbl_info.'.remote_host',
            );
        $tables[$tbl_info]['types'] = array
            (
                'visitor_id'         => DBQUERY_FIELD_TYPE_INT,
                'seance_id'          => DBQUERY_FIELD_TYPE_INT,
                'prev_seance_id'     => DBQUERY_FIELD_TYPE_INT,
                'visit_number'       => DBQUERY_FIELD_TYPE_INT,
                'creation_time'      => DBQUERY_FIELD_TYPE_DATETIME,
                'entry_page'         => DBQUERY_FIELD_TYPE_CHAR255,
                'referer_host'       => DBQUERY_FIELD_TYPE_CHAR200,
                'referer'            => DBQUERY_FIELD_TYPE_CHAR255,
                'remote_ip'          => DBQUERY_FIELD_TYPE_CHAR50,
                'remote_host'        => DBQUERY_FIELD_TYPE_CHAR255,
            );
        $tables[$tbl_info]['primary'] = array
            (
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'UNIQUE KEY UNQ_seance_id' => 'seance_id',
                'IDX_visitor_id' => 'visitor_id',
                'creation_time' => 'creation_time'
            );

        $tbl_info = 'reports_visitor_seance_info';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'idd'                => $tbl_info.'.idd',
                'seance_id'          => $tbl_info.'.seance_id',
                'page_url'           => $tbl_info.'.page_url',
                'visit_time'         => $tbl_info.'.visit_time',
                'visitors_online'    => $tbl_info.'.visitors_online'
            );
        $tables[$tbl_info]['types'] = array
            (
                'idd'                => DBQUERY_FIELD_TYPE_INT .
                                        ' NOT NULL auto_increment',
                'seance_id'          => DBQUERY_FIELD_TYPE_INT,
                'page_url'           => DBQUERY_FIELD_TYPE_INT,
                'visit_time'         => DBQUERY_FIELD_TYPE_DATETIME,
                'visitors_online'    => DBQUERY_FIELD_TYPE_INT
            );
        $tables[$tbl_info]['primary'] = array
            (
                'idd'
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'IDX_sid'          => 'seance_id',
                'IDX_visit_time'   => 'visit_time',
                'IDX_page_url'     => 'page_url'
            );

        $tbl_info = 'reports_page_urls';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'id'       => $tbl_info.'.id',
                'page_url' => $tbl_info.'.page_url',
            );
        $tables[$tbl_info]['types'] = array
            (
                'id'       => DBQUERY_FIELD_TYPE_INT. ' NOT NULL auto_increment',
                'page_url' => DBQUERY_FIELD_TYPE_TEXT,
            );
        $tables[$tbl_info]['primary'] = array
            (
                'id'
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'IDX_page' => 'page_url (256)'
            );

        $tbl_info = 'reports_carts_stat';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'datetime'              => $tbl_info.'.datetime',
                'carts_created_qty'     => $tbl_info.'.carts_created_qty',
            );
        $tables[$tbl_info]['types'] = array
            (
                'datetime'              => DBQUERY_FIELD_TYPE_DATETIME,
                'carts_created_qty'     => DBQUERY_FIELD_TYPE_INT. ' NOT NULL default 0',
            );
        $tables[$tbl_info]['primary'] = array
            (
                'datetime'
            );
        $tables[$tbl_info]['indexes'] = array
            (
            );

        $tbl_info = 'reports_orders_stat';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'order_stat_id'             => $tbl_info.'.order_stat_id',
                'order_id'                  => $tbl_info.'.order_id',
                'order_status_id'           => $tbl_info.'.order_status_id',
                'order_payment_status_id'   => $tbl_info.'.order_payment_status_id',
                'order_deleted'             => $tbl_info.'.order_deleted',
                'order_datetime'            => $tbl_info.'.order_datetime',
                'order_total'               => $tbl_info.'.order_total',
                'order_tax_total'           => $tbl_info.'.order_tax_total',
                'order_currency'            => $tbl_info.'.order_currency',
            );
        $tables[$tbl_info]['types'] = array
            (
                'order_stat_id'             => DBQUERY_FIELD_TYPE_INT. ' NOT NULL auto_increment',
                'order_id'                  => DBQUERY_FIELD_TYPE_INT. ' NOT NULL DEFAULT 0',
                'order_status_id'           => DBQUERY_FIELD_TYPE_INT. ' NOT NULL DEFAULT 0',
                'order_payment_status_id'   => DBQUERY_FIELD_TYPE_INT. ' NOT NULL DEFAULT 0',
                'order_deleted'             => DBQUERY_FIELD_TYPE_INT. ' NOT NULL DEFAULT 0',
                'order_datetime'            => DBQUERY_FIELD_TYPE_DATETIME. ' NOT NULL DEFAULT \'0000-00-00 00:00\'',
                'order_total'               => DBQUERY_FIELD_TYPE_FLOAT. ' NOT NULL default 0',
                'order_tax_total'           => DBQUERY_FIELD_TYPE_FLOAT. ' NOT NULL default 0',
                'order_currency'            => DBQUERY_FIELD_TYPE_INT. ' NOT NULL default 0',
            );
        $tables[$tbl_info]['primary'] = array
            (
                'order_stat_id'
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'IDX_datetime' => 'order_datetime'
            );

        $tbl_info = 'reports_orders_products_stat';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'order_id'                  => $tbl_info.'.order_id',
                'product_id'                => $tbl_info.'.product_id',
                'amount'                    => $tbl_info.'.amount',
            );
        $tables[$tbl_info]['types'] = array
            (
                'order_id'                  => DBQUERY_FIELD_TYPE_INT. ' NOT NULL DEFAULT 0',
                'product_id'                => DBQUERY_FIELD_TYPE_INT. ' NOT NULL DEFAULT 0',
                'amount'                    => DBQUERY_FIELD_TYPE_INT. ' NOT NULL DEFAULT 0',
            );
        $tables[$tbl_info]['primary'] = array
            (
                'order_id',
                'product_id'
            );

        $tbl_info = 'reports_crawlers_info';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'id'                        => $tbl_info.'.id',
                'agent_string'              => $tbl_info.'.agent_string',
                'name'                      => $tbl_info.'.name',
                'type'                      => $tbl_info.'.type'
            );
        $tables[$tbl_info]['types'] = array
            (
                'id'                        => DBQUERY_FIELD_TYPE_INT. ' NOT NULL auto_increment',
                'agent_string'              => DBQUERY_FIELD_TYPE_TEXT. ' NOT NULL ',
                'name'                      => DBQUERY_FIELD_TYPE_CHAR255. ' NOT NULL DEFAULT ""',
                'type'                      => DBQUERY_FIELD_TYPE_CHAR5. 'NOT NULL DEFAULT "R"'
            );
        $tables[$tbl_info]['primary'] = array
            (
                'id'
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'IDX_agent' => 'agent_string (200)'
            );

        $tbl_info = 'reports_crawlers_visits';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'visitor_id'                => $tbl_info.'.visitor_id',
                'visit_time'                => $tbl_info.'.visit_time',
                'agent_string'              => $tbl_info.'.agent_string',
                'name'                      => $tbl_info.'.name',
                'type'                      => $tbl_info.'.type',
                'ip'                        => $tbl_info.'.ip',
                'host'                      => $tbl_info.'.host',
                'referrer'                  => $tbl_info.'.referrer',
                'entry_page'                => $tbl_info.'.entry_page',
                'scanned_pages'              => $tbl_info.'.scanned_pages'
            );
        $tables[$tbl_info]['types'] = array
            (
                'visitor_id'                => DBQUERY_FIELD_TYPE_INT. ' NOT NULL auto_increment',
                'visit_time'                => DBQUERY_FIELD_TYPE_DATETIME .' NOT NULL DEFAULT "0000-00-00 00:00"',
                'agent_string'              => DBQUERY_FIELD_TYPE_TEXT. ' NOT NULL ',
                'name'                      => DBQUERY_FIELD_TYPE_CHAR255. ' NOT NULL DEFAULT ""',
                'type'                      => DBQUERY_FIELD_TYPE_CHAR5. ' NOT NULL DEFAULT "R"',
                'ip'                        => DBQUERY_FIELD_TYPE_CHAR20. ' NOT NULL DEFAULT "0.0.0.0"',
                'host'                      => DBQUERY_FIELD_TYPE_CHAR255. ' NOT NULL DEFAULT ""',
                'referrer'                  => DBQUERY_FIELD_TYPE_CHAR255. ' NOT NULL DEFAULT ""',
                'entry_page'                => DBQUERY_FIELD_TYPE_CHAR255. ' NOT NULL DEFAULT ""',
                'scanned_pages'             => DBQUERY_FIELD_TYPE_LONGTEXT. ' NOT NULL '
            );
        $tables[$tbl_info]['primary'] = array
            (
                'visitor_id'
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'IDX_visit_time'   => 'visit_time',
                'IDX_name'         => 'name'
                #'IDX_scaned_pages' => 'scaned_pages (300)'
            );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function clearAllStat()
    {

        global $application;
        global $zone;

        if ($zone == 'AdminZone')
        {
            $tables = array(
                'reports_product_stat',
                'reports_product_info',
                'reports_visitor_info',
                'reports_visitor_seances',
                'reports_visitor_seance_info',
                'reports_carts_stat',
                'reports_orders_stat',
                'reports_orders_products_stat',
                'reports_crawlers_visits'
            );
            $prefix = $application->getAppIni("DB_TABLE_PREFIX");
            foreach ($tables as $table_name)
            {
                $param = array("table" => $prefix.$table_name);
            	execQuery("RESET_REPORT_TABLE_DATA",$param);
            }
        }
    }

    function clearVisitorStat()
    {
	    
	    global $application;
	    global $zone;

	    if ($zone == 'AdminZone')
	    {
		    $tables = array(
				    'reports_visitor_info',
				    'reports_visitor_seances',
				    'reports_visitor_seance_info',
				    'reports_crawlers_visits'
				   );
		    $prefix = $application->getAppIni("DB_TABLE_PREFIX");
		    foreach ($tables as $table_name)
		    {
			    $param = array("table" => $prefix.$table_name);
			    execQuery("RESET_REPORT_TABLE_DATA",$param);
		    }
	    }
    }

    function clearSaleStat()
    {
	    
	    global $application;
	    global $zone;

	    if ($zone == 'AdminZone')
	    {
		    $tables = array(
				    'reports_product_stat',
				    'reports_product_info',
				    'reports_carts_stat',
				    'reports_orders_stat',
				    'reports_orders_products_stat',
				   );
		    $prefix = $application->getAppIni("DB_TABLE_PREFIX");
		    foreach ($tables as $table_name)
		    {
			    $param = array("table" => $prefix.$table_name);
			    execQuery("RESET_REPORT_TABLE_DATA",$param);
		    }
	    }
    }

    /**
     * Returns the page id from reports_page_urls table
     * If page is not found it creates it
     */
    function getPageURLID($page_url)
    {
        global $application;

        $page_id = execQuery('SELECT_REPORT_PAGE_URL_ID',
                             array('page_url' => $page_url));

        if ($page_id)
            return $page_id[0]['id'];

        execQuery('INSERT_REPORT_PAGE_URL',
                  array('page_url' => $page_url));

        return $application -> db -> DB_Insert_Id();
    }

    /**
     * Returns the array of pages from reports_page_urls table
     * DOES NOT add any page
     */
    function getPagesByIDs($ids)
    {
        $result = array();

        $pages = execQuery('SELECT_REPORTS_PAGES_BY_IDS',
                           array('ids' => $ids));

        if (is_array($pages))
            foreach($pages as $v)
                $result[$v['id']] = $v['page_url'];

        return $result;
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Reports::getTables());
        global $application;
        $application->db->getDB_Result($query);
    }


}

?>