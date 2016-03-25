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
class bar_chart
{
    function bar_chart()
    {
        $this->xlabels = array();
        $this->series_data = array();
    }

    function set_xlabels($xlabels)
    {
    	$this->xlabels = $xlabels;
    }
    function get_xlabels()
    {
    	return $this->xlabels;
    }

    function set_series_data($series_data)
    {
    	$this->series_data = $series_data;
    }
    function get_series_data()
    {
    	return $this->series_data;
    }

};
?>