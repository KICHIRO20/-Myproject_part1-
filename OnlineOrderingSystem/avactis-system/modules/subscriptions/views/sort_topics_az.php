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

class Subscriptions_SortTopics
{
    function Subscriptions_SortTopics()
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
        $this->fetchTopics();

        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        $application->registerAttributes(array(
            'TopicsList',
            'TopicsIdsStr',
        ));


        $vars = array(
                'FormAction' => $this->urlSaveOrder(),
                );
        $retval = $this->_tmplFiller->fill('', 'sort_topics.tpl.html', $vars);
        return $retval;
    }

    function initFormData()
    {
        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $SessionPost = modApiFunc("Session", "get", "SessionPost");
            $this->ViewState = $SessionPost["ViewState"];

            if(isset($this->ViewState["hasError"]) && $this->ViewState["hasError"] == "true")
            {
                $this->ErrorsArray = $this->ViewState["ErrorsArray"];
                unset($this->ViewState["ErrorsArray"]);
            }

            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->ViewState =
                array(
                    "hasError"          => "false",
                    "hasCloseScript"    => "false"
                     );
            $this->POST = array();
        }
    }

    function fetchTopics()
    {
        $this->topics = modApiFunc('Subscriptions', 'getTopicsList');
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

    function outputTopicsList()
    {
        $options = '';
        foreach (array_keys($this->topics) as $i)
        {
            $t = & $this->topics[$i];
            $options .= '<option value='.$t['topic_id'].'>'.$t['topic_name'].'</option>';
        }
        return $options;
    }

    function urlSaveOrder()
    {
        $request = new Request();
        $request->setView( 'Subscriptions_SortTopics' );
        #$request->setAction( 'update_orders' );
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