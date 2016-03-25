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
 * TimelineItemView view class
 *
 * @author Alexey Florinsky
 * @version $Id$
 * @package Timeline
 */

class TimelineItemView
{
    function TimelineItemView()
    {
        $this->_TmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/timeline-item-view/');
    }

    function output()
    {
        $item_id = modApiFunc('Request','getValueByKey','id');
        if (empty($item_id) or $item_id == null)
        {
            return "";
        }

        $item = modApiFunc('Timeline','getTimelineItemById', $item_id);
        if ($item == null or !isset($item['datetime']))
        {
            return '';
        }

        $date = modApiFunc('Localization', 'date_format', $item['datetime']); //
        $time_obj = new CStoreDatetime(strtotime($item['datetime'])); //
        $time = date('H:i:s', $time_obj->getTimestamp());

        $params = array(
            'TIME' => $date.' '.$time,
            'TYPE' => $item['type'],
            'HEADER' => $item['header'],
            'BODY' => gzinflate($item['body']),
        );
        return $this->_TmplFiller->fill("", "container.tpl.html", $params);
    }

    function outputTimelineHeader($item_id)
    {
        $item = modApiFunc('Timeline','getTimelineItemById', $item_id);
        $html = '';

        $date = modApiFunc('Localization', 'date_format', $item['datetime']); //
        $time_obj = new CStoreDatetime(strtotime($item['datetime'])); //
        $time = date('H:i:s', $time_obj->getTimestamp());

        $params = array(
            'TIME' => $date.' '.$time,
            'TYPE' => $item['type'],
            'HEADER' => $item['header'],
        );

        return $date.' '.$time.' '.$item['type'].'<br>'.$item['header'];
    }
}

?>