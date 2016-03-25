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
 * ReportGroups view class
 *
 * Display links to report pages
 *
 * @author Alexey Florinsky
 * @version $Id$
 * @package Reports
 */

class ReportGroups
{
    function ReportGroups()
    {
        $this->_TmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/report-groups/');
    }

    function output($group_id_range_from = 0, $group_id_range_to = 999999, $group_title)
    {
        $groups = modApiFunc('Reports','getReportGroups');
        $current_group_id = (int)modApiFunc('Request','getValueByKey','report_group_id');
        if ($current_group_id !== null and isset($groups[$current_group_id]))
        {
            $current_group_id = (int)$current_group_id;
        }
        else
        {
            reset($groups);
            $current_group_id = array_keys($groups);
            $current_group_id = $current_group_id[0];
        }

        $links = '';
        foreach ($groups as $grp_id => $grp)
        {
            if ($grp_id < $group_id_range_from or $grp_id > $group_id_range_to)
            {
                continue;
            }

            $data = array();
            $data['LinkName'] = $grp['GROUP_NAME'];
            $data['LinkDescription'] = $grp['GROUP_DESCRIPTION'];

            $request = new Request();
            $request->setView(CURRENT_REQUEST_URL);
            $request->setKey('report_group_id', $grp_id);
            $data['LinkHref'] = $request->getURL();
            if ($current_group_id === $grp_id and $current_group_id >= $group_id_range_from and $current_group_id <= $group_id_range_to)
            {
                $links .= $this->_TmplFiller->fill("", "group-link-selected.tpl.html", $data);
            }
            else
            {
                $links .= $this->_TmplFiller->fill("", "group-link.tpl.html", $data);
            }
        }

        return $this->_TmplFiller->fill("", "container.tpl.html", array('ReportGroupLinks' => $links, 'Title'=>getMsg('RPTS', $group_title)));
    }
}

?>