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
 * Tools Module, CZLayoutsList View
 *
 * @package Tools
 * @author Vadim Lyalikov
 */
class CZLayoutsList
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * ctor
     */
    function CZLayoutsList()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');
    }

    function outputLayoutConfigs()
    {
        global $application;

        $retval = "";
        $config_array = LayoutConfigurationManager::static_get_cz_layouts_list();

        if(sizeof($config_array) > 0)
        {
            $i = 1;
            foreach ($config_array as $layout_config_ini_path => $config)
            {
                LayoutConfigurationManager::static_activate_cz_layout($layout_config_ini_path);

                $request = new CZRequest();
                $request->setView('ProductInfo');
                $request->setAction('SetCurrentProduct');
                $request->setKey('prod_id', "1");

                $template_contents = array(
                                           "LayoutN" => $i
                                          ,"LayoutName" => $config["SITE_URL"]
                                          ,"CZStorefrontHref" => $config["SITE_URL"]
                                          ,"CZStorefrontProductInfoHref" => $request->getURL()
                                          );
                $this->_Template_Contents = $template_contents;
                $application->registerAttributes($this->_Template_Contents);

                $retval.= modApiFunc('TmplFiller', 'fill', "tools/cz_layouts/","layout_config_item.tpl.html", array());
                $i++;
            }
        }
        else
        {
            $retval.= modApiFunc('TmplFiller', 'fill', "tools/cz_layouts/","layout_config_no_item.tpl.html", array());
        }
        return $retval;
    }

    /**
     *
     */
    function output()
    {
        global $application;

//        $request = new Request();
//        $request->setView('PHPInfo');
//        $link = $request->getURL();

        $template_contents = array(
                                   "LayoutConfigsList" => $this->outputLayoutConfigs()
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "tools/cz_layouts/","container.tpl.html", array());
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        if (array_key_exists($tag, $this->_Template_Contents))
        {
            $value = $this->_Template_Contents[$tag];
        }
        return $value;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */


    /**#@-*/

}
?>