function ambCpl_categorySelect_init(){
    jQuery('.ambCpl_admin_pt_select').each(function(){
        var thisSelect = jQuery(this);
        ambCpl_categorySelect(thisSelect);
    });

    jQuery('.ambCpl_admin_pt_select').change(function(){
        var thisSelect = jQuery(this);
        ambCpl_categorySelect(thisSelect);
        var catOptions = thisSelect.closest('.ambCpl_form_wrapper').find('.ambCpl_admin_cat_select').children('option');
        catOptions.removeAttr('selected');
    });
}
function ambCpl_categorySelect(thisSelect){
    var postTypeSelected = thisSelect.find("option:selected").val();
    var catOptions = thisSelect.closest('.ambCpl_form_wrapper').find('.ambCpl_admin_cat_select').children('option');
    catOptions.each(function(){
        var thisPostType = jQuery(this).data('post-type');
        if(thisPostType == postTypeSelected){
            jQuery(this).show();
        }else if(thisPostType != 'all'){
            jQuery(this).hide();
        }
    });    
}
