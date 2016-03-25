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
class stock_chart_amchart
{
    function stock_chart_amchart()
    {
        $this->csv_file_url = NULL;
        $this->graph_csv_prefix = 'asc_column';
        $this->ncharts = 0;
        $this->graph_titles = array();
        $this->graph_units = array();
        //
        $this->graph_colours = array("#FF0000", "#00FF00", "#0000FF", "#00FFFF", "#FF00FF", "FFFF00");

        $this->width = "400";
        $this->height = "300";
        $this->text_size = 11;
    }

    function set_text_size($size)
    {
        $this->text_size = $size;
    }

    function get_text_size()
    {
        return $this->text_size;
    }

    function set_width($width)
    {
        $this->width = $width;
    }

    function set_height($height)
    {
        $this->height = $height;
    }

    function set_csv_file_url($csv_file_url)
    {
        $this->csv_file_url = $csv_file_url;
    }

    function set_graph_titles($graph_titles, $units)
    {
        $this->graph_titles = $graph_titles;
        $this->graph_units = $units;
        $this->ncharts = sizeof($graph_titles);
    }

    function getAmchartsXML($xml_doc)
    {
        //                  ,                      ,                  .
        $xml_doc = str_replace('<?xml version="1.0" encoding="ISO-8859-1"?>', "", $xml_doc);
        $xml_doc = str_replace("'", "ASC_LINE_CHART_AMCHART_TAG", $xml_doc);
        $xml_doc = str_replace("\"", "'", $xml_doc);
        $xml_doc = str_replace("ASC_LINE_CHART_AMCHART_TAG", "\"", $xml_doc);
        $xml_doc = str_replace("\n", "", $xml_doc);
        return $xml_doc;
    }

