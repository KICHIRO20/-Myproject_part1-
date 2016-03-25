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

class SubscribeOnCheckoutOutput
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'subscribe-on-checkout.ini',
            'files' => array(
                'Container' => TEMPLATE_FILE_SIMPLE,
            ),
            'options' => array(
            )
        );
        return $format;
    }

    function SubscribeOnCheckoutOutput()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->hide = false;
        if ($application->issetBlockTagFatalErrors('SubscribeOnCheckoutOutput')) {
            $this->hide = true;
            return;
        }

        $mode = modApiFunc('Settings', 'getParamValue', 'SUBSCRIPTIONS', 'CHECKOUT_SUBSCRIBE_MODE');
        if ($mode != 'MANUAL') {
            $this->hide = true;
            return;
        }

        $this->signed_id = modApiFunc('Customer_Account', 'getCurrentSignedCustomer') !== null;
        $this->topics = modApiFunc('Subscriptions', 'getCustomerTopics', $this->signed_id);
        if (sizeof($this->topics) == 0) {
            $this->hide = true;
            return;
        }

        $prerequisiteValidationResults = modApiFunc("Checkout", "getPrerequisiteValidationResults", 'subscriptionTopics');
        $ids = @$prerequisiteValidationResults['validatedData']['Topics']['value'];
        $this->selected_topics_ids = empty($ids) ? array() : explode(',', $ids);

        $this->selected_topics = array();
        foreach (array_keys($this->topics) as $i) {
            $topic = & $this->topics[$i];
            if (in_array($topic['topic_id'], $this->selected_topics_ids)) {
                $this->selected_topics[] = $topic['topic_name'];
            }
        }
        if (sizeof($this->selected_topics) == 0) {
            $this->hide = true;
        }
    }

    function output()
    {
        if ($this->hide) {
            return '';
        };

        global $application;

        $_template_tags = array(
            'Local_TopicsList',
            'Local_TopicName',
            );

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('SubscribeOnCheckoutOutput');
        $this->templateFiller->setTemplate($this->template);

        return $this->templateFiller->fill('Container');
    }

    function getTag($tag)
    {
        $value = null;

        switch($tag)
        {
            case 'Local_TopicsList':
                $value = '';
                foreach (array_keys($this->selected_topics) as $this->topic_num) {
                    $value .= $this->templateFiller->fill('TopicItem');
                }
                break;

            case 'Local_TopicName':
                $value = $this->selected_topics[$this->topic_num];
                break;
        };

        return $value;
    }

    var $signed_id;
    var $ViewState = array();
    var $MessageText;

};

?>