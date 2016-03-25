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
class TaxRatesTranslator
{

    var $BSIZE = 128;
    var $NONE  =  -1;
    var $EOS   ='\0';

    var $NUM   = 256;
    var $DIV   = 257;
    var $MOD   = 258;
    var $ID    = 259;
    var $DONE  = 260;
    var $EOF   = 261;

    /**
     * Lexer.
     */
    var $lexbuf;
    var $lineno;// = 1;
    var $tokenval;// = $this->NONE;
    //The following variables are necessary for the proper processing of
    // negative numbers and variables.
    var $LexCnt;// = 0
    var $LexPrevVal;// = ""
    var $LexUnaryMinus;// = false

    //"Stream functions".
    //Emulate with string member variable.
    var $input;
    var $input_pos;


    /**
     * Parser.
     */
    var $lookahead;

   /**
     * Symbol.
     */
//    var $STRMAX =  999;
//    var $SYMMAX =  100;

    var $lexemes;
    var $lastchar;// = -1;
    var $lastentry;// = 0;
    //

    var $symtable;// = array();
    var $keywords;// = array
//    (
//         "div" => $this->DIV,
//        ,"mod" => $this->MOD,
//        ,0 => 0
//    );

    function TaxRatesTranslator($input)
    {
        $this->input = $input;


        //Lexer
        $this->lineno = 1;
        $this->tokenval = $this->NONE;
        $this->LexanCnt = 0;
        $this->LexPrevVal = 0;
        $this->LexUnaryMinus = false;

        //Symbol
        $this->lastchar = -1;
        $this->lastentry = 0;

        $this->symtable = array();
        $this->keywords = array
        (
            "div" => $this->DIV
           ,"mod" => $this->MOD
           ,"" => 0
        );
        $this->init();
    }

    //"Stream functions".
    //Emulate with string member variable.
    function _getchar()
    {
        if(_ml_strlen($this->input) > $this->input_pos)
        {
            return $this->input{$this->input_pos++};
        }
        else
        {
            //$this->input_pos++;
            return $this->EOF;
        }
    }

    function _ungetc($ch)
    {
        if($this->input_pos > 0)
        {
            $this->input{--$this->input_pos} = $ch;
        }
        else
        {
            die(__CLASS__ . "::" . __FUNCTION__ . "() line " . __LINE__ . ": input_pos == 0.");
        }
    }

    function _isdigit($ch)
    {
        $pattern = "/^[0-9]$/";
        return preg_match($pattern, $ch);
    }

    //: : return the proper error code, if no match existed.
    function _scanf_one_token($format)
    {
        $input_rest = _ml_substr($this->input, $this->input_pos);
        switch($format)
        {
            case "%f":
            {
                //The numbers of type [0-9]+\.[0-9]+ are accepted now.
                //The point(especially point) is required as separator of integer
                //and fractional part, and also not empty integer and fractional
                //parts.
                $pattern = "/^[0-9]+.[0-9]+/";
                $matches = array();
                if(preg_match($pattern, $input_rest, $matches) == 1)
                {
                    //successful match
                    $this->input_pos += _ml_strlen($matches[0]);
                    return floatval($matches[0]);
                }
                else
                {
                    //not matched
                    die(__CLASS__ . "::" . __FUNCTION__ . "() line " . __LINE__ . ": match(\"/^[0-9]+.[0-9]+/\") (float number) failed. Input ='" . $input_rest . "'");
                }


                break;
            }
            default:
            {
                //unexpected format
                die(__CLASS__ . "::" . __FUNCTION__ . "() line " . __LINE__ . ": unexpected format:'" . $format . "'.");
            }
        }

///        $vals = sscanf($input_rest, $format);
///        if(sizeof($vals) == 1)
///        {
///            $this->input_pos += _ml_strlen($vals[0]);
///            return $vals[0];
///        }
///        else
///        {
///            die(__CLASS__ . "::" . __FUNCTION__ . "() line " . __LINE__ . ": match failed.");
///        }
    }

    function _isalpha($ch)
    {
        $pattern = "/^[a-zA-Z]$/";
        return preg_match($pattern, $ch);
    }

    function _isalnum($ch)
    {
        $pattern = "/^[a-zA-Z0-9]$/";
        return preg_match($pattern, $ch);
    }
    //End "Stream functions".

    function is_correct_identifier_name($v)
    {
        $pattern_1 = "/^\{[pt]_[0-9]+\}$/";
        $pattern_2 = "/^[a-zA-Z][a-zA-Z0-9]*$/";
        return preg_match($pattern_1, $v) ||
               preg_match($pattern_2, $v);
    }

