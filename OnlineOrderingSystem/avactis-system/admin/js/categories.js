function ManageCategories_confirmDelete(tree_obj, node) {
    if (node.getAttribute('ctg_id') == '1') {
        return false;
    }
    return confirm(msg_ctg_del_cfm);
}

var flag_is_tree_changed = false;
function ManageCategories_isTreeChanged()
{
    return flag_is_tree_changed;
}
function ManageCategories_onTreeChanged(tree_obj) 
{
    if (tree_obj.settings.save_alert) {
        tree_obj.settings.save_alert.css('left', 
                tree_obj.settings.save_alert.parent().width() - tree_obj.settings.save_alert.width() - 36);
        tree_obj.settings.save_alert.fadeIn(300);
    }
    enableButton('btn_saveCategoriesTree', function() { SaveCategoriesTree(tree_obj); });
    //enableButton('btn_saveCategoriesTree2', function() { SaveCategoriesTree(tree_obj); });
    flag_is_tree_changed = true;
}
function ManageCategories_onTreeUnchanged(tree_obj, after_fadeout) 
{
    if (tree_obj.settings.save_alert) {
        tree_obj.settings.save_alert.fadeOut(200, after_fadeout);
    }
    disableButton('btn_saveCategoriesTree');
    //disableButton('btn_saveCategoriesTree2');
    flag_is_tree_changed = false;
}

function CopyTreeState(tree_obj, node, node_json)
{
    if (node.is('.closed')) node_json.state = 'closed';
    if (node.is('.open')) node_json.state = 'open';
    var a_obj = node.children('a');
    if (node.attr('id') == undefined || node.attr('id') == '') {
        node.attr('id', node_json.attributes.id);
        node.attr('ctg_id', node_json.attributes.ctg_id);
    }
    if (node_json.children && node_json.children.length) {
        node.children('ul').children('li').each(function() {
        	var $cat = jQuery(this);
        	var cat_name = $cat.children('a').text();
        	for (var i = 0; i < node_json.children.length; i++) {
            	if (cat_name == node_json.children[i].data) {
            		CopyTreeState(tree_obj, $cat, node_json.children[i]);
            	}
        	}
        });
    }
}

function SaveCategoriesTree(tree_obj) 
{
    ManageCategories_onTreeUnchanged(tree_obj, function() { 
        tree_obj.settings.saving_msg.css('left', 
                tree_obj.settings.saving_msg.parent().width() - tree_obj.settings.saving_msg.width() - 36);
        tree_obj.settings.saving_msg.fadeIn(300); 
        });
    
    var root_node = tree_obj.container.children("ul").children("li");
    if (root_node.size() > 0) {
        var tree_str = ConvertTreeToStr(root_node, 0);
    } else {
        var tree_str = '';
    }
    var ctg_id = '';
    if (tree_obj.selected) {
        ctg_id = tree_obj.selected.attr('ctg_id');
    }
    
    jQuery.post(
            'jquery_ajax_handler.php', // backend
            {
                'asc_action': 'save_ctg_tree',
                'tree_id' : tree_obj.settings.uniq_id,
                'ctg_id' : ctg_id,
                'tree_str': tree_str
            },
            function(result, errors) 
            {
                var tree_json = 'tree_json = ' + result.tree_json;
                tree_json = eval(tree_json);
                var root_node = tree_obj.container.children('ul').children('li');
                CopyTreeState(tree_obj, root_node, tree_json);
                tree_obj.settings.data.json = tree_json;
                tree_obj.refresh();
                if (tree_obj.settings.saving_msg) {
                    tree_obj.settings.saving_msg.fadeOut(200);
                }
                ManageCategories_adjustUndoOrigin();
                ManageCategories_turnButtons(tree_obj);
                ResetWholeCache(tree_obj);
            },
            'json'
    );    
}

function ConvertTreeToStr(node, level)
{
    var ctg_id = node.attr('ctg_id') || 'new';
    var node_name = node.children('a').text().replace('\t',' ').replace('\n',' ');
    var str = ctg_id+'\t'+level+'\t'+node_name+'\n';
    if(node.children('ul').size() > 0) {
        node.children('ul').children('li').each(function (idx, elm) {
            str += ConvertTreeToStr(jQuery(elm), level+1);
        });
    }
    return str;
}

