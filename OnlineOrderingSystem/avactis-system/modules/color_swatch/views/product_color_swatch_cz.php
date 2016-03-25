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
| Avactis (TM) Shopping Cart software developed by Pentasoft Corp.
| http://www.avactis.com
| -----------------------------------------------------------------------
| All source codes & content (c) Copyright 2004-2010, Pentasoft Corp.
| unless specifically noted otherwise.
| =============================================
| This source code is released under the Avactis License Agreement.
| The las version of this license can be found here:
| http://www.avactis.com/license.php
|
| By using this software, you acknowledge having read this license agreement
| and agree to be bound thereby.
|
 ***********************************************************************/
?><?php


/**
 * ColorSwatch module.
 * @author HBWSL
 * @package ColorSwatch
 * @access  public
 */
class ColorSwatchImages
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * The view constructor.
     *
     * @ finish the functions on this page
     */
	function getTemplateFormat()
    {
    	$format = array(
    	    'layout-file'        => 'color-swatch-block.ini'
    	   ,'files' => array(
    	       'BlockContainer' => TEMPLATE_FILE_SIMPLE
               ,'BlockItem' => TEMPLATE_FILE_SIMPLE
               ,'ColorSwatchListContainer' => TEMPLATE_FILE_SIMPLE
               ,'ColorSwatchListItem' => TEMPLATE_FILE_SIMPLE

    	    )
    	   ,'options' => array(

    	    )
    	);
    	return $format;

    }

    function ColorSwatchImages()
    {

    }

    function outputResultMessage()
    {
    	global $application;
    	if(modApiFunc("Session","is_set","ResultMessage"))
    	{
    		$msg=modApiFunc("Session","get","ResultMessage");
    		modApiFunc("Session","un_set","ResultMessage");
    		$template_contents=array(
    				"ResultMessage" => $this->_var2msg($msg)
    		);
    		$this->_Template_Contents=$template_contents;
    		$application->registerAttributes($this->_Template_Contents);
    		$this->mTmplFiller = &$application->getInstance('TmplFiller');
    		return $this->mTmplFiller->fill("catalog/product_edit/", "result-message.tpl.html",array());
    	}
    }


    /**
     * Returns the Product Listing view.
     *
     * @return string the Products List view.
     *
     * @ finish the functions on this page
     */
    function output()
    {
        global $application;

        $this->product_id=func_get_arg(0);

		$application->registerAttributes(
							array(
									'Local_ProductBookmarks',
									'CurrencySign',
									'Local_ProductID',
									'Local_ColorSwatchItems',
									'Local_ColorSwatchImages',
                                    'Local_ColorSwatchImagesProductList',
                                    'Local_ColorSwatchNumbers',
                                    'Local_ColorSwatchLabelText',
                                    'Local_ColorSwatchOnProductListPage',
                                    'Local_ColorSwatchOnProductListPageItems',

								));

			$this->templateFiller = new TemplateFiller();
        	$this->template = $application->getBlockTemplate('ColorSwatchImages');
        	$this->templateFiller->setTemplate($this->template);
      	return $this->templateFiller->fill('BlockContainer');


    }

    /**
     * @ describe the function ProductList->getTag.
     */

    /**#@-*/


	  function getTag($tag)
	  {
		    global $application;
			$value = null;
			switch ($tag)
			{
			    case 'Local_ProductBookmarks':
		            $value = getProductBookmarks('color_swatch',$this->product_id);
		            break;

			     case 'CurrencySign':
                	         $value = modApiFunc("Localization", "getCurrencySign");
                	         break;

				 case 'Local_ProductID':
				 	$value = $this->product_id;
				 	break;

				 case 'Local_ColorSwatchItems':
			 		$value = $this->templateFiller->fill('BlockItem');
			 		break;
                 case 'Local_ColorSwatchOnProductListPage':
                        $value = $this->templateFiller->fill('ColorSwatchListContainer');

                       break;
                  case 'Local_ColorSwatchOnProductListPageItems':
                        $value = $this->templateFiller->fill('ColorSwatchListItem');
                       break;
                 case 'Local_ColorSwatchNumbers':
                        $value = modApiFunc('ColorSwatch','getNumberOfColors',$this->product_id);
                       break;

				case 'Local_ColorSwatchLabelText':
                        $value = modApiFunc('ColorSwatch','getLabelText',$this->product_id);
                       break;


				case 'Local_ColorSwatchImages':
				$siteurl = $application->getAppIni('URL_IMAGES_DIR');

				$productid = $this->product_id;

				$colorname = modApiFunc('ColorSwatch','getColorSwatchInfo',$productid);
				$numberofcolors = modApiFunc('ColorSwatch','getNumberOfColors',$productid);

				if(!empty($colorname))
				{

					$colorswatchimgdir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

					$colorswatchimagesinfolder = scandir($colorswatchimgdir."/avactis-images/color_swatch", 1);
					$checkedimagesinfolder = scandir($colorswatchimgdir."/avactis-images/color_checked", 1);

					$colorimagesfldr = implode(",",$colorswatchimagesinfolder);
					$checkedimages = implode(",",$checkedimagesinfolder);

					$expcolorimagesfldr = explode(",",$colorimagesfldr);

					$expcolorname = explode(",",$colorname);

						$query = execQuery('SELECT_COLOR_SWATCH_ALL_ROWS',array('product_id'=>$productid));

						foreach($query as $row)
						{
						        $titlecolor = $row['name'];
						        $indcolorname = $row['name'];
                                if (strpos($indcolorname,' ') !== false) {
                                  $indcolorname = str_replace(" ","_",$indcolorname);
                                }
							$mainimage = $row['main_image'];
							$checkedimagetag = $row['checked_image'];

							$expmainimage = explode("'",$mainimage);
							$expcheckedimg = explode("'",$checkedimagetag);
							$mainimgsrc = isset($expmainimage[1]) ? $expmainimage[1] : NULL;
			        	                $checkedimgsrc = isset($expcheckedimg[1]) ? $expcheckedimg[1] : NULL;

							$value .= "<img style='cursor:pointer;margin-right: -2px;border:none !important;' title='$titlecolor' id='".$indcolorname."_".$productid."' src='$mainimage' />&nbsp;&nbsp;";


							$value.= '<script type="text/javascript">
										jQuery(document).ready(function($){

											$("#colorsw_'.$productid.' img").click(function(){

											 var idval =  $(this).attr("id");


								 			if((idval == "'.$indcolorname."_".$productid.'"))
											{

							$("#'.$indcolorname."_".$productid.'").attr("disabled","disabled");

							$("#'.$indcolorname."_".$productid.'").after("<span id=\"checkedimg_'.$indcolorname."_".$productid.'\" style=\"display:inline; cursor:pointer; margin-left: -25px; margin-top: 6px;position: absolute; \" ><img class=\"chimg\" src=\"'.$siteurl.'color_checked/checkmark.png\"></span>");


						       if(($(".chimg:visible").length) > '.$numberofcolors.')
			                               {

			                                   $("#checkedimg_'.$indcolorname."_".$productid.'").css("display","none");
			                                   $("#checkedimg_'.$indcolorname."_".$productid.'").removeAttr("disabled");
			                                   $("#'.$indcolorname."_".$productid.'").removeAttr("disabled");
			                                   $("#colsw_msg").css("display","block");
			                                   $("#colsw_msg").text("*You can only choose '.$numberofcolors.' color(s).");

			                               } else{
			                               $("#colsw_msg").css("display","none");
			                               }

// Start title

                                                 if(($("#'.$indcolorname."_".$productid.'").attr("disabled")) == "disabled")
                                                 {

                                                   var colortitle = $(this).attr("title");
                                                   var currentname = $("#colorname").val();

		                                   if(currentname == "")
		                                   {
		                                      currentname = colortitle;

		                                   }
		                                   else
		                                   {
                                                        currentname = currentname + "," + colortitle;
		                                   }
					           currentname = currentname.replace(",,",",");
		                                   $("#colorname").attr("value", currentname);
	                                           colortitle ="";
                                                 }
	                                         // End title


											}




$("#checkedimg_'.$indcolorname."_".$productid.'").click(function(){

if($("#checkedimg_'.$indcolorname."_".$productid.'").is(":visible"))
{

           $("#checkedimg_'.$indcolorname."_".$productid.'").css("display","none");
           $("#checkedimg_'.$indcolorname."_".$productid.'").attr("disabled","false");
           $("#'.$indcolorname."_".$productid.'").attr("disabled","false");

           var colortitle = $("#'.$indcolorname."_".$productid.'").attr("title");

           var currentname = $("#colorname").val();
           currentname = currentname.replace(colortitle + "," , "");
           currentname = currentname.replace(","+colortitle , "");
           currentname = currentname.replace(colortitle , "");

           currentname = currentname.replace(",,", ",");
           $("#colorname").attr("value", currentname);



}
});



										});


										});
										</script>';
						}


					}

				break;

                              case 'Local_ColorSwatchImagesProductList':
				$siteurl = $application->getAppIni('URL_IMAGES_DIR');

				$productid = $this->product_id;

				$colorname = modApiFunc('ColorSwatch','getColorSwatchInfo',$productid);
				$numberofcolors = modApiFunc('ColorSwatch','getNumberOfColors',$productid);

				if(!empty($colorname))
				{

					$colorswatchimgdir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
					$colorswatchimagesinfolder = scandir($colorswatchimgdir."/avactis-images/color_swatch", 1);
					$checkedimagesinfolder = scandir($colorswatchimgdir."/avactis-images/color_checked", 1);

					$colorimagesfldr = implode(",",$colorswatchimagesinfolder);
					$checkedimages = implode(",",$checkedimagesinfolder);

					$expcolorimagesfldr = explode(",",$colorimagesfldr);

					$expcolorname = explode(",",$colorname);

						$query = execQuery('SELECT_COLOR_SWATCH_ALL_ROWS',array('product_id'=>$productid));

						foreach($query as $row)
						{

						        $indcolorname = $row['name'];
							$mainimage = $row['main_image'];
							$checkedimagetag = $row['checked_image'];

							$expmainimage = explode("'",$mainimage);
							$expcheckedimg = explode("'",$checkedimagetag);
							$mainimgsrc = isset($expmainimage[1]) ? $expmainimage[1] : NULL;
			        	                $checkedimgsrc = isset($expcheckedimg[1]) ? $expcheckedimg[1] : NULL;

							$value .= "<img class='prdswtchimg' style='margin-right: -2px;border:none !important;' title='$indcolorname' id='".$indcolorname."_".$productid."' src='$mainimage' />&nbsp;&nbsp;";


						}


					}

				break;

			};
			return $value;
	  }
