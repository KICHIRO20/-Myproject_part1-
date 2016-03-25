function changeCountryStateActivation(Element)
{
    var object = document.getElementById('ci_'+Element.value);
    var objectCode = document.getElementById('div_'+Element.value);
    var objectDefault = document.getElementById('default_'+Element.value);
    if (Element.checked)
    {
        object.disabled = false;
        object.style.backgroundColor = "#ffffff";
        object.style.borderColor = "";
        objectCode.style.fontWeight = "bold";
        objectCode.style.color = "";
        objectDefault.disabled = false;
        if (DOM)
        {
            var TD1 = objectCode.parentNode;
            TD1.style.backgroundColor = "#eef2f8";
            var TD2 = object.parentNode;
            TD2.style.backgroundColor = "#eef2f8";
            var TD3 = Element.parentNode;
            TD3.style.backgroundColor = "#eef2f8";
            var TD4 = objectDefault.parentNode;
            TD4.style.backgroundColor = "#eef2f8";
        }
    }
    else
    {
        object.disabled = true;
        object.style.backgroundColor = "#f5f5f5";
        object.style.borderColor = "#f5f5f5";
        objectCode.style.fontWeight = "normal";
        objectCode.style.color = "#808080";
        objectDefault.disabled = true;
        if (DOM)
        {
            var TD1 = objectCode.parentNode;
            TD1.style.backgroundColor = "#f5f5f5";
            var TD2 = object.parentNode;
            TD2.style.backgroundColor = "#f5f5f5";
            var TD3 = Element.parentNode;
            TD3.style.backgroundColor = "#f5f5f5";
            var TD4 = objectDefault.parentNode;
            TD4.style.backgroundColor = "#f5f5f5";
        }
    }
}