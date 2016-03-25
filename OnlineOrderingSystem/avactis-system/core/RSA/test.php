<?php
/* require("./Math/BCMath.php");
$Crypt_RSA_Math_BCMath_o = new Crypt_RSA_Math_BCMath();
$Crypt_RSA_Math_BCMath_o;
pack(); */
function _use($file)
{
    include_once( $file );
}

require_once("../RSA.php");
require_once("../PEAR.php");
require_once("./KeyPair.php");
require_once("./Key.php");
require_once("../BigInteger.php");
define('MATH_BIGINTEGER_MODE', MATH_BIGINTEGER_MODE_INTERNAL);
set_time_limit(3600);

    function convert_rsa_private_key_from_asc_into_cryptrsa_format($rsa_private_key_asc_format)
    {
        $key = $rsa_private_key_asc_format;
        $key_pattern = "/n\:([0-9a-f]*)\;d\:([0-9a-f]*)\;/";
        $matches = array();
        if(preg_match($key_pattern, $key, $matches))
        {
            $key_obj = new Crypt_RSA_Key(hex2bin($matches[1]), hex2bin($matches[2]), "private");
            return $key_obj;
        }
        else
        {
            //report error
            return false;
        }
    }

//$rsa_obj = new Crypt_RSA();
//print_r($rsa_obj); die();
$key_pair = new Crypt_RSA_KeyPair(1024);
print_r($key_pair);
die();

$rsa_private_key = convert_rsa_private_key_from_asc_into_cryptrsa_format("n:47abc90d18592337a98519407a28f5ca2025fa2cb2e9f32287befbc79361ae5625366234b7547a4d66826004ce43fb8bb0114834688f7a6ca1356d8d485b458d;d:19bd813a28c7abc5390f049339b7d6d9f1a413606d813dbb49e61d00d400c3475cd570974714b0eed2ab46770ad1fa59cae34d8046cb0c6f9ae0892c18e68c2c;");
$rsa_obj = new Crypt_RSA;
$blowfish_key = $rsa_obj->decrypt("TpeU+MmI8t/+wj4gOTAHmXP22UxYUciIGb6zkFUSc3V6buo/TU3RPonlQTccs5/UGn4f/dgnCbHwVxv8mGGHTA==", $rsa_private_key);
echo "$blowfish_key"; die();
//BEGIN 2007.02.11 Testing RSA decryption

/*$key_pair = new Crypt_RSA_KeyPair(512);
//512 bits key pair

echo date(DATE_RFC822) . "<br>\n";
echo "SAMPLE Key Pair:<br>\n";
echo "serialized key_pair object: '".  base64_encode(serialize($key_pair)); echo "'<br>\n";
echo "_public_key->_modulus: '" .      bin2hex($key_pair->_public_key->_modulus); echo "'<br>\n";
echo "_public_key->_exp: '".           bin2hex($key_pair->_public_key->_exp); echo "'<br>\n";
echo "asc_pub_key:  '". serialize("n:".bin2hex($key_pair->_public_key->_modulus).";e:" .bin2hex($key_pair->_public_key->_exp).";");  echo "'<br>\n";
echo "asc_priv_key: '". serialize("n:".bin2hex($key_pair->_private_key->_modulus).";d:".bin2hex($key_pair->_private_key->_exp).";"); echo "'<br>\n";
echo "_private_key->_modulus: '".      bin2hex($key_pair->_private_key->_modulus); echo "'<br>\n";
echo "_private_key->_exp: '".          bin2hex($key_pair->_private_key->_exp); echo "'<br>\n";
die();*/


     $rsa_obj = new Crypt_RSA;
     $key_pair    = unserialize(base64_decode('TzoxNzoiQ3J5cHRfUlNBX0tleVBhaXIiOjc6e3M6OToiX21hdGhfb2JqIjtPOjIxOiJDcnlwdF9SU0FfTWF0aF9CQ01hdGgiOjE6e3M6NjoiZXJyc3RyIjtzOjA6IiI7fXM6ODoiX2tleV9sZW4iO2k6NTEyO3M6MTE6Il9wdWJsaWNfa2V5IjtPOjEzOiJDcnlwdF9SU0FfS2V5Ijo3OntzOjk6Il9tYXRoX29iaiI7UjoyO3M6ODoiX21vZHVsdXMiO3M6NjQ6IkeryQ0YWSM3qYUZQHoo9cogJfossunzIoe++8eTYa5WJTZiNLdUek1mgmAEzkP7i7ARSDRoj3psoTVtjUhbRY0iO3M6NDoiX2V4cCI7czozMjoiCao44Ied3wqy1ejahTBa9v8r14QrNGENuOFlE8BmDLgiO3M6OToiX2tleV90eXBlIjtzOjY6InB1YmxpYyI7czo4OiJfa2V5X2xlbiI7aTo1MTI7czo3OiJfZXJyb3JzIjthOjA6e31zOjE0OiJfZXJyb3JfaGFuZGxlciI7czowOiIiO31zOjEyOiJfcHJpdmF0ZV9rZXkiO086MTM6IkNyeXB0X1JTQV9LZXkiOjc6e3M6OToiX21hdGhfb2JqIjtSOjI7czo4OiJfbW9kdWx1cyI7czo2NDoiR6vJDRhZIzephRlAeij1yiAl+iyy6fMih777x5NhrlYlNmI0t1R6TWaCYATOQ/uLsBFINGiPemyhNW2NSFtFjSI7czo0OiJfZXhwIjtzOjY0OiIZvYE6KMerxTkPBJM5t9bZ8aQTYG2BPbtJ5h0A1ADDR1zVcJdHFLDu0qtGdwrR+lnK402ARssMb5rgiSwY5owsIjtzOjk6Il9rZXlfdHlwZSI7czo3OiJwcml2YXRlIjtzOjg6Il9rZXlfbGVuIjtpOjUxMjtzOjc6Il9lcnJvcnMiO2E6MDp7fXM6MTQ6Il9lcnJvcl9oYW5kbGVyIjtzOjA6IiI7fXM6MTc6Il9yYW5kb21fZ2VuZXJhdG9yIjtzOjk6IgBsYW1iZGFfMSI7czo3OiJfZXJyb3JzIjthOjA6e31zOjE0OiJfZXJyb3JfaGFuZGxlciI7czowOiIiO30='));
