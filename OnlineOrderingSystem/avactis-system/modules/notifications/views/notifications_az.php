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
 * Notifications Module, OrderChangedStatusNotifications View.
 *
 * @package Notifications
 * @author Alexey Florinsky
 */
class NotificationsList
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * OrderInitiallyPlacedNotifications  constructor.
     */
    function NotificationsList ()
    {
    }

    /**
     * Outputs a list of specified views.
     */
    function outputNotificationsList()
    {
        global $application;
        $retval = "";

        $list = modApiFunc("Notifications", "getNotificationsList");
        $n = sizeof($list);
        if ($n == 0)
        {
            $retval.= modApiFunc('TmplFiller', 'fill', "notifications/list/","item_na.tpl.html", array());
            $n++;
        }
        else
        {
            $i = 0;
            foreach ($list as $item)
            {
                $request = new Request();
                $request->setView('NotificationInfo');
                $request->setAction('SetCurrentNotification');
                $request->setKey('n_id', $item['Id']);
                $Info_Link = $request->getURL();

                $item['I'] = $i;
                $item['InfoLink'] = $Info_Link;
                $item['Active'] = $item['Active']=='checked'? getMsg('NTFCTN','NTFCTN_INFO_YES_LABEL'):getMsg('NTFCTN','NTFCTN_INFO_NO_LABEL');
                $item['From_addr'] = modApiFunc("Notifications", "getExtendedEmail" ,$item['From_addr'], $item['Email_Code'], false, (!empty($item['Admin_ID']) ? $item['Admin_ID'] : NULL));
                $this->_Template_Contents = $item;
                $application->registerAttributes($this->_Template_Contents);
                $retval.= modApiFunc('TmplFiller', 'fill', "notifications/list/","item.tpl.html", array());
                $i++;
            }
        }
        for ($i=0; $i<(10-$n); $i++)
        {
            $retval.= modApiFunc('TmplFiller', 'fill', "notifications/list/","item_empty.tpl.html", array());
        }
        return $retval;
    }

    /**
     * Outputs a view.
     */
    function output()
    {
        global $application;

        $request = new Request();
        $request->setView('NotificationInfo');
        $request->setAction('SetCurrentNotification');
        $request->setKey('n_id', 'Add');
        $Add_Link = $request->getURL();

        $request = new Request();
        $request->setView('NotificationInfo');
        $request->setAction('SetCurrentNotification');
        $request->setKey('n_id', '');
        $Edit_Link = $request->getURL();

        $request = new Request();
        $request->setView('Notifications');
        $request->setAction('DeleteNotification');
        $request->setKey('n_id', '');
        $Delete_Link = $request->getURL();

        $template_contents = array(
                                    'Items' => $this->outputNotificationsList()
                                   ,'AddLink' => $Add_Link
                                   ,'EditLink' => $Edit_Link
                                   ,'DeleteLink' => $Delete_Link
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "notifications/list/","container.tpl.html", array());
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        if (array_key_exists($tag, $this->_Template_Contents))
        {
            $value = $this->_Template_Contents[$tag];
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


    /**#@-*/

}
?>