    function is_price_identifier($v)
    {
        $pattern = "/^\{[p]_[0-9]+\}$/";
        return preg_match($pattern, $v);
    }

    function price_identifier_name_to_price_id($v)
    {
        $pattern = "/[0-9]+/";
        preg_match($pattern, $v, $matches);
        return $matches[0];
    }

    function is_tax_identifier($v)
    {
        $pattern = "/^\{[t]_[0-9]+\}$/";
        return preg_match($pattern, $v);
    }

    function tax_identifier_name_to_tax_id($v)
    {
        $pattern = "/[0-9]+/";
        preg_match($pattern, $v, $matches);
        return $matches[0];
    }

    /**
     * Error.
     */
    function error($msg)
    {
        echo $msg;
        exit(1);
    }

    /**
     * Symbol.
     */
    function lookup($s)
    {
        return array_key_exists($s, $this->symtable);
    }

    function insert($s, $tok)
    {
        //lexstr => token
        $this->symtable[$s] = $tok;
    }

    /**
     * Init.
     */
    function init()
    {
        foreach($this->keywords as $keyword_lexstr => $keyword_token)
        {
            $this->insert($keyword_lexstr, $keyword_token);
        }
    }

    /**
     * Emitter.
     */
    function emit($t, $tval)
    {
        //The output in target language. It isn't required.
/*        switch ($t)
        {
            case '+': case '-': case '*': case '/':
            {
    	        echo $t;
    	        break;
            }
    	    case $this->DIV:
    	    {
    	        echo "DIV\n";
    	        break;
    	    }
    	    case $this->MOD:
    	    {
    	        echo "MOD\n";
    	        break;
    	    }
    	    case $this->NUM:
    	    {
    	        echo $tval . " ";
    	        break;
    	    }
    	    case $this->ID:
    	    {
    	        echo $tval . " "; //$this->symtable[$tval]['lexptr'] . " ";
    	        break;
    	    }
    	    default:
    	    {
    	        echo "token " . $t . ", tokenval " . $tval . "\n";
    	    }
        }
*/
    }

    /**
     * Lexer.
     */
    function lexan()
    {
        //
        //call counter
        $this->LexCnt++;

        $this->lexbuf = "";
        while(true)
        {
            $retval = "";
            $t = $this->_getchar();
            //The "-" character can be the negative number feature
            //  only at the beginning of expression or after an open bracket.
            //  Either a negative number/identifier
            //  or the expression contains an error.

    	    if ($t == ' ' || $t == '\t')
    	        ;
            else if ($t == '-' && ($this->LexCnt == 1 // the very beginning of expression
                                || $this->LexPrevVal == '('))
            {
                //Variants of type -0.2
                //  i.e. negative numbers with marks before.
                $this->LexPrevVal = $t;
                $this->LexUnaryMinus = true;
                //Stay into the lexical analyser, go on reading.
                //  If there is an id or a positive number,then success ,
                //  an error otherwise.
                return $this->lexan();
            }
            else if ($this->_isdigit($t))
            {
    	        $this->_ungetc($t);
    	        $this->tokenval = $this->_scanf_one_token("%f");

    	        //The number can be negative.
    	        if($this->LexUnaryMinus)
    	        {
    	            $this->tokenval = - $this->tokenval;
    	            $this->LexUnaryMinus = false;
    	        }
    	        $this->LexPrevVal = $this->tokenval;
    	        return $this->NUM;
            }
    	    else if ($this->_isalpha($t) || $t == '{')
    	    {
    	        $p = 0;
    	        $b = 0;
    	        while ($this->_isalnum($t) || $t == '{' || $t == '}' || $t == '_')
    	        {
    	            $this->lexbuf .= $t;
    	            $b++;
    		        $t = $this->_getchar();
    		        if ($b >= $this->BSIZE)
    		        {
    		            error("compiler error");
    		        }
                }
    	        //$this->lexbuf{$b} = $this->EOS;
    	        if ( $t != $this->EOF)
    	        {
    	            $this->_ungetc($t);
    	        }

    	        if(!$this->is_correct_identifier_name($this->lexbuf))
    	        {
    	            $this->error("Incorrect identifier name format: '" . $this->lexbuf . "'");
    	        }
    	        if(!$this->lookup($this->lexbuf))
    	        {
    	            $this->insert($this->lexbuf, $this->ID);
    	        }

    	        $this->tokenval = $this->lexbuf;
//    	        //The id with the unary minus before.
//    	        if($this->LexUnaryMinus)
//    	        {
//    	            $this->tokenval = - $this->tokenval;
    	            $this->LexUnaryMinus = false;
//    	        }
    	        $this->LexPrevVal = $this->tokenval;
    	        return $this->symtable[$this->lexbuf]; //['token'];
    	    }
    	    else if ($this->LexUnaryMinus === true)
    	    {
    	       //Error! The unary minus can stand either before a number,
    	        //  or before the id. These cases are mentioned above.
    	        $this->error("Unexpected unary minus at pos " . $this->input_pos . ". \$t = '" . $t . "'");
    	    }
    	    else if ($t == $this->EOF)
    	    {
    	        $this->LexPrevVal = "";
    	        return $this->DONE;
    	    }
    	    else
    	    {
    	        $this->LexPrevVal = $t;
    	        $this->tokenval = $this->NONE;
    	        return $t;
    	    }
        }
    }

