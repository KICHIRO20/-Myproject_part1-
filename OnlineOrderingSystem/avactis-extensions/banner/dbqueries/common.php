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
class SELECT_BANNERS extends DB_Select
{
	function initQuery($params)
	{
		$tables = Banner::getTables();
		$bannerLocation=$params['banner_location'];
		$columns = $tables['banners']['columns'];

		$this->addSelectField($columns["bannerid"], "bannerid");
		$this->addSelectField($columns["location"], "location");
		$this->addSelectField($columns["width"], "width");
		$this->addSelectField($columns["height"], "height");
		$this->addSelectField($columns["order_by"], "order_by");
		$this->addSelectField($columns["start_date"], "start_date");
		$this->addSelectField($columns["end_date"], "end_date");
		$this->addSelectField($columns["effect"], "effect");
		$this->addSelectField($columns["width"], "width");
		$this->addSelectField($columns["nav"], "nav");
		$this->WhereValue($columns["location"],  DB_EQ, $bannerLocation);
	}
}

class SELECT_IMAGE_CONTENT extends DB_Select
{
	function initQuery($params)
	{
		$tables = Banner::getTables();
		$bannerid = $params['banner_id'];
		$columns = $tables['banners_images']['columns'];

		$this->addSelectField($columns["imageid"], "imageid");
		$this->addSelectField($columns["image_path"], "image_path");
		$this->addSelectField($columns["image_type"], "image_type");
		$this->addSelectField($columns["image_x"], "image_x");
		$this->addSelectField($columns["image_y"], "image_y");
		$this->addSelectField($columns["image_size"], "image_size");
		$this->addSelectField($columns["url"], "url");
		$this->addSelectField($columns["order_by"], "order_by");
		$this->addSelectField($columns["filename"], "filename");
		$this->addSelectField($columns["alt"], "alt");
		$this->addSelectField($columns["avail"], "avail");
		$this->addSelectField($columns["type"], "type");

		$this->WhereValue($columns["bannerid"],  DB_EQ, $bannerid);
		$this->WhereAND();
		$this->WhereValue($columns["avail"],  DB_EQ, 'Y');
	}
}
class SELECT_IMAGES extends DB_Select{
	function initQuery($params)
	{
		$tables = Banner::getTables();
		$bannerid = $params['banner_id'];
		$columns = $tables['banners_images']['columns'];

		$this->addSelectField($columns["imageid"], "imageid");
		$this->addSelectField($columns["image_path"], "image_path");
		$this->addSelectField($columns["image_type"], "image_type");
		$this->addSelectField($columns["image_x"], "image_x");
		$this->addSelectField($columns["image_y"], "image_y");
		$this->addSelectField($columns["image_size"], "image_size");
		$this->addSelectField($columns["url"], "url");
		$this->addSelectField($columns["order_by"], "order_by");
		$this->addSelectField($columns["filename"], "filename");
		$this->addSelectField($columns["alt"], "alt");
		$this->addSelectField($columns["avail"], "avail");
		$this->addSelectField($columns["type"], "type");

		$this->WhereValue($columns["bannerid"],  DB_EQ, $bannerid);

	}
}
class SELECT_HTML_CONTENT extends DB_Select
{
	function initQuery($params)
	{
		$tables = Banner::getTables();
		$bannerid = $params['banner_id'];
		$columns = $tables['banners_html']['columns'];

		$this->addSelectField($columns["id"], "id");
		$this->addSelectField($columns["order_by"], "order_by");
		$this->addSelectField($columns["code"], "code");
		$this->addSelectField($columns["type"], "type");
		$this->addSelectField($columns["avail"], "avail");
		$this->WhereValue($columns["bannerid"],  DB_EQ, $bannerid);
		$this->WhereAND();
		$this->WhereValue($columns["avail"],  DB_EQ, 'Y');
	}
}

