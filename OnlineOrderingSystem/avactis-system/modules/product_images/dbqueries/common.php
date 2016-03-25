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

class SELECT_PRODUCT_IMAGES_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Product_Images::getTables();
        $img_table = $tables['pi_images']['columns'];

        $product_id = $params['product_id'];

        $this->addSelectTable('pi_images');
        foreach($img_table as $k => $v)
            if ($k != 'alt_text')
            {
                $this->addSelectField($v);
            }
            else
            {
                $this -> setMultiLangAlias('_alt', 'pi_images',
                                           $img_table['alt_text'],
                                           $img_table['image_id'],
                                           'Product_Images');
                $this -> addSelectField($this -> getMultiLangAlias('_alt'),
                                        'alt_text');
            }
        $this->WhereValue($img_table['product_id'], DB_EQ, $product_id);
        $this->SelectOrder($img_table['sort_order'], 'ASC');
    }
}

class SELECT_PRODUCT_IMAGES_SETTINGS extends DB_Select
{
    function initQuery($params)
    {
        $this->addSelectTable('pi_settings');
        $this->addSelectField('*');
    }
}

?>