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
 * @package ProductImages
 * @author Egor V. Derevyankin
 *
 */

class PI_ImagesList
{
    function PI_ImagesList()
    {
        loadCoreFile('html_form.php');
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('PI',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("product_images/misc/", "result-message.tpl.html",array());
        }
        elseif(modApiFunc("Session","is_set","Errors"))
        {
            $return_html_code="";
            $errors=modApiFunc("Session","get","Errors");
            modApiFunc("Session","un_set","Errors");
            foreach($errors as $ekey => $eval)
            {
                $template_contents=array(
                    "ErrorMessage" => (is_int($eval) ? modApiFunc('Shell','getMsgByErrorCode',$eval) : getMsg('PI',$eval))
                );
                $this->_Template_Contents=$template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $return_html_code.=$this->mTmplFiller->fill("product_images/misc/", "error-message.tpl.html",array());
            }

            return $return_html_code;
        }
        else
            return "";
    }

    function outThumbIMG($img_info,$thumb_side=70,$img_id='',$out_link=true)
    {
        global $application;

        if($img_info['thumb_path']!='')
        {
            $sizes = $img_info['thumb_sizes'];
			if(file_exists($application->getAppIni('PATH_IMAGES_DIR').$img_info['thumb_path']))
				$thumb_url = $application->getAppIni('URL_IMAGES_DIR').$img_info['thumb_path'];
			else
	            $thumb_url = $application->getAppIni('URL_IMAGES_DIR').basename($img_info['thumb_path']);
        }
        else
        {
            $sizes = $img_info['image_sizes'];
			if(file_exists($application->getAppIni('PATH_IMAGES_DIR').$img_info['thumb_path']))
	            $thumb_url = $application->getAppIni('URL_IMAGES_DIR').$img_info['image_path'];
			else
	            $thumb_url = $application->getAppIni('URL_IMAGES_DIR').basename($img_info['image_path']);
        };

		if(file_exists($application->getAppIni('PATH_IMAGES_DIR').$img_info['image_path']))
        	$image_url = $application->getAppIni('URL_IMAGES_DIR').$img_info['image_path'];
		else
        	$image_url = $application->getAppIni('URL_IMAGES_DIR').basename($img_info['image_path']);
        $sizes = unserialize($sizes);

        list($x,$y) = modApiFunc('Product_Images','convertSizes',$sizes['width'],$sizes['height'],$thumb_side);

        if($out_link)
            return '<a class="color_box" href="'.$image_url.'" target="_blank"><img '.($img_id!=''?'id="'.$img_id.'"':'').' src="'.$thumb_url.'" width="'.$x.'" height="'.$y.'" border="0"></a>';
        else
            return '<img '.($img_id!=''?'id="'.$img_id.'"':'').' src="'.$thumb_url.'" width="'.$x.'" height="'.$y.'" border="0">';
    }

    function outImageSizes($img_info)
    {
        $sizes = unserialize($img_info['image_sizes']);
        $html_code = $sizes['width'].'x'.$sizes['height'].', '.modApiFunc('Product_Files','formatFileSize',$sizes['filesize']);
        return $html_code;
    }

