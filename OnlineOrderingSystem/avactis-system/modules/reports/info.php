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
 * Reports module meta info.
 *
 * @package Reports
 * @author Alexey Florinsky
 * @version $Id: info.php 5629 2008-09-19 10:12:41Z af $
 */
$extapi=array(
        'CProductStatisticCollector' => 'report-collectors/report_data_product_stat_collector.php',
        'CVisitorStatisticCollector' => 'report-collectors/report_data_visitors_stat_collector.php',
        'COrdersStatisticCollector' => 'report-collectors/report_data_orders_stat_collector.php',
        'CCartsStatisticCollector'  => 'report-collectors/report_data_carts_stat_collector.php',

        'CProductStatisticsByYears' => 'report-sources/report_data_product_stat_source.php',
        'CProductStatisticsByDays' => 'report-sources/report_data_product_stat_source.php',
        'CProductStatisticsByMonths' => 'report-sources/report_data_product_stat_source.php',
        'CProductSoldStatisticsByYears' => 'report-sources/report_data_product_stat_source.php',
        'CProductSoldStatisticsByDays' => 'report-sources/report_data_product_stat_source.php',
        'CProductSoldStatisticsByMonths' => 'report-sources/report_data_product_stat_source.php',
        'CProductSoldExtendedStatisticsByDays' => 'report-sources/report_data_product_stat_source.php',

        'CVisitorBrowserStatistics' => 'report-sources/report_data_visitor_stat_source.php',
        'CVisitorOSStatistics' => 'report-sources/report_data_visitor_stat_source.php',
        'CVisitorTopRefererStatistics' => 'report-sources/report_data_visitor_stat_source.php',
        'CVisitorTopViewedPageStatistics' => 'report-sources/report_data_visitor_stat_source.php',
        'CTopTenCustomerStatistics'   => 'report-sources/report_top_10_customer_stat_source.php',

        'CVisitorStatisticsByDatetimePeriod' => 'report-sources/report_data_visitor_stat_source.php',
        'CVisitorStatisticsByDays' => 'report-sources/report_data_visitor_stat_source.php',
        'CVisitorStatisticsByMonths' => 'report-sources/report_data_visitor_stat_source.php',
        'CVisitorStatisticsByYears' => 'report-sources/report_data_visitor_stat_source.php',

        'CVisitorRecentVisitorStatistics'  => 'report-sources/report_data_visitor_stat_source.php',
        'CVisitorRecentCrawlerStatistics'  => 'report-sources/report_data_visitor_stat_source.php',
        'CSeanceClickPathBySeanceId'  => 'report-sources/report_data_visitor_stat_source.php',
        'CScannedPagesByCrawler'  => 'report-sources/report_data_visitor_stat_source.php',

        'CProductAllSellersByItems' => 'report-sources/report_data_product_stat_source.php',
        'CProductAllSellersByItemsFiltered' => 'report-sources/report_data_product_stat_source.php',
        'CProductAllViewed' => 'report-sources/report_data_product_stat_source.php',
        'CProductAllAddedToCartTimes' => 'report-sources/report_data_product_stat_source.php',
        'CProductAllDeletedFromCartTimes' => 'report-sources/report_data_product_stat_source.php',
        'CProductAllAddedToCartQuantity' => 'report-sources/report_data_product_stat_source.php',
        'CProductAllDeletedFromCartQuantity' => 'report-sources/report_data_product_stat_source.php',

        'CProductTop10SellersByItems' => 'report-sources/report_data_product_stat_source.php',
        'CProductTop10SellersByItemsFiltered' => 'report-sources/report_data_product_stat_source.php',
        'CProductTop10Viewed' => 'report-sources/report_data_product_stat_source.php',
        'CProductTop10AddedToCartTimes' => 'report-sources/report_data_product_stat_source.php',
        'CProductTop10DeletedFromCartTimes' => 'report-sources/report_data_product_stat_source.php',
        'CProductTop10AddedToCartQuantity' => 'report-sources/report_data_product_stat_source.php',
        'CProductTop10DeletedFromCartQuantity' => 'report-sources/report_data_product_stat_source.php',
        'CProductSummaryStatistics' => 'report-sources/report_data_product_stat_source.php',

        'COrderTotalStatisticsByDatetimePeriod' => 'report-sources/report_data_order_stat_source.php',
        'COrderTotalStatisticsByDays' => 'report-sources/report_data_order_stat_source.php',
        'COrderTotalStatisticsByMonths' => 'report-sources/report_data_order_stat_source.php',
        'COrderTotalStatisticsByYears' => 'report-sources/report_data_order_stat_source.php',

        'COrdersVisitorsCartsStatisticsByDatetimePeriod' => 'report-sources/report_data_order_stat_source.php',
        'COrdersVisitorsCartsStatisticsByDays' => 'report-sources/report_data_order_stat_source.php',
        'COrdersVisitorsCartsStatisticsByMonths' => 'report-sources/report_data_order_stat_source.php',
        'COrdersVisitorsCartsStatisticsByYears' => 'report-sources/report_data_order_stat_source.php',

        'CReportRenderFlatTable' => 'report-renders/report_render_flat_table.php',
        'CReportRenderSettings' => 'abstract/report_render_settings.php',
        'CReportRenderChart' => 'report-renders/report_render_chart.php',
        'CReportRenderSimpleHTMLTable' => 'report-renders/report_render_simple_table.php',
        'CReportRenderBinaryExcel'=> 'report-renders/report_render_binary_excel.php',
        'CReportRenderCSV' => 'report-renders/report_render_csv.php',
        'CReportRenderStockChart' => 'report-renders/report_render_stock_chart.php',
    );



