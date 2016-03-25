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
 * News Module, NewsSettings View.
 *
 * @package News
 * @author Timur Nasibullin
 */
class NewsSettings
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * NewsSettings constructor.
     */
    function NewsSettings()
    {

    }

    /**
     *
     */
    function output()
    {
        global $application;

        $application->registerAttributes(array(
            'GSNewsDisplayCount',
            'InputControlName',
            'InputControlSize',
            'InputControlOptions'
        ));

        return modApiFunc('TmplFiller','fill','news/news_settings/','container.tpl.html',array());
    }

    function getTag($tag)
    {
        $value = '';
        switch ($tag)
        {
            case 'GSNewsDisplayCount':
                $options = modApiFunc('News','getNewsDisplayCountArray');
                $selected = modApiFunc('News','getValue',NEWS_DISPLAY_COUNT);
                $this->_Input_Control = array(
                    'name' => NEWS_DISPLAY_COUNT,
                    'size' => 1,
                    'selected' => $selected,
                    'options' => $options
                );
                $value = modApiFunc('TmplFiller','fill','news/news_settings/','select-box.tpl.html',array());
                break;
            case 'InputControlName':
                $value = $this->_Input_Control['name'];
                break;
            case 'InputControlSize':
                $value = $this->_Input_Control['size'];
                break;
            case 'InputControlOptions':
                foreach ($this->_Input_Control['options'] as $key => $label)
                {
                    $selected = "";
                    if (isset($this->_Input_Control['selected']) && $this->_Input_Control['selected'] == $key)
                    {
                        $selected = " selected";
                    }
                    $value .= "<option value=\"$key\"$selected>$label</option>";
                };
                break;
            default:
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
    * @var array
    */
    var $_Input_Control;

    /**#@-*/

}
?>