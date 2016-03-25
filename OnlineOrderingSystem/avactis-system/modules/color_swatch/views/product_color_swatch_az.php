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
| The latest version of this license can be found here:
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
class PI_ColorSwatch
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
    function PI_ColorSwatch()
    {

 	}

	function getSavedOkMessage()
    {
    	if(!isset($this->mTmplFiller))
    	{
    		$this->mTmplFiller = $application->getInstance('TmplFiller');
    	}
    	if(modApiFunc("Session","is_set","SavedMsg"))
        {
        	modApiFunc("Session","un_set","SavedMsg");
    		$res = $this->mTmplFiller->fill("color_swatch/", "saved_ok_msg.tpl.html", array());
    		return $res;
    	}
    	else
    	{
    		return '';
    	}
    }

    function outputResultMessage()
    {
    	global $application;
    	if(modApiFunc("Session","is_set","ResultMessage"))
    	{
    		modApiFunc("Session","un_set","ResultMessage");

    		$this->mTmplFiller = &$application->getInstance('TmplFiller');
    		return $this->mTmplFiller->fill("color_swatch/", "result-message.tpl.html",array());
    	}
    }

    function outputDeleteMessage()
    {
    	global $application;
    	if(modApiFunc("Session","is_set","DeleteMsg"))
    	{
    		modApiFunc("Session","un_set","DeleteMsg");

    		$this->mTmplFiller = &$application->getInstance('TmplFiller');
    		return $this->mTmplFiller->fill("color_swatch/", "delete-message.tpl.html",array());
    	}
    }


	function getAddColorform()
	{
		global $application;
		$request = &$application->getInstance('Request');
		$this->mTmplFiller = &$application->getInstance('TmplFiller');
		return $this->mTmplFiller->fill("color_swatch/", "add-item-color-swatch.tpl.html",array());
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
        $request = &$application->getInstance('Request');
        $this->product_id = $request->getValueByKey('product_id');
        //$this->TemplateFiller = $application->getInstance('TmplFiller');
		$application->registerAttributes(
							array(
									'Local_ProductBookmarks',
									'CurrencySign',
									'AddNewColor_Form',
									'SelectColorsForm',
                                    'AddLabelText',
									'ListOfColors',
									'ResultMessage',
                                    'SavedOkMessage',
                                    'DeleteMessage'
								));
		$this->mTmplFiller = &$application->getInstance('TmplFiller');

		return $this->mTmplFiller->fill("color_swatch/", "container_color_swatch.tpl.html",array());

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

				 case 'SelectColorsForm':
				 	$numberofcolors = modApiFunc('ColorSwatch','getNumberOfColors',$this->product_id);

				 	$value = '<select name="colorsnum">
							 	<option value="1" '.(($numberofcolors=='1')?'selected="selected"':"").'>1</option>
							 	<option value="2" '.(($numberofcolors=='2')?'selected="selected"':"").'>2</option>
							 	<option value="3" '.(($numberofcolors=='3')?'selected="selected"':"").'>3</option>
							 	<option value="4" '.(($numberofcolors=='4')?'selected="selected"':"").'>4</option>
							 	<option value="5" '.(($numberofcolors=='5')?'selected="selected"':"").'>5</option>

								</select>';
				 	break;

                               case 'AddLabelText':
                                  $labeltext = modApiFunc('ColorSwatch','getLabelText',$this->product_id);
                                    $value = '<td class="value" style="background-color:#EEF2F8;"><input size="20" type="text" name="label_text" value="'.$labeltext.'" />&nbsp;</td>';
                                    break;

				case 'ListOfColors':
				 	$colorinfo = modApiFunc('ColorSwatch','getColorSwatchRows',$this->product_id);
				 	for($i=0;$i<count($colorinfo);$i++)
				 	{
				 		$colorname = $colorinfo[$i]['name'];
				 		$colorid = $colorinfo[$i]['id'];
				 		$main_image = $colorinfo[$i]['main_image'];
				 		$checked_image = $colorinfo[$i]['checked_image'];
				 		$label_text = $colorinfo[$i]['label_text'];
				 		$currencysign = modApiFunc("Localization", "getCurrencySign");
				 		$value .= '<tr>
							     	<td class="value" style="background-color:#EEF2F8;width:18%"><input size="30" type="text" name="UpdateValues['.$colorid.'][colorname]" value="'.$colorname.'"  readonly="readonly" style="margin-left:50px;"/></td>
							     	<td class="value" style="background-color:#EEF2F8;"><input size="56" type="text" name="UpdateValues['.$colorid.'][main_image]" value="'.$main_image.'" style="margin-left: 50px;" />&nbsp;</td>
							     	<!--<td class="value" style="background-color:#EEF2F8;"><input size="50" type="text" name="UpdateValues['.$colorid.'][checked_image]" value="'.$checked_image.'" />&nbsp;</td>-->
							     	<!--<td class="value" style="background-color:#EEF2F8;"><input size="20" type="text" name="UpdateValues['.$colorid.'][label_text]" value="'.$label_text.'" />&nbsp;</td>-->
							     	<td class="value" style="background-color:#EEF2F8; width: 2%;"><input size="10" type="checkbox" value="'.$colorid.'" name="toDeleteValues['.$colorid.']" /></td>
							     	<input type="hidden" name="UpdateValues['.$colorid.'][colorid]" value="'.$colorid.'" />
							     </tr>';
				 	}
				 	break;

				case 'AddNewColor_Form':
					$value = $this->getAddColorform();
					break;

				case 'ResultMessage':
					$value = $this->outputResultMessage();
					break;

				case 'SavedOkMessage':
					$value = $this->getSavedOkMessage();
					break;

               case 'DeleteMessage':
                      $value = $this->outputDeleteMessage();
                      break;
			}
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
    var $TemplateFiller;
}
?>