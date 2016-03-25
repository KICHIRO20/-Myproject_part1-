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
class SELECT_IMAGE extends DB_Select
{
    function initQuery($params)
    {
        $tables = Images::getTables();
        $columns = $tables['images']['columns'];

        $image_id = $params['image_id'];
        $this->addSelectField($columns['image_id'],        'image_id');
        $this->addSelectField($columns['image_media'],     'image_media');
        $this->addSelectField($columns['image_path'],      'image_path');
        $this->addSelectField($columns['image_url'],       'image_url');
        $this->addSelectField($columns['image_mime_type'], 'image_mime_type');
        $this->addSelectField($columns['image_width'],     'image_width');
        $this->addSelectField($columns['image_height'],    'image_height');
        $this->addSelectField($columns['image_filesize'],  'image_filesize');
        $this->addSelectField($columns['image_alt_text'],  'image_alt_text');
        $this->WhereValue($columns['image_id'], DB_EQ, $image_id);
    }
}

class SELECT_IMAGE_THUMBNAIL extends DB_Select
{
    function initQuery($params)
    {
        $tables = Images::getTables();
        $images = $tables['images']['columns'];
        $image_thumbnails = $tables['image_thumbnails']['columns'];

        $image_id         = $params['image_id'];
        $thumbnail_side = $params['thumbnail_side'];

        $this->addSelectField($images['image_id'],        'image_id');
        $this->addSelectField($images['image_media'],     'image_media');
        $this->addSelectField($images['image_path'],      'image_path');
        $this->addSelectField($images['image_url'],       'image_url');
        $this->addSelectField($images['image_mime_type'], 'image_mime_type');
        $this->addSelectField($images['image_width'],     'image_width');
        $this->addSelectField($images['image_height'],    'image_height');
        $this->addSelectField($images['image_filesize'],  'image_filesize');
        $this->addSelectField($images['image_alt_text'],  'image_alt_text');
        $this->WhereField($images['image_id'], DB_EQ, $image_thumbnails['thumbnail_image_id']);
        $this->WhereAND();
        $this->WhereField($image_thumbnails['image_id'], DB_EQ, $image_id);
        $this->WhereAND();
        $this->addWhereOpenSection();

        $this->addWhereOpenSection();
        $this->WhereValue($images['image_width'], DB_EQ, $thumbnail_side);
        $this->WhereAND();
        $this->WhereValue($images['image_height'], DB_LTE, $thumbnail_side);
        $this->addWhereCloseSection();

        $this->WhereOR();

        $this->addWhereOpenSection();
        $this->WhereValue($images['image_width'], DB_LTE, $thumbnail_side);
        $this->WhereAND();
        $this->WhereValue($images['image_height'], DB_EQ, $thumbnail_side);
        $this->addWhereCloseSection();

        $this->addWhereCloseSection();
    }
}

class SELECT_IMAGE_THUMBNAILS_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Images::getTables();
        $image_thumbnails = $tables['image_thumbnails']['columns'];
        $image_id         = $params['image_id'];

        $this->addSelectField($image_thumbnails['thumbnail_image_id'],  'thumbnail_image_id');
        $this->WhereField($image_thumbnails['image_id'], DB_EQ, $image_id);
    }
}

class INSERT_IMAGE extends DB_Insert
{
    function INSERT_IMAGE()
    {
        parent::DB_Insert('images');
    }

    function initQuery($params)
    {
        $tables = Images::getTables();
        $img = $tables["images"]['columns'];

        //  add to the table order_person_data
        $this->addInsertValue($params['image_media'],     $img["image_media"]);
        $this->addInsertValue($params['image_path'],      $img["image_path"]);
        $this->addInsertValue($params['image_url'],       $img["image_url"]);
        $this->addInsertValue($params['image_mime_type'], $img["image_mime_type"]);
        $this->addInsertValue($params['image_width'],     $img["image_width"]);
        $this->addInsertValue($params['image_height'],    $img["image_height"]);
        $this->addInsertValue($params['image_filesize'],  $img["image_filesize"]);
        $this->addInsertValue($params['image_alt_text'],  $img["image_alt_text"]);
    }
}

class INSERT_IMAGE_THUMBNAIL extends DB_Insert
{
    function INSERT_IMAGE_THUMBNAIL()
    {
        parent::DB_Insert('image_thumbnails');
    }

    function initQuery($params)
    {
        $tables = Images::getTables();
        $img_thumbs = $tables["image_thumbnails"]['columns'];

        //  add to the table order_person_data
        $this->addInsertValue($params['image_id'],           $img_thumbs["image_id"]);
        $this->addInsertValue($params['thumbnail_image_id'], $img_thumbs["thumbnail_image_id"]);
    }
}


class DELETE_IMAGES extends DB_Delete
{
    function DELETE_IMAGES()
    {
        parent::DB_Delete('images');
    }

    function initQuery($params)
    {
        $image_id_list = $params['image_id_list'];

        $tables = Images::getTables();
        $table = $tables['images']['columns'];

        $this->Where($table['image_id'], DB_IN, ' ('.implode(',',$image_id_list).') ');
    }
}

class DELETE_IMAGE_THUMBNAILS extends DB_Delete
{
    function DELETE_IMAGE_THUMBNAILS()
    {
        parent::DB_Delete('image_thumbnails');
    }

    function initQuery($params)
    {
    	$image_id = $params['image_id'];

        $tables = Images::getTables();
        $table = $tables['image_thumbnails']['columns'];

        $this->Where($table['image_id'], DB_EQ, $image_id);
    }
}

class UPDATE_IMAGE extends DB_Update
{
    function UPDATE_IMAGE()
    {
        parent::DB_Update('images');
    }

    function initQuery($params)
    {
        $tables = Images::getTables();
        $columns = $tables['images']['columns'];

        $this->addUpdateValue($columns['image_media'], $params['image_media']);
        $this->addUpdateValue($columns['image_path'], $params['image_path']);
        $this->addUpdateValue($columns['image_url'], $params['image_url']);
        $this->addUpdateValue($columns['image_mime_type'], $params['image_mime_type']);
        $this->addUpdateValue($columns['image_width'], $params['image_width']);
        $this->addUpdateValue($columns['image_height'], $params['image_height']);
        $this->addUpdateValue($columns['image_filesize'], $params['image_filesize']);
        $this->addUpdateValue($columns['image_alt_text'], $params['image_alt_text']);
        $this->WhereValue($columns["image_id"], DB_EQ, $params['image_id']);
    }
}
?>