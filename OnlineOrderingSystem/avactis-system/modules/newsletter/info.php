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
 * @package Newsletter
 * @author Egor Makarov
 *
 */
$moduleInfo = array
    (
        'name'         => 'Newsletter',
        'shortName'    => 'NLT',
        'groups'       => 'Main',
        'description'  => 'module description',
        'version'      => '0.1.47700',
        'author'       => 'Egor Makarov',
        'contact'      => '',
        'systemModule'  => false,
        'constantsFile'=> 'const.php',
        'mainFile'     => 'newsletter_api.php',
        'resFile'      => 'newsletter-messages',

        'actions' => array
        (
           'AdminZone' => array(
               'do_newsletter_save' => 'do_newsletter_save.php'
              ,'do_newsletter_send' => 'do_newsletter_send.php'
              ,'do_newsletter_delete' => 'do_newsletter_delete.php'
          ),
        ),

        'hooks' => array
        (
        ),

        'views' => array
        (
            'AdminZone' => array(
                'Newsletter_Compose'   => 'newsletter_compose_az.php'
               ,'Newsletter_List'     => 'newsletter_list_az.php'
               ,'Newsletter_Send'     => 'newsletter_send_az.php'
            ),
            'CustomerZone' => array(
            )
        )
    );

?>