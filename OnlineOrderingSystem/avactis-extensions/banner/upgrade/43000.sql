INSERT INTO `admin_pages` (
`id` ,
`identifier` ,
`classname` ,
`title` ,
`heading` ,
`help_identifier` ,
`item_value`
)
VALUES (
NULL , 'BannerManagement', 'BannerManagement',
'BN,BN_TITLE', 'BN,BN_SYS_MANAGE',
'BannerManagement', '0'
);

INSERT INTO `admin_pages` (
`id` ,
`identifier` ,
`classname` ,
`title` ,
`heading` ,
`help_identifier` ,
`item_value`
)
VALUES (
NULL , 'BannerLocation', 'BannerLocation',
'BN,BN_TITLE', 'BN,BN_SYS_MANAGE',
'BannerLocation', '0'
);

INSERT INTO `admin_pages` (
`id` ,
`identifier` ,
`classname` ,
`title` ,
`heading` ,
`help_identifier` ,
`item_value`
)
VALUES (
NULL , 'BannerContentManagement', 'BannerContentManagement',
'BN,BN_TITLE', 'BN,BN_SYS_MANAGE',
'BannerContentManagement', '0'
);

INSERT INTO `menu_admin` (
`id` ,
`menu_name` ,
`menu_description` ,
`icon_image` ,
`parent` ,
`has_subcategory` ,
`visibility` ,
`menu_url` ,
`group_name` ,
`new_window`
)
VALUES (
NULL , 'BN,BN_TITLE', 'BN,BN_TITLE_DESCR', '', 'SYS,EXTENSION_CNFGR', '1', '1',
'admin_template.php?identifier=BannerLocation', 'home', '0'
);

INSERT INTO `menu_admin` (
`id` ,
`menu_name` ,
`menu_description` ,
`icon_image` ,
`parent` ,
`has_subcategory` ,
`visibility` ,
`menu_url` ,
`group_name` ,
`new_window`
)
VALUES (
NULL , 'BN,BN_TOP', 'BN,BN_TOP_DESCR', '', 'BN,BN_TITLE', '0', '1',
'admin_template.php?identifier=BannerManagement&type=T', 'home', '0'
);

INSERT INTO `menu_admin` (
`id` ,
`menu_name` ,
`menu_description` ,
`icon_image` ,
`parent` ,
`has_subcategory` ,
`visibility` ,
`menu_url` ,
`group_name` ,
`new_window`
)
VALUES (
NULL , 'BN,BN_BOTTOM', 'BN,BN_BOTTOM_DESCR', '', 'BN,BN_TITLE', '0', '1',
'admin_template.php?identifier=BannerManagement&type=B', 'home', '0'
);

INSERT INTO `menu_admin` (
`id` ,
`menu_name` ,
`menu_description` ,
`icon_image` ,
`parent` ,
`has_subcategory` ,
`visibility` ,
`menu_url` ,
`group_name` ,
`new_window`
)
VALUES (
NULL , 'BN,BN_LEFT_BANNER', 'BN,BN_LEFT_BANNER_DESCR', '', 'BN,BN_TITLE', '0', '1',
'admin_template.php?identifier=BannerManagement&type=L', 'home', '0'
);

INSERT INTO `menu_admin` (
`id` ,
`menu_name` ,
`menu_description` ,
`icon_image` ,
`parent` ,
`has_subcategory` ,
`visibility` ,
`menu_url` ,
`group_name` ,
`new_window`
)
VALUES (
NULL , 'BN,BN_RIGHT_BANNER', 'BN,BN_RIGHT_BANNER_DESCR', '', 'BN,BN_TITLE', '0', '1',
'admin_template.php?identifier=BannerManagement&type=R', 'home', '0
'
);
