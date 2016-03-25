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

class Subscriptions_Signature
{
    function Subscriptions_Signature()
    {
        global $application;
        $mmObj = & $application->getInstance('Modules_Manager');
        $mmObj->includeAPIFileOnce('Subscriptions');
        $mmObj->includeAPIFileOnce('Request');
        $mmObj->includeAPIFileOnce('Session');
    }

    function output()
    {
        global $application;
        $this->_messageResources = & Subscriptions::getMessageResources();
        $this->_tmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/');

        $this->initFormData();

        if($this->ViewState['hasCloseScript'] == 'true') {
            modApiFunc('application', 'closeChild');
            return;
        }

        $config_array = LayoutConfigurationManager::static_get_cz_layouts_list();
        $config = reset($config_array);
        $url = $config['SITE_URL'].'unsubscribe.php?key=%KEY_UNSUBSCRIBE%';

        $signature = modApiFunc('Configuration', 'getValue', SYSCONFIG_NEWSLETTERS_SIGNATURE);

        $vars = array(
                'FormAction' => $this->urlSaveSignature(),
                'SignatureHtml' => htmlspecialchars($signature),
                'TipUnsubscribe' => getMsg('SUBSCR', 'TIP_SIGNATURE', $url),
                );
        $retval = $this->_tmplFiller->fill('', 'signature.tpl.html', $vars);
        return $retval;
    }

    function initFormData()
    {
        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            $SessionPost = modApiFunc('Session', 'get', 'SessionPost');
            $this->ViewState = $SessionPost['ViewState'];

            if(@ $this->ViewState['hasError'] == 'true')
            {
                $this->ErrorsArray = $this->ViewState['ErrorsArray'];
                unset($this->ViewState['ErrorsArray']);
            }

            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->ViewState =
                array(
                    'hasError'          => 'false',
                    'hasCloseScript'    => 'false'
                     );
            $this->POST = array();
        }
    }

    function getTag($tag)
    {
        global $application;

        $res = '';
        switch($tag)
        {
            case 'TopicsList':
                $res = $this->outputTopicsList();
                break;

            case 'TopicsIdsStr':
                $res = $this->getTopicsIdsStr();
        }
        return $res;
    }

    function urlSaveSignature()
    {
        $request = new Request();
        $request->setView( 'Subscriptions_Signature' );
        return $request->getURL();
    }

    function getTopicsIdsStr()
    {
        $ids = array();
        foreach(array_keys($this->topics) as $i) {
            $ids[] = $this->topics[$i]['topic_id'];
        }
        return implode('|', $ids);
    }

}
?>