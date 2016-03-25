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
class Newsletter_Send
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Newsletter_Send constructor
     */
    function Newsletter_Send()
    {
    }

    /**
     *
     */
    function output()
    {
    	global $application;
        $request = &$application->getInstance('Request');
        $templateFiller = $application->getInstance('TmplFiller');

        $letter_id = $request->getValueByKey('letter_id');
        $letter_info = modApiFunc('Newsletter', 'getMessageInfo', $letter_id);

        if (empty($letter_info)) {
        	$vars = array('Title' => getMsg('NLT', 'DELIVERY_TITLE'));
        	return $templateFiller->fill('newsletter/', 'error_no_newsletter.tpl.html', $vars);
        }
        $counts = modApiFunc('Subscriptions', 'getLettersEmailsCount',  array($letter_id));
        $letter_topics = $this->getLetterTopics($letter_id);

        $templateContents = array(
                                'LetterId' => $letter_id
                               ,'LetterFrom' => prepareHTMLDisplay($letter_info['letter_from_name']) .
                                    '&nbsp;&lt;' . prepareHTMLDisplay($letter_info['letter_from_email']) . '&nbsp;&gt;'
                               ,'LetterSubject' => prepareHTMLDisplay($letter_info['letter_subject'])
                               ,'LetterTopics' => $letter_topics
                               ,'EnableSend' => @$counts[$letter_id] > 0 ? 'yes' : ''
                               ,'TotalRecipients' => (int) @$counts[$letter_id]
                               );
        $application->registerAttributes($templateContents);
        $this->_templateContents = $templateContents;

        return $templateFiller->fill('newsletter/', 'delivery_progress.tpl.html', array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_templateContents);
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    function getLetterTopics($letter_id)
    {
        global $application;
        $templateFiller = $application->getInstance('TmplFiller');

        $topics = modApiFunc('Subscriptions', 'getLettersTopicsToSend', array($letter_id));
        $res = '';
        if (isset($topics[$letter_id])) {
            foreach(array_keys($topics[$letter_id]) as $i) {
                $t = & $topics[$letter_id][$i];
                $vars = array(
                        'TopicName' => $t['topic_name'],
                        'TopicEmails' => $t['topic_emails'],
                        );
                $res .= $templateFiller->fill('newsletter/', 'topic.item.send.tpl.html', $vars);
            }
            $vars = array(
                    'Topics' => $res,
                    );
            $res = $templateFiller->fill('newsletter/', 'topics.send.tpl.html', $vars);
        }
        else {
            $res = getMsg('SUBSCR', 'ALERT_NO_TOPICS_SELECTED');
        }

        return $res;
    }

     var $_templateContents;

    /**#@-*/

}
?>