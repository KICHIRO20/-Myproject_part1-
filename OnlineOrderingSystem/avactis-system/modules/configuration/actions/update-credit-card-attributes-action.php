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
 * Action handler on update credit card attributes.
 *
 * @package Configuration
 * @access  public
 * @author Ravil Garafutdinov
 */
class UpdateCreditCardAttributes extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     *
     */
    function UpdateCreditCardAttributes()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $cc_id = $request->getValueByKey("cc_id");
        $attrs = $request->getValueByKey("attrs");
        $post_visible = $request->getValueByKey("visible");
        $post_required = $request->getValueByKey("required");
        $SessionPost = $_POST;

        $visible = array();
        $required = array();
        foreach ($attrs as $id)
        {
            if (isset($post_visible[$id]) && $post_visible[$id] == 'on')
            {
                $visible[$id] = 1;
            }
            else
            {
                $visible[$id] = 0;
            }
            if (isset($post_required[$id]) && $post_required[$id] == 'on')
            {
                $required[$id] = 1;
            }
            else
            {
                $required[$id] = 0;
            }
        }

        $params = array(
             'type' => $cc_id
            ,'visible' => $visible
            ,'required' => $required
        );

        modApiFunc('Configuration', 'clearAttributesForCardType', $cc_id);
        modApiFunc('Configuration', 'addAttributesForCardType', $params);

//        modApiFunc('Session', 'set', 'ResultMessage', $messages);

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setKey('cc_id', $cc_id);
        $application->redirect($request);
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