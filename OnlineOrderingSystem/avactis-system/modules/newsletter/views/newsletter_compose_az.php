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
 *
 * @package Newsletter
 * @author Egor Makarov
 */
class Newsletter_Compose
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Newsletter_Compose constructor
     */
    function Newsletter_Compose()
    {
    	global $application;
        	//                                        id       .                  -
		$request = &$application->getInstance('Request');
        $this->_letterId = $request->getValueByKey('letter_id');
        if ($this->_letterId != null)
        {
        	$this->initFromDB($this->_letterId);
            $this->_action = 'edit';
        }
        else
        {
        	$this->initWithDefaults();
        	$this->_action = 'new';
        }
    }

    /**
     *                                                SessionPost
     */
    function initFromSessionPost()
    {
        $session_post = modApiFunc('Session', 'get', 'SessionPost');
        $this->_viewState = $session_post['ViewState'];
        if(isset($this->_viewState["ErrorsArray"]) &&
           count($this->_viewState["ErrorsArray"]) > 0)
        {
            $this->_errorsArray = $this->_viewState["ErrorsArray"];
            unset($this->_viewState["ErrorsArray"]);
        }

        $this->_post = $session_post;
    }

    /**
     *
     */
    function initFromDB($id_letter)
    {
    	$letter_info = modApiFunc('Newsletter', 'getMessageInfo', $id_letter);
		if (empty($letter_info)) {
			return;
		}
        foreach ($letter_info as $key => $val)
        {
        	$this->_post[$key] = prepareHTMLDisplay($val);
        }
        $this->_post["letter_id"] = $id_letter;
        $this->_viewState = array ('hasCloseScript' => 'false', 'FormSubmitValue' => 'save');
        $this->_post['topic'] = modApiFunc('Subscriptions', 'getLetterTopics', $id_letter);
    }

    /**
     *
     */
    function initWithDefaults()
    {
    	// :
    	$this->_post = array (
                                 'letter_subject' => '(no subject)'
                                ,'letter_from_name' => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_NAME)
                                ,'letter_from_email' => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_EMAIL)
                                ,'letter_html' => htmlspecialchars(modApiFunc('Configuration', 'getValue', SYSCONFIG_NEWSLETTERS_SIGNATURE))
                                ,'letter_id' => ''
                                ,'topic' => array(),
                             );
        $this->_viewState = array ('hasCloseScript' => 'false', 'FormSubmitValue' => 'save');
    }

    /**
     * @return String Return html code for hidden form fields representing @var $this->ViewState array.
     */
    function outputViewState()
    {
        $retval = "";
        foreach ($this->_viewState as $key => $value)
        {
            $retval .= "<input type=\"hidden\" name=\"ViewState[" .$key . "]\" value=\"" . $value . "\">";
        }
        return $retval;
    }

    /**
     * @return String                  ,            HTML-                           .
     */
    function outputErrors()
    {
        global $application;
        if (!is_array($this->_errorsArray) || sizeof($this->_errorsArray) == 0)
        {
            return;
        }
        $result = "";
        $application->registerAttributes(array('ErrorIndex', 'Error'));
        $this->_errorIndex = 0;
        foreach ($this->_errorsArray as $error)
        {
            $this->_errorIndex++;
            $this->_error = $this->MessageResources->getMessage($error);
            $result .= $this->mTmplFiller->fill("newsletter/", "error.tpl.html", array());
        }
        return $result;
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg = modApiFunc("Session","get","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('NLT',$msg)
            );
            modApiFunc("Session","un_set","ResultMessage");
            $this->_templateContents=$template_contents;
            $application->registerAttributes($this->_templateContents);
            return $this->_templateFiller->fill('newsletter/', 'result-message.tpl.html', array());
        }

    }

    /**
     *
     */
    function output()
    {
    	global $application;

        if(@$this->_viewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        $this->_templateFiller = &$application->getInstance('TmplFiller');

        if (empty($this->_post)) {
        	$vars = array('Title' => getMsg('NLT', 'COMPOSE_TITLE'));
        	return $this->_templateFiller->fill('newsletter/', 'error_no_newsletter.tpl.html', $vars);
        }

        loadCoreFile('request.php');
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setAction('do_newsletter_save');
        $request->setKey('page_view', 'Newsletter_Compose');
        $save_url = $request->getURL();

        $templateContents = array (
                                'Subject' => $this->_post['letter_subject']
                               ,'FromName' => $this->_post['letter_from_name']
                               ,'FromEmail' => $this->_post['letter_from_email']
                               ,'BodyHtml' => $this->_post['letter_html']
                               ,'SaveActionUrl' => $save_url
                               ,'LetterId' => $this->_post['letter_id']
                               ,'ViewState' => $this->outputViewState()
                               ,'SaveActionUrl' => $save_url
                               ,'Errors' => $this->outputErrors()
                               ,'TopicsList' => $this->getTopicsList()
                               ,'AscAction' => $this->_action
                               ,'ResultMessageRow' => $this->outputResultMessage()
                            );
        $this->_templateContents = $templateContents;

        $application->registerAttributes($this->_templateContents);

        return $this->_templateFiller->fill('newsletter/', 'compose.tpl.html', array());
    }

    function getTag($tag)
    {
    	$value = null;
    	if ($tag == 'ErrorIndex')
        {
        	$value = $this->_errorIndex;
        }
        else if ($tag == 'Error')
        {
        	$value = $this->_error;
        }
    	else
        {
        	$value = getKeyIgnoreCase($tag, $this->_templateContents);
        }
        return $value;
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    function getTopicsList()
    {
        $value = '';
        $topics = modApiFunc('Subscriptions', 'getSendTopics');
        if($topics) {
            foreach(array_keys($topics) as $i) {
                $t = & $topics[$i];
                $vars = array(
                        'TopicId' => $t['topic_id'],
                        'TopicName' => $t['topic_name'],
                        'TopicEmails' => $t['topic_emails'],
                        'TopicChecked' => @$this->_post['topic'][ $t['topic_id'] ] ? 'checked' : '',
                        );
                $value .= $this->_templateFiller->fill('newsletter/', 'topic.item.tpl.html', $vars);
            }
        }
        else {
            $value = getMsg('SUBSCR', 'ALERT_NO_TOPICS');
        }
        return $value;
    }

    var $_post = array();

    var $_templateContents;

    var $_templateFiller;

    //var $_letterInfo;

    //var $_letterId;

    var $_errorsArray;

    var $_viewState = array();

    var $_errorIndex;

    var $_error;

    /**#@-*/

}
?>