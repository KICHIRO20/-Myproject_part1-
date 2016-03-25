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

class DatetimePeriodSelector
{
    function DatetimePeriodSelector()
    {

    }

    function output($params)
    {
        $params_default = array(
            'ID' => md5(uniqid(rand(), true)),
            'CALLBACK_JS_FUNCTION' => '',
            'CURRENT_VALUE' => DATETIME_PERIOD_UNDEFINED,
            'MIN_DISCONTINUITY' => DATETIME_PERIOD_DISCONTINUITY_DAY,
        );
        $params = array_merge($params_default, $params);
        return $this->getHTML($params);
    }

    function getHTML($params)
    {
        $html = '';

        $TmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/report-period-selector/');

        $html_option_list = '';

        //
        //                ,      -                                ,                -
        $options = modApiFunc('Reports','getDatetimePeriodLabels',$params['MIN_DISCONTINUITY']);

        foreach($options as $option_group_label => $option_list)
        {
            //                    HTML
            $html_group = '';
            foreach($option_list as $option_label)
            {
                $is_selected = '';
                if ($option_label == $params['CURRENT_VALUE'])
                {
                    $is_selected = 'SELECTED';
                }

                //
                $option_human_name = getMsg('RPTS',$option_label);
                $period = modApiFunc('Reports','getTimestampPeriodByDatetimeLabel',$option_label);
                if ($period !== null)
                {
                    list($option_from_timestamp, $option_to_timestamp) = $period;
                    $option_human_from = modApiFunc("Localization", "date_format", $option_from_timestamp, false);
                    $option_human_to = modApiFunc("Localization", "date_format", $option_to_timestamp, false);
                    if ($option_human_from == $option_human_to)
                    {
                        $option_human_name .= ': '.$option_human_from;
                    }
                    else
                    {
                        $option_human_name .= ': '.$option_human_from.' - '.$option_human_to;
                    }
                }
                $html_group .= $TmplFiller->fill('','select-drop-down-option.tpl.html',
                                                    array(
                                                            'OPTION_VALUE'=>$option_label,
                                                            'OPTION_NAME'=>$option_human_name,
                                                            'SELECTED' => $is_selected,
                ));
            }

            //
            $group_human_name = getMsg('RPTS', $option_group_label);

            //            HTML
            $html_option_list .= $TmplFiller->fill('','select-drop-down-group.tpl.html', array('GROUP_NAME'=>$group_human_name, 'OPTIONS'=>$html_group));
        }

        $container_tag_values = array(
                                        'selector_id' => $params['ID'],
                                        'callback-js-function' => $params['CALLBACK_JS_FUNCTION'],
                                        'OPTIONS_LIST' => $html_option_list,
                                     );
        return $TmplFiller->fill("", "select-drop-down-container.tpl.html", $container_tag_values);
    }

}

?>