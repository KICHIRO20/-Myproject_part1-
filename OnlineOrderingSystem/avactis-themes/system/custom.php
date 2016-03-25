<?php
/*
        CUSTOM TAGS
*/

// display three (by default) random products
function ProductSet_Rand($q=3, $cols=3)
{
    global $application;
    $set = new CProductSetTagSettings();
    $set->template['Directory'] = 'catalog/product-set/rand/';

    $set->filter->category_id = 1;
    if (modApiFunc('Catalog','isCorrectCategoryId',$set->filter->category_id) == false)
    {
        return '';
    }

    $set->filter->select_mode_recursiveness = IN_CATEGORY_RECURSIVELY; # const: IN_CATEGORY_ONLY or IN_CATEGORY_RECURSIVELY

    #$set->filter->filter_sale_price_min = 1; # int
    #$set->filter->filter_sale_price_max = 100; # int

    $set->filter->setSelectLimits(0, $q);
    $set->filter->sort_by = SORT_BY_RAND;

    ProductSet($set, $cols);
}

// display jCarousel plugin
function ProductSet_Carousel()
{
    global $application;
    $set = new CProductSetTagSettings();
    $set->template['Directory'] = 'catalog/product-set/carousel/';

    $set->filter->category_id = 44; // Hot Deals Category
    if (modApiFunc('Catalog','isCorrectCategoryId',$set->filter->category_id) == false)
    {
        return '';
    }

    $set->filter->select_mode_recursiveness = IN_CATEGORY_RECURSIVELY; # const: IN_CATEGORY_ONLY or IN_CATEGORY_RECURSIVELY

//    $set->filter->sort_by = SORT_BY_RAND;

    #$set->filter->filter_sale_price_min = 1; # int
    #$set->filter->filter_sale_price_max = 100; # int

    $set->filter->use_paginator = false;
    # this line is required because "Hot Deals" category is offline
    $set->filter->select_online_products_only = false;
    $set->filter->setSelectLimits(0, 10);

    ProductSet($set, 0);
}


?>