    function getChartSettings()
    {
        loadCoreFile('obj_xml.php');
        $xml = new xml_doc();
        $root = $xml->createTag('settings');

        //  <max_series></max_series>
        //$xml->createTag('max_series', array(), 0, $root);

        $xml->createTag('text_size', array(), $this->text_size, $root);

        $xml->createTag('margins', array(), '0', $root);
        //                   ,
        $xml->createTag('equal_spacing', array(), 'false', $root);

        //               :
        $data_sets = $xml->createTag('data_sets', array(), '', $root);
        $data_set = $xml->createTag('data_set', array(), '', $data_sets);
        $xml->createTag('file_name', array(), urlencode($this->csv_file_url), $data_set);

        $csv = $xml->createTag('csv', array(), '', $data_set);
        $xml->createTag('reverse', array(), 'true', $csv);
        $xml->createTag('separator', array(), ';', $csv);
        $xml->createTag('date_format', array(), 'YYYY-MM-DD', $csv);
        $xml->createTag('decimal_separator', array(), '.', $csv);

        $columns = $xml->createTag('columns', array(), '', $csv);
        //
        $xml->createTag('column', array(), 'date', $columns);
        for($i = 1; $i <= $this->ncharts; $i++)
        {
            $xml->createTag('column', array(), $this->graph_csv_prefix . $i, $columns);
        }

        //                                       .           ,
        //         ,                                                 .
        //                  .
        // $h2 -                timeline
        // $h1 -
        //                ,                 ,                    $h1
        //                      $h1+$h2
        $h2 = 1.6 * $this->text_size;
        $h = $this->height;
        $h1 = (int)floor(1.0*($h - $h2)/(1.0*$this->ncharts));

        //                  :
        $charts = $xml->createTag('charts', array(), '', $root);
        for($i = 1; $i <= $this->ncharts; $i++)
        {
            $chart = $xml->createTag('chart', array(), '', $charts);

            $n = $this->ncharts;
            $xml->createTag('title', array(), $this->graph_titles[$i-1], $chart);
            $xml->createTag('border_color', array(), '#CCCCCC', $chart);
            $xml->createTag('border_alpha', array(), '100', $chart);

            $grid = $xml->createTag('grid', array(), '', $chart);
            $x = $xml->createTag('x', array(), '', $grid);
            $xml->createTag('enabled', array(), 'true', $x);

            $values = $xml->createTag('values', array(), '', $chart);
            $x = $xml->createTag('x', array(), '', $values);

            //
            if ($i == $this->ncharts)
            {
                $xml->createTag('height', array(), "" . ((int) (100.0 * ($h1+$h2) / $h)), $chart);
                $xml->createTag('enabled', array(), 'true', $x);
            }
            else
            {
                $xml->createTag('height', array(), "" . ((int) (100.0 * $h1 / $h)), $chart);
                $xml->createTag('enabled', array(), 'false', $x);
            }

            $xml->createTag('bg_color', array(), '#EEEEEE', $x);
            $xml->createTag('color', array(), '#000000', $x);

            $legend = $xml->createTag('legend', array(), '', $chart);
            $xml->createTag('show_date', array(), 'true', $legend);

            //
            $graphs = $xml->createTag('graphs', array(), '', $chart);
            $graph = $xml->createTag('graph', array(), '', $graphs);

            $data_sources = $xml->createTag('data_sources', array(), '', $graph);
            //                                              .
            //                                           close (open, low   high                )
            //                      amcharts stock.                       amcharts\stock\amstock\amstock_settings.xml
            $xml->createTag('close', array(), $this->graph_csv_prefix . $i, $data_sources);

            //                            ,    stock chart                                      .
            //               ,                                          .
            if ($i == $this->ncharts)
            {
                $xml->createTag('period_value', array(), 'average', $graph);
            }
            else
            {
                $xml->createTag('period_value', array(), 'average', $graph); // sum
            }

            //
            $xml->createTag('color', array(), $this->graph_colours[$i-1], $graph);

            //                                                               :
            $legend = $xml->createTag('legend', array(), '', $graph);
            $u = isset($this->graph_units[$i-1]) ? $this->graph_units[$i-1] : '';
            $xml->createTag('date', array("key" => "false", "title" => "false"), '<![CDATA[{average}'.urlencode($u).']]>', $legend); // {close}
            $xml->createTag('show_date', array(), 'true', $legend);

            $xml->createTag('bullet', array(), 'round_outline', $graph);
        }

        //
        $data_set_selector = $xml->createTag('data_set_selector', array(), '0', $root);
        $xml->createTag('enabled', array(), 'false', $data_set_selector);

        $period_selector = $xml->createTag('period_selector', array(), '0', $root);
        $xml->createTag('enabled', array(), 'false', $period_selector);

        $header = $xml->createTag('header', array(), '0', $root);
        $xml->createTag('enabled', array(), 'false', $header);

        $scroller = $xml->createTag('scroller', array(), '0', $root);
        $xml->createTag('enabled', array(), 'false', $scroller);

        $xml_doc = $xml->generate();
        $xml_doc = $this->getAmchartsXML($xml_doc);
        return  $xml_doc;
    }

    function __getBottomHeight()
    {
        //                                      x
        $len = 0;
        $xlabels = $this->stock_chart->get_xlabels();
        if(!empty($xlabels))
        {
            foreach($xlabels as $label)
            {
                $len = max($len, _ml_strlen($label['text']));
            }
        }

        // 4                     35
        return max(round(($len/4)*28), 35);
    }

    function output()
    {
        global $application;

        if($this->csv_file_url === NULL)
        {
            return "";
        }
        else
        {
            $unique_control_id = uniqid('');
            $this->_Template_Contents = array
            (
			   "CSV_FILE_URL" => urlencode($this->csv_file_url)
               ,"UNIQUE_CONTROL_ID" => $unique_control_id
               ,"TITLE1" => $this->graph_titles[0]
               ,"TITLE2" => $this->graph_titles[1]
               ,"TITLE3" => $this->graph_titles[2]
//               ,"CHART_SETTINGS" => addslashes($this->getChartSettings())
//               ,"CHART_WIDTH" => $this->width
//               ,"CHART_HEIGHT" => $this->height,
            );

            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/');
            $res = $this->mTmplFiller->fill("stock_chart_amcharts/", "container.tpl.html",$this->_Template_Contents);
            return array("html" => $res, "unique_control_id" => $unique_control_id);
       }
    }
};
?>