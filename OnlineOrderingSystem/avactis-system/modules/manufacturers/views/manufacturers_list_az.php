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
 *
 * @package Manufacturers
 * @access  public
 * @author  Vadim Lyalikov
 */
class ManufacturersList
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
    function ManufacturersList()
    {
        global $application;

        $this->mTmplFiller = &$application->getInstance('TmplFiller');
    }

    function getLinkToManufacturersAddManufacturer()
    {
        $_request = new Request();
        $_request->setView  ( 'AddManufacturer' );

        global $application;
        return $_request->getURL();
    }

    /**
     * Return the Manufacturers Navigator list view.
     *
     * @ finish the functions on this page
     */
    function outputManufacturersList()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');

        $ManufacturersList = modApiFunc('Manufacturers', 'getManufacturersList');

        $this->ManufacturersSize = sizeof($ManufacturersList);
        $retval = "";
        $count = 0;
        foreach ($ManufacturersList as $key => $value)
        {
            $checked = "";
            $this->_Current_Manufacturer = &$value;
//            $this->_Current_Manufacturer["cssstyle"] = ((modApiFunc("PromoCodes", "isPromoCodeApplicableWithoutMinSubtotal", $value["id"]) === true) ? "color: black" : "color: #AAAAAA" );
            $request = new Request();
            $request->setView  ( 'EditManufacturer' );
            $request->setAction( 'set_editable_manufacturer' );
            $request->setKey   ( 'manufacturer_id', $value["manufacturer_id"]);
            $href = $request->getURL();

            $image_obj = new image_obj($value["manufacturer_image_id"]);
             $this->_Current_Manufacturer = array("local_manufacturerid" => $value["manufacturer_id"],
                          "local_manufacturername" => prepareHTMLDisplay($value["manufacturer_name"]),
                          "local_manufacturerurl" => $value["manufacturer_site_url"],
                          "local_manufacturerimage" => $image_obj->is_empty() ? "" : getimage_output_az('mnf_image_' . $value["manufacturer_id"], $image_obj),
                          "local_manufacturerstatus" => $value["manufacturer_active"] == DB_TRUE ? getMsg('MNF', 'STATUS_ACTIVE') : getMsg('MNF', 'STATUS_INACTIVE'),
                          "local_manufacturercssstyle" => "",
                          "local_manufacturereditmanufacturerhref" => $href
                         );
            $this->_Current_Manufacturer["editmanufacturerhref"] = $href;

            $application->registerAttributes($this->_Current_Manufacturer);//->getAdditionalManufacturerTagList());
            $retval .= $this->mTmplFiller->fill("manufacturers/manufacturers_list/", "list_item.tpl.html", array());

            $count++;
        }

        $min_list_size = 10;
        if($count== 0)
        {
            $retval .= $this->mTmplFiller->fill("manufacturers/manufacturers_list/", "list_item_empty_na_values.tpl.html", array());
            $count++;
        }

        for(;$count < $min_list_size; $count++)
        {
            $retval .= $this->mTmplFiller->fill("manufacturers/manufacturers_list/", $count == $min_list_size -1 ? "list_bottom_item_empty.tpl.html" : "list_item_empty.tpl.html", array());
        }

        modApiFunc('Manufacturers', 'unsetEditableManufacturerID');
        return $retval;
    }

    /**
     * Return the Manufacturers Navigator view.
     *
     * @ finish the functions on this page
     */
    function output()
    {
        global $application;

        # Get CManufacturerInfo object list
        $ManufacturersList = modApiFunc('Manufacturers', 'getManufacturersList');
        $this->ManufacturersSize = sizeof($ManufacturersList);

        $application->registerAttributes(array(
             'Manufacturer_CurrentPath'
            ,'AddManufacturerHref'
            ,'DelManufacturerHref'
            ,'EditManufacturerHref'
            ,'SortManufacturerHref'
            ,'AlertMessage'
            ,'SortManufacturersHref'
            ,'SortAlertMessage'
        ));
        $retval = $this->mTmplFiller->fill("manufacturers/manufacturers_list/", "list.tpl.html", array());
        return $retval;
    }

    /**
     * @                      ManufacturersNavigationBar->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
        	case 'Items':
        		$value = $this->outputManufacturersList();
        		break;

            case 'AddManufacturerHref':
                $request = new Request();
                $request->setView  ( 'AddManufacturer' );
                $value = $request->getURL();
        	    break;

        	case 'DelManufacturerHref':
                $request = new Request();
                $request->setView  ( 'ManufacturersList' );
                $request->setAction( 'del_manufacturers' );
                $request->setKey   ( 'manufacturer_id', '');
                $value = $request->getURL();
        	    break;

        	case 'EditManufacturerHref':
                $request = new Request();
                $request->setView  ( 'EditManufacturer' );
                $request->setAction( 'set_editable_manufacturer' );
                $request->setKey   ( 'manufacturer_id', '');
                $value = $request->getURL();
        	    break;

            case 'SortManufacturersHref':
                $request = new Request();
                $request->setView  ( 'SortManufacturers' );
                $value = $request->getURL();
                break;

            case 'AlertMessage':
                $MessageResources = &$application->getInstance('MessageResources',"manufacturers-messages", "AdminZone");
                if ($this->ManufacturersSize==0)
                {
                    $err_mes = new ActionMessage(array('NB_009'));
                }
                else
                {
                    $err_mes = new ActionMessage(array('NB_006'));
                }
                $value = "'".$MessageResources->getMessage($err_mes)."'";
                break;
            case 'SortAlertMessage':
            	$mnfs_num = sizeof(modApiFunc("Manufacturers", "getManufacturersList"));
                if ($mnfs_num== 0 || $mnfs_num == 1)
                {
                	$value = getMsg('MNF', 'NB_007');
                }
                else
                {
                    $value = '';
                }
            	break;

        	default:
        	    if (_ml_strpos($tag, 'Local_Manufacturer') === 0)
        	    {
                    $tag = _ml_substr($tag, _ml_strlen('Local_Manufacturer'));
        	    }
        	    $tag = _ml_strtolower($tag);
                if(!empty($this->_Current_Manufacturer))
                {
                    if (isset($this->_Current_Manufacturer[$tag]))
                    {
                        $value = $this->_Current_Manufacturer[$tag];
                        //             ,              ,           .
                    	switch($tag)
                        {
                        	case "name":
                            {
                            	$value = prepareHTMLDisplay($value);
                                break;
                            }
                            case "editmanufacturerhref":
                            {
                                break;
                            }
                        	case "status":
                        	{
                        		switch($value)
                        	    {
                        	        case 1:
                        	        {
                        	        	$value = getMsg('MNF', "MANUFACTURERS_MODULE_STATUS_ACTIVE");
                        	        	break;
                        	        }
                        	        case 2:
                        	        {
                        	        	$value = getMsg('MNF', "MANUFACTURERS_MODULE_STATUS_INACTIVE");
                        	        	break;
                        	        }
                        	        default:
                        	        {
                        	        	//: report error;
                        	        	break;
                        	        }
                        	    }
                        		break;
                        	}
                        }
    	            }
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
     * Pointer to the template filler object.
     * Needs to track sequences of identical templates, like lists.
     */
    var $mTmplFiller;

    var $_Current_Manufacturer = array();
    /**#@-*/

}
?>