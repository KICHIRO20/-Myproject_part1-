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
 *                        compose.
 *                                                                  .
 * @package Newsletter
 * @author Egor Makarov
 */
class do_newsletter_save extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * do_newsletter_save constructor
     */
    function do_newsletter_save()
    {
    }

    function saveLetter($session_post)
    {
        $new_letter_id = null;
        if (empty($session_post['letter_id']))
        {
            $new_letter_id = modApiFunc('Newsletter', 'addMessage', $session_post);
        }
        else
        {
            modApiFunc('Newsletter', 'updateMessage', $session_post['letter_id'], $session_post);
            $new_letter_id = $session_post['letter_id'];
        }
        modApiFunc('Subscriptions', 'setLetterTopics', $new_letter_id, @$session_post['topic']);
        return $new_letter_id;
    }

    /**
     *
     */
    function onAction()
    {
        global $application;

    	$request = new Request();

        $session_post = array();
        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            modApiFunc('Session', 'un_set', 'SessionPost');
        }

        $session_post = $_POST;

        switch ($session_post['ViewState']['FormSubmitValue'])
        {
        	case 'save':
                // :
                $session_post['ViewState']['ErrorsArray'] = array();

                if (empty($session_post['letter_from_name']) == true || trim($session_post['letter_from_name']) == null)
                {
                	$session_post['ViewState']['ErrorsArray'][] = 'ERROR_NO_FROM_NAME';
                }

                if (empty($session_post['letter_from_email']) == true || trim($session_post['letter_from_email']) == null)
                {
                    $session_post['ViewState']['ErrorsArray'][] = 'ERROR_NO_FROM_EMAIL';
                }

                if (empty($session_post['letter_subject']) == true || trim($session_post['letter_subject']) == null)
                {
                    $session_post['ViewState']['ErrorsArray'][] = 'ERROR_NO_SUBJECT';
                }

                if (sizeof($session_post['ViewState']['ErrorsArray']) == 0)
                {
                	unset($session_post['ViewState']['ErrorsArray']);
                    $new_letter_id = $this->saveLetter($session_post);
                    modApiFunc('Session','set','ResultMessage','NLT_SAVE_NEWSLETTER');
//                    $session_post['ViewState']['hasCloseScript'] = 'true';

					$request = new Request();
        			$request->setView(CURRENT_REQUEST_URL);
        			$request->setKey('letter_id',$new_letter_id);
					$application->redirect($request);
                }
                modApiFunc('Session', 'set', 'SessionPost', $session_post);
                break;
            default:
                break;
        }
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */


    /**#@-*/

}
?>