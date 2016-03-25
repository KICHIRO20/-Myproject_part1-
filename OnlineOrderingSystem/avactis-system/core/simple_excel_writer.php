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

class CSimpleBinaryExcel
{
    function CSimpleBinaryExcel()
    {
        $this->__content = '';
    }

    function begin()
    {
        $this->__content = '';
        $this->__content .= pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
    }

    function end()
    {
        $this->__content .= pack("ss", 0x0A, 0x00);
        return;
    }

    function writeNumber($Row, $Col, $Value)
    {
        $this->__content .= pack("sssss", 0x203, 14, $Row, $Col, 0x0);
        $this->__content .= pack("d", $Value);
    }

    function writeLabel($Row, $Col, $Value )
    {
        $L = _byte_strlen($Value);
        $this->__content .= pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
        $this->__content .= $Value;
    }

    function getContent()
    {
        return $this->__content;
    }

    var $__content;
}

?>