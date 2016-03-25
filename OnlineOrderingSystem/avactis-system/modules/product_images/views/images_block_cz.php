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

class ProductDetailedImages
{
    function ProductDetailedImages()
    {
    }

    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'product-images-block.ini'
           ,'files' => array(
                'BlockContainer' => TEMPLATE_FILE_SIMPLE
               ,'BlockItem' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function genThumbImageTag()
    {
        global $application;

        $img_info = $this->current_image;

        if($img_info['thumb_path']!='')
        {
            $sizes = $img_info['thumb_sizes'];
            $thumb_url = modApiFunc('Product_Images', 'getImageURL', $this->current_image['thumb_path']);
        }
        else
        {
            $sizes = $img_info['image_sizes'];
            $thumb_url = modApiFunc('Product_Images', 'getImageURL', $this->current_image['image_path']);
        };

        $sizes = unserialize($sizes);

        list($x,$y) = modApiFunc('Product_Images','convertSizes',$sizes['width'],$sizes['height'],$this->sets['THUMB_SIDE']);

        return '<img src="'.$thumb_url.'" width="'.$x.'" height="'.$y.'" border="0" alt="'.$img_info['alt_text'].'">';
    }

    function outputOneImage($image_info)
    {
        $this->current_image = $image_info;
        return $this->templateFiller->fill('BlockItem');
    }

    function output()
    {
        global $application;
        if(func_num_args() == 0)
            $product_id = modApiFunc('Catalog','getCurrentProductId');
        else
            $product_id = func_get_arg(0);
        $this->PImages = modApiFunc('Product_Images','getImagesListForProduct',$product_id);
        if(empty($this->PImages))
            return '';

        $this->sets = modApiFunc('Product_Images','getSettings');

        $_tags = array(
            'Local_ImagesList'
           ,'Local_ImageId'
           ,'Local_ImageWidth'
           ,'Local_ImageHeight'
           ,'Local_ImageAltText'
           ,'Local_ImageSrc'
           ,'Local_Image'
           ,'Local_ThumbnailId'
           ,'Local_ThumbnailWidth'
           ,'Local_ThumbnailHeight'
           ,'Local_ThumbnailAltText'
           ,'Local_ThumbnailSrc'
           ,'Local_Thumbnail'
           ,'Local_Columns'
        );

        $application->registerAttributes($_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('ProductDetailedImages');
        $this->templateFiller->setTemplate($this->template);

        return $this->templateFiller->fill('BlockContainer');
    }

    function getTag($tag)
    {
        global $application;

        $value = null;

        switch($tag)
        {
            case 'Local_ImagesList':
                $disable_trtd = $application->getAppIni('PRODUCT_LIST_DISABLE_TR_TD');
                if ($disable_trtd != null && strtolower($disable_trtd) === 'yes')
                    $disable_trtd = true;
                else
                    $disable_trtd = false;

                $value = '';
                $outed = 0;
                $per_line = $this->sets['THUMBS_PER_LINE'];
                foreach($this->PImages as $k => $image_info)
                {
                    if($outed == 0)
                    {
                        if ($disable_trtd == false) $value .= '<tr>';
                    }

                    $value .= $this->outputOneImage($image_info);

                    $outed++;
                    if($outed == $per_line)
                    {
                        if ($disable_trtd == false) $value .= '</tr>';
                        $outed = 0;
                    };
                };

                if($outed != 0)
                {
                    if ($disable_trtd == false) $value .= str_repeat('<td></td>',($per_line - $outed)) . '</tr>';
                }

                break;

            case 'Local_ImageId':
                $value = 'i'.$this->current_image['image_id'];
                break;

            case 'Local_ImageWidth':
                $_s = unserialize($this->current_image['image_sizes']);
                $value = $_s['width'];
                break;

            case 'Local_ImageHeight':
                $_s = unserialize($this->current_image['image_sizes']);
                $value = $_s['height'];
                break;

            case 'Local_ImageAltText':
                $value = $this->current_image['alt_text'];
                break;

            case 'Local_ImageSrc':
                $value = modApiFunc('Product_Images', 'getImageURL', $this->current_image['image_path']);
                break;

            case 'Local_Image':
                $_s = unserialize($this->current_image['image_sizes']);
                $value = '<img src="'.modApiFunc('Product_Images', 'getImageURL', $this->current_image['image_path']).'" width="'.$_s['width'].'" height="'.$_s['height'].'" alt="'.$this->current_image['alt_text'].'">';
                break;

            case 'Local_ThumbnailId':
                $value = 't'.$this->current_image['image_id'];
                break;

            case 'Local_ThumbnailWidth':
                if($this->current_image['thumb_path']!='')
                    $sizes = $this->current_image['thumb_sizes'];
                else
                    $sizes = $this->current_image['image_sizes'];
                $sizes = unserialize($sizes);
                list($x,$y) = modApiFunc('Product_Images','convertSizes',$sizes['width'],$sizes['height'],$this->sets['THUMB_SIDE']);
                $value = $x;
                break;

            case 'Local_ThumbnailHeight':
                if($this->current_image['thumb_path']!='')
                    $sizes = $this->current_image['thumb_sizes'];
                else
                    $sizes = $this->current_image['image_sizes'];
                $sizes = unserialize($sizes);
                list($x,$y) = modApiFunc('Product_Images','convertSizes',$sizes['width'],$sizes['height'],$this->sets['THUMB_SIDE']);
                $value = $y;
                break;

            case 'Local_ThumbnailAltText':
                $value = $this->current_image['alt_text'];
                break;

            case 'Local_ThumbnailSrc':
                if($this->current_image['thumb_path']!='')
                    $value = modApiFunc('Product_Images', 'getImageURL', $this->current_image['thumb_path']);
                else
                    $value = modApiFunc('Product_Images', 'getImageURL', $this->current_image['image_path']);
                break;

            case 'Local_Thumbnail':
                $value = $this->genThumbImageTag();
                break;

            case 'Local_Columns':
                $value = $this->sets['THUMBS_PER_LINE'];
                break;
        };

        return $value;
    }

    var $PImages;
    var $current_image;
    var $sets;
};

?>