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
 * A number of columns in the Cart Content view.
 */
define('COLS_IN_CART_THUMBNAIL', 1);
/**
 * The usage of two colors for the view contents background.
 */
define('USE_TWO_COLORS_IN_CART_THUMBNAIL', false);
/**
 * The first background color.
 */
define('COLOR1_IN_CART_THUMBNAIL', 'FFFFFF');
/**
 * The second background color.
 */
define('COLOR2_IN_CART_THUMBNAIL', 'EEEEEE');

/**
 * Cart_Thumbnail view.
 *
 * @package Cart
 * @author Alexander Girin
 */
class CartThumbnail
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Cart_Thumbnail constructor
     */
    function CartThumbnail()
    {
    }

    /**
     * Outputs the cart contents view.
     */
    function output()
    {
        $cc = modApiFunc('Cart', 'getCartContent');

        $unit_values = '';
        $unit_id = '';
        if (NULL == $cc)
        {
            $items = modApiFunc('TmplFiller', 'fill', "cart/cart_thumbnail/","list_empty.tpl.html",array());
            $retval = modApiFunc('TmplFiller', 'fill', "cart/cart_thumbnail/","list.tpl.html",
                                         array('{ListItems}' => $items, '{ListFooter}' => ''));
        }
        else
        {
            $items = "";
            $col =1;
            $item_color = COLOR1_IN_CART_THUMBNAIL;
            foreach ($cc as $productInfo)
            {
                $request = new Request();
                $request->setView  ( 'Catalog_ProdInfo' );
                $request->setAction( 'SetCurrentProduct' );
                $request->setKey   ( 'prod_id', $productInfo['{ID}']);
                $productInfo['{Info_Link}'] = $request->getURL();

                $unit_values = $productInfo['attributes']['{Unit_Price}']['attr_unit_type_values'];
                $unit_id = $productInfo['attributes']['{Unit_Price}']['attr_unit_type_value'];

                $productInfo['{Item_Color}'] = $item_color;
                if (USE_TWO_COLORS_IN_CART_THUMBNAIL)
                {
                    if ($item_color == COLOR1_IN_CART_THUMBNAIL)
                    {
                        $item_color = COLOR2_IN_CART_THUMBNAIL;
                    }
                    else
                    {
                        $item_color = COLOR1_IN_CART_THUMBNAIL;
                    }
                }
                if ($col == 1)
                {
                    $items .= '<tr><td>';
                    $col++;
                }
                else
                {
                    $items .= '<td>';
                    $col++;
                }
                if ($col > COLS_IN_CART_THUMBNAIL)
                {
                    $col = 1;
                }
                $items .= modApiFunc('TmplFiller', 'fill', "cart/cart_thumbnail/", "list_item.tpl.html", $productInfo);
            }
            $footer = modApiFunc('TmplFiller', 'fill', "cart/cart_thumbnail/","list_footer.tpl.html",
                                         array('{SubTotal}' => modApiFunc('Cart', 'getCartSubtotal').' '.$unit_values[$unit_id]));
            $retval = modApiFunc('TmplFiller', 'fill', "cart/cart_thumbnail/","list.tpl.html",
                                         array('{ListItems}' => $items, '{ListFooter}' => $footer));
        }

        return $retval;
    }


    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */


    /**#@-*/

}
?>