//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    function __getSortTagsArrayWithPrefix($prefix)
    {
        $tags = array_keys($this->__sort_tag_suffix);
        foreach($tags as $key=>$value)
        {
            $tags[$key] = $prefix.$value;
        }
        return $tags;
    }


    var $__sort_tag_prefix = 'Local_SortBy';

    var $__sort_tag_suffix = array(
                    'Default'               => SORT_BY_PRODUCT_SORT_ORDER,
                    'SalePrice'             => SORT_BY_PRODUCT_SALE_PRICE,
                    'ListPrice'             => SORT_BY_PRODUCT_LIST_PRICE,
                    'Name'                  => SORT_BY_PRODUCT_NAME,
                    'DateAdded'             => SORT_BY_PRODUCT_DATE_ADDED,
                    'DateUpdated'           => SORT_BY_PRODUCT_DATE_UPDATED,
                    'QuantityInStock'       => SORT_BY_PRODUCT_QUANTITY_IN_STOCK,
                    'SKU'                   => SORT_BY_PRODUCT_SKU,
                    'PerItemShippingCost'   => SORT_BY_PRODUCT_PER_ITEM_SHIPPING_COST,
                    'PerItemHandlingCost'   => SORT_BY_PRODUCT_PER_ITEM_HANDLING_COST,
                    'Weight'                => SORT_BY_PRODUCT_WEIGHT,
                );

    var $_Current_Product = array();
    var $_Cat_Info = array();

	var $template;
    /**#@-*/

    /**
     *
     */
    var $ProdNumInCat;

    /**
     *
     */
    var $pl;

    var $_cats_paths;

    var $cb_params;

    var $psf_filter;

    var $paginator_name;
    var $templateFiller;
};
?>