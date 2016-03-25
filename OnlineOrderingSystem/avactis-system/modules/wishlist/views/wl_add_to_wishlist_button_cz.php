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
 * @package WishList
 * @author Sergey E. Kulitsky
 *
 */

class AddToWishlistButton
{
    function AddToWishlistButton()
    {
        global $application;

        $this -> mTmplFiller = new TemplateFiller();
        $this -> _templates = array(
            'container'    => 'AddToWishListButtonContainer',
        );

        $this -> NoView = false;
        if ($application -> issetBlockTagFatalErrors('AddToWishlistButton'))
            $this -> NoView = true;
    }

    function getTemplateFormat()
    {
        $format = array(
            'layout-file' => 'wl-add-to-wishlist-button-block.ini',
            'files'       => array(
                'AddToWishListButtonContainer' => TEMPLATE_FILE_SIMPLE
            ),
            'options'     => array(
            )
        );
        return $format;
    }

    /**
     * The output of the Viewer
     */
    function output()
    {
        global $application;

        if ($this -> NoView)
            return '';

        if(modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_ENABLE_WISHLIST) == 0)
            return '';

        $settings = modApiFunc('Customer_Account','getSettings');
        if(isset($settings['CHECKOUT_TYPE']) && $settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_QUICK)
            return '';

        // wishlist is available only for signed in customers
        // so showing nothing for anonymous ones
        //if (!modApiFunc('Customer_Account', 'getCurrentSignedCustomer'))
        //    return '';

        // setting up the template engine
        $template_block = $application -> getBlockTemplate('AddToWishlistButton');
        $this -> mTmplFiller -> setTemplate($template_block);

        // no local tags needed so just showing the template
        return $this -> mTmplFiller -> fill($this -> _templates['container']);
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $NoView;
    var $mTmplFiller;
    var $_Template_Contents;
};

?>