/*
$Id$
vim: set ts=2 sw=2 sts=2 et:
*/

/*
Common wrappers for CKEditor
*/
function enableHtmlEditor(id ,name ,cid){
	if (!jQuery("#"+id) || !jQuery("#"+id+'Box') || !jQuery("#"+id+'Adv') || !jQuery("#"+id+'Dis') || (typeof CKEDITOR === "undefined"))
		return false;
	 CKEDITOR.replace(id);
	  
	  jQuery("#html_bannerEditEnb"+cid+", #html_bannerEditDisB"+cid+"").hide();
	  jQuery("#html_bannerEditEnbB"+cid+", #html_bannerEditDis"+cid+"").show();

		setCookie(id+'EditorEnabled', 'Y');

		if (localBrowser == 'Opera' && localVersion == '9.00') {
			window.scrollTo(sx, sy);
		}
	
}
function disableHtmlEditor(id ,name ,cid){
	if (!jQuery("#"+id) || !jQuery("#"+id+'Box') || !jQuery("#"+id+'Adv') || !jQuery("#"+id+'Dis') || (typeof CKEDITOR === "undefined"))
		return false;

  get_html_editor(id).destroy();

  jQuery("#html_bannerEditEnbB"+cid+", #html_bannerEditDis"+cid+"").hide();
  jQuery("#html_bannerEditEnb"+cid+", #html_bannerEditDisB"+cid+"").show();

	if (localBFamily == 'MSIE')
		setTimeout("document.getElementById('"+id+"').value = document.getElementById('"+id+"').value;", 100);

	deleteCookie(id+'EditorEnabled');
	
}
function enableEditor(id, name) {
	if (!jQuery("#"+id) || !jQuery("#"+id+'Box') || !jQuery("#"+id+'Adv') || !jQuery("#"+id+'Dis') || (typeof CKEDITOR === "undefined"))
		return false;
	
  CKEDITOR.replace(id);
  
  jQuery("#"+id+"Enb, #"+id+"DisB").hide();
  jQuery("#"+id+"EnbB, #"+id+"Dis").show();

	setCookie(id+'EditorEnabled', 'Y');

	if (localBrowser == 'Opera' && localVersion == '9.00') {
		window.scrollTo(sx, sy);
	}
}

function disableEditor(id, name) {
	if (!jQuery("#"+id) || !jQuery("#"+id+'Box') || !jQuery("#"+id+'Adv') || !jQuery("#"+id+'Dis') || (typeof CKEDITOR === "undefined"))
		return false;

  get_html_editor(id).destroy();

  jQuery("#"+id+"EnbB, #"+id+"Dis").hide();
  jQuery("#"+id+"Enb, #"+id+"DisB").show();

	if (localBFamily == 'MSIE')
		setTimeout("document.getElementById('"+id+"').value = document.getElementById('"+id+"').value;", 100);

	deleteCookie(id+'EditorEnabled');
}

function editor_get_xhtml_body(name) {
  return get_html_editor(name).getData();
}

function editor_puthtml(name, value) {
  get_html_editor(name).setData(value);
}

function get_html_editor(name) {
  obj = CKEDITOR.instances[name];
  return obj;
}

