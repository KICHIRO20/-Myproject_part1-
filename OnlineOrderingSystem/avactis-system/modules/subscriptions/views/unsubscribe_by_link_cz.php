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
 * @package Subscriptions
 * @author
 *
 */

class UnsubscribeByLink
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'unsubscribe-by-link.ini',
            'files' => array(
                'Confirmation'  => TEMPLATE_FILE_SIMPLE,
                'TopicItem'     => TEMPLATE_FILE_SIMPLE,
                'InvalidKey'    => TEMPLATE_FILE_SIMPLE,
                'Unsubscribed'  => TEMPLATE_FILE_SIMPLE,
                'ErrorOccured'  => TEMPLATE_FILE_SIMPLE,
                'Error'         => TEMPLATE_FILE_SIMPLE,
            ),
            'options' => array(
            )
        );
        return $format;
    }

    function UnsubscribeByLink()
    {
        global $application;
        $this->key = modApiFunc('Request', 'getValueByKey', 'key');
        $this->rec = modApiFunc('Newsletter', 'getUnsubscribeRecord', $this->key);
        if (modApiFunc('Session', 'is_Set', 'SessionPost')) {
            $SessionPost = modApiFunc('Session', 'get', 'SessionPost');
            $this->ViewState = $SessionPost['ViewState'];
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else {
            $this->ViewState = array();
        }
    }

    function output()
    {
        global $application;

        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('UnsubscribeByLink');
        $this->templateFiller->setTemplate($this->template);
        if ($this->ViewState && @$this->ViewState['ErrorsArray']) {
            // some errors occured while unsubscribing
            $application->registerAttributes(array(
                    'Local_ErrorsList', 'Local_Error',
                    ));
            return $this->templateFiller->fill('ErrorOccured');
        }
        elseif ($this->ViewState && @$this->ViewState['Stage'] == 'finish') {
            // unsubscribing succeeded
            return $this->templateFiller->fill('Unsubscribed');
        }
        elseif ($this->rec) {
            $subscribed_topics_ids = modApiFunc('Subscriptions', 'getSubscribedTopics', $this->rec['email_id']);
            $this->topics_ids = array_intersect(explode(',', $this->rec['topics_ids']), $subscribed_topics_ids);
            if ($this->topics_ids) {
                // show unsubscribing form
                $application->registerAttributes(array(
                        'Local_FormAction', 'Local_KeyUnsubscribe',
                        'Local_TopicsList',
                        'Local_TopicId', 'Local_TopicName',
                        ));
                return $this->templateFiller->fill('Confirmation');
            }
            else {
                return $this->templateFiller->fill('AlreadyUnsubscribed');
            }
        }
        else {
            // invalid key
            return $this->templateFiller->fill('InvalidKey');
        }
    }

    function getTag($tag)
    {
        $value = null;

        switch($tag)
        {
            case 'Local_FormAction':
                $request = new Request();
                $request->setView(CURRENT_REQUEST_URL);
                $value = $request->getURL();
                break;

            case 'Local_KeyUnsubscribe':
                $value = $this->key;
                break;

            case 'Local_TopicsList':
                if ($this->topics_ids) {
                    $topics = modApiFunc('Subscriptions', 'getTopicsByIds', $this->topics_ids);
                    foreach($topics as $this->topic) {
                        $value .= $this->templateFiller->fill('TopicItem');
                    }
                }
                break;

            case 'Local_TopicId':
                $value = $this->topic['topic_id'];
                break;

            case 'Local_TopicName':
                $value = $this->topic['topic_name'];
                break;

            case 'Local_ErrorsList':
                foreach ($this->ViewState['ErrorsArray'] as $this->error) {
                    $value .= $this->templateFiller->fill('Error');
                }
                break;

            case 'Local_Error':
                $value = $this->error;
                break;

        };

        return $value;
    }

    var $rec;
    var $topic;
    var $ViewState;
};

?>