function ManageCategories_editCategory(tree_obj, node)
{
    OpenCtgWindow('EditCat', url_ctg_edit, 'Edit', tree_obj, node);
}

var current_category_id = null;
function ManageCategories_onNodeSelected(tree_obj, node) 
{
    if (node != false) {
        var ctg_id = node.getAttribute('ctg_id');
        if (ctg_id != null) {
        	current_category_id = ctg_id;
            enableButton('mng_ctg_add',  function() { OpenCtgWindow('AddCat',  url_ctg_add, 'AddCat', tree_obj, node); } );
            enableButton('mng_ctg_edit', function() { OpenCtgWindow('EditCat', url_ctg_edit, 'Edit', tree_obj, node); } );
            LoadCategoryReview(tree_obj, ctg_id);
        }
        else {
            disableButton('mng_ctg_add');
            disableButton('mng_ctg_edit');
            hideBlock('cat_review_loading');
            hideBlock('cat_review_content');
            hideBlock('cat_review_choose');
            showBlock('cat_review_save', 1000);
        }
        if (ctg_id == '1') {
            disableButton('mng_ctg_del');
        }
        else {
            enableButton('mng_ctg_del',  function() { tree_obj.remove(node); } );
        }
    } else {
        disableButton('mng_ctg_add');
        disableButton('mng_ctg_edit');
        disableButton('mng_ctg_del');
        hideBlock('cat_review_loading');
        hideBlock('cat_review_content');
        hideBlock('cat_review_choose');
        showBlock('cat_review_save', 1000);
    }
}
function ManageCategories_onGoToProducts(node, url)
{
    if (node != false) {
        var ctg_id = node.getAttribute('ctg_id');
        window.location.href = url + ctg_id;
    }    
}

var categories_undo_stack = [];
var categories_undo_pointer = 0;
var categories_undo_origin = 0;
function ManageCategories_incrementUndoOrigin(tree_obj)
{
    if (! ManageCategories_isTreeChanged()) {
        categories_undo_origin ++;
    }
}
function ManageCategories_adjustUndoOrigin()
{
    categories_undo_origin = categories_undo_pointer;
}
function ManageCategories_turnButtons(tree_obj)
{
    if (categories_undo_pointer == categories_undo_origin) {
        if (ManageCategories_isTreeChanged(tree_obj)) {
            ManageCategories_onTreeUnchanged(tree_obj);
        }
    }
    else {
        if (! ManageCategories_isTreeChanged(tree_obj)) {
            ManageCategories_onTreeChanged(tree_obj);
        }
    }
    
    if (categories_undo_pointer > 0) {
        enableButton('btn_undoCategoriesTree', function() { ManageCategories_undo(tree_obj); return false; });
    }
    else {
        disableButton('btn_undoCategoriesTree');
    }
    
    if (categories_undo_stack.length > 0 && 
            categories_undo_pointer < categories_undo_stack.length - 1) {
        enableButton('btn_redoCategoriesTree', function () { ManageCategories_redo(tree_obj); return false; });
    }
    else {
        disableButton('btn_redoCategoriesTree');
    }
}
function ManageCategories_pushRollback(tree_obj, rb, current_state)
{
    if (categories_undo_pointer > categories_undo_stack.length) {
        categories_undo_pointer = categories_undo_stack.length;
    }
    categories_undo_stack[ categories_undo_pointer++ ] = rb;
    categories_undo_stack.length = categories_undo_pointer;
    categories_undo_stack[ categories_undo_pointer ] = current_state;
    ManageCategories_turnButtons(tree_obj);
}
function ManageCategories_overwriteRollback(tree_obj, rb, current_state)
{
    if (categories_undo_pointer > categories_undo_stack.length) {
        categories_undo_pointer = categories_undo_stack.length;
    }
    if (categories_undo_pointer > 0) {
        categories_undo_stack[ categories_undo_pointer ] = current_state;
    }
}
function ManageCategories_undo(tree_obj)
{
    if (categories_undo_stack.length > 0) {
        var rb = categories_undo_stack[ --categories_undo_pointer ];
        jQuery.tree_rollback(rb);
        tree_obj.reselect();
        ManageCategories_onNodeSelected(tree_obj, tree_obj.selected.get(0));
    }
    ManageCategories_turnButtons(tree_obj);
}

