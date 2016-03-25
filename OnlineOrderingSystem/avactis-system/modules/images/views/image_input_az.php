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
 * @package Images
 * @author Vadim Lyalikov
 *
 */

/**
 *                                    .
 *                   image_obj.
 *  . .                               : image_id            NULL (               )            .
 *                                                           .                   .
 */
class image_input_az
{
    function image_input_az()
    {
    }

    function asc_ctor($image_obj)
    {
    	$this->image_obj = $image_obj;
    }

    function output()
    {
        global $application;
        $parameters = func_get_args();
        $this->asc_ctor($parameters[1]);

        $image_data = modApiFunc("Images", "getImageData", $this->image_obj->get_id());
        $image_data['image_obj'] = $this->image_obj;

        loadCoreFile('JsHttpRequest.php');

        $this->_Template_Contents = array
        (
            "IIId" => $parameters[0]
           ,"IIimage_data" => JsHttpRequest::php2js($image_data)
           ,"EMPTY_IMAGE_SRC" => modApiFunc("Images", "getAZImageSRC", EMPTY_IMAGE_BASENAME)
           ,"EMPTY_IMAGE_WIDTH" => EMPTY_IMAGE_WIDTH
           ,"EMPTY_IMAGE_HEIGHT" => EMPTY_IMAGE_HEIGHT
           ,"IMAGE_THUMB_SIZE" => IMAGE_THUMB_SIZE
           ,"EMPTY_IMAGE_ALT_TEXT" => getMsg('IMG', 'EMPTY_IMAGE_ALT_TEXT')
        );
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/');
        $res  = $this->mTmplFiller->fill("image_input/", "js.tpl.html",array());
        $res .= $this->mTmplFiller->fill("image_input/", "container.tpl.html",array());
        return $res;
    }

    function getTag($tag)
    {
        global $application;

        $value = null;
        if(isset($this->_Template_Contents[$tag]))
        {
        	$value = $this->_Template_Contents[$tag];
        }
        return $value;
    }

    var $image_obj;
};

?>