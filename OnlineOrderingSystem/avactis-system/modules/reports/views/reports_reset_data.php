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

class ReportsResetData
{
    function ReportsResetData()
    {
        $this->_TmplFiller = new TmplFiller(dirname(dirname(__FILE__)).'/templates_az/report-reset-data/');
    }

    function output()
    {
        $params = array(
                'REPORT_DATA_RESET' => getMsg('RPTS','REPORT_DATA_RESET'),
                'CONFIRM_WARNING' => getMsg('RPTS','CONFIRM_WARNING'),
        );
        return $this->_TmplFiller->fill("", "container.tpl.html", $params);
    }
}

?>