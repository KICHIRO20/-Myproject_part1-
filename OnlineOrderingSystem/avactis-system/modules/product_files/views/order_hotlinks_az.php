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
 * @package ProductFiles
 * @author Egor V. Derevyankin
 *
 */

class PF_OrderHotlinks
{
    function PF_OrderHotlinks()
    {
    }

    function output_HotlinksList()
    {
        global $application;
        $html_code = '';

        foreach($this->PFHotlinks as $k => $hl_info)
        {
            $finfo = modApiFunc('Product_Files','getPFileInfo',$hl_info['file_id']);
            $template_contents = array(
                'FileName' => ($finfo != null and file_exists($finfo['file_path']) and is_file($finfo['file_path'])) ? $finfo['file_name'] : '<span style="color: red;">'.getMsg('PF','FILE_WAS_DELETED').'</span>'
               ,'HotlinkKey' => /*$hl_info['hotlink_key']*/ str_rev_pad($hl_info['hotlink_value'],90)
               ,'HotlinkValue' => $hl_info['hotlink_value']
               ,'HotlinkExpireDate' => date("d M Y, H:i",$hl_info['expire_date'])
               ,'jsHotlinkED' => date("Y/m/d/H/i",$hl_info['expire_date'])
               ,'HotlinkTries' => $hl_info['was_try'].'/'.$hl_info['max_try']
               ,'HotlinkStatus' => $hl_info['status']
               ,'ZeroTriesButton' => '<a class="btn btn-default green'.($hl_info['was_try']==0 ? ' button_disabled' : '').'" onClick="'.($hl_info['was_try']==0 ? '' : 'go(\'popup_window.php?asc_action=zero_hotlink_tries&opid='.$this->opid.'&hl_id='.$hl_info['hotlink_id'].'\')').'">'.getMsg('PF','BTN_ZERO_TRIES').'</a>'
               ,'HotlinkID' => $hl_info['hotlink_id']
               ,'OPID' => $this->opid
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("product_files/order_hotlinks/", "one-hotlink.tpl.html",array());
        }

        return $html_code;
    }

    function output()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $this->opid = $request->getValueByKey('opid');
        $this->PFHotlinks = modApiFunc('Product_Files','getHotlinksList',$this->opid);

        $op_info = modApiFunc('Checkout','getOrderProductInfo',$this->opid);

        $template_contents = array(
            'OrderId' => sprintf("%05d",$op_info['order_id'])
           ,'ProductName' => $op_info['order_product_name']
           ,'HotlinksList' => $this->output_HotlinksList()
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_files/order_hotlinks/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $opid;
    var $PFHotlinks;
};

?>