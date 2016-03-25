String.prototype.encode_tags = function()
{
    return this.replace(/\<\?php/g,'(?php')
               .replace(/\?\>/g,'?)')
               .replace(/</g,'(lt;')
               .replace(/>/g,')gt;');
}

String.prototype.decode_tags = function()
{
    return this.replace(/\(\?php/g,'<?php')
               .replace(/\?\)/g,'?>')
               .replace(/\(lt;/g,'<')
               .replace(/\)gt;/g,'>');
}