    function outImagesByOne()
    {
        global $application;
        $html_code = '';//'<tr bgcolor="#CED4DD"><td colspan="2">'.getMsg('PI','HDR_IMAGE').'</td><td>'.getMsg('PI','HDR_ALT_TEXT').'</td><td>'.getMsg('PI','HDR_SIZE').'</td></tr>';

        foreach($this->PImages as $k => $pi_info)
        {
            $template_contents = array(
                'CycleColor' => ($k % 2) == 0 ? '#FFFFFF' : '#EEF2F8'
               ,'ImageID' => $pi_info['image_id']
               ,'ImageThumb' => $this->outThumbIMG($pi_info)
               ,'ImageSize' => $this->outImageSizes($pi_info)
               ,'ImageAltTextField' => HtmlForm::genInputTextField('','img_alt_text['.$pi_info['image_id'].']','50',$pi_info['alt_text'])
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("product_images/images_list/", "one-image.tpl.html",array());
        }

        return $html_code;
    }

    function out_Buttons()
    {
        global $application;
        $template_contents = array(
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_images/images_list/", "buttons.tpl.html",array());
    }

    function output_ImagesList()
    {
        global $application;
        $template_contents = array(
            'ProductID' => $this->product_id
           ,'ListByOne' => count($this->PImages) ? $this->outImagesByOne() : '<tr><td colspan=4 class="value popup_dialog_body_right_padded" style="padding-top: 50px; padding-bottom: 50px; vertical-align: middle; text-align: center; font-weight: bold;">'.getMsg('PI','NO_IMAGES_DEFINED').'</td></tr>'
           ,'Buttons' => count($this->PImages) ? $this->out_Buttons() : ''
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_images/images_list/", "list.tpl.html",array());
    }

    function output_AddImageForm()
    {
        global $application;

        $err_code = modApiFunc('Product_Images','checkImageUploading');

        if($err_code != 0)
        {
            $template_contents = array(
                'ErrorAddImage' => ($err_code == 1) ? getMsg('PI','ERR_UPL_DISABLED_IN_INI') :
                    ( ($err_code == 2) ? getMsg('PI','ERR_UPL_DIR_IS_NOT_DIR',$application->getAppIni('PATH_IMAGES_DIR')) :
                        getMsg('PI','ERR_UPL_DIR_IS_NOT_WRITABLE',$application->getAppIni('PATH_IMAGES_DIR')) )
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("product_images/images_list/", "error-add-image-form.tpl.html",array());
        }
        else
        {
            $sets = modApiFunc('Product_Images','getSettings');

            $template_contents = array(
                'ProductID' => $this->product_id
               ,'AddImageNoteMessage' => getMsg('PI','ADD_IMAGE_NOTE_MSG',modApiFunc('Product_Files','formatFileSize',modApiFunc('Shell','getMaxUploadSize')))
               ,'ThumbSizeNoteMessage' => getMsg('PI','NOTE_THUMB_SIZES',$sets['THUMB_SIDE'],$sets['THUMB_SIDE'])
               ,'imgDirURL' => $application->getAppIni('URL_IMAGES_DIR')
               ,'imgDirPath' => $application->getAppIni('PATH_IMAGES_DIR')
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("product_images/images_list/", "add-image-form.tpl.html",array());
        };
    }

    function out_jsSOarr()
    {
        $a = array();
        foreach($this->PImages as $k => $image_info)
            $a[] = $image_info['image_id'];
        return implode(",",$a);
    }

    function out_ThumbsTable()
    {
        global $application;
        $html_code = '';

        $sets = modApiFunc('Product_Images','getSettings');

        $outed = 0;
        $cur_line = 0;
        $per_line = $sets['THUMBS_PER_LINE'];

        foreach($this->PImages as $k => $image_info)
        {
            if($outed == 0)
                $html_code .= '<tr>';

            $template_contents = array(
                'ThumbIMG' => $this->outThumbIMG($image_info,$sets['THUMB_SIDE'],'c_'.$cur_line.'_'.$outed,false)
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("product_images/sort_form/", "item.tpl.html",array());

            $outed++;

            if($outed < $per_line and ($cur_line * $per_line + $outed) < count($this->PImages))
                $html_code .= '<td><i style="cursor:pointer;" class="fa fa-arrows-h" onClick="switchImages('.($cur_line).','.($outed-1).','.($cur_line).','.($outed).');"> </i></td>';

            if($outed == $per_line)
            {
                $html_code .= '</tr>';
                $outed = 0;
                $cur_line++;
            };

            if($outed ==0 and $cur_line * $per_line < count($this->PImages))
            {
                $html_code .= '<tr>';
                for($i=0;$i<$per_line;$i++)
                {
                    if($cur_line * $per_line + $i < count($this->PImages))
                        $html_code .= '<td><img src="images/arrows-switch-ud.gif" onClick="switchImages('.($cur_line-1).','.$i.','.($cur_line).','.$i.');"></td><td></td>';
                    else
                        $html_code .= '<td></td>'.($i!=($per_line-1)?'<td></td>':'');
                }
                $html_code .= '</tr>';
            };
        }

        if($outed != 0)
            $html_code .= str_repeat('<td></td>',($per_line - $outed)*2).'</tr>';

        return $html_code;
    }

    function outputSortForm()
    {
        global $application;
        $settings = modApiFunc('Product_Images','getSettings');
        $thumbs_per_line = $settings['THUMBS_PER_LINE'];

        $sets = modApiFunc('Product_Images','getSettings');

        $template_contents = array(
            'jsSortOrderArrayValues' => $this->out_jsSOarr()
           ,'ThumbsTable' => $this->out_ThumbsTable()
           ,'ProductID' => $this->product_id
           ,'ThumbSide' => $sets['THUMB_SIDE']
           ,'ThumbsPerLine' => $thumbs_per_line
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_images/sort_form/", "container.tpl.html",array());
    }

    function output()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $this->product_id = $request->getValueByKey('product_id');
        $settings = modApiFunc('Product_Images','getSettings');

        $prod_obj = &$application->getInstance('CProductInfo',$this->product_id);
        $this->PImages = modApiFunc('Product_Images','getImagesListForProduct',$this->product_id);

        $template_contents = array(
            'ProductName' => $prod_obj->getProductTagValue('Name')
           ,'Local_ProductBookmarks' => getProductBookmarks('images',$this->product_id)
           ,'ResultMessage' => $this->outputResultMessage()
           ,'ImagesList' => $this->output_ImagesList()
           ,'AddImageForm' => $this->output_AddImageForm()
           ,'SortForm' => $this->outputSortForm()
           ,'UpdButDisplay' => (count($this->PImages) > 0 ? '' : 'none')
           ,'ThumbsPerLine' => $settings['THUMBS_PER_LINE']
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_images/images_list/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        if ($tag == 'ProductInfoLink') {
            $cz_layouts = LayoutConfigurationManager::static_get_cz_layouts_list();
            LayoutConfigurationManager::static_activate_cz_layout(array_shift(array_keys($cz_layouts)));
            $request = new CZRequest();
            $request->setView  ( 'ProductInfo' );
            $request->setAction( 'SetCurrentProduct' );
            $request->setKey   ( 'prod_id', $this->product_id);
            $request->setProductID($this->product_id);
            return $request->getURL();
        }
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $product_id;
    var $PImages;
};

?>