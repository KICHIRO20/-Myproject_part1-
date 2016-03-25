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
 * News Module, AvactisHomeNews View.
 *
 * @package News
 * @author Alexey Florinsky, Timur Nasibullin
 */
class AvactisHomeNews
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * AvactisHomeNews constructor.
     */
    function AvactisHomeNews()
    {
        global $application;

        $this->news_updates_count = 0;
        $this->news_updates_count = modApiFunc('News','updateNews');
        $this->newsList = modApiFunc('News','getNewsList');
        $this->templateFiller = &$application->getInstance('TmplFiller');
    }

    /**
     *
     */
    function output()
    {
        global $application;

        $application->registerAttributes(array(
            'NewsTitle',
            'NewsLink',
            'NewsContent',
            'NewsCategory',
            'NewsDate',
            'NewsLastBuidDate'
        ));
        $retval = '';
        if($this->newsList == NULL)
        {
            $retval = $this->templateFiller->fill('news/avactis_home_news/','list_empty.tpl.html',array());
        }
        else
        {
            $retval = $this->templateFiller->fill('news/avactis_home_news/','list.tpl.html',array());
        };

        return $retval;
    }

   /**
    * Outputs the news list in HTML code.
    */
    function outputNewsList()
    {
        $retval = '';
        foreach($this->newsList as $item)
        {
            $this->_News_Contents = $item;
            $retval .= $this->templateFiller->fill('news/avactis_home_news/','list_item.tpl.html',array());
        };
        return $retval;
    }

	function getTag($tag)
	{
		$value = '';
		switch ($tag)
		{
        case 'NewsLastBuidDate':
            $lastBuildDate = modApiFunc('News','getValue',NEWS_LAST_BUILD_DATE);
            $time = modApiFunc('Localization','timestamp_time_format',$lastBuildDate);
            $date = modApiFunc('Localization','date_format',$lastBuildDate);
            $value = $date.'&nbsp;'.$time;
            break;

        case 'Items':
            $value = $this->outputNewsList();
            break;

        case 'NewsDate':
            $value = modApiFunc('Localization','date_format',intval($this->_News_Contents['NewsDate']));
            break;

        default:
            if(array_key_exists($tag,$this->_News_Contents))
            {
                $value = $this->_News_Contents[$tag];
            };
            break;
		};
		return $value;
	}
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */
    /**
    * @var TmplFiller
    */
    var $templateFiller;
    /**
    * @var array
    */
    var $newsList;
    /**
    * @var array
    */
    var $_News_Contents;

    /**#@-*/
}
?>