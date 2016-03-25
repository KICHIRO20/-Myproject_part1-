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
 * Tools Module, ServerInfo View
 *
 * @package Tools
 * @author Alexey Florinsky
 */
class ServerInfo
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * AvactisHomeNews constructor
     */
    function ServerInfo()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');
    }

    /**
     *
     *
     * @
     * @param
     * @return
     */
    function outputModules()
    {
        global $application;

        $retval = "";
        $modules = modApiFunc("Modules_Manager", "getActiveModules");
        $i = 2;
        foreach ($modules as $moduleInfo)
        {
            $template_contents = array(
                                       "ModuleName" => $moduleInfo->name." ".$this->MessageResources->getMessage("ADMIN_AS_INFO_VERSION_LABEL")
                                      ,"ModuleVersion" => $moduleInfo->version
                                      ,"ModuleN" => $i
                                      );
            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);

            $retval.= modApiFunc('TmplFiller', 'fill', "tools/server_info/","module_item.tpl.html", array());
            $i++;
        }
        return $retval;
    }

    /**
     *
     */
    function output()
    {
        global $application;

        ob_start();
        phpinfo(1);
        $content = ob_get_contents();
        ob_end_clean();

        $flag = true;
        while ($flag)
        {
            $pos = _ml_strpos($content, "\n");
            $line = _ml_substr($content, 0, $pos);
            $content = _ml_substr($content, $pos+1);
            if (_ml_strpos($line, "System"))
            {
                $line = _ml_substr($line, 0, _ml_strrpos($line, "<"));
                $line = _ml_substr($line, 0, _ml_strrpos($line, "<"));
                $line = _ml_substr($line, _ml_strrpos($line, ">")+1);
                $flag = false;
            }
        }
        $OS = $line;
        $request = new Request();
        $request->setView('PHPInfo');
        $link = $request->getURL();
        global $db_link;
        $template_contents = array(
                                   "ProductVersion" => PRODUCT_VERSION_NUMBER
                                  ,"ProductVersionType" => PRODUCT_VERSION_TYPE
                                  ,"ProductReleaseDate" => PRODUCT_VERSION_DATE
                                  ,"CoreVersion" => CORE_VERSION
                                  ,"ModulesList" => $this->outputModules()
                                  ,"phpVersion"  => PHP_VERSION
                                  ,"MySQLVersion"=> mysqli_get_server_info($db_link)
                                  ,"ServerOS"    => $OS
                                  ,"WebServerVersion" => $_SERVER["SERVER_SOFTWARE"]
                                  ,"PHPInfoLink" => $link
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "tools/server_info/","container.tpl.html", array());
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