function ManageCategories_redo(tree_obj)
{
    if (categories_undo_stack.length > 0 && 
            categories_undo_pointer < categories_undo_stack.length - 1) {
        var cs = categories_undo_stack[ ++categories_undo_pointer ];
        jQuery.tree_rollback(cs);
        tree_obj.reselect();
        ManageCategories_onNodeSelected(tree_obj, tree_obj.selected.get(0));
    }
    ManageCategories_turnButtons(tree_obj);
}

var categories_reviews_cache = [];
function ResetCategoryReview(tree_obj, ctg_id)
{
    categories_reviews_cache[ctg_id] = undefined;
}
function ResetWholeCache(tree_obj)
{
	categories_reviews_cache = [];
    ReloadCategoryReview(tree_obj, tree_obj.selected.attr('ctg_id'))
}
function ReloadCategoryReview(tree_obj, ctg_id)
{
    ResetCategoryReview(tree_obj, ctg_id);
    LoadCategoryReview(tree_obj, ctg_id);
}
function LoadCategoryReview(tree_obj, ctg_id)
{
    if (categories_reviews_cache[ctg_id]) {
        putHtmlToElement('cat_review_content', categories_reviews_cache[ctg_id]);
        hideBlock('cat_review_choose');
        hideBlock('cat_review_save');
        hideBlock('cat_review_loading');
        showBlock('cat_review_content', 1000);
    }
    else {
        var current_ctg_id = tree_obj.selected.attr('ctg_id');
        if (current_ctg_id == ctg_id) {
        	hideBlock('cat_review_choose');
        	hideBlock('cat_review_save');
        	hideBlock('cat_review_content');
        	showBlock('cat_review_loading', 1000);
        }
        jQuery.post(
                'jquery_ajax_handler.php', // backend
                {
                    'asc_action': 'get_ctg_review',
                    'category_id': ctg_id
                },
                function(result, errors) 
                {
                    if (result == null) {
                        var loading = document.getElementById('cat_review_loading');
                        if (loading.style.display != 'none') {
                            hideBlock('cat_review_save');
                            hideBlock('cat_review_loading');
                            hideBlock('cat_review_content');
                            showBlock('cat_review_choose', 1000);
                        }                        
                    }
                    else {
                        categories_reviews_cache[ctg_id] = result['review'];
                        if (current_ctg_id == ctg_id) {
                            putHtmlToElement('cat_review_content', result['review']);
                            var loading = document.getElementById('cat_review_loading');
                            if (loading.style.display != 'none') {
                                hideBlock('cat_review_save');
                                hideBlock('cat_review_loading');
                                showBlock('cat_review_content', 1000);
                            }
                        }
                    }
                },
                'json'
        );
    }
}

function OpenCtgWindow(windowName, windowURL, action, tree_obj, node)
{
    var URL = windowURL + node.getAttribute('ctg_id') + '&tree_id=' + tree_obj.container[0].id;
    //var newWin = go(URL, windowName);
    var newWin = openURLinNewWindow(URL, windowName);
    //newWin.focus();
}

function ManageCategories_updateCategoryName(tree_id, ctg_id, new_name)
{
    jQuery('#'+tree_id).find('li').filter(
            function(i){ return jQuery(this).attr('ctg_id') == ctg_id; }
            ).children('a').html(new_name);
    ManageCategories_reloadCategoryReview(tree_id, ctg_id);
}

function ManageCategories_reloadCategoryReview(tree_id, ctg_id)
{
    ReloadCategoryReview(jQuery.tree_reference(tree_id), ctg_id);
}

function ManageCategories_addSubCategory(tree_id, parent_id, ctg_id, new_name)
{
    var tree_obj = jQuery.tree_reference(tree_id);
    var parent = jQuery('#'+tree_id).find('li').filter(
            function(i){ return jQuery(this).attr('ctg_id') == parent_id; }
            ).get(0);
    if (tree_obj && parent) {
        var child_obj = { 
                attributes: { 
                    id: tree_id+'_cat_'+ctg_id, 
                    ctg_id: ctg_id, 
                    rel: 'folder' 
                },
                data: new_name
            };
        var new_node = tree_obj.create(child_obj, parent);
        tree_obj.select_branch(new_node);
    }
}