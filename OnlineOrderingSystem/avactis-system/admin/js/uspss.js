function isValidEmail(email, extended)
{
    var supported = 0;
    if (window.RegExp)
    {
        var tempStr = "a";
        var tempReg = new RegExp(tempStr);
        if (tempReg.test(tempStr))
            supported = 1;
    }
    if (!supported) 
        return (email.indexOf(".") > 1) && (email.indexOf("@") > 1);
    var r1 = new RegExp("(@.*@)|(\\.\\.)|(@\\.)|(^\\.)");
    if (extended)
    {
        var r2 = new RegExp("^(.+<)?(.+\\@(\\[?)[a-zA-Z0-9\\-\\.]+\\.([a-zA-Z]{2,10}|[0-9]{1,3})(\\]?)){1}>?$");
    }
    else
    {
        var r2 = new RegExp("^.+\\@(\\[?)[a-zA-Z0-9\\-\\.]+\\.([a-zA-Z]{2,10}|[0-9]{1,3})(\\]?)$");
    }
    return (!r1.test(email) && r2.test(email));
}

function isValidOldPassword(password, old_password)
{
    if (password!=old_password)
    {
        return false;
    }
    return true;
}

function isEqNewAndVerifyPasswords(new_password, verify_new_password)
{
    if (new_password!=verify_new_password)
    {
        return false;
    }
    return true;
}

function isAllFieldsFilled(fields)
{
	for (i=0; i<fields.length; i++)
	{
		if (fields[i] == '')
		{
			return false;
		}
	}
    return true;
}

function isValidNewPasswordLength(new_password)
{
    var length = new_password.length;
    if (length<8||length>32)
    {
        return false;
    }
    return true
}

function isComplicatedNewPassword(new_password)
{
    var groups = 0;
    var reg = new RegExp("[0-9]");
    if (reg.test(new_password))
    {
        groups++;
    }
    reg = new RegExp("[a-z]");
    if (reg.test(new_password))
    {
        groups++;
    }
    reg = new RegExp("[A-Z]");
    if (reg.test(new_password))
    {
        groups++;
    }
    reg = new RegExp("[\x21-\x2f\x3a-\x40]");
    if (reg.test(new_password))
    {
        groups++;
    }
    if (groups>=2)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function isDifferentFromOldPassword(old_password, new_password)
{
    if (old_password == hex_md5(new_password))
    {
        return false;
    }
    return true;
}

function isDifferentFromEmail(email, new_password)
{
    if (email == new_password)
    {
        return false;
    }
    return true;
}