echo "new Crypt_RSA"; echo "<br>\n"; flush();

$plain_data = '54b6c54c6a0ff1100055c61fb19760e4';

//$asc_pub_key = unserialize(base64_decode('TzoxMzoiQ3J5cHRfUlNBX0tleSI6Nzp7czo5OiJfbWF0aF9vYmoiO086MzA6IkNyeXB0X1JTQV9NYXRoX01hdGhfQmlnSW50ZWdlciI6MTp7czo2OiJlcnJzdHIiO3M6MDoiIjt9czo4OiJfbW9kdWx1cyI7czo2NDoizbT24d/HiB0v8yw5h/Fj4VVeHI+xeM5iNzRR2/6M1gQ9ey0eamYC5S+b1Nz+erXoLah3rp+Fs0kQF72c2gAG1iI7czo0OiJfZXhwIjtzOjQ6Igtc7XwiO3M6OToiX2tleV90eXBlIjtzOjY6InB1YmxpYyI7czo4OiJfa2V5X2xlbiI7aTo1MTI7czo3OiJfZXJyb3JzIjthOjA6e31zOjE0OiJfZXJyb3JfaGFuZGxlciI7czowOiIiO30='));

     $enc_data = $rsa_obj->encrypt($plain_data, $key_pair->getPublicKey());
//$enc_data = "ecJCh0i5P+iVD42NYSHy8BlnNxoPtMxqS27JK69s+TbvWE182AXBxpXG1pQZI75FFcTHOuRrCqbjOGrElmMRgQ==";
echo "rsa_obj->encrypt"; echo "<br>\n"; flush();
echo $enc_data . "<br>\n";
//echo "enc_data"; echo "<br>\n"; flush();

     // decryption (usually using private key)
///     $plain_data1 = $rsa_obj->decrypt($enc_data, $key_pair->getPrivateKey());
///echo "rsa_obj->decrypt"; echo "<br>\n"; flush();
///echo $plain_data1;
die();
//END 2007.02.11




//$str = "   ";

$str = "        ";
$str[0] = chr(7);
$str[1] = chr(125);
$str[2] = chr(114);
$str[3] = chr(103);
$str[4] = chr(156);
$str[5] = chr(145);
$str[6] = chr(70);
$str[7] = chr(59);

$str_invert = "        ";
$str_invert[0] = chr(59);
$str_invert[1] = chr(70);
$str_invert[2] = chr(145);
$str_invert[3] = chr(156);
$str_invert[4] = chr(103);
$str_invert[5] = chr(114);
$str_invert[6] = chr(125);
$str_invert[7] = chr(7);

/*$str[0] = chr(145);
$str[1] = chr(70);
$str[2] = chr(59);*/
//echo $str; die();
$dec =   7.0 +
       125.0 * 256 +
       114.0 * 256 * 256 +
       103.0 * 256 * 256 * 256 +
       156.0 * 256 * 256 * 256 * 256 +
       145.0 * 256 * 256 * 256 * 256 * 256 +
        70.0 * 256 * 256 * 256 * 256 * 256 * 256 +
        59.0 * 256 * 256 * 256 * 256 * 256 * 256 * 256;
$bi = new Math_BigInteger($str_invert, 256);
//echo $bi->toString(); die();
//echo $dec; die();

$bi1 = new Math_BigInteger(2);
$bi2 = new Math_BigInteger(1);
//$res = $bi1->divide($bi2);
//print_r($res);
//die();


$num = "4271261397543976199";
$bi = new Math_BigInteger($num, 10);
$res = $bi->toBytes();
//echo $res;die();

    function check_error(&$obj)
    {
        if ($obj->isError())
        {
            $error = $obj->getLastError();
            switch ($error->getCode())
            {
                case CRYPT_RSA_ERROR_WRONG_TAIL :
                    // nothing to do
                    break;
                default:
                    // echo error message and exit
                    die('error: '. $error->getMessage());
            }
        }
    }

$open_pgp_key = "YTozOntpOjA7czoxNjoisVaMWPXkdG2K4FmUHjqQxiI7aToxO3M6ODoiB31yZ5yRRjsiO2k6MjtzOjY6InB1YmxpYyI7fQ==";

$public_key = Crypt_RSA_Key::fromString($open_pgp_key);
check_error($public_key);
$rsa_obj = new Crypt_RSA;
check_error($rsa_obj);
$ServerInfo = "ABCD2007.01.22";
//print_r($rsa_obj);die("333");
$encrypted_string = $rsa_obj->encrypt($ServerInfo, $public_key);
check_error($rsa_obj);
echo "TEST PASSED SUCCESSFULLY<br>";
echo "encrypted string='". $encrypted_string ."'";


?>