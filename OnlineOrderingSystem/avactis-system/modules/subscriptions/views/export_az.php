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
 * @package OrdersExport
 * @author Alexey Florinsky
 *
 */

class Subscriptions_Export
{
    function Subscriptions_Export()
    {
    }

    function output()
    {
        global $application;

        $this->mTmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/');
        $topics = modApiFunc('Request', 'getValueByKey', 'topics');
        $topics_ids = explode(',', $topics);
        $topics_names = $this->getTopicsNames($topics_ids);
        $unique_emails = modApiFunc('Subscriptions', 'getTopicsEmailsCount', $topics_ids, true);
        $total_emails = modApiFunc('Subscriptions', 'getTopicsEmailsCount', $topics_ids, false);
        $no_emails = $total_emails == 0;

        $vars = array(
            'TopicsIds' => $topics,
            'TopicsNames' => $topics_names,
            'UniqueEmailsCount' => $unique_emails,
            'TotalEmailsCount' => $total_emails,
            'MessageNoEmails' => $no_emails ? $this->mTmplFiller->fill('export/', 'message_no_emails.tpl.html', array()) : '',
            'ClassDownload' => $no_emails ? 'button_disabled' : '',
            'OnClickDownload' => $no_emails ? '' : 'downloadCSV()',
        );

        return $this->mTmplFiller->fill('export/', 'container.tpl.html', $vars);
    }

    function getTopicsNames($topics_ids)
    {
        global $application;

        $topics = modApiFunc('Subscriptions', 'getTopicsByIds', $topics_ids);
        $res = '';
        foreach(array_keys($topics) as $i) {
            $t = & $topics[$i];
            $vars = array(
                    'TopicName' => $t['topic_name'],
                    'TopicEmails' => $t['topic_emails'],
                    );
            $res .= $this->mTmplFiller->fill('export/', 'topic.item.tpl.html', $vars);
        }

        $vars = array(
                'Topics' => $res,
                );
        $res = $this->mTmplFiller->fill('export/', 'topics.tpl.html', $vars);
        return $res;
    }

};

?>