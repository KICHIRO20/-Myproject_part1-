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
 *                                  .
 * @package Newsletter
 * @author Egor Makarov
 */
class Newsletter_List
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Newsletter_List constructor
     */
    function Newsletter_List()
    {
    	$this->_messageResources = new MessageResources('newsletter-messages', 'AdminZone');
    }

    function outputMessagesList()
    {
    	global $application;

    	$messages = modApiFunc('Newsletter', 'getMessagesList');
    	$this->getTopicsNames($messages);

        $templateContents = array (
                                 'LetterCreationDate' => ''
                                ,'LetterSentDate'    => ''
                                ,'LetterFrom'       => ''
                                ,'LetterSubject'    => ''
                                ,'LetterNum'        => ''
                                ,'LetterId'         => ''
                                ,'LetterTopics'     => ''
                            );
        $application->registerAttributes($templateContents);

        $result = '';
        $counter = 0;
        foreach ($messages as $msg)
        {
            $counter++;

            $creation_date = modApiFunc('Localization', 'date_format', $msg['letter_creation_date']) . " " .
                modApiFunc('Localization', 'SQL_time_format', $msg['letter_creation_date']);

            $sent_date = $msg['letter_sent_date'] == null ?
                $this->_messageResources->getMessage('LETTER_NOT_SENT') :
                modApiFunc('Localization', 'date_format', $msg['letter_sent_date']) . " " . modApiFunc('Localization', 'SQL_time_format', $msg['letter_sent_date']);

            $this->_listTemplateContents = array (
                                            'LetterCreationDate'=> $creation_date
                                           ,'LetterSentDate'    => $sent_date
                                           ,'LetterFrom'        => prepareHTMLDisplay($msg['letter_from_name']) . '&nbsp;&lt;'. prepareHTMLDisplay($msg['letter_from_email']) .'&gt;'
                                           ,'LetterSubject'     => prepareHTMLDisplay($msg['letter_subject'])
                                           ,'LetterNum'         => $counter
                                           ,'LetterId'          => $msg['letter_id']
                                           ,'LetterTopics'      => $msg['topics_names']
                                       );
            $result .= $this->_tmplFiller->fill('newsletter/', 'item.tpl.html', array());
        }

        if ($counter == 0)
        {
        	$result .= $this->_tmplFiller->fill('newsletter/', 'item_no_items.tpl.html', array());
            $counter++;
        }

        for ($i=0; $i < 10-$counter; $i++)
        {
            $result .= $this->_tmplFiller->fill('newsletter/', 'item_empty.tpl.html', array());
        }

        return $result;
    }


    /**
     *
     */
    function output()
    {
    	global $application;
        $this->_tmplFiller = &$application->getInstance('TmplFiller');

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setAction('do_newsletter_delete');
        $self_url = $request->getURL();
        $self_url = "{$self_url}&letter_id=";

        $templateContents = array (
            'MessagesList' => $this->outputMessagesList()
            ,'SelfUrl' => $self_url
        );

        $this->_templateContents = $templateContents;
        $application->registerAttributes($this->_templateContents);
        $result = $this->_tmplFiller->fill('newsletter/', 'list.tpl.html', array());
        return $result;
    }

    function getTag($tag)
    {
    	$res = null;
    	if ($tag == 'MessagesList' || $tag == 'SelfUrl')
        {
        	$res = getKeyIgnoreCase($tag, $this->_templateContents);
        }
        else
        {
            $res = getKeyIgnoreCase($tag, $this->_listTemplateContents);
        }
        return $res;
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    function getTopicsNames(&$letters)
    {
        $letters_ids = array();
        foreach (array_keys($letters) as $i) {
            $letters_ids[] = $letters[$i]['letter_id'];
        }
        $letters_topics_names = modApiFunc('Subscriptions', 'getLettersTopicsNames', $letters_ids);

        foreach (array_keys($letters) as $i) {
            $l = & $letters[$i];
            $l['topics_names'] = @implode(', ', @$letters_topics_names[ $l['letter_id'] ]);
            if (empty($l['topics_names'])) {
                $l['topics_names'] = '-';
            }
        }
    }

    var $_templateContents;
    var $_listTemplateContents;
    var $_tmplFiller;
    var $_messageResources;


    /**#@-*/

}
?>