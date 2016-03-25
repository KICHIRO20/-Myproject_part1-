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
 * GiftCertificate module meta info.
 *
 * @package GiftCertificate
 * @author Alexey Florinsky
 * @version $Id$
 */

$moduleInfo = array
(
    'name' => 'GiftCertificateApi',
    'directory' => 'gift_certificate',
    'shortName' => 'GCT',
    'groups' => 'Main',
    'description' => 'GiftCertificate module description',
    'version' => '0.1.47700',
    'author' => 'Alexey Florinsky',
    'contact' => '',
    'systemModule' => true,
    'mainFile' => 'gift_certificate_api.php',
    'constantsFile' => 'const.php',
    'resFile'       => 'gift_certificate',
    'extraAPIFiles' => array(
        'GiftCertificateInfo' => 'abstract/gift_certificate_classes.php',
        'GiftCertificateBase' => 'abstract/gift_certificate_classes.php',
        'GiftCertificateCreator' => 'abstract/gift_certificate_classes.php',
        'GiftCertificateUpdater' => 'abstract/gift_certificate_classes.php',
        'GiftCertificateLogger' => 'abstract/gift_certificate_classes.php',
        'GiftCertificate' => 'abstract/gift_certificate_classes.php',
),
    'actions'       => array(
         'AdminZone'    => array(
             'GiftCertificateUpdateAction'=> 'GiftCertificateUpdateAction.php',
             'GiftCertificateAddAction' => 'GiftCertificateAddAction.php',
             'GiftCertificateDellAction' => 'GiftCertificateDellAction.php',
         ),
         'AddGiftCertificateAction' => 'AddGiftCertificateAction.php',
         'RemoveGiftCertificateAction' => 'RemoveGiftCertificateAction.php',
         'AddGCToCart' => 'AddGCToCart.php',
         #'RemoveGCFromCart' => 'RemoveGCFromCart.php',

    ),
    'views' => array
    (
         'AdminZone' => array
         (
               'GiftCertificateListView'    => 'gc_list_az.php',
               'GiftCertificateEditView'    => 'gc_edit_az.php',
               'GiftCertificateAddView'     => 'gc_add_az.php',
         ),
         'CustomerZone' => array
         (
               'GiftCertificateForm'        => 'gc_add_cz.php',
               'CreateGiftCertificateForm'  => 'gc_create_cz.php',
         )
    ),

    'admin_access' => array(
        'actions' => array(
            'GiftCertificateUpdateAction' => 'MARKETING:GENERAL',
            'GiftCertificateAddAction'    => 'MARKETING:GENERAL',
        ),
        'views' => array(
            'GiftCertificateListView' => 'MARKETING:GENERAL',
            'GiftCertificateEditView' => 'MARKETING:GENERAL',
            'GiftCertificateAddView'  => 'MARKETING:GENERAL',
        ),
    ),

);

?>