<?php

class Crypt_RSA_Math_Math_BigInteger
{
    /**
     * error description
     *
     * @var string
     * @access public
     */
    var $errstr = '';

    /**
     * Checks BigInteger class definition.
     *
     * On failure saves error description in $this->errstr
     *
     * @access public
     */
    function Crypt_RSA_Math_Math_BigInteger()
    {
    }

    /**
     * Transforms binary representation of large integer into its native form.
     *
     * Example of transformation:
     *    $str = "\x12\x34\x56\x78\x90";
     *    $num = 0x9078563412;
     *
     * @param string $str
     * @return gmp resource
     * @access public
     */
    function bin2int($str)
    {
        $bi = new Math_BigInteger(_byte_strrev($str), 256);
        return $bi->toString();
        //return bi_unserialize($str);
    }


    /**
     * Transforms large integer into binary representation.
     *
     * Example of transformation:
     *    $num = 0x9078563412;
     *    $str = "\x12\x34\x56\x78\x90";
     *
     * @param gmp resource $num
     * @return string
     * @access public
     */
    function int2bin($num)
    {
        $bi = new Math_BigInteger($num, 10);
        return _byte_strrev($bi->toBytes());
    }

    /**
     * Returns part of number $num, starting at bit
     * position $start with length $length
     *
     * @param gmp resource $num
     * @param int start
     * @param int length
     * @return gmp resource
     * @access public
     */
    function subint($num, $start, $length)
    {
        $start_byte = intval($start / 8);
        $start_bit = $start % 8;
        $byte_length = intval($length / 8);
        $bit_length = $length % 8;
        if ($bit_length) {
            $byte_length++;
        }
        $bi = new Math_BigInteger($num, 10);
        $divider = new Math_BigInteger(1 << $start_bit);
        $res = $bi->divide($divider);
        $bi = $res[0];
        $tmp = _byte_strrev(_byte_substr($bi->toBytes(), $start_byte, $byte_length));
        $tmp = str_pad($tmp, $byte_length, "\0");
        $tmp = _byte_substr_replace($tmp, $tmp{$byte_length - 1} & _byte_chr(0xff >> (8 - $bit_length)), $byte_length - 1, 1);
        return $this->bin2int($tmp);
    }

    /**
     *                             -      ,               .                          .
     */
    function strip_minus($num)
    {
        if(!empty($num))
        {
            if($num[0] == '-')
            {
                return _byte_substr($num, 1);
            }
            else
            {
                return $num;
            }
        }
        else
        {
            return $num;
        }
    }
    /**
     * Compares abs($num1) to abs($num2).
     * Returns:
     *   -1, if abs($num1) < abs($num2)
     *   0, if abs($num1) == abs($num2)
     *   1, if abs($num1) > abs($num2)
     *
     * @param string $num1
     * @param string $num2
     * @return int
     * @access public
     */
    function cmpAbs($num1, $num2)
    {
        //strip minus
        $num1 = $this->strip_minus($num1);
        $num2 = $this->strip_minus($num2);
        $num1 = new Math_BigInteger($num1, 10);
        $num2 = new Math_BigInteger($num2, 10);
        return $num1->compare($num2);
/*        $this->strip_minus($num1);
        $num2 = $this->strip_minus($num2);
        $strlen1 = _byte_strlen($num1);
        $strlen2 = _byte_strlen($num2);
        if($strlen1 < $strlen2)
        {
            return -1;
        }
        else if($strlen1 > $strlen2)
        {
            return 1;
        }
        else
        {
            for($i =0; $i < $strlen1; $i+)
            {
                if($num1[$i] < $num2[$i])
                {
                    return -1;
                }
                else if($num1[$i] > $num2[$i])
                {
                    return 1;
                }
            }
            return 0;
        }
*/
    }

    /**
     * Generates random number wich bit length $bits_cnt,
     * using $random_generator as random generator function.
     * If is_set_higher_bit != false, then higer bit of result
     * will be set to 1.
     *
     * @param int $bits_cnt
     * @param string $rnd_generator
     * @return string
     * @access public
     */
    function getRand($bits_cnt, $random_generator, $is_set_higher_bit = false)
    {
        $bytes_cnt = intval($bits_cnt / 8);
        $bits_cnt %= 8;
        $result = new Math_BigInteger($is_set_higher_bit ? 1 : (call_user_func($random_generator) & 1));
        $num256 = new Math_BigInteger(256);
        for ($i = 0; $i <= $bytes_cnt; $i++) {
            $r = new Math_BigInteger(call_user_func($random_generator) & 0xff);
            $result = $result->multiply($num256);
            $result = $result->add($r);
        }
        $result->divide(new Math_BigInteger(1 << (9 - $bits_cnt)));
        return $result->toBytes();
    }


    /**
     * Returns bit length of number $num
     *
     * @param string $num
     * @return int
     * @access public
     */
    function bitLen($num)
    {
        $tmp = $this->int2bin($num);
        $bit_len = _byte_strlen($tmp) * 8;
        $tmp = _byte_ord($tmp{_byte_strlen($tmp) - 1});
        if (!$tmp) {
            $bit_len -= 8;
        } else {
            while (!($tmp & 0x80)) {
                $bit_len--;
                $tmp <<= 1;
            }
        }
        return $bit_len;
    }

    /**
     * Calculates pow($num, $pow) (mod $mod)
     *
     * @param string $num
     * @param string $pow
     * @param string $mod
     * @return string
     * @access public
     */
    function powmod($num, $pow, $mod)
    {
        $num = new Math_BigInteger($num, 10);
        $pow = new Math_BigInteger($pow, 10);
        $mod = new Math_BigInteger($mod, 10);
        $res = $num->modPow($pow, $mod);

        return $res->toString();

/*        if (function_exists('bcpowmod')) {
            // bcpowmod is only available under PHP5
            return bcpowmod($num, $pow, $mod);
        }

        // emulate bcpowmod
        $result = '1';
        do {
            if (!bccomp(bcmod($pow, '2'), '1')) {
                $result = bcmod(bcmul($result, $num), $mod);
            }
            $num = bcmod(bcpow($num, '2'), $mod);
            $pow = bcdiv($pow, '2');
        } while (bccomp($pow, '0'));
        return $result;*/
    }

    /**
     * Calculates bitwise or of $num1 and $num2,
     * starting from bit $start_pos for number $num1
     *
     * @param string $num1
     * @param string $num2
     * @param int $start_pos
     * @return string
     * @access public
     */
    function bitOr($num1, $num2, $start_pos)
    {
//        $num1 = new Math_BigInteger($num1, 10);
        $num2 = new Math_BigInteger($num2, 10);

        $start_byte = intval($start_pos / 8);
        $start_bit = $start_pos % 8;
        $tmp1 = $this->int2bin($num1);
        $num2 = $num2->multiply(new Math_BigInteger(1 << $start_bit));
        $tmp2 = $this->int2bin($num2->toString());
        if ($start_byte < _byte_strlen($tmp1)) {
            $tmp2 |= _byte_substr($tmp1, $start_byte);
            $tmp1 = _byte_substr($tmp1, 0, $start_byte) . $tmp2;
        } else {
            $tmp1 = str_pad($tmp1, $start_byte, "\0") . $tmp2;
        }
        return $this->bin2int($tmp1);
    }

    /**
     * Returns name of current wrapper
     *
     * @return string name of current wrapper
     * @access public
     */
    function getWrapperName()
    {
        return 'Math_BigInteger';
    }
}

?>