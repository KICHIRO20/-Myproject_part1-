function $tag(){$arg_list = func_get_args();return __block_tag_output('$tag',$arg_list);}
if (function_exists('get$tag')==false)
{
    function get$tag()
    {
        global $__tag_output_disabled;
        $arg_list = func_get_args();
        ob_start();
         __block_tag_output('$tag',$arg_list);
        $ret = ob_get_clean();
        return $ret;
    }
}