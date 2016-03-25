<?php
        $group_info = array('GROUP_NAME'        => 'CATALOG_NAVIGATION',
                            'GROUP_DESCRIPTION' => array('NAME'        => array('CTL', 'CTL_NAV_NAME'),
                                                         'DESCRIPTION' => array('CTL', 'CTL_NAV_DESCR')),
                            'GROUP_VISIBILITY'  => 'SHOW');

        modApiFunc('Settings', 'createGroup', $group_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_NAV_MANUFACTURER_FILTER',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_NAV_MANUFACTURER_FLR_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_NAV_MANUFACTURER_FLR_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'WHOLE_CATALOG',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_NAV_WHOLE_CTL'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_NAV_WHOLE_CTL') ),
                                       ),
                                 array(  'VALUE' => 'CURRENT_CATEGORY',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_NAV_CUR_CAT_ONLY'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_NAV_CUR_CAT_ONLY') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'CURRENT_CATEGORY',
                         'PARAM_DEFAULT_VALUE' => 'CURRENT_CATEGORY',
        );
        modApiFunc('Settings', 'createParam', $param_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CTL_NAV_NONEMPTY_MANUCATRURERS',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_NAV_NONEMPTY_MANUCATRURERS_NAME'),
                                                       'DESCRIPTION' => array('CTL', 'CTL_NAV_NONEMPTY_MANUCATRURERS_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'SHOW_EMPTY',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_NAV_SHOW'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_NAV_SHOW') ),
                                       ),
                                 array(  'VALUE' => 'HIDE_EMPTY',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CTL', 'CTL_NAV_HIDE'),
                                                                       'DESCRIPTION' => array('CTL', 'CTL_NAV_HIDE') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'HIDE_EMPTY',
                         'PARAM_DEFAULT_VALUE' => 'HIDE_EMPTY',
        );
        modApiFunc('Settings', 'createParam', $param_info);

?>