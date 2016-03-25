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

_use(dirname(__FILE__).'/gc_edit_az.php');

/**
 * Output Gift Certificate Editor
 *
 * @package GiftCertificate
 * @author Alexey Florinsky
 */
class GiftCertificateAddView extends GiftCertificateEditView
{
    function GiftCertificateAddView()
    {
        parent::GiftCertificateEditView();
    }

    function getCurrentGC()
    {
        loadClass('GiftCertificateCreator');
        $gc = new GiftCertificateCreator();
        if (modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            $SessionPost = modApiFunc('Session', 'get', 'SessionPost');
            modApiFunc('Session', 'un_Set', 'SessionPost');
            $gc->initByMap($SessionPost);
        }
        return $gc;
    }

    function getActionName()
    {
        return 'GiftCertificateAddAction';
    }

    function getTag($tag)
    {
        global $application;

        $value = null;
        if ($tag == 'Local_mode') $value = 'add';
        return $value;
    }

}

?>