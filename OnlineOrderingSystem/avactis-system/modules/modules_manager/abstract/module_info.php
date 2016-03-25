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
 * Ancillary class to use module info.
 *
 * @package Modules_Manager
 * @access  public
 * @author Alexey Kolesnikov
 */
class ModuleInfo
{
    /**#@+
     * @access public
     */

    /**
     * Loads module info from the associative array.
     */
    function loadFromArray($moduleInfo)
    {
        global $zone;
    	$this->name         = $moduleInfo['name'];
        $this->shortName    = isset($moduleInfo['shortName']) ? $moduleInfo['shortName'] : null;
    	$this->groups       = $moduleInfo['groups'];
    	$this->directory    = $moduleInfo['directory'] . '/';
        $this->description  = $moduleInfo['description'];
        $this->version      = $moduleInfo['version'];
        $this->author       = $moduleInfo['author'];
        $this->contact      = $moduleInfo['contact'];
        $this->systemModule = $moduleInfo['systemModule'];
        $this->mainFile     = $this->directory . $moduleInfo['mainFile'];
        $this->constantsFile= isset($moduleInfo['constantsFile']) ? $this->directory.$moduleInfo['constantsFile'] : null;
        $this->extraAPIFiles= null;
        $this->resFile      = isset($moduleInfo['resFile']) ? $moduleInfo['resFile'] : null;
        $this->SectionByView = isset($moduleInfo['SectionByView']) ? $moduleInfo['SectionByView'] : array();
        $this->ViewBySection = isset($moduleInfo['ViewBySection']) ? $moduleInfo['ViewBySection'] : array();
        $this->storefrontLayout = isset($moduleInfo['storefrontLayout']) ? $moduleInfo['storefrontLayout'] : '';
        $this->ext_def_hooks= isset($moduleInfo['ext_def_hooks']) ? $this->directory."/ext_hooks/".$moduleInfo['ext_def_hooks'] : null;

        // get extraAPIFiles
        if (isset($moduleInfo['extraAPIFiles']))
        {
            $this->extraAPIFiles = array();
            foreach ($moduleInfo['extraAPIFiles'] as $class=>$file)
            {
                $this->extraAPIFiles[$class] = $this->directory . $file;
            }
        }

        // get actions and store file names if any
        if (array_key_exists('actions', $moduleInfo))
        {
            foreach (array_keys($moduleInfo['actions']) as $action)
            {
                //            $action           ,           ,
                // $moduleInfo['actions'][$action]
                if ($action == 'CustomerZone' || $action == 'AdminZone')
                {
                    //
                    if ($action == $zone)
                    {
                        foreach ($moduleInfo['actions'][$action] as $zone_actionName => $zone_actionFile)
                        {
                            $this->actionFiles[$zone_actionName] = $this->directory . 'actions/' . $zone_actionFile;
                        }
                    }
                }
                else
                {
                    //
                    $this->actionFiles[$action] = $this->directory . 'actions/' . $moduleInfo['actions'][$action];
                }
            }
        }

        // load hooks list and save file names
        if (array_key_exists('hooks', $moduleInfo))
        {
            foreach ($moduleInfo['hooks'] as $hook => $hookInfo)
            {
                $hookActions = explode(",", $hookInfo['onAction']);
                if ($hookActions)
                {
            	   $this->hookFiles[$hook] = $this->directory . 'hooks/' . $hookInfo['Hook_File'];
        	       $this->hookMap[$hook] = $hookActions;
                }
            }
        }

        // get views and store file names if any
        if (array_key_exists('views', $moduleInfo))
        {
            if(array_key_exists('AdminZone',$moduleInfo['views']))
            {
                foreach (array_keys($moduleInfo['views']['AdminZone']) as $view)
                {
                	if (strpos($this->directory,"avactis-extensions")){
                		$this->azViewFiles[$view] = $this->directory . 'views/admin/' . $moduleInfo['views']['AdminZone'][$view];
                	} else {
                    $this->azViewFiles[$view] = $this->directory . 'views/' . $moduleInfo['views']['AdminZone'][$view];
                }
            }
            }

            if(array_key_exists('CustomerZone',$moduleInfo['views']))
            {
                foreach (array_keys($moduleInfo['views']['CustomerZone']) as $view)
                {
                	if (strpos($this->directory,"avactis-extensions")){
	                    	$this->czViewFiles[$view] = $this->directory . 'views/frontend/' . $moduleInfo['views']['CustomerZone'][$view];
                	} else {
                    $this->czViewFiles[$view] = $this->directory . 'views/' . $moduleInfo['views']['CustomerZone'][$view];
                }
            }
            }

            //
            if(array_key_exists('Aliases',$moduleInfo['views']))
            {
                foreach($moduleInfo['views']['Aliases'] as $alias_name => $view_name)
                {
                    $this->czAliases[$alias_name] = $view_name;
                }
            }
        }
    }

    public static function __set_state($data)
    {
        $mi = new ModuleInfo();
        foreach ($data as $property => $value) {
            $mi->$property = $value;
        }
        return $mi;
    }

    var $id;
    var $directory;
    var $name;
    var $shortName;
    var $description;
    var $version;
    var $author;
    var $contact;
    var $systemModule;
    var $mainFile;
    var $installed;
    var $extraAPIFiles;
    var $constantsFile;
    var $resFile;
    var $actionFiles = array();
    var $czViewFiles = array();
    var $azViewFiles = array();
    var $czAliases = array();
    var $hookFiles = array();
    var $hookMap = array();
    var $storefrontLayout = array();
    var $ext_def_hooks;
    /**#@-*/

    /**#@+
     * @access private
     */
    /**#@-*/

}
?>