$adminzoneview=array
         (
            'ReportTop10SellersByItems' => 'reports_top_10.php',
            'ReportTop10ByViews'  => 'reports_top_10.php',
            'ReportTop10AddedToCartTimes' => 'reports_top_10.php',
            'ReportTop10DeletedFromCartTimes' => 'reports_top_10.php',
            'ReportTop10AddedToCartQuantity' => 'reports_top_10.php',
            'ReportTop10DeletedFromCartQuantity' => 'reports_top_10.php',
            'ReportTop10SellersByItemsLast30Days' => 'reports_top_10.php',

         		'ReportAllSellersByItems' => 'reports_products.php',
         		'ReportAllByViews'  => 'reports_products.php',
         		'ReportAllAddedToCartTimes' => 'reports_products.php',
         		'ReportAllDeletedFromCartTimes' => 'reports_products.php',
         		'ReportAllAddedToCartQuantity' => 'reports_products.php',
         		'ReportAllDeletedFromCartQuantity' => 'reports_products.php',
         		'ReportAllSellersByItemsLast30Days' => 'reports_products.php',

            'ReportProductSummaryStatisticsByDays' => 'reports_summary_product_stat.php',
            'ReportProductSummaryStatisticsByMonths' => 'reports_summary_product_stat.php',
            'ReportProductSummaryStatisticsByYears' => 'reports_summary_product_stat.php',

            'ChartProductSalesByDay' => 'charts_product_sales_by_period.php',
            'ChartProductSalesByMonth' => 'charts_product_sales_by_period.php',
            'ChartProductSalesByYear' => 'charts_product_sales_by_period.php',

            'ReportProductSalesVsViewsByDay' => 'reports_product_views_vs_sales_by_period.php',

            'ReportProductAddedCartVsSalesByDay' => 'reports_product_cart_vs_sales_by_period.php',

            'ChartProductViewsByDay' => 'charts_product_views_by_period.php',
            'ChartProductViewsByMonth' => 'charts_product_views_by_period.php',
            'ChartProductViewsByYear' => 'charts_product_views_by_period.php',

            'ReportVisitorBrowsers' => 'reports_visitor_browsers.php',
            'ReportVisitorOS' => 'reports_visitor_os.php',
            'ReportTopVisitorReferers' => 'reports_top_referers.php',
            'ReportTopVisitorFullReferers' => 'reports_top_referers.php',
            'ReportTopViewedPages' => 'reports_top_pages.php',

            'ReportVisitorStatisticsByDay' => 'reports_visitor_statistics.php',
            'ReportVisitsStatisticsByDay' => 'reports_visitor_statistics.php',

            'ReportRecentVisitorStatistics' => 'reports_recent_visitors.php',
            'ReportRecentCrawlersStatistics' => 'reports_recent_visitors.php',
            'ReportRecentVisitorStatisticsShort' => 'reports_recent_visitors.php',
            'ReportTopTenCustomerStatistics' => 'reports_top_10_customers.php',

            'ReportSeanceClickPath' => 'reports_seance_click_path.php',

            'ChartVisitorsByDay' => 'charts_visitor_statistics.php',
            'ChartFirstTimeVisitorsByDay'  => 'charts_visitor_statistics.php',
            'ChartRepeatVisitorsByDay' => 'charts_visitor_statistics.php',
            'ChartSeancesByDay' => 'charts_visitor_statistics.php',
            'ChartPageViewsByDay' => 'charts_visitor_statistics.php',

            'ChartOrdersByDay' => 'charts_orders_by_period.php',
            'ChartOrdersByMonth' => 'charts_orders_by_period.php',
            'ChartOrdersByYear' => 'charts_orders_by_period.php',
            'ChartOrdersByDayLast10Days' => 'charts_orders_by_period.php',
            'ChartOrdersByDayLast10Months' => 'charts_orders_by_period.php',

            'ChartTaxByDay' => 'charts_tax_by_period.php',
            'ChartTaxByMonth' => 'charts_tax_by_period.php',
            'ChartTaxByYear' => 'charts_tax_by_period.php',

            'ReportAbandonmentRatesByDay' => 'reports_abandonment_rates_by_period.php',

            'ReportOrdersPerVisitorsRatesByDay' => 'reports_orders_per_visitors_by_period.php',
            'ReportOrdersPerVisitsRatesByDay' => 'reports_orders_per_visitors_by_period.php',
            'ReportSalesPerVisitorsRatesByDay'=> 'reports_orders_per_visitors_by_period.php',
            'ReportSalesPerVisitsRatesByDay'=> 'reports_orders_per_visitors_by_period.php',

            'ReportGroups' => 'report_groups.php',
            'ReportGroupPage' => 'report_group_page.php',

            'DatetimePeriodSelector' => 'datetime_period_selector.php',

            'StatisticsPageViewsTotal'      => 'one_tag_reports.php',
            'StatisticsPageViewsThisDay'    => 'one_tag_reports.php',
            'StatisticsPageViewsLastDay'    => 'one_tag_reports.php',
            'StatisticsPageViewsThisWeek'   => 'one_tag_reports.php',
            'StatisticsPageViewsLastWeek'   => 'one_tag_reports.php',
            'StatisticsPageViewsThisMonth'  => 'one_tag_reports.php',
            'StatisticsPageViewsLastMonth'  => 'one_tag_reports.php',
            'StatisticsPageViewsThisYear'   => 'one_tag_reports.php',
            'StatisticsPageViewsLastYear'   => 'one_tag_reports.php',

            'StatisticsVisitsTotal'         => 'one_tag_reports.php',
            'StatisticsVisitsThisDay'       => 'one_tag_reports.php',
            'StatisticsVisitsLastDay'       => 'one_tag_reports.php',
            'StatisticsVisitsThisWeek'      => 'one_tag_reports.php',
            'StatisticsVisitsLastWeek'      => 'one_tag_reports.php',
            'StatisticsVisitsThisMonth'     => 'one_tag_reports.php',
            'StatisticsVisitsLastMonth'     => 'one_tag_reports.php',
            'StatisticsVisitsThisYear'      => 'one_tag_reports.php',
            'StatisticsVisitsLastYear'      => 'one_tag_reports.php',

            'StatisticsVisitorsTotal'       => 'one_tag_reports.php',
            'StatisticsVisitorsThisDay'     => 'one_tag_reports.php',
            'StatisticsVisitorsLastDay'     => 'one_tag_reports.php',
            'StatisticsVisitorsThisWeek'    => 'one_tag_reports.php',
            'StatisticsVisitorsLastWeek'    => 'one_tag_reports.php',
            'StatisticsVisitorsThisMonth'   => 'one_tag_reports.php',
            'StatisticsVisitorsLastMonth'   => 'one_tag_reports.php',
            'StatisticsVisitorsThisYear'    => 'one_tag_reports.php',
            'StatisticsVisitorsLastYear'    => 'one_tag_reports.php',

            'StatisticsSalesTotalToday'     => 'one_tag_reports.php',
            'StatisticsOrdersNumberToday'   => 'one_tag_reports.php',
            'StatisticsVisitorsOnlineRaw'   => 'one_tag_reports.php',
            'StatisticsVisitorsOnline'      => 'one_tag_reports.php',
            'StatisticsVisitorsOnlineYesterday'  => 'one_tag_reports.php',
            'StatisticsMaxVisitorsOnline'        => 'one_tag_reports.php',
            'StatisticsMaxUniqueVisitors'        => 'one_tag_reports.php',
            'StatisticsAverageVisitorsPerDay'    => 'one_tag_reports.php',

            'ReportsResetData' => 'reports_reset_data.php',

         );

