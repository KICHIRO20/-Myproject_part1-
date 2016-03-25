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
loadModuleFile('wishlist/wishlist_api.php');

// ---------------------------
// Select queries
// ---------------------------

class SELECT_WISHLIST_CONTENT extends DB_Select
{
    function initQuery($params)
    {
        $tables = Wishlist :: getTables();
        $wltable = $tables['wishlist']['columns'];

        $this -> addSelectField($wltable['wl_id'], 'wl_id');
        $this -> addSelectField($wltable['customer_id'], 'customer_id');
        $this -> addSelectField($wltable['product_id'], 'product_id');
        $this -> addSelectField($wltable['qty'], 'quantity');
        $this -> addSelectField($wltable['data'], 'data');

        if (isset($params['wl_id']))
        {
            $this -> WhereValue($wltable['wl_id'], DB_EQ, $params['wl_id']);
            $this -> WhereAND();
        }

        $this -> WhereValue($wltable['customer_id'], DB_EQ,
                            $params['customer_id']);

        $this -> SelectOrder($wltable['wl_id']);
    }
}

// ---------------------------
// Update queries
// ---------------------------

class UPDATE_WISHLIST_QUANTITY extends DB_Update
{
    function UPDATE_WISHLIST_QUANTITY()
    {
        parent :: DB_Update('wishlist');
    }

    function initQuery($params)
    {
        $tables = Wishlist :: getTables();
        $wltable = $tables['wishlist']['columns'];

        $this -> addUpdateValue($wltable['qty'], $params['qty']);

        $this -> WhereValue($wltable['wl_id'], DB_EQ, $params['wl_id']);
    }
}

// ---------------------------
// Insert queries
// ---------------------------

class INSERT_WISHLIST_RECORD extends DB_Insert
{
    function INSERT_WISHLIST_RECORD()
    {
        parent :: DB_Insert('wishlist');
    }

    function initQuery($params)
    {
        $tables = Wishlist :: getTables();
        $wltable = $tables['wishlist']['columns'];

        $this -> addInsertValue($params['customer_id'], $wltable['customer_id']);
        $this -> addInsertValue($params['product_id'], $wltable['product_id']);
        $this -> addInsertValue($params['qty'], $wltable['qty']);
        $this -> addInsertValue(serialize($params['data']), $wltable['data']);
    }
}

// ---------------------------
// Delete queries
// ---------------------------

class DELETE_WISHLIST_RECORD extends DB_Delete
{
    function DELETE_WISHLIST_RECORD()
    {
        parent :: DB_Delete('wishlist');
    }

    function initQuery($params)
    {
        $tables = Wishlist :: getTables();
        $wltable = $tables['wishlist']['columns'];

        $this -> WhereValue($wltable['wl_id'], DB_EQ, $params['wl_id']);
        $this -> WhereAND();
        $this -> WhereValue($wltable['customer_id'], DB_EQ, $params['customer_id']);
    }
}
?>