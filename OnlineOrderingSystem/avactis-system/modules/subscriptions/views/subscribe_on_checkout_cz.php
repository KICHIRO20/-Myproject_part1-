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

class SubscribeOnCheckout
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

    function SubscribeOnCheckout()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->hide = false;
        if ($application->issetBlockTagFatalErrors('SubscribeOnCheckout')) {
            $this->hide = true;
            return;
        }

        $mode = modApiFunc('Settings', 'getParamValue', 'SUBSCRIPTIONS', 'CHECKOUT_SUBSCRIBE_MODE');
        if ($mode != 'MANUAL') {
            $this->hide = true;
            return;
        }

        $this->can_unsubscribe = modApiFunc('Subscriptions', 'canClientUnsubscribe');
        $this->signed_id = modApiFunc('Customer_Account', 'getCurrentSignedCustomer') !== null;
        $this->topics = modApiFunc('Subscriptions', 'getCustomerTopics', $this->signed_id);
        if (sizeof($this->topics) == 0) {
            $this->hide = true;
        }

        /*
        $this->subscribed = array();
        $this->email = modApiFunc('Subscriptions', 'getCustomerSubscribedEmail');
        if (! empty($this->email)) {
            if ($this->can_unsubscribe) {
                $this->subscribed = modApiFunc('Subscriptions', 'getSubscribedTopics', $this->email, $this->signed_id);

            }
        }
        */
    }

    function output()
    {
        if ($this->hide) {
            return '';
        };

        global $application;

        $prerequisiteValidationResults = modApiFunc('Checkout', 'getPrerequisiteValidationResults', 'subscriptionTopics');
        $ids = @$prerequisiteValidationResults['validatedData']['Topics']['value'];
        $this->selected_topics_ids = empty($ids) ? array() : explode(',', $ids);

        $_template_tags = array(
            'Local_TopicsList',
            'Local_TopicId',
            'Local_TopicName',
            'Local_TopicChecked',
            );

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('SubscribeOnCheckout');
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
                foreach (array_keys($this->topics) as $this->topic_num) {
                    $value .= $this->templateFiller->fill('TopicItem');
                }
                break;

            case 'Local_TopicId':
                $value = $this->topics[$this->topic_num]['topic_id'];
                break;

            case 'Local_TopicName':
                $value = $this->topics[$this->topic_num]['topic_name'];
                break;

            case 'Local_TopicChecked':
                $value = in_array($this->topics[$this->topic_num]['topic_id'], $this->selected_topics_ids) ? 'checked="checked"' : '';
                break;
        };

        return $value;
    }

    var $signed_id;
    var $ViewState = array();
    var $MessageText;

};

?>