class SELECT_HTML_ALL extends DB_Select
{
	function initQuery($params)
	{
		$tables = Banner::getTables();
		$bannerid = $params['banner_id'];
		$columns = $tables['banners_html']['columns'];

		$this->addSelectField($columns["id"], "id");
		$this->addSelectField($columns["order_by"], "order_by");
		$this->addSelectField($columns["code"], "code");
		$this->addSelectField($columns["type"], "type");
		$this->addSelectField($columns["avail"], "avail");
		$this->WhereValue($columns["bannerid"],  DB_EQ, $bannerid);

	}
}
class SELECT_BANNER_IMAGES_SETTINGS extends DB_Select
{
    function initQuery($params)
    {
        $this->addSelectTable('banners_settings');
        $this->addSelectField('*');
    }
}
class SELECT_BANNERS_BASED_ON_TIME extends DB_Select
{
	function initQuery($params)
	{
		$tables = Banner::getTables();
		$bannerLocation = $params['banner_location'];
		$currentTime = $params['banner_period'];

		$columns = $tables['banners']['columns'];

	    $this->addSelectField($columns["bannerid"], "bannerid");
		$this->addSelectField($columns["location"], "location");
		$this->addSelectField($columns["width"], "width");
		$this->addSelectField($columns["height"], "height");
		$this->addSelectField($columns["order_by"], "order_by");
		$this->addSelectField($columns["start_date"], "start_date");
		$this->addSelectField($columns["end_date"], "end_date");
		$this->addSelectField($columns["effect"], "effect");
		$this->addSelectField($columns["width"], "width");
		$this->addSelectField($columns["nav"], "nav");
		$this->WhereValue($columns["location"],  DB_EQ, $bannerLocation);
		$this->WhereAND();
		$this->WhereValue($columns["start_date"],  DB_LTE, $currentTime);
		$this->WhereAND();
		$this->WhereValue($columns["end_date"],  DB_GTE, $currentTime);
	}
}
class INSERT_BANNER_HTML_CONTENT extends DB_Insert
{
	function INSERT_BANNER_HTML_CONTENT()
	{
		parent :: DB_Insert('banners_html');
	}

	function initQuery($params)
	{
		$tables = Banner :: getTables();
		$tr = $tables['banners_html']['columns'];
		$this -> addInsertValue($params[0]['bannerid'], $tr['bannerid']);
		$this -> addInsertValue($params[0]['code'], $tr['code']);
		$this -> addInsertValue($params[0]['type'], $tr['type']);
	}
}
class UPDATE_BANNER_INFO extends DB_Update
{
	function UPDATE_BANNER_INFO()
	{
		parent::DB_Update('banners');
	}

	function initQuery($params)
	{
		$tables = Banner::getTables();
		$tr = $tables['banners']['columns'];

		$this->addUpdateValue($tr['width'], $params[0]['banner_width']);
		$this->addUpdateValue($tr['height'], $params[0]['banner_height']);
		$this->addUpdateValue($tr['start_date'], $params[0]['start_date']);
		$this->addUpdateValue($tr['end_date'], $params[0]['end_date']);
		$this->addUpdateValue($tr['effect'], $params[0]['banner_effect']);
		$this->addUpdateValue($tr['nav'], $params[0]['nav']);
		$this->WhereValue($tr["bannerid"], DB_EQ, $params[0][banner_id]);

	}
}

class UPDATE_IMAGE_INFO extends DB_Update
{
	function UPDATE_IMAGE_INFO()
	{
		parent::DB_Update('banners_images');
	}

	function initQuery($params)
	{
		$tables = Banner::getTables();
		$tr = $tables['banners_images']['columns'];

		$this->addUpdateValue($tr['url'], $params[0]['url']);
		$this->addUpdateValue($tr['alt'], $params[0]['alt']);
		$this->addUpdateValue($tr['avail'], $params[0]['avail']);
		$this->WhereValue($tr["imageid"], DB_EQ, $params[0]['imageid']);

	}
}

class UPDATE_HTML_INFO extends DB_Update
{
	function UPDATE_HTML_INFO()
	{
		parent::DB_Update('banners_html');
	}

	function initQuery($params)
	{
		$tables = Banner::getTables();
		$tr = $tables['banners_html']['columns'];

		$this->addUpdateValue($tr['code'], $params[0]['code']);
		$this->addUpdateValue($tr['avail'], $params[0]['avail']);
		$this->WhereValue($tr["id"], DB_EQ, $params[0]['contentid']);

	}
}
class INSERT_BANNER_IMAGE_CONTENT extends DB_Insert
{
	function INSERT_BANNER_IMAGE_CONTENT()
	{
		parent :: DB_Insert('banners_images');
	}

	function initQuery($params)
	{
		$tables = Banner :: getTables();
		$tr = $tables['banners_images']['columns'];
		$this -> addInsertValue($params[0]['bannerid'], $tr['bannerid']);
		$this -> addInsertValue($params[0]['image_path'], $tr['image_path']);
		$this -> addInsertValue($params[0]['image_type'], $tr['image_type']);
		$this -> addInsertValue($params[0]['image_x'], $tr['image_x']);
		$this -> addInsertValue($params[0]['image_y'], $tr['image_y']);
		$this -> addInsertValue($params[0]['image_size'], $tr['image_size']);
		$this -> addInsertValue($params[0]['filename'], $tr['filename']);
		$this -> addInsertValue($params[0]['date'], $tr['date']);
		$this -> addInsertValue($params[0]['alt'], $tr['alt']);
		$this -> addInsertValue($params[0]['url'], $tr['url']);
		$this -> addInsertValue($params[0]['type'], $tr['type']);

	}
}


?>