    /**
     * Parser.
     */
    function parse()
    {
        $this->lookahead = $this->lexan();
        //Parse an expression expr.
//        while ($this->lookahead != $this->DONE)
//        {
            $this->expr();

            if($this->input_pos != _ml_strlen($this->input))
            {
                echo "\n<br>this->input_pos=" .$this->input_pos. " _ml_strlen(this->input)= " . _ml_strlen($this->input);
                $this->error("\n<br>The whole string doesn't much 'expression' pattern.");
            }
//            $this->match(';');
//        }
    }

    function expr()
    {
        $this->term();
        while(true)
        {
            switch($this->lookahead)
            {
    	        case '+':
    	        case '-':
    	        {
    	            $t = $this->lookahead;
    		        $this->match($this->lookahead);
    		        $this->term();
    		        $this->emit($t, $this->NONE);
    		        continue;
    	        }
                default:
                {
    	            return;
                }
    	    }
        }
    }

    function term()
    {
        $this->factor();
        while (true)
        {
            switch ($this->lookahead)
            {
    	        case '*':
    	        case '/':
    	        case $this->DIV:
    	        case $this->MOD:
    	        {
    	            $t = $this->lookahead;
    		        $this->match($this->lookahead);
    		        $this->factor();
    		        $this->emit($t, $this->NONE);
    		        continue;
    	        }
    	        default:
    	        {
    	            return;
    	        }
            }
    	}
    }

    function factor()
    {
        switch ($this->lookahead)
        {
            case '(':
            {
    	        $this->match('(');
    	        $this->expr();
    	        $this->match(')');
    	        break;
            }
    	    case $this->NUM:
    	    {
    	        $this->emit($this->NUM, $this->tokenval);
    	        $this->match($this->NUM);
    	        break;
    	    }
    	    case $this->ID:
    	    {
    	        $this->emit($this->ID, $this->tokenval);
    	        $this->match($this->ID);
    	        break;
    	    }
    	    default:
    	    {
    	        $this->error("\n<br>syntax error: lookahead ='" . $this->lookahead . "'\n");
    	    }
        }
    }

    function match($t)
    {
        if ($this->lookahead == $t)
        {
            $this->lookahead = $this->lexan();
        }
        else
        {
    	    $this->error("\n<br>syntax error: t = '" . $t . "'  !=  lookahead = '" . $this->lookahead . "'");
        }
    }

    /**
     * Main.
     */
    function main()
    {
        $this->parse();
        //: if a string is not parsed till the end, i.e. a subexpression
        // is dedicated, then error.
        //exit(0);
    }

    function getPriceList()
    {
        $val = array();
        foreach($this->symtable as $lexstr => $token)
        {
            if($this->is_price_identifier($lexstr))
            {
                $val[] = $this->price_identifier_name_to_price_id($lexstr);
            }
        }
        return $val;
    }

    function getTaxList()
    {
        $val = array();
        foreach($this->symtable as $lexstr => $token)
        {
            if($this->is_tax_identifier($lexstr))
            {
                $val[] = (int)$this->tax_identifier_name_to_tax_id($lexstr);
            }
        }
        return $val;
    }
}

//$obj = new TaxRatesTranslator("12+34*5");

function TaxRatesTranslator_main()
{
    $obj = new TaxRatesTranslator(empty($_POST['input']) ? "abc" : $_POST['input']);
    $obj->parse();
    $TaxArray = $obj->getTaxList();
    echo "\n<br>";
    print_r($TaxArray);
}
?>