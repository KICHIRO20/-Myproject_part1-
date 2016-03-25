<?php
        $group_info = array('GROUP_NAME'        => 'PRODUCT_GROUP_EDIT',
                            'GROUP_DESCRIPTION' => array('NAME'        => array('CTL', 'CTL_PGE_NAME'),
                                                         'DESCRIPTION' => array('CTL', 'CTL_PGE_DESCR')),
                            'GROUP_VISIBILITY'  => 'SHOW');

        modApiFunc('Settings', 'createGroup', $group_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_NAME',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_NAME_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_NAME_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_YES'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_NO'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_SALEPRC',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_SALEPRC_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_SALEPRC_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_YES'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_NO'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_LISTPRC',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_LISTPRC_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_LISTPRC_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_YES'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_NO'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_QUINSTOCK',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_QUINSTOCK_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_QUINSTOCK_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_YES'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_NO'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_LOWLEV',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_LOWLEV_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_LOWLEV_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_YES'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_NO'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_SKU',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_SKU_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_SKU_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_YES'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_NO'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_QUINORDER',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_QUINORDER_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_QUINORDER_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_YES'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_NO'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'NO',
                         'PARAM_DEFAULT_VALUE' => 'NO',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_AVAIL',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_AVAIL_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_AVAIL_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_YES'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_NO'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'NO',
                         'PARAM_DEFAULT_VALUE' => 'NO',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_TAXCLASS',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_TAXCLASS_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_TAXCLASS_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_YES'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_NO'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'NO',
                         'PARAM_DEFAULT_VALUE' => 'NO',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_MANUFACTURER',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_MANUFACTURER_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_MANUFACTURER_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_YES'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_NO'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'NO',
                         'PARAM_DEFAULT_VALUE' => 'NO',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_SHIPPRC',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_SHIPPRC_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_SHIPPRC_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_YES'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_NO'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'NO',
                         'PARAM_DEFAULT_VALUE' => 'NO',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_HANDPRC',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_HANDPRC_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_HANDPRC_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_YES'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_NO'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'NO',
                         'PARAM_DEFAULT_VALUE' => 'NO',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_WEIGHT',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_WEIGHT_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_WEIGHT_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_YES'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_NO'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_FREESHIP',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_FREESHIP_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_FREESHIP_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_YES'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_NO'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_NEEDSHIP',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_NEEDSHIP_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_NEEDSHIP_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_YES'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_NO'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'NO',
                         'PARAM_DEFAULT_VALUE' => 'NO',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_CUSTOMER_REVIEWS',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CUSTOMER_REVIEWS_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CUSTOMER_REVIEWS_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_YES'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_NO'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'NO',
                         'PARAM_DEFAULT_VALUE' => 'NO',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_PGE_TABULATION',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_TABULATION_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_TABULATION_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'RIGHT',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_RIGHT'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_RIGHT') ),
                                       ), 
                                 array(  'VALUE' => 'DOWN',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_PGE_CFG_DOWN'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_PGE_CFG_DOWN') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'DOWN',
                         'PARAM_DEFAULT_VALUE' => 'DOWN',
        );
        modApiFunc('Settings', 'createParam', $param_info);
?>