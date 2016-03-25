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
 * Catalog module meta info.
 *
 * @package Catalog
 * @author Alexey Kolesnikov
 * @version $Id$
 */

$moduleInfo = array (
    'name'          => 'Catalog',
    'shortName'     => 'CTL',
    'groups'        => 'Main',
    'description'   => 'Catalog module description',
    'version'       => '0.1.47700',
    'author'        => 'Alexey Kolesnikov',
    'contact'       => '',
    'systemModule'  => false,
    'mainFile'      => 'catalog_api.php',
    'constantsFile' => 'const.php',
    'resFile'       => 'catalog-messages',
    'extraAPIFiles' => array(
         'CatalogSearch'            => 'abstract/catalog_search.php',
         'CCategoryInfo'            => 'abstract/category_class.php',
         'CProductInfo'             => 'abstract/product_class.php',
         'DataReaderProductsDB'     => 'abstract/data_reader_products_db.php',
         'DataFilterProductsDBCSV'  => 'abstract/data_filter_products_db_csv.php',
         'DataFilterProductsCSVDB'  => 'abstract/data_filter_products_csv_db.php',
         'DataWriterProductsDB'     => 'abstract/data_writer_products_db.php',
         'ProductsBrowser'          => 'abstract/products_browser.php',
         'CategoriesBrowser'        => 'abstract/categories_browser.php',
         'CategoriesBrowserDynamic' => 'abstract/categories_browser_dynamic.php',
         'CProductListFilter'       => 'abstract/product_list_filter.php'
    ),
    'actions'       => array(
         'AdminZone'    => array(
             'AddCategoryInfo'           => 'add_category_info_action.php',
             'MoveToCategory'            => 'move_category_action.php',
             'MoveToProducts'            => 'move_products_action.php',
             'CopyToProducts'            => 'copy_products_action.php',
             'SaveSortedCategories'      => 'save_sorted_categories_action.php',
             'SaveSortedProducts'        => 'save_sorted_products_action.php',
             'UpdateCategoryInfo'        => 'update_category_info_action.php',
             'UpdateProductInfo'         => 'update_product_info_action.php',
             'AddProductInfoAction'      => 'add_product_info_action.php',
             'ConfirmDeleteCategory'     => 'confirm_delete_category_action.php',
             'ConfirmDeleteProducts'     => 'confirm_delete_products_action.php',
             'ConfirmDeleteProductTypes' => 'confirm_delete_product_types_action.php',
             'AddProductTypeAction'      => 'add_product_type_action.php',
             'UpdateProductTypeAction'   => 'update_product_type_action.php',
             'AddCustomAttributeAction'  => 'add_custom_attribute_action.php',
             'UpdateCustomAttribute'     => 'update_custom_attribute_action.php',
             'update_product_cats'       => 'update_product_cats.php',
             'ajax_get_plist'            => 'ajax_get_plist.php',
             'do_products_export'        => 'do_products_export.php',
             'do_products_import'        => 'do_products_import.php',
             'get_ctg_review'            => 'get_ctg_review.php',
             'save_ctg_tree'             => 'save_ctg_tree.php',
             'UpdateProductGroup'        => 'update_product_group.php',
             'SetProductGroup'           => 'set_product_group.php',
             'SetSearchProductFormFilter'=> 'set_search_product_form_filter.php'
    ),
         'SetCurrCat' => 'setcurrcat_action.php',
         'SetCurrMnf' => 'setcurrmnf_action.php',
         'SetProductTypeFilter'      => 'set_product_type_filter_action.php',
         'SetProductListSortField'   => 'set_product_list_sort_field.php',
         'SetMoveToCat'              => 'set_moveto_cat_action.php',
         'SetCurrentProduct'         => 'setcurrprod_action.php',
         'SetCurrentProductType'     => 'set_current_product_type_action.php',
         'SetEditableCategory'       => 'set_editable_category_action.php',
         'SetEditableProducts'       => 'set_editable_products_action.php',
         'SearchProducts'            => 'search_products_action.php',
    ),
    'hooks' => array
    (
        # 'hook_class_name' => array ( 'onAction'  => 'action_class_name',
        #                              'Hook_File' => 'hook_file_name' )
        'manufacturers_deleted' => array ( 'onAction'  => 'del_manufacturers',
                                           'Hook_File' => 'manufacturers_deleted.php' )
    ),
    'views'         => array(
         'AdminZone'    => array(
             'NavigationBar'         => 'navigation-bar-az.php',
             'ProductList'           => 'prodslist_az.php',
             'ProductInfo'           => 'product_info_az.php',
             'AddProductInfo'        => 'add_product_info_az.php',
             'EditProductInfo'       => 'edit_product_info_az.php',
             'AddCategory'           => 'add_category_az.php',
             'MoveCategory'          => 'move_category_az.php',
             'MoveProducts'          => 'move_products_az.php',
             'CopyProducts'          => 'copy_products_az.php',
             'DeleteCategory'        => 'delete_category_az.php',
             'DeleteProducts'        => 'delete_products_az.php',
             'ViewCategory'          => 'view_category_az.php',
             'SortCategories'        => 'sort_categories_az.php',
             'SortProducts'          => 'sort_products_az.php',
             'EditCategory'          => 'edit_category_az.php',
             'ManageProductTypes'    => 'manage_product_types_az.php',
             'AddProductType'        => 'add_product_type_az.php',
             'EditProductType'       => 'edit_product_type_az.php',
             'DeleteProductType'     => 'delete_product_type_az.php',
             'AddCustomAttribute'    => 'add_custom_attribute_az.php',
             'Breadcrumb'            => 'breadcrumb-az.php',
             'EditCustomAttribute'   => 'edit_custom_attribute_az.php',
             'AttributeHelp'         => 'attribute-help-az.php',
             'SearchForm'            => 'search_form_az.php',
             'SearchFormShort'       => 'search_form_short_az.php',
             'SearchResult'          => 'search_result_az.php',
             'ProductBookmarks'      => 'product_bookmarks.php',
             'ExportProductsView'    => 'export_products_view_az.php',
             'ImportProductsView'    => 'import_products_view_az.php',
             'MngProductCats'        => 'mng_product_cats_az.php',
             'CategoryBookmarks'     => 'category_bookmarks.php',
             'CategoryReview'        => 'category_review_az.php',
             'ProductGroupEdit'      => 'product_group_edit_az.php',
             'ProductSearchForm'     => 'product_search_form_az.php',
             'ProductSearchFormShort'=> 'product_search_form_short_az.php'
         ),
         'CustomerZone' => array(
             'NavigationBar'         => 'navigation-bar-cz.php',
             'NavigationBarCustom'   => 'navigation-bar-custom-cz.php',
             'ProductList'           => 'prodslist_cz.php',
             'CategorySheet'         => 'category_sheet_cz.php',
             'Subcategories'         => 'subcategories_cz.php',
             'Breadcrumb'            => 'cat_breadcrumb_cz.php',
             'ProductInfo'           => 'product_info_cz.php',
             'SearchForm'            => 'search_form_cz.php',
             'ProductListSorter'     => 'product_list_sorter_cz.php',
             'SearchResult'          => 'search_result_cz.php',
             'ProductSet'            => 'product_set_view.php',
             'CustomerReviewsProductInfo' => 'cr_product_info_cz.php',
             'ProductTypeFilter'     => 'product_type_filter_cz.php'
         ),
         'Aliases' => array(
             'NavigationBarDynatree' => 'NavigationBarCustom'
         )
    )
);
?>