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


class CString
{

    function CString()
    {
		$this->ent_replace = array(
		'&lt;',
		'&gt;',
		'&amp;',
		'&quot;',
		'&nbsp;',
		'&iexcl;',
		'&cent;',
		'&pound;',
		'&curren;',
		'&yen;',
		'&brvbar;',
		'&sect;',
		'&uml;',
		'&copy;',
		'&ordf;',
		'&laquo;',
		'&not;',
		'&shy;',
		'&reg;',
		'&macr;',
		'&deg;',
		'&plusmn;',
		'&sup2;',
		'&sup3;',
		'&acute;',
		'&micro;',
		'&para;',
		'&middot;',
		'&cedil;',
		'&sup1;',
		'&ordm;',
		'&raquo;',
		'&frac14;',
		'&frac12;',
		'&frac34;',
		'&iquest;',
		'&Agrave;',
		'&Acute;',
		'&Acirc;',
		'&Atilde;',
		'&Auml;',
		'&Aring;',
		'&AElig;',
		'&Ccedil;',
		'&Egrave;',
		'&Eacute;',
		'&Ecirc;',
		'&Euml;',
		'&Igrave;',
		'&Iacute;',
		'&Icirc;',
		'&Iuml;',
		'&ETH;',
		'&Ntilde;',
		'&Ograve;',
		'&Oacute;',
		'&Ocirc;',
		'&Otilde;',
		'&Ouml;',
		'&times;',
		'&Oslash;',
		'&Ugrave;',
		'&Uacute;',
		'&Ucirc;',
		'&Uuml;',
		'&Yacute;',
		'&THORN;',
		'&szlig;',
		'&agrave;',
		'&aacute;',
		'&acirc;',
		'&atilde;',
		'&auml;',
		'&aring;',
		'&aelig;',
		'&ccedil;',
		'&egrave;',
		'&eacute;',
		'&ecirc;',
		'&euml;',
		'&igrave;',
		'&iacute;',
		'&icirc;',
		'&iuml;',
		'&eth;',
		'&ntilde;',
		'&ograve;',
		'&oacute;',
		'&ocirc;',
		'&otilde;',
		'&ouml;',
		'&divide;',
		'&oslash;',
		'&ugrave;',
		'&uacute;',
		'&ucirc;',
		'&uuml;',
		'&yacute;',
		'&thorn;',
		'&yuml;',
        '&mdash;',
        '&rsquo;'
		);

		$this->ent_with = array(
		'<',
		'>',
		'&',
		'"',
		_byte_chr(160),
		_byte_chr(161),
		_byte_chr(162),
		_byte_chr(163),
		_byte_chr(164),
		_byte_chr(165),
		_byte_chr(166),
		_byte_chr(167),
		_byte_chr(168),
		_byte_chr(169),
		_byte_chr(170),
		_byte_chr(171),
		_byte_chr(172),
		_byte_chr(173),
		_byte_chr(174),
		_byte_chr(175),
		_byte_chr(176),
		_byte_chr(177),
		_byte_chr(178),
		_byte_chr(179),
		_byte_chr(180),
		_byte_chr(181),
		_byte_chr(182),
		_byte_chr(183),
		_byte_chr(184),
		_byte_chr(185),
		_byte_chr(186),
		_byte_chr(187),
		_byte_chr(188),
		_byte_chr(189),
		_byte_chr(190),
		_byte_chr(191),
		_byte_chr(192),
		_byte_chr(193),
		_byte_chr(194),
		_byte_chr(195),
		_byte_chr(196),
		_byte_chr(197),
		_byte_chr(198),
		_byte_chr(199),
		_byte_chr(200),
		_byte_chr(201),
		_byte_chr(202),
		_byte_chr(203),
		_byte_chr(204),
		_byte_chr(205),
		_byte_chr(206),
		_byte_chr(207),
		_byte_chr(208),
		_byte_chr(209),
		_byte_chr(210),
		_byte_chr(211),
		_byte_chr(212),
		_byte_chr(213),
		_byte_chr(214),
		_byte_chr(215),
		_byte_chr(216),
		_byte_chr(217),
		_byte_chr(218),
		_byte_chr(219),
		_byte_chr(220),
		_byte_chr(221),
		_byte_chr(222),
		_byte_chr(223),
		_byte_chr(224),
		_byte_chr(225),
		_byte_chr(226),
		_byte_chr(227),
		_byte_chr(228),
		_byte_chr(229),
		_byte_chr(230),
		_byte_chr(231),
		_byte_chr(232),
		_byte_chr(233),
		_byte_chr(234),
		_byte_chr(235),
		_byte_chr(236),
		_byte_chr(237),
		_byte_chr(238),
		_byte_chr(239),
		_byte_chr(240),
		_byte_chr(241),
		_byte_chr(242),
		_byte_chr(243),
		_byte_chr(244),
		_byte_chr(245),
		_byte_chr(246),
		_byte_chr(247),
		_byte_chr(248),
		_byte_chr(249),
		_byte_chr(250),
		_byte_chr(251),
		_byte_chr(252),
		_byte_chr(253),
		_byte_chr(254),
		_byte_chr(255),
        '-',
        '`'
		);

		for($c=1;$c<256;$c++)
        {
		    array_push($this->ent_replace,'&#' . $c . ';');
		    array_push($this->ent_with,_byte_chr($c));
		}
    }

    /**
     * Is used to merge whitespace.
     * It merges many sequential whitespace characters into a single space.
     *
     * @param $string - The string you want to modify.
     * @return A string with condensed whitespace.
     */
    function mergeWhiteSpace($string)
    {
        // remove the whitespace from this text
        $string = str_replace("\n", " ", $string);
        $string = str_replace("\t", " ", $string);
        $string = str_replace("\r", " ", $string);
        $string = preg_replace("/\s+/", " ", $string);
        return $string;
    }

    /**
     * Removes HTML tags and HTML-characters from the specified string.
     *
     * @param string $str HTML text
     * @return string Plain text string
     */
    function stripHTML($str)
    {

        /*
         Removes carriage, returns and linefeeds only after HTML tags.
		 Otherwise manually stripping plaintext would corrupt it.
         */
		$str = preg_replace("'>(. ?|)(\n|\r)'si", ">", $str);
		$str = preg_replace("'>(\n|\r)'si", ">", $str);

        // Get rid of comment tags
        $str = preg_replace("'<!--(.*?)-->'si", "", $str);

        // Handle processing instructions separately from comments
        // fixing overly greedy tag to separate handling of comments and
        // processing instructions (ie <!DOCTYPE ... >
        $str = preg_replace("'<![^>]*?>'si", "", $str);

        // Get rid of everything inside script and head
        $str = preg_replace("'<script[^>]*?>.*?</script>'si", "", $str);
        $str = preg_replace("'<head[^>]*?>.*?</head>'si", "", $str);

        // Clean up any HTML tags that are left.
        $str = preg_replace("'<(.*?)>'si", "", $str);

        $str = str_replace($this->ent_replace,$this->ent_with,$str);

		return $str;
	}

    var $ent_replace;
    var $ent_with;
}

?>