$moduleInfo = array
(
    'name' => 'Reports',
    'shortName' => 'RPTS',
    'groups' => 'Main',
    'description' => 'Reports module',
    'version' => '0.1.47700',
    'author' => 'Alexey Florinsky',
    'contact' => '',
    'systemModule' => true,
    'mainFile' => 'reports_api.php',
    'constantsFile' => 'const.php',
    'resFile'      => 'reports-messages',
    'extraAPIFiles' => $extapi,
    'actions'       => array(
         'AdminZone' => array(
             'ClearStat' => 'clear_stat.php'
         ),
         'getReportContent' => 'getReportContent.php',
         ),
    'views' => array
    (
         'AdminZone' =>$adminzoneview,
         'CustomerZone' => array
         (
            'StatisticsPageViewsTotal'      => 'one_tag_reports.php',
            'StatisticsPageViewsThisDay'    => 'one_tag_reports.php',
            'StatisticsPageViewsLastDay'    => 'one_tag_reports.php',
            'StatisticsPageViewsThisWeek'   => 'one_tag_reports.php',
            'StatisticsPageViewsLastWeek'   => 'one_tag_reports.php',
            'StatisticsPageViewsThisMonth'  => 'one_tag_reports.php',
            'StatisticsPageViewsLastMonth'  => 'one_tag_reports.php',
            'StatisticsPageViewsThisYear'   => 'one_tag_reports.php',
            'StatisticsPageViewsLastYear'   => 'one_tag_reports.php',

            'StatisticsVisitsTotal'         => 'one_tag_reports.php',
            'StatisticsVisitsThisDay'       => 'one_tag_reports.php',
            'StatisticsVisitsLastDay'       => 'one_tag_reports.php',
            'StatisticsVisitsThisWeek'      => 'one_tag_reports.php',
            'StatisticsVisitsLastWeek'      => 'one_tag_reports.php',
            'StatisticsVisitsThisMonth'     => 'one_tag_reports.php',
            'StatisticsVisitsLastMonth'     => 'one_tag_reports.php',
            'StatisticsVisitsThisYear'      => 'one_tag_reports.php',
            'StatisticsVisitsLastYear'      => 'one_tag_reports.php',

            'StatisticsVisitorsTotal'       => 'one_tag_reports.php',
            'StatisticsVisitorsThisDay'     => 'one_tag_reports.php',
            'StatisticsVisitorsLastDay'     => 'one_tag_reports.php',
            'StatisticsVisitorsThisWeek'    => 'one_tag_reports.php',
            'StatisticsVisitorsLastWeek'    => 'one_tag_reports.php',
            'StatisticsVisitorsThisMonth'   => 'one_tag_reports.php',
            'StatisticsVisitorsLastMonth'   => 'one_tag_reports.php',
            'StatisticsVisitorsThisYear'    => 'one_tag_reports.php',
            'StatisticsVisitorsLastYear'    => 'one_tag_reports.php',

            'StatisticsSalesTotalToday'     => 'one_tag_reports.php',
            'StatisticsOrdersNumberToday'   => 'one_tag_reports.php',
            'StatisticsVisitorsOnlineRaw'   => 'one_tag_reports.php',
            'StatisticsVisitorsOnline'      => 'one_tag_reports.php',
            'StatisticsVisitorsOnlineYesterday'  => 'one_tag_reports.php',
            'StatisticsMaxVisitorsOnline'        => 'one_tag_reports.php',
            'StatisticsMaxUniqueVisitors'        => 'one_tag_reports.php',
            'StatisticsAverageVisitorsPerDay'    => 'one_tag_reports.php',
         )
    )
);

?>