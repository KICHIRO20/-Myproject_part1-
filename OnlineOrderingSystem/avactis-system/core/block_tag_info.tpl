function $tag(){$arg_list = func_get_args();return __info_tag_output('$tag',$arg_list);}
if (function_exists('get$tag')==false)
{
    function get$tag()
    {
        global $__tag_output_disabled;
        $arg_list = func_get_args();
        ob_start();
        __info_tag_output('$tag',$arg_list);
        $ret = ob_get_clean();
        return $ret;
     }
}

if (function_exists('getVal$tag')==false)
{
    function getVal$tag()
    {
        global $__tag_output_disabled;
        global $__localization_disable_formatting__;
        $__localization_disable_formatting__ = true;
        $arg_list = func_get_args();
        ob_start();
        __info_tag_output('$tag',$arg_list);
        $ret = ob_get_clean();
        $__localization_disable_formatting__ = false;
        return $ret;
     }
}