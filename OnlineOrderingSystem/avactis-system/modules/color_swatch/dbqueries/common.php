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




// Color Swatch - update queries
class UPDATE_COLOR_SWATCH_ROWS extends DB_Update
{
	function UPDATE_COLOR_SWATCH_ROWS()
	{
		parent::DB_Update('color_swatch');
	}

	function initQuery($params)
	{
		$tables = ColorSwatch::getTables();
		$upcol = $tables['color_swatch']['columns'];

		$this->addUpdateValue($upcol['name'], $params['name']);

		$this->addUpdateValue($upcol['main_image'], $params['main_image']);
		$this->addUpdateValue($upcol['checked_image'], $params['checked_image']);
		//$this->addUpdateValue($upcol['label_text'], $params['label_text']);
		//$this->addUpdateValue($upcol['number_of_colors'], $params['number_of_colors']);

		$this->WhereField($upcol["id"], DB_EQ, $params['id']);
	}
}

class UPDATE_COLOR_SWATCH_NUMBER_LABEL extends DB_Update
{
	function UPDATE_COLOR_SWATCH_NUMBER_LABEL()
	{
		parent::DB_Update('color_swatch');
	}

	function initQuery($params)
	{
		$tables = ColorSwatch::getTables();
		$upnum = $tables['color_swatch']['columns'];

		$this->addUpdateValue($upnum['label_text'], $params['label_text']);
		$this->addUpdateValue($upnum['number_of_colors'], $params['number_of_colors']);

		$this->WhereField($upnum["product_id"], DB_EQ, $params['product_id']);
	}
}

// Added for color swatch - additional images
class SELECT_PRODUCT_ADDITIONAL_IMAGES_LIST extends DB_Select
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






// Color swatch - insert queries

class INSERT_COLOR_SWATCH_RECORD extends DB_Insert
{
    function INSERT_COLOR_SWATCH_RECORD()
    {
        parent::DB_Insert('color_swatch');
    }

    function initQuery($params)
    {
        $tables = ColorSwatch :: getTables();
        $pcs = $tables['color_swatch']['columns'];

       // $this->addInsertValue($params['id'], $pcs['id']);
        $this->addInsertValue($params['product_id'], $pcs['product_id']);
        $this->addInsertValue($params['name'], $pcs['name']);

        $this->addInsertValue($params['main_image'], $pcs['main_image']);
        $this->addInsertValue($params['checked_image'], $pcs['checked_image']);
        //$this->addInsertValue($params['label_text'], $pcs['label_text']);

    }
}




// Color Swatch - delete queries
class DELETE_COLOR_SWATCH_ROW_BY_ID extends DB_Delete
{
    function DELETE_COLOR_SWATCH_ROW_BY_ID()
    {
        parent :: DB_Delete('color_swatch');
    }

    function initQuery($params)
    {
        $tables = ColorSwatch :: getTables();
        $delcol = $tables['color_swatch']['columns'];

        $this -> WhereField($delcol['id'], DB_EQ, $params['id']);
    }
}



// Color Swatch - select queries
class SELECT_COLOR_SWATCH_ROWS extends DB_Select
{
	function initQuery($params)
	{
		$tables = ColorSwatch :: getTables();
		$colsel = $tables['color_swatch']['columns'];

		$this->addSelectField($colsel['id'], 'id');
		$this->addSelectField($colsel['product_id'], 'product_id');
		$this->addSelectField($colsel['name'], 'name');

		$this->addSelectField($colsel['main_image'], 'main_image');
		$this->addSelectField($colsel['checked_image'], 'checked_image');
                $this->addSelectField($colsel['label_text'], 'label_text');
		$this->addSelectField($colsel['number_of_colors'], 'number_of_colors');

		$this->WhereValue($colsel['name'], DB_EQ, $params['name']);
		$this->WhereAND();
		$this->WhereValue($colsel['product_id'], DB_EQ, $params['product_id']);

	}
}

class SELECT_COLOR_SWATCH_ALL_ROWS extends DB_Select
{
	function initQuery($params)
	{
		$tables = ColorSwatch :: getTables();
		$colsel = $tables['color_swatch']['columns'];

		$this->addSelectField($colsel['id'], 'id');
		$this->addSelectField($colsel['product_id'], 'product_id');
		$this->addSelectField($colsel['name'], 'name');

		$this->addSelectField($colsel['main_image'], 'main_image');
		$this->addSelectField($colsel['checked_image'], 'checked_image');
                $this->addSelectField($colsel['label_text'], 'label_text');
		$this->addSelectField($colsel['number_of_colors'], 'number_of_colors');

		$this->WhereValue($colsel['product_id'], DB_EQ, $params['product_id']);
	}
}

class SELECT_COLOR_SWATCH_ROWS_BY_ID extends DB_Select
{
	function initQuery($params)
	{
		$tables = ColorSwatch :: getTables();
		$colsel = $tables['color_swatch']['columns'];

		$this->addSelectField($colsel['id'], 'id');
		$this->addSelectField($colsel['product_id'], 'product_id');
		$this->addSelectField($colsel['name'], 'name');

		$this->addSelectField($colsel['main_image'], 'main_image');
		$this->addSelectField($colsel['checked_image'], 'checked_image');
        $this->addSelectField($colsel['label_text'], 'label_text');
		$this->addSelectField($colsel['number_of_colors'], 'number_of_colors');

		$this->WhereValue($colsel['id'], DB_EQ, $params['id']);
	}
}

class SELECT_OPTION_NAME extends DB_Select
{
	function initQuery($params)
	{
		$tables = Product_Options::getTables();
		$value = $tables['po_options']['columns'];

		$option_id = $params['option_id'];

		$this->addSelectTable('po_options');
		$this->addSelectField($value['option_id'],'option_id');
		$this->WhereValue($value['entity_id'], DB_EQ, $params['entity_id']);
		$this->WhereAND();
		$this->WhereValue($value['option_name'], DB_EQ, $params['option_name']);

	}

}


?>