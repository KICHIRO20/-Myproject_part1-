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
/***********************************************************************
| Avactis (TM) Shopping Cart software developed by HBWSL.
| http://www.avactis.com
| -----------------------------------------------------------------------
| All source codes & content (c) Copyright 2004-2013, HBWSL.
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
 * Banner module.
 * "Banner -> Banner Location" View.
 *
 * @package Banner
 * @access  public
 * @author  Ninad
 *
 */


class BannerLocation
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * The view constructor.
     * About data flow. All data is transferred.
     * <p> Action -> View :
     * <p> Through session variable @var $SessionPost (created from POST data),
     * especially it's $SessionPost["ViewState"] array, containing current View
     * state information. The state does not include such information like already
     * inputted name, description values. It includes variables, determining the
     * view structure: table or list, image or input field etc. @see @var SessionPost.
     * <p> View -> Action :
     * <p> Through POST data. All form'related session data is removed while
     * processing view output.
     */
    function BannerLocation()
    {
        global $application;
        $this->mTmplFiller = &$application->getInstance('TmplFiller');

    }
    function output()
    {
    	global $application;
        $template_contents = array();
        $template_contents= array(

                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller->setTemplatePath("avactis-extensions/");
        return $this->mTmplFiller->fill("banner/", "banner_location/banner_location.tpl.html", array());
    }


    /**
     * @ describe the function AddCategory->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
        if ($value == null)
        {
            switch ($tag)
        	{
        	    case 'Breadcrumb':
                    $obj = &$application->getInstance('Breadcrumb');
                    $value = $obj->output(false);
        	        break;

        	    case 'ErrorIndex':
        	        $value = $this->_error_index;
        	        break;

        	    case 'Error':
        	        $value = $this->_error;
        	        break;
        	};
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
     * Pointer to the module object.
     */
    var $pCatalog;

    /**
     * Pointer to the template filler object.
     * It needs to track sequences of identical templates, like lists.
     */
    var $mTmplFiller;
    /**#@-*/

    /**
     * Pointer to the received from action or prepared FORM data.
     */
    var $POST;

    /**
     * View state structure. It comes from action.
     * $SessionPost["ViewState"] structure example:
     * <br>array
     * <br>(
     * <br>    "hasCloseScript"  = "false"           //true/false
     * <br>    "ErrorsArray"     =  array()          //true/false
     * <br>    "LargeImage"      = "image.jpg"       //
     * <br>    "SmallImage"      = "image_small.jpg" //
     * <br>)
     */
    var $ViewState;

    /**
     * List of error ids. It comes from action.
     */
    var $ErrorsArray;
    var $ErrorMessages;

    var $_Template_Contents;

    var $MessageResources;
    var $_error_index;
    var $_error;
}
?>