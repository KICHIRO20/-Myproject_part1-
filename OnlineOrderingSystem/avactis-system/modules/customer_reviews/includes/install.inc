<?php
        global $application;
    	
        $tables = Customer_Reviews::getTables();           
        $query = new DB_Table_Create($tables);

        $group_info = array('GROUP_NAME'        => 'CUSTOMER_REVIEWS',
                            'GROUP_DESCRIPTION' => array('NAME'        => array('CR', 'CR_NAME'),
                                                         'DESCRIPTION' => array('CR', 'CR_SETTINGS')),
                            'GROUP_VISIBILITY'  => 'SHOW');
        
        modApiFunc('Settings','createGroup', $group_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CUSTOMER_REVIEWS_ENABLE',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CR', 'CUSTOMER_REVIEWS_ENABLE_NAME'),
                                                       'DESCRIPTION' => array('CR', 'CUSTOMER_REVIEWS_ENABLE_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CR', 'CR_CFG_YES'),
                                                                       'DESCRIPTION' => array('CR', 'CR_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CR', 'CR_CFG_NO'),
                                                                       'DESCRIPTION' => array('CR', 'CR_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings','createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CUSTOMER_REVIEWS_WRITING',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CR', 'CUSTOMER_REVIEWS_WRITING_NAME'),
                                                       'DESCRIPTION' => array('CR', 'CUSTOMER_REVIEWS_WRITING_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'ALL',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CR', 'CR_WRITING_ALL'),
                                                                       'DESCRIPTION' => array('CR', 'CR_WRITING_ALL_DESCR') ),
                                       ), 
                                 array(  'VALUE' => 'REGONLY',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CR', 'CR_WRITING_REGONLY'),
                                                                       'DESCRIPTION' => array('CR', 'CR_WRITING_REGONLY_DESCR') ),
                                       ),
                                 array(  'VALUE' => 'NONE',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CR', 'CR_WRITING_NONE'),
                                                                       'DESCRIPTION' => array('CR', 'CR_WRITING_NONE_DESCR') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'ALL',
                         'PARAM_DEFAULT_VALUE' => 'ALL',
        );
        modApiFunc('Settings','createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CUSTOMER_REVIEWS_MULTIPLE',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CR', 'CUSTOMER_REVIEWS_MULTIPLE_NAME'),
                                                       'DESCRIPTION' => array('CR', 'CUSTOMER_REVIEWS_MULTIPLE_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CR', 'CR_CFG_YES'),
                                                                       'DESCRIPTION' => array('CR', 'CR_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CR', 'CR_CFG_NO'),
                                                                       'DESCRIPTION' => array('CR', 'CR_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'NO',
                         'PARAM_DEFAULT_VALUE' => 'NO',
        );
        modApiFunc('Settings','createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CUSTOMER_REVIEWS_VIEWING',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CR', 'CUSTOMER_REVIEWS_VIEWING_NAME'),
                                                       'DESCRIPTION' => array('CR', 'CUSTOMER_REVIEWS_VIEWING_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'ALL',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CR', 'CR_VIEWING_ALL'),
                                                                       'DESCRIPTION' => array('CR', 'CR_VIEWING_ALL_DESCR') ),
                                       ), 
                                 array(  'VALUE' => 'REGONLY',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CR', 'CR_VIEWING_REGONLY'),
                                                                       'DESCRIPTION' => array('CR', 'CR_VIEWING_REGONLY_DESCR') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'ALL',
                         'PARAM_DEFAULT_VALUE' => 'ALL',
        );
        modApiFunc('Settings','createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CUSTOMER_REVIEWS_APPROVING',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CR', 'CUSTOMER_REVIEWS_APPROVING_NAME'),
                                                       'DESCRIPTION' => array('CR', 'CUSTOMER_REVIEWS_APPROVING_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'ALL',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CR', 'CR_APPROVING_ALL'),
                                                                       'DESCRIPTION' => array('CR', 'CR_APPROVING_ALL_DESCR') ),
                                       ), 
                                 array(  'VALUE' => 'ANONYMOUS',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CR', 'CR_APPROVING_ANONYMOUS'),
                                                                       'DESCRIPTION' => array('CR', 'CR_APPROVING_ANONYMOUS_DESCR') ),
                                       ),
                                 array(  'VALUE' => 'NONE',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CR', 'CR_APPROVING_NONE'),
                                                                       'DESCRIPTION' => array('CR', 'CR_APPROVING_NONE_DESCR') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'NONE',
                         'PARAM_DEFAULT_VALUE' => 'NONE',
        );
        modApiFunc('Settings','createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CUSTOMER_REVIEWS_SORTORDER',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CR', 'CUSTOMER_REVIEWS_SORTORDER_NAME'),
                                                       'DESCRIPTION' => array('CR', 'CUSTOMER_REVIEWS_SORTORDER_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'DATE_DESC',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CR', 'CR_SORTORDER_DATE_DESC'),
                                                                       'DESCRIPTION' => array('CR', 'CR_SORTORDER_DATE_DESC_DESCR') ),
                                       ), 
                                 array(  'VALUE' => 'DATE_ASC',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CR', 'CR_SORTORDER_DATE_ASC'),
                                                                       'DESCRIPTION' => array('CR', 'CR_SORTORDER_DATE_ASC_DESCR') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'DATE_DESC',
                         'PARAM_DEFAULT_VALUE' => 'DATE_DESC',
        );
        modApiFunc('Settings','createParam', $param_info);

        $param_info = array( 
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CUSTOMER_REVIEWS_AJAX_ACTIVE',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CR', 'CUSTOMER_REVIEWS_AJAX_ACTIVE_NAME'),
                                                       'DESCRIPTION' => array('CR', 'CUSTOMER_REVIEWS_AJAX_ACTIVE_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array( 
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CR', 'CR_CFG_YES'),
                                                                       'DESCRIPTION' => array('CR', 'CR_CFG_YES') ),
                                       ), 
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CR', 'CR_CFG_NO'),
                                                                       'DESCRIPTION' => array('CR', 'CR_CFG_NO') ),
                                       )), 
                         'PARAM_CURRENT_VALUE' => 'NO',
                         'PARAM_DEFAULT_VALUE' => 'NO',
        );
        modApiFunc('Settings','createParam', $param_info);

        modApiFunc('Customer_Reviews', 'insertNewRate', 'Overall', 'Y');

        modApiFunc('EventsManager', 'addEventHandler',
                   'ProductsDeleted', 'Customer_Reviews',
                   'onProductsDeleted');
?>