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
 * ColorSwatch module
 *
 * @package ColorSwatch
 * @author HBWSL
 */
class ColorSwatch
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Module constructor.
     */
    function ColorSwatch()
    {

    }

    /**
     * Returns initials totals for cart/order
     */

    function install()
    {
        global $application;

        $tables = ColorSwatch::getTables();           #the array of the Cart module tables
        $query = new DB_Table_Create($tables);


         $labels = array(
				'PRD_COLOR_SWATCH' => 'Color Swatches'
				);
		foreach($labels as $k => $v)
		{
			modApiFunc('Resources', 'addLabelToDB', $k, $v, 'CART');

		}

		$labelcz = array(
						'CLR_SWTCH_ERR_MSG' => 'Please select atleast 1 Color Swatch Image'
					);
		foreach($labelcz as $key => $val)
		{
			modApiFunc('Resources', 'addLabelToDB', $key, $val, 'CZ');
		}

			$labelsnew = array(
				'PRD_COLR_SWATCH_TITLE' => 'Color Swatch Management',
				'CLR_SAVED_MSG' => 'The color details has been successfully added.',
				'CLR_NUMBER_MSG' => 'The number of colors to display have been successfully saved.',
				'CLR_DELETE_MSG' => 'The color details have been successfully deleted.',
				'CLR_SWTCH_MAIN_IMG' => 'Main Image',
				'CLR_SWTCH_CHECKED_IMG' => 'Checked Image',
				'CLR_UPDATE_MSG' => 'The color details have been successfully updated.',
				);

		foreach($labelsnew as $k => $v)
		{
		 	modApiFunc('Resources', 'addLabelToDB', $k, $v, 'CLRSW');
		}


    }

    /**
     * Deinstalls the module.
     *
     * The uninstall() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * ColorSwatch::getTables() instead of $this->getTables().
     *
     * @todo finish the functions on this page
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(ColorSwatch::getTables());
        global $application;
        $application->db->getDB_Result($query);
    }

    /**
     * Checks if the module was installed.
     *
     * @todo finish the functions on this page
     * @return
     */
    function isInstalled()
    {

    }

    /**
     * Gets the array of meta description of module tables.
     *
     * @todo May be add more tables
     * @return array - meta description of module tables
     */
    function getTables()
    {


        $tables = array();
        $colorswatch = 'color_swatch';
        $tables[$colorswatch] = array();
        $tables[$colorswatch]['columns'] = array
        (
        		'id'                => 'color_swatch.id'
        		,'product_id'		=> 'color_swatch.product_id'
        		,'name'             => 'color_swatch.name'
        		,'main_image'       => 'color_swatch.main_image'
        		,'checked_image'    => 'color_swatch.checked_image'
                ,'label_text'       => 'color_swatch.label_text'
        		,'number_of_colors' => 'color_swatch.number_of_colors'
        );
        $tables[$colorswatch]['types'] = array
        (
        		'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
        		,'product_id'	    => DBQUERY_FIELD_TYPE_INT .' NOT NULL'
        		,'name'             => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
        		,'main_image'       => DBQUERY_FIELD_TYPE_TEXT.' NOT NULL'
        		,'checked_image'    => DBQUERY_FIELD_TYPE_TEXT.' NULL'
                ,'label_text'       => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'Select Color\''
        		,'number_of_colors' => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT \'1\''
        );
        $tables[$colorswatch]['primary'] = array
        (
        		'id'
        );

        global $application;
        return $application->addTablePrefix($tables);
    }


	function addColorSwatchInfo($Colorswatchinfo)
    {
    	global $application;

    	$productid = $Colorswatchinfo["product_id"];
    	$colornamefirst = $Colorswatchinfo["colorname_first"];
    	$colormainimage = $Colorswatchinfo["color_main_image"];
    	$colorcheckedimage = $Colorswatchinfo["color_checked_image"];
    	$colorlabeltext = $Colorswatchinfo["color_label_text"];


    		$selquery1 = execQuery('SELECT_COLOR_SWATCH_ROWS',array('name' =>$colornamefirst, 'product_id'=>$productid ));

    		$numrows1 = count($selquery1);
    		if(($numrows1 == 0) && ($productid != ""))
    		{
    			execQuery('INSERT_COLOR_SWATCH_RECORD',array('product_id' => $productid,'name' => $colornamefirst, 'main_image'=>$colormainimage, 'checked_image'=>$colorcheckedimage));

    		}

    	//return the new product ID to use later in different hooks
    	return true;
    }

    function updateColorSwatchInfo($ColorswatchEditinfo)
    {
    	global $application;
    	$colorarray = $ColorswatchEditinfo['UpdateValues'];

    	$numericcolorarray = array_values($colorarray);

    	$number_of_colors = $ColorswatchEditinfo['colorsnum'];


    	for($i=0;$i<count($numericcolorarray);$i++)
    	{
    		$colorid = $numericcolorarray[$i]['colorid'];

    		$colorname = $numericcolorarray[$i]['colorname'];
    		$colormainimage = $numericcolorarray[$i]["main_image"];
    		$colorcheckedimage = $numericcolorarray[$i]["checked_image"];
    		$colorlabeltext = $numericcolorarray[$i]["label_text"];

    	 	$query = execQuery('SELECT_COLOR_SWATCH_ROWS_BY_ID',array('id'=>$colorid));

    	 	foreach($query as $row)
    	 	{

    	 		$updcolorid = $row['id'];
    	 		execQuery('UPDATE_COLOR_SWATCH_ROWS',array('name' => $colorname, 'main_image'=>$colormainimage, 'checked_image'=>$colorcheckedimage,'id'=>$updcolorid));

     	 	}
    	}


    	return true;
    }

    function updateNumberAndLabel($ColorswatchEditval)
    {
         global $application;
         $prodid = $ColorswatchEditval['product_id'];
         $number_of_colors = $ColorswatchEditval['colorsnum'];
         $colorlabeltext = $ColorswatchEditval["label_text"];
         execQuery('UPDATE_COLOR_SWATCH_NUMBER_LABEL',array('label_text'=>$colorlabeltext,'number_of_colors'=>$number_of_colors,'product_id' => $prodid));

         return true;
    }

    function deleteColorSwatchInfo($Colorswatchdelinfo)
    {
    	global $application;
    	$colordelarray = $Colorswatchdelinfo['toDeleteValues'];
    	$numericdelarray = array_values($colordelarray);
    	for($i=0;$i<count($numericdelarray);$i++)
    	{
    		$colordelid = $numericdelarray[$i];
    		execQuery('DELETE_COLOR_SWATCH_ROW_BY_ID',array('id'=>$colordelid));
    	}

    	return true;

    }

    function getColorSwatchInfo($product_id)
    {
	$name = "";
    	$query = execQuery('SELECT_COLOR_SWATCH_ALL_ROWS',array('product_id'=>$product_id));

    	foreach($query as $row)
    	{
    		$name .=  $row['name'].",";
    	}
    	return $name;
    }

    function getColorSwatchRows($product_id)
    {
    	$result = execQuery('SELECT_COLOR_SWATCH_ALL_ROWS',array('product_id'=>$product_id));

    	return $result;
    }

    function getNumberOfColors($product_id)
    {
	$numberofcolors = "";
    	$result = execQuery('SELECT_COLOR_SWATCH_ALL_ROWS',array('product_id'=>$product_id));

    	foreach($result as $row)
    	{
    		$numberofcolors = $row['number_of_colors'];
    	}
    	return $numberofcolors;
    }

    function getLabelText($product_id)
    {
	$labeltext = "";
    	$result = execQuery('SELECT_COLOR_SWATCH_ALL_ROWS',array('product_id'=>$product_id));

    	foreach($result as $row)
    	{
    		$labeltext = $row['label_text'];
    	}
    	return $labeltext;
    }


    function getColorHash($color)
    {

        return crc32(serialize($color));
    }

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /*
     * Cart Content (stored in session)
     */
    var $CartContent;

    /*
     * Detailed Cart Content
     */
    var $DetailedCartContent;

    /*
     * Cart orders
     */
    var $CartOrders;

    /*
     * Cart totals
     */
    var $CartTotals;

    // this is obvious. this variable shows if any products in the cart were deleted by failed integrity check.
    var $wasCartModifiedbyIntegrityCheck;

    /**#@-*/

}
?>