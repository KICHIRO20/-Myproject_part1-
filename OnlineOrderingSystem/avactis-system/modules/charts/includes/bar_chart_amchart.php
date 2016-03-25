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
class bar_chart_amchart
{
    function bar_chart_amchart()
    {
        $this->bar_chart = NULL;
        $this->unit = '';
        $this->unit_position = 'right';
        $this->width  = "400";
        $this->height = "300";
        $this->column_width_percent = 60;
        $this->title = '';
    }

    function setTitle($t)
    {
        $this->title = $t;
    }

    //unit = {'%', '', '$', ...);
    function set_unit($unit)
    {
    	$this->unit = $unit;
    }

    //unit_position = {'right', 'left'};
    function set_unit_position($unit_position)
    {
    	$this->unit_position = $unit_position;
    }

    function set_width($width)
    {
    	$this->width = $width;
    }

    function set_height($height)
    {
    	$this->height = $height;
    }

    function set_bar_chart($bar_chart)
    {
        $this->bar_chart = $bar_chart;
    }


    function getAmchartsXML($xml_doc)
    {
    	//                  ,                      ,                  .
        $xml_doc = str_replace('<?xml version="1.0" encoding="ISO-8859-1"?>', "", $xml_doc);
        $xml_doc = str_replace("'", "ASC_BAR_CHART_AMCHART_TAG", $xml_doc);
        $xml_doc = str_replace("\"", "'", $xml_doc);
        $xml_doc = str_replace("ASC_BAR_CHART_AMCHART_TAG", "\"", $xml_doc);
        $xml_doc = str_replace("\n", "", $xml_doc);
        return $xml_doc;
    }

    function getJSONChartData()
    {
		$arrData = array();
		$xlabels = $this->bar_chart->get_xlabels();
		$series_data = $this->bar_chart->get_series_data();

		for($i=0; $i<count($xlabels); $i++)
		{
			$arrData[$i] = array('x' => $xlabels[$i]['text'], 'y' => $series_data[0]['values'][$i]['text']);
		}
		$jsonData = json_encode($arrData);
	return $jsonData;
	}

    function getChartData()
    {
        loadCoreFile('obj_xml.php');
        $xml = new xml_doc();
        $root = $xml->createTag('chart');
        $series = $xml->createTag('series', array(), '', $root);
        $graphs = $xml->createTag('graphs', array(), '', $root);

        //                  x
        $xlabels = $this->bar_chart->get_xlabels();
        if(!empty($xlabels))
        {
        	$xid = 0;
        	foreach($xlabels as $label)
        	{
                $xml->createTag('value', array('xid' => $xid++), $label['text'], $series);
        	}
        }

		$gradient_fill_colors_defaults = array(
				0 => array('border' => '#3F3EFE', 'center' => '#7D7DFE'),
				1 => array('border' => '#72C54E', 'center' => '#A2D598'),
				2 => array('border' => '#989898', 'center' => '#B5B5B5'),
			);

        //
        $series_data = $this->bar_chart->get_series_data();
        if(!empty($series_data))
        {
            $gid = 0;
            foreach($series_data as $series)
            {
				$color = isset($series['color']) ? $series['color'] : $gradient_fill_colors_defaults[$gid % sizeof($gradient_fill_colors_defaults)]['border'];
				$gradient_fill_color_border = isset($series['gradient_fill_colors']['border']) ? $series['gradient_fill_colors']['border'] : $gradient_fill_colors_defaults[$gid % sizeof($gradient_fill_colors_defaults)]['border'];
				$gradient_fill_color_center = isset($series['gradient_fill_colors']['center']) ? $series['gradient_fill_colors']['center'] : $gradient_fill_colors_defaults[$gid % sizeof($gradient_fill_colors_defaults)]['center'];

                $graph = $xml->createTag('graph', array('gid' => $gid++, 'title' => $series['title'], 'color' => $color, 'gradient_fill_colors' => "$gradient_fill_color_border, $gradient_fill_color_center, $gradient_fill_color_border"), '', $graphs);
                $xid = 0;
                foreach($series['values'] as $value)
                {
                    $xml->createTag('value', array('xid' => $xid++), $value['text'], $graph);
                }
            }
        }

        $xml_doc = $xml->generate();
        $xml_doc = $this->getAmchartsXML($xml_doc);
        return  $xml_doc;
    }

    function set_column_width_percent($column_width_percent)
    {
        $this->column_width_percent = $column_width_percent;
    }

    //
    function getChartSettings()
    {
        loadCoreFile('obj_xml.php');
        $xml = new xml_doc();
        $root = $xml->createTag('settings');

        $xml->createTag('font', array(), 'Tahoma', $root);

        //
        $legend = $xml->createTag('legend', array(), '', $root);
        $xml->createTag('enabled', array(), 'false', $legend);

        //                    -               ,
        $column = $xml->createTag('column', array(), '', $root);
        $xml->createTag('gradient', array(), 'horizontal', $column);

        //
        $xml->createTag('width', array(), $this->column_width_percent, $column);

        //                    -               ,
        $xml->createTag('data_labels', array(), '<![CDATA[<b>{value}</b>]]>', $column);
        $xml->createTag('data_labels_text_color', array(), '#404040', $column);
        $xml->createTag('data_labels_text_size', array(), '', $column);
        $xml->createTag('data_labels_position', array(), 'above', $column);

        //                    -               ,
        $xml->createTag('text_size', array(), '11', $root);


        //           X -
        $values = $xml->createTag('values', array(), '', $root);
        $category = $xml->createTag('category', array(), '', $values);
        $xml->createTag('rotate', array(), '90', $category);

        //
        $value = $xml->createTag('value', array(), '', $values);
        $xml->createTag('min', array(), 0, $value);
        $xml->createTag('unit', array(), urlencode($this->unit), $value);
        $xml->createTag('unit_position', array(), $this->unit_position, $value);

        $labels = $xml->createTag('labels', array(), '', $root);
        $label = $xml->createTag('label', array(), '', $labels);
        $xml->createTag('text', array(), '<![CDATA[<b>'.$this->title.'</b>]]>', $label);
        $xml->createTag('text_size', array(), '11', $label);
        $xml->createTag('text_color', array(), '666666', $label);
        $xml->createTag('align', array(), 'center', $label);

        //                             (title)
        $xml->createTag('y', array(), '17', $label);

        $plot_area = $xml->createTag('plot_area', array(), '', $root);
        //
        $xml->createTag('color', array(), '#F6F6F6', $plot_area);
        $xml->createTag('alpha', array(), '100', $plot_area);
        //                   Y (5          '1000%')
        $margins = $xml->createTag('margins', array(), '', $plot_area);
        $xml->createTag('left', array(), '80', $margins);
        $xml->createTag('bottom', array(), $this->__getBottomHeight(), $margins);
        $xml->createTag('top', array(), '35', $margins);

        $xml_doc = $xml->generate();
        $xml_doc = $this->getAmchartsXML($xml_doc);
        return  $xml_doc;
    }

    function __getBottomHeight()
    {
        //                                      x
        $len = 0;
        $xlabels = $this->bar_chart->get_xlabels();
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

        if($this->bar_chart === NULL)
        {
            return "";
        }
        else
        {
        	$unique_control_id = uniqid('');
            $this->_Template_Contents = array
            (
                "CHART_DATA" => $this->getJSONChartData()
               ,"UNIQUE_CONTROL_ID" => $unique_control_id
               ,"TITLE" => $this->title
//               ,"CHART_SETTINGS" => addslashes($this->getChartSettings())
//               ,"CHART_WIDTH" => $this->width
//               ,"CHART_HEIGHT" => $this->height
            );

            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/');
            $res = $this->mTmplFiller->fill("bar_chart_amcharts/", "container.tpl.html",$this->_Template_Contents);
            return array("html" => $res, "unique_control_id" => $unique_control_id);
       }
    }
};
?>