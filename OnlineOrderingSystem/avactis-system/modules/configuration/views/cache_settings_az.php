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
 * Configuration Module, Cache Settings.
 *
 * @package Configuration
 * @author Alexey Florinsky
 */
class CacheSettings
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * CacheSettings constructor.
     */
    function CacheSettings()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');
    }

    /**
     *
     */
    function output()
    {
        global $application;

        $application->registerAttributes(array(
            'CacheSize', 'CacheLevelSelectControlName',
            'CacheLevelSelectControlOptions','CacheLevelValue',
            'isSelectedCacheLevel','CacheLevelLabel', 'ResultMessageRow', 'ResultMessage'
        ));

        return modApiFunc('TmplFiller', 'fill', "configuration/cache_settings/","container.tpl.html", array());
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('SYS',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("configuration/cache_settings/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
    }

    function getTag($tag)
	{
		global $application;
		$value = null;
		switch ($tag)
		{
            case 'CacheSize':
                $value = modApiFunc('Configuration', 'getCacheSize', true);
                break;

            case 'CacheLevelSelectControlName':
                $value = SYSCONFIG_CACHE_LEVEL;
                break;

            case 'CacheLevelSelectControlOptions':
                $currentCacheLevel = modApiFunc('Configuration','getValue',SYSCONFIG_CACHE_LEVEL);

                $CacheLevelLabels = array(
                    0 => $this->MessageResources->getMessage("CACHE_SETTINGS_LEVEL_DISABLED"),
                    1 => $this->MessageResources->getMessage("CACHE_SETTINGS_LEVEL_MINIMUM"),
                    2 => $this->MessageResources->getMessage("CACHE_SETTINGS_LEVEL_MEDIUM"),
                    3 => $this->MessageResources->getMessage("CACHE_SETTINGS_LEVEL_MAXIMUM")
                );

                for ($i=0; $i<4 ; $i++ )
                {
                    $this->_selectCacheLevelOptionData = array(
                        'CacheLevelValue' => $i,
                        'isSelectedCacheLevel' => ($i == $currentCacheLevel) ? 'SELECTED' : '',
                        'CacheLevelLabel' => $CacheLevelLabels[$i]
                    );
                    $value .= modApiFunc('TmplFiller', 'fill', "configuration/cache_settings/","select_options.tpl.html", array());
                }
                break;

            case 'CacheLevelValue';
                $value = $this->_selectCacheLevelOptionData['CacheLevelValue'];
                break;

            case 'isSelectedCacheLevel';
                $value = $this->_selectCacheLevelOptionData['isSelectedCacheLevel'];
                break;

            case 'CacheLevelLabel';
                $value = $this->_selectCacheLevelOptionData['CacheLevelLabel'];
                break;
            case 'ResultMessageRow':
            	$value = $this->outputResultMessage();
                break;
            case 'ResultMessage':
            	$value = $this->_Template_Contents['ResultMessage'];
            	break;


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

    var $MessageResources;
    var $_selectCacheLevelOptionData;

    /**#@-*/

}
?>