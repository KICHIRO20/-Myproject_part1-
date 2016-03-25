<?php
        global $application;
        $obj = &$application->getInstance('MessageResources');

//        $tables = $this->getTables();           
        $tables = Catalog::getTables();           
        $query = new DB_Table_Create($tables);

        # Dump data for table 'attribute_groups'
        $table = 'attribute_groups';            #the name of the filled table
        $columns = $tables[$table]['columns'];  #the array of field names of the table

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue('PT_ATTRGRP_001', $columns['name']);
        $query->addInsertValue(1, $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['id']);
        $query->addInsertValue('PT_ATTRGRP_002', $columns['name']);
        $query->addInsertValue(2, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['id']);
        $query->addInsertValue('PT_ATTRGRP_003', $columns['name']);
        $query->addInsertValue(3, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['id']);
        $query->addInsertValue('PT_ATTRGRP_004', $columns['name']);
        $query->addInsertValue(4, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(5, $columns['id']);
        $query->addInsertValue('PT_ATTRGRP_005', $columns['name']);
        $query->addInsertValue(5, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(6, $columns['id']);
        $query->addInsertValue('PT_ATTRGRP_006', $columns['name']);
        $query->addInsertValue(8, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(7, $columns['id']);
        $query->addInsertValue('PT_ATTRGRP_007', $columns['name']);
        $query->addInsertValue(6, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(8, $columns['id']);
        $query->addInsertValue('PT_ATTRGRP_008', $columns['name']);
        $query->addInsertValue(7, $columns['sort_order']);
        $application->db->getDB_Result($query);


        # Dump data for table 'attributes'
        $table = 'attributes';                  
        $columns = $tables[$table]['columns'];  

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue(1, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('currency', $columns['ut']);
        $query->addInsertValue('SalePrice', $columns['view_tag']);
        $query->addInsertValue('PRD_SALEPRC_NAME', $columns['name']);
        $query->addInsertValue('PRD_SALEPRC_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('15', $columns['max']);
        $query->addInsertValue('15', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(1, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['id']);
        $query->addInsertValue(1, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('currency', $columns['ut']);
        $query->addInsertValue('ListPrice', $columns['view_tag']);
        $query->addInsertValue('PRD_LISTPRC_NAME', $columns['name']);
        $query->addInsertValue('PRD_LISTPRC_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('15', $columns['max']);
        $query->addInsertValue('15', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(2, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['id']);
        $query->addInsertValue(1, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('item', $columns['ut']);
        $query->addInsertValue('QuantityInStock', $columns['view_tag']);
        $query->addInsertValue('PRD_QUINSTOCK_NAME', $columns['name']);
        $query->addInsertValue('PRD_QUINSTOCK_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('15', $columns['max']);
        $query->addInsertValue('15', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(3, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['id']);
        $query->addInsertValue(1, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('string256', $columns['ut']);
        $query->addInsertValue('SKU', $columns['view_tag']);
        $query->addInsertValue('PRD_SKU_NAME', $columns['name']);
        $query->addInsertValue('PRD_SKU_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('256', $columns['max']);
        $query->addInsertValue('30', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(4, $columns['sort_order']);
        $application->db->getDB_Result($query);

/*Commented By Girin Removed product Attributes according Task ASC-87
        $query = new DB_Insert($table);
        $query->addInsertValue(5, $columns['id']);
        $query->addInsertValue(1, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('item', $columns['ut']);
        $query->addInsertValue('MinQuantity', $columns['view_tag']);
        $query->addInsertValue('PRD_QUINORDER_NAME', $columns['name']);
        $query->addInsertValue('PRD_QUINORDER_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('15', $columns['max']);
        $query->addInsertValue('15', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(5, $columns['sort_order']);
        $application->db->getDB_Result($query);
*/

        $query = new DB_Insert($table);
        $query->addInsertValue(6, $columns['id']);
        $query->addInsertValue(1, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('item', $columns['ut']);
        $query->addInsertValue('LowStockLevel', $columns['view_tag']);
        $query->addInsertValue('PRD_LOWLEV_NAME', $columns['name']);
        $query->addInsertValue('PRD_LOWLEV_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('15', $columns['max']);
        $query->addInsertValue('15', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(6, $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(7, $columns['id']);
        $query->addInsertValue(1, $columns['ag_id']);
        $query->addInsertValue(7, $columns['it_id']);
        $query->addInsertValue('', $columns['ut']);
        $query->addInsertValue('Available', $columns['view_tag']);
        $query->addInsertValue('PRD_AVAIL_NAME', $columns['name']);
        $query->addInsertValue('PRD_AVAIL_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('10', $columns['max']);
        $query->addInsertValue('5', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(7, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(8, $columns['id']);
        $query->addInsertValue(1, $columns['ag_id']);
        $query->addInsertValue(6, $columns['it_id']);
        $query->addInsertValue('', $columns['ut']);
        $query->addInsertValue('TaxClass', $columns['view_tag']);
        $query->addInsertValue('PRD_TAX_CLASS_NAME', $columns['name']);
        $query->addInsertValue('PRD_TAX_CLASS_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('0', $columns['max']);
        $query->addInsertValue('0', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(8, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(9, $columns['id']);
        $query->addInsertValue(2, $columns['ag_id']);
        $query->addInsertValue(5, $columns['it_id']);
        $query->addInsertValue('', $columns['ut']);
        $query->addInsertValue('LargeImage', $columns['view_tag']);
        $query->addInsertValue('PRD_LRGIMG_NAME', $columns['name']);
        $query->addInsertValue('PRD_LRGIMG_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('0', $columns['max']);
        $query->addInsertValue('0', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(9, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(10, $columns['id']);
        $query->addInsertValue(2, $columns['ag_id']);
        $query->addInsertValue(5, $columns['it_id']);
        $query->addInsertValue('', $columns['ut']);
        $query->addInsertValue('SmallImage', $columns['view_tag']);
        $query->addInsertValue('PRD_SMLIMG_NAME', $columns['name']);
        $query->addInsertValue('PRD_SMLIMG_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('0', $columns['max']);
        $query->addInsertValue('0', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(10, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(11, $columns['id']);
        $query->addInsertValue(2, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('string256', $columns['ut']);
        $query->addInsertValue('ImageAltText', $columns['view_tag']);
        $query->addInsertValue('PRD_IMGDESCR_NAME', $columns['name']);
        $query->addInsertValue('PRD_IMGDESCR_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('256', $columns['max']);
        $query->addInsertValue('70', $columns['size']);
        $query->addInsertValue('Y', $columns['multilang']);
        $query->addInsertValue(11, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(12, $columns['id']);
        $query->addInsertValue(3, $columns['ag_id']);
        $query->addInsertValue(2, $columns['it_id']);
        $query->addInsertValue('mstring', $columns['ut']);
        $query->addInsertValue('ShortDescription', $columns['view_tag']);
        $query->addInsertValue('PRD_SHRDESCR_NAME', $columns['name']);
        $query->addInsertValue('PRD_SHRDESCR_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('1', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('0', $columns['max']);
        $query->addInsertValue('0', $columns['size']);
        $query->addInsertValue('Y', $columns['multilang']);
        $query->addInsertValue(12, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(13, $columns['id']);
        $query->addInsertValue(3, $columns['ag_id']);
        $query->addInsertValue(2, $columns['it_id']);
        $query->addInsertValue('mstring', $columns['ut']);
        $query->addInsertValue('DetailedDescription', $columns['view_tag']);
        $query->addInsertValue('PRD_DETDESCR_NAME', $columns['name']);
        $query->addInsertValue('PRD_DETDESCR_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('1', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('0', $columns['max']);
        $query->addInsertValue('0', $columns['size']);
        $query->addInsertValue('Y', $columns['multilang']);
        $query->addInsertValue(13, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(14, $columns['id']);
        $query->addInsertValue(4, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('currency', $columns['ut']);
        $query->addInsertValue('PerItemShippingCost', $columns['view_tag']);
        $query->addInsertValue('PRD_SHIPPRC_NAME', $columns['name']);
        $query->addInsertValue('PRD_SHIPPRC_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('15', $columns['max']);
        $query->addInsertValue('15', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(14, $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(15, $columns['id']);
        $query->addInsertValue(4, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('weight', $columns['ut']);
        $query->addInsertValue('Weight', $columns['view_tag']);
        $query->addInsertValue('PRD_WEIGHT_NAME', $columns['name']);
        $query->addInsertValue('PRD_WEIGHT_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('15', $columns['max']);
        $query->addInsertValue('15', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(16, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(17, $columns['id']);
        $query->addInsertValue(5, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('string256', $columns['ut']);
        $query->addInsertValue('PageTitle', $columns['view_tag']);
        $query->addInsertValue('PRD_PAGETTL_NAME', $columns['name']);
        $query->addInsertValue('PRD_PAGETTL_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('256', $columns['max']);
        $query->addInsertValue('75', $columns['size']);
        $query->addInsertValue('Y', $columns['multilang']);
        $query->addInsertValue(17, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(18, $columns['id']);
        $query->addInsertValue(5, $columns['ag_id']);
        $query->addInsertValue(2, $columns['it_id']);
        $query->addInsertValue('string1024', $columns['ut']);
        $query->addInsertValue('MetaKeywords', $columns['view_tag']);
        $query->addInsertValue('PRD_METAKWRD_NAME', $columns['name']);
        $query->addInsertValue('PRD_METAKWRD_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('0', $columns['max']);
        $query->addInsertValue('0', $columns['size']);
        $query->addInsertValue('Y', $columns['multilang']);
        $query->addInsertValue(18, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(19, $columns['id']);
        $query->addInsertValue(5, $columns['ag_id']);
        $query->addInsertValue(2, $columns['it_id']);
        $query->addInsertValue('string1024', $columns['ut']);
        $query->addInsertValue('MetaDescription', $columns['view_tag']);
        $query->addInsertValue('PRD_METADESCR_NAME', $columns['name']);
        $query->addInsertValue('PRD_METADESCR_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('0', $columns['max']);
        $query->addInsertValue('0', $columns['size']);
        $query->addInsertValue('Y', $columns['multilang']);
        $query->addInsertValue(19, $columns['sort_order']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(20, $columns['id']);
        $query->addInsertValue(4, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('currency', $columns['ut']);
        $query->addInsertValue('PerItemHandlingCost', $columns['view_tag']);
        $query->addInsertValue('PRD_HANDRC_NAME', $columns['name']);
        $query->addInsertValue('PRD_HANDRC_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('15', $columns['max']);
        $query->addInsertValue('15', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(15, $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(21, $columns['id']);
        $query->addInsertValue(4, $columns['ag_id']);
        $query->addInsertValue(3, $columns['it_id']);
        $query->addInsertValue('', $columns['ut']);
        $query->addInsertValue('FreeShipping', $columns['view_tag']);
        $query->addInsertValue('PRD_FREESHIP_NAME', $columns['name']);
        $query->addInsertValue('PRD_FREESHIP_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('0', $columns['max']);
        $query->addInsertValue('0', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(16, $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(22, $columns['id']);
        $query->addInsertValue(4, $columns['ag_id']);
        $query->addInsertValue(3, $columns['it_id']);
        $query->addInsertValue('', $columns['ut']);
        $query->addInsertValue('NeedShipping', $columns['view_tag']);
        $query->addInsertValue('PRD_NEEDSHIP_NAME', $columns['name']);
        $query->addInsertValue('PRD_NEEDSHIP_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('0', $columns['max']);
        $query->addInsertValue('0', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(17, $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(23, $columns['id']);
        $query->addInsertValue(5, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('string1024', $columns['ut']);
        $query->addInsertValue('SEOPrefix', $columns['view_tag']);
        $query->addInsertValue('PRD_SEOURL_NAME', $columns['name']);
        $query->addInsertValue('PRD_SEOURL_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('256', $columns['max']);
        $query->addInsertValue('75', $columns['size']);
        $query->addInsertValue('Y', $columns['multilang']);
        $query->addInsertValue(20, $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(24, $columns['id']);
        $query->addInsertValue(1, $columns['ag_id']);
        $query->addInsertValue(8, $columns['it_id']);
        $query->addInsertValue('', $columns['ut']);
        $query->addInsertValue('Manufacturer', $columns['view_tag']);
        $query->addInsertValue('PRD_MANUFACTURER_NAME', $columns['name']);
        $query->addInsertValue('PRD_MANUFACTURER_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('0', $columns['max']);
        $query->addInsertValue('0', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(60, $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(25, $columns['id']);
        $query->addInsertValue(7, $columns['ag_id']);
        $query->addInsertValue(9, $columns['it_id']);
        $query->addInsertValue('', $columns['ut']);
        $query->addInsertValue('CustomerReviews', $columns['view_tag']);
        $query->addInsertValue('PRD_CUSTOMER_REVIEWS_NAME', $columns['name']);
        $query->addInsertValue('PRD_CUSTOMER_REVIEWS_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('0', $columns['max']);
        $query->addInsertValue('0', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(61, $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(26, $columns['id']);
        $query->addInsertValue(9, $columns['ag_id']);
        $query->addInsertValue(10, $columns['it_id']);
        $query->addInsertValue('', $columns['ut']);
        $query->addInsertValue('MembershipVisibility', $columns['view_tag']);
        $query->addInsertValue('PRD_MEMBERSHIP_VISIBILITY_NAME', $columns['name']);
        $query->addInsertValue('PRD_MEMBERSHIP_VISIBILITY_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('0', $columns['max']);
        $query->addInsertValue('0', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(62, $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(27, $columns['id']);
        $query->addInsertValue(1, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('string256', $columns['ut']);
        $query->addInsertValue('UPC', $columns['view_tag']);
        $query->addInsertValue('PRD_UPC_NAME', $columns['name']);
        $query->addInsertValue('PRD_UPC_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('15', $columns['max']);
        $query->addInsertValue('15', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(63, $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(28, $columns['id']);
        $query->addInsertValue(1, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('string256', $columns['ut']);
        $query->addInsertValue('EAN', $columns['view_tag']);
        $query->addInsertValue('PRD_EAN_NAME', $columns['name']);
        $query->addInsertValue('PRD_EAN_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('15', $columns['max']);
        $query->addInsertValue('15', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(64, $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(29, $columns['id']);
        $query->addInsertValue(1, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('string256', $columns['ut']);
        $query->addInsertValue('JAN', $columns['view_tag']);
        $query->addInsertValue('PRD_JAN_NAME', $columns['name']);
        $query->addInsertValue('PRD_JAN_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('15', $columns['max']);
        $query->addInsertValue('15', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(65, $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(30, $columns['id']);
        $query->addInsertValue(1, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('string256', $columns['ut']);
        $query->addInsertValue('ISBN', $columns['view_tag']);
        $query->addInsertValue('PRD_ISBN_NAME', $columns['name']);
        $query->addInsertValue('PRD_ISBN_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('15', $columns['max']);
        $query->addInsertValue('15', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(66, $columns['sort_order']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(31, $columns['id']);
        $query->addInsertValue(1, $columns['ag_id']);
        $query->addInsertValue(1, $columns['it_id']);
        $query->addInsertValue('string256', $columns['ut']);
        $query->addInsertValue('MPN', $columns['view_tag']);
        $query->addInsertValue('PRD_MPN_NAME', $columns['name']);
        $query->addInsertValue('PRD_MPN_DESCR', $columns['descr']);
        $query->addInsertValue('standard', $columns['type']);
        $query->addInsertValue('0', $columns['allow_html']);
        $query->addInsertValue('0', $columns['min']);
        $query->addInsertValue('256', $columns['max']);
        $query->addInsertValue('15', $columns['size']);
        $query->addInsertValue('N', $columns['multilang']);
        $query->addInsertValue(67, $columns['sort_order']);
        $application->db->getDB_Result($query);

        # Dump data for table 'product_types'
        $table = 'product_types';               
        $columns = $tables[$table]['columns'];  

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue($obj->getMessage('PRDTYPE_GENERAL_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('PRDTYPE_GENERAL_DESCR'), $columns['descr']);
        $application->db->getDB_Result($query);


        # Dump data for table 'product_type_attributes'
        $table = 'product_type_attributes';     
        $columns = $tables[$table]['columns'];  

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(1, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(true, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(2, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(3, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(4, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);

/*Commented By Girin Removed product Attributes according Task ASC-87
        $query = new DB_Insert($table);
        $query->addInsertValue(5, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(5, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('0', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);
*/

        $query = new DB_Insert($table);
        $query->addInsertValue(6, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(6, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(7, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(7, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('1', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(8, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(8, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('1', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(9, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(9, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(10, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(10, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(11, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(11, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(12, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(12, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(13, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(13, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(14, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(14, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(15, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(15, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(17, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(17, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(18, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(18, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(19, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(19, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(20, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(20, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(21, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(21, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('2', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(22, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(22, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('1', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(23, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(23, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(24, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(24, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(25, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(25, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(26, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(26, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(27, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(27, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(28, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(28, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(29, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(29, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(30, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(30, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(31, $columns['id']);
        $query->addInsertValue(1, $columns['pt_id']);
        $query->addInsertValue(31, $columns['a_id']);
        $query->addInsertValue(true, $columns['type_attr_visible']);
        $query->addInsertValue(false, $columns['type_attr_required']);
        $query->addInsertValue('', $columns['type_attr_def_val']);
        $application->db->getDB_Result($query);

        # Dump data for table 'input_types'
        $table = 'input_types';                 
        $columns = $tables[$table]['columns'];  

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue(0, $columns['ut_id']);
        $query->addInsertValue('TEXT', $columns['name']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['id']);
        $query->addInsertValue(0, $columns['ut_id']);
        $query->addInsertValue('TEXTAREA', $columns['name']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['id']);
        $query->addInsertValue(0, $columns['ut_id']);
        $query->addInsertValue('SELECT', $columns['name']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['id']);
        $query->addInsertValue(0, $columns['ut_id']);
        $query->addInsertValue('FILE', $columns['name']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(5, $columns['id']);
        $query->addInsertValue(0, $columns['ut_id']);
        $query->addInsertValue('IMAGE', $columns['name']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(6, $columns['id']);
        $query->addInsertValue(0, $columns['ut_id']);
        $query->addInsertValue('SELECT', $columns['name']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(7, $columns['id']);
        $query->addInsertValue(0, $columns['ut_id']);
        $query->addInsertValue('SELECT', $columns['name']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(9, $columns['id']);
        $query->addInsertValue(0, $columns['ut_id']);
        $query->addInsertValue('SELECT', $columns['name']);
        $application->db->getDB_Result($query);

        # Dump data for table 'input_type_values'
        $table = 'input_type_values';           
        $columns = $tables[$table]['columns'];  

        $query = new DB_Insert($table);
        $query->addInsertValue(PRODUCT_FREESHIPPING_YES, $columns['id']);
        $query->addInsertValue(3, $columns['it_id']);
        $query->addInsertValue('PRDTYPE_VALUE_YES', $columns['value']);
        $application->db->getDB_Result($query);


        $query = new DB_Insert($table);
        $query->addInsertValue(PRODUCT_FREESHIPPING_NO, $columns['id']);
        $query->addInsertValue(3, $columns['it_id']);
        $query->addInsertValue('PRDTYPE_VALUE_NO', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(PRODUCT_STATUS_ONLINE, $columns['id']);
        $query->addInsertValue(7, $columns['it_id']);
        $query->addInsertValue('PRDTYPE_VALUE_STATUS_ONLINE', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(PRODUCT_STATUS_OFFLINE, $columns['id']);
        $query->addInsertValue(7, $columns['it_id']);
        $query->addInsertValue('PRDTYPE_VALUE_STATUS_OFFLINE', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(PRODUCT_CUSTOMER_REVIEWS_MESSAGE_RATE, $columns['id']);
        $query->addInsertValue(9, $columns['it_id']);
        $query->addInsertValue('PRDTYPE_VALUE_REVIEW_RATE', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(PRODUCT_CUSTOMER_REVIEWS_MESSAGE, $columns['id']);
        $query->addInsertValue(9, $columns['it_id']);
        $query->addInsertValue('PRDTYPE_VALUE_REVIEW', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(PRODUCT_CUSTOMER_REVIEWS_RATE, $columns['id']);
        $query->addInsertValue(9, $columns['it_id']);
        $query->addInsertValue('PRDTYPE_VALUE_RATE', $columns['value']);
        $application->db->getDB_Result($query);

        $query = new DB_Insert($table);
        $query->addInsertValue(PRODUCT_CUSTOMER_REVIEWS_NOREVIEW, $columns['id']);
        $query->addInsertValue(9, $columns['it_id']);
        $query->addInsertValue('PRDTYPE_VALUE_NOREVIEW', $columns['value']);
        $application->db->getDB_Result($query);

        # Dump data for table 'categories'
        $table = 'categories';           
        $columns = $tables[$table]['columns'];  

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue(1, $columns['left']);
        $query->addInsertValue(2, $columns['right']);
        $query->addInsertValue(0, $columns['level']);
        $query->addInsertValue(0, $columns['sort_order']);
        $application->db->getDB_Result($query);

        # Dump data for table 'categories_descr'
        $table = 'categories_descr';           
        $columns = $tables[$table]['columns'];  

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue($obj->getMessage('CTGR_HOME_NAME'), $columns['name']);
        $query->addInsertValue($obj->getMessage('CTGR_HOME_DESCR'), $columns['descr']);
        $application->db->getDB_Result($query);

       _use(dirname(__FILE__).'/install_group_edit_settings.inc');
       _use(dirname(__FILE__).'/install_catalog_navigation_settings.inc');
?>
