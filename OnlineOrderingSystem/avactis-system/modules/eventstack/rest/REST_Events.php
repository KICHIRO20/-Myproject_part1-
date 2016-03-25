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

class REST_Events extends RESTResponse
{
    function getEventList()
    {
        $since = modApiFunc('Request','getValueByKey','since');
        $limit = modApiFunc('Request','getValueByKey','limit');

        $this->setResponseOk(modApiFunc('EventStack', 'getEvents', $since, $limit));
    }
}