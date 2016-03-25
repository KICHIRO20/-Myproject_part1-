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
 * Checkout Payment Method List view.
 *
 * @package Checkout
 * @author Vadim Lyalikov
 */
class CheckoutNavigationBar
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Gets a template format for the given view.
     */
    function getTemplateFormat()
    {
    	$format = array(
    	    'layout-file'        => 'checkout-navigation-bar-config.ini'
    	   ,'files' => array(
    	        'Container'      => TEMPLATE_FILE_SIMPLE,
    	        'Item'           => TEMPLATE_FILE_SIMPLE,
    	        'ItemClickable'  => TEMPLATE_FILE_SIMPLE
    	    )
    	   ,'options' => array(
    	    )
    	);
    	return $format;
    }

    /**
     *  CheckoutNavigationBar constructor.
     */
    function CheckoutNavigationBar()
    {
        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("CheckoutNavigationBar"))
        {
            $this->NoView = true;
        }

        $this->checkoutCurrStepID = modApiFunc("Checkout", "getCurrentStepID");
    }

    /**
     * Returns the Checkout NavigationBar List view.
     *
     * It gets a list of all the steps in the checkout process.
     * checkout         . It should be known each name of each step and                                            ,
     * references for the already taken steps. To click it the user can go for the
     * required step.
     *
     * All steps with numbers less then a current one are already taken.
     * The step with the number checkoutCurrStepID is not taken.
     *
     * @ finish the functions on this page
     */
    function outputCheckoutNavigationBarList()
    {
        global $application;

        $checkoutStepsInfo = modApiFunc("Checkout", "getStepsInfo");

        $application->registerAttributes(array("StepLink" => "",
                                               "StepName" => ""
                                              )
                                        );

        $retval = "";
        foreach($checkoutStepsInfo as $checkoutStepInfo)
        {
            if ($checkoutStepInfo['ID'] < $this->checkoutCurrStepID)
            {
            	$template = "ItemClickable";
            }
            else
            {
            	$template = "Item";
            }
            $this->_Step_Info = $checkoutStepInfo;
            $retval .= $this->mTmplFiller->fill($template);
        }

        return $retval;
    }

    /**
     * Returns the generated ProductList view.
     *
     * @return string
     */
    function output()
    {
        global $application;

        #Define whether to output the view or not
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "CheckoutNavigationBar", "Errors");
            return;
        }
        else
        {
            $application->outputTagErrors(true, "CheckoutNavigationBar", "Warnings");
        }

        $this->mTmplFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate('CheckoutNavigationBar');
        $this->mTmplFiller->setTemplate($this->template);

        $application->registerAttributes(array("Items" => ""));

        $retval = $this->mTmplFiller->fill("Container");

        return $retval;
    }

    /**
     * Processes tags in the templates for the given view.
     *
     * @return string tag value, if the tag is not processed. NULL otherwise.
     */
    function getTag($tag)
    {
        $value = null;
    	switch ($tag)
    	{
    	    case 'Items':
                $value = $this->outputCheckoutNavigationBarList();
    	        break;

    	    default:
    	        list($entity, $tag) = getTagName($tag);
        	    if ($entity == 'step')
        	    {
        	        $value = getKeyIgnoreCase($tag, $this->_Step_Info);
        	    }
        		break;
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

    /**
     * Reference to the object TemplateFiller.
     *
     * @var TemplateFiller
     */
    var $templateFiller;

    /**
     * The current selected template.
     *
     * @var array
     */
    var $template;

    /**
     * The number of the current step Checkout. It save in its own variable,
     * to avoid the errors, which can occur if it changes suddenly.
     *
     * @var array
     */
    var $checkoutCurrStepID;

    /**
     * Info about the current step outputted to the template.
     *
     * @var array
     */
    var $_Step_Info;

    /**#@-*/
}
?>