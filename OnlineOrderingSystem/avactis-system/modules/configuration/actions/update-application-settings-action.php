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
 * Action handler on clear cache.
 *
 * @package Configuration
 * @access  public
 * @author Alexey Florinsky
 */
class UpdateApplicationSettings extends AjaxAction
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
    function UpdateApplicationSettings()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;
        $r = $application->getInstance('Request');
        $data = $r->getValueByKey(SETTIGS_POST_MAP_NAME);
        $group = $r->getValueByKey('group');
        $view = $r->getValueByKey('page_view');

        $messages = array( /*'ERRORS' => array(), 'MESSAGES' => array()*/ );
        if ($data != null and is_array($data))
        {
            foreach ($data as $gname=>$gdata)
            {
                if (modApiFunc('Settings','isGroupExist', $gname) == false)
                {
                    $messages['ERRORS'][] = str_replace('{GRP}',$gname, getMsg('SYS','GRP_DOESNT_EXIST'));
                    break;
                }

                foreach ($gdata as $pname=>$pvalue)
                {
                    if (modApiFunc('Settings','isParamExist', $gname, $pname) == false)
                    {
                        $messages['ERRORS'][] = str_replace('{PRM}',$pname, getMsg('SYS','PARAM_DOESNT_EXIST'));
                    }

                    if (modApiFunc('Settings','setParamValue', $gname, $pname, $pvalue) == false)
                    {
                        $messages['ERRORS'][] = str_replace('{PRM}',modApiFunc('Settings','getParamNameDescription',$gname, $pname), getMsg('SYS','FAILED_TO_SET'));
                    }
                    else
                    {
                        modApiFunc('EventsManager','throwEvent','AdvancedSettingsUpdated');
                    }
                }
            }

            if (!isset($messages['ERRORS']))
            {
                $messages['MESSAGES'][] = getMsg('SYS','PARAM_UPDATED');
            }
            modApiFunc('Session','set','AplicationSettingsMessages', $messages);
        }

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setKey('group', $group);
        $request->setKey('page_view', $view);
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