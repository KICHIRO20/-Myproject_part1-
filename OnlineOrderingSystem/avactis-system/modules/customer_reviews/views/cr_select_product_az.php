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
 * @package Customer_Reviews
 * @author Sergey E. Kulitsky
 *
 */

/**
 * Definition of CR_Select_Product viewer
 * The viewer is used to select a product
 */
class CR_Select_Product
{
    /**
     * Constructor
     */
    function CR_Select_Product()
    {
        // initializing the template filler
        $this -> mTmplFiller = new TmplFiller();

        // getting params from the request
        $this -> _formname = modApiFunc('Request', 'getValueByKey',
                                        'formname');
        $this -> _pidfield = modApiFunc('Request', 'getValueByKey',
                                        'pidfield');
        $this -> _pnamefield = modApiFunc('Request', 'getValueByKey',
                                          'pnamefield');
        $this -> _prefilledPID = modApiFunc('Request', 'getValueByKey',
                                            'productid');

        // if we need to run onproductselection method
        // in the parent window (yes/no)
        $this -> _use_callback = modApiFunc('Request', 'getValueByKey',
                                            'use_callback');

        // validating the params
        if (!$this -> _formname)
            $this -> _formname = 'SelectProductForm';

        if (!$this -> _pidfield)
            $this -> _pidfield = 'product_id';

        if (!$this -> _pnamefield)
            $this -> _pnamefield = 'product_name';

        if ($this -> _use_callback != 'yes')
            $this -> _use_callback = 'no';
    }

    /**
     * The main function to output the viewer
     */
    function output()
    {
        global $application;

        // filling params for product browser
        $pb_params = array(
            'show_category_path' => true,
            'buttons' => array(
                'add' => array(
                    'label' => 'BTN_SELECT',
                    'callback' => 'selectProduct(%PID%, %PNAME%);',
                    'default_state' => 'disabled',
                    'enable_condition' => 'product_selected'
                )
            ),
            'choosed_control_array' => 'product_array'
        );

        // creating product browser class
        loadClass('ProductsBrowser');
        $this -> pb_obj = new ProductsBrowser();

        $template_contents = array(
            'Local_ProductsBrowser' => $this -> pb_obj -> output($pb_params),
            'CallbackForm'          => $this -> _formname,
            'CallbackIDField'       => $this -> _pidfield,
            'CallbackNameField'     => $this -> _pnamefield,
            'CallbackFunction'      => $this -> _use_callback,
            'PrefilledPID'          => $this -> _prefilledPID
        );

        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'customer_reviews/select_product/',
                   'container.tpl.html',
                   array()
               );
    }

    /**
     * Processes the tags
     */
    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $mTmplFiller;
    var $pb_obj;
    var $_formname;
    var $_pidfield;
    var $_pnamefield;
    var $_use_callback;
    var $_prefilledPID;
};

?>