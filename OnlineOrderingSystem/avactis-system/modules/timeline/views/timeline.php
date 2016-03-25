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
 * TimelineView view class
 *
 * @author Alexey Florinsky
 * @version $Id$
 * @package Timeline
 */

class TimelineView
{
    function TimelineView()
    {
        $this->_TmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/timeline-view/');
    }

    function output()
    {
        $text_filter_data = modApiFunc('Timeline','getFilterByText');
        if ($text_filter_data != null and isset($text_filter_data[0]) and isset($text_filter_data[1]) and is_array($text_filter_data[1]))
        {
            list($user_input, $index_words_list) = $text_filter_data;
        }
        else
        {
            $user_input = '';
        }

        $params = array(
            'CONTENT' => $this->outputTimelineItems(),
            'FILTER' => $this->outputFilter(),
            'TEXT_FILTER_USER_INPUT' => $user_input,
        );
        return $this->_TmplFiller->fill("", "container.tpl.html", $params);
    }

    function outputFilter()
    {
        $types = modApiFunc('Timeline','getTimelineTypes');
        $user_selected_types = modApiFunc('Timeline','getFilterByTypes');

        $col = 4;
        $cnt = 1;
        $html = '';
        $items = '';
        foreach ($types as $type)
        {
            $params = array(
                'TYPE_VALUE' => $type['types'],
                'TYPE_NAME' => $type['types'],
                'CHECKED' => in_array($type['types'], $user_selected_types) ? 'checked="Yes"' : '',
            );
            $items .= $this->_TmplFiller->fill("", "filter_item.tpl.html", $params);

            if ($cnt == $col)
            {
                $params = array(
                    'ITEMS' => $items,
                );
                $html .= $this->_TmplFiller->fill("", "filter_line.tpl.html", $params);
                $items = '';
                $cnt = 0;
            }

            $cnt++;
        }

        if (!empty($items))
        {
            $params = array(
                'ITEMS' => $items,
            );
            $html .= $this->_TmplFiller->fill("", "filter_line.tpl.html", $params);
        }

        return $html;
    }

    function outputTimelineItems()
    {
        $items_list = modApiFunc('Timeline','getTimelineHeaders');
        $html = '';
        $group = '';
        foreach ($items_list as $item)
        {
            $time_obj = new CStoreDatetime(strtotime($item['datetime'])); //
            $time = date('H:i:s', $time_obj->getTimestamp());
            $date = modApiFunc('Localization', 'date_format', $time_obj->getTimestamp(), false); // false -

            //                        -
            if ($group !== $date)
            {
                $html .= $this->_TmplFiller->fill("", "group.tpl.html", array('GROUP'=>$date));
                $group = $date;
            }

            $details = '';
            if ($item['is_body_empty'] == 0)
            {
                $details = '&nbsp;';
            }
            else
            {
                $params = array(
                    'LINK_TITLE' => getMsg('TL','DETAILS'),
                    'ID' => $item['id'],
                );
                $details = $this->_TmplFiller->fill("", "link.tpl.html", $params);
            }

            $params = array(
                'ID' => $item['id'],
                'TIME' => $time,
                'TYPE' => $item['type'],
                'HEADER' => $item['header'],
                'DETAILS' => $details,
            );
            $html .= $this->_TmplFiller->fill("", "line.tpl.html", $params);
        }
        return $html;
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case 'PaginatorLine':
                $obj = &$application->getInstance($tag);
                $value = $obj->output("TimelinePaginator", "TimelineView");
                break;

            case 'PaginatorRows':
                $obj = &$application->getInstance($tag);
                $value = $obj->output("TimelinePaginator", 'TimelineView','PGNTR_REC_ITEMS');
                break;
        }

        return $value;
    }

}

?>