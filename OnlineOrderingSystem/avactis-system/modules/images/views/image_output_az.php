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
 *                 .
 *                   image_obj.
 *  . .                               : image_id            NULL (               )            .
 *                                                 .
 * (                                                         )
 */
class image_output_az
{
    function image_output_az()
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
        if(!$this->image_obj->is_empty())
        {
        	//:                    Images :: getImageData($image_id)
            $image_data = modApiFunc("Images", "selectImageThumbnail", $this->image_obj->get_id(), IMAGE_THUMB_SIZE);
            if($image_data === NULL)
            {
                $this->_Template_Contents = array
                (
                    "IOId" => $parameters[0]
                   ,"IOSrc" => modApiFunc("Images", "getAZImageSRC", EMPTY_IMAGE_BASENAME)
                   ,"IOAltText" => getMsg('IMG', 'EMPTY_IMAGE_ALT_TEXT')
                );
            }
	        else
	        {
                $this->_Template_Contents = array
                (
                    "IOId" => $parameters[0]
                   ,"IOSrc" => modApiFunc("Images", "getImageSRC", $image_data)
                   ,"IOAltText" => prepareHTMLDisplay($image_data['image_alt_text'])
                );
            }
        }

        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/');
        $res = $this->mTmplFiller->fill("image_output/", "container.tpl.html",array());
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