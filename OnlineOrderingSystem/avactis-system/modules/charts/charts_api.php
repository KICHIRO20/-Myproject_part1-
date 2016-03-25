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
 * @package Charts
 * @author Vadim Lyalikov
 *
 */

class Charts
{
    function Charts()
    {}

    function install()
    {
        $query = new DB_Table_Create(Charts::getTables());
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Charts::getTables());
    }

    function getBarChartAmchart($xlabels, $series, $width = NULL, $height = NULL, $unit = '', $unit_position = 'right', $column_width_percent = 60, $title='')
    {
        loadModuleFile('charts/includes/bar_chart_amchart.php');
        loadModuleFile('charts/includes/bar_chart.php');

        $chart = new bar_chart_amchart();
        $chart->set_column_width_percent($column_width_percent);
        $chart->setTitle($title);

        $bar_chart = new bar_chart();
        $bar_chart->set_xlabels($xlabels);
        $bar_chart->set_series_data($series);

        $chart->set_bar_chart($bar_chart);
        if($height !== NULL)
        {
            $chart->set_height($height);
        }
        if($width !== NULL)
        {
            $chart->set_width($width);
        }
        $chart->set_unit($unit);
        $chart->set_unit_position($unit_position);

        $res = $chart->output();
        return $res;
    }

    function getLineChartAmchartTextSize()
    {
        loadModuleFile('charts/includes/line_chart_amchart.php');
        loadModuleFile('charts/includes/line_chart.php');

        $chart = new line_chart_amchart();
        return $chart->get_text_size();
    }


    function getLineChartAmchart($xlabels, $series, $width = NULL, $height = NULL, $unit = '', $unit_position = 'right', $frequency = '', $title = '' )
    {
        loadModuleFile('charts/includes/line_chart_amchart.php');
        loadModuleFile('charts/includes/line_chart.php');

        $line_chart = new line_chart();
        $line_chart->set_xlabels($xlabels);
        $line_chart->set_series_data($series);

        $chart = new line_chart_amchart();
        $chart->set_frequency($frequency);
        $chart->set_line_chart($line_chart);
        $chart->setTitle($title);
        if($height !== NULL)
        {
            $chart->set_height($height);
        }
        if($width !== NULL)
        {
            $chart->set_width($width);
        }
        $chart->set_unit($unit);
        $chart->set_unit_position($unit_position);

        $res = $chart->output();
        return $res;
    }

    function getStockChartAmchart($csv_file_url, $graph_titles, $width = NULL, $height = NULL, $units = array())
    {
        _use(dirname(__FILE__).'/includes/stock_chart_amchart.php');

        $chart = new stock_chart_amchart();
        $chart->set_csv_file_url($csv_file_url);
        $chart->set_graph_titles($graph_titles, $units);

        if($height !== NULL)
        {
            $chart->set_height($height);
        }

        if($width !== NULL)
        {
            $chart->set_width($width);
        }

        $res = $chart->output();
        return $res;
    }

    function getTables()
    {
        global $application;
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        };
        $tables = array();
        return $application->addTablePrefix($tables);
    }

    //========================================================================================================//
    //=== PUBLIC functions  ==================================================================================//
    //========================================================================================================//
};

?>