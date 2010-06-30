/**
 *
 */
var MultiSelectAutoComplete = {
    // @todo get this as an xhr call to /api
    look_up_groups: ['members_it','members_product_driver','members_l10n',
                     'members_marketing','members_qa', 'members_security',
                     'members_webdev', 'members_other'],
    //
    lookup_dictionary: {},
    // array of values that autocomplete searches against
    search_list: []

}

$(document).ready(function(){
    $.ajax({
      url: URL_BASE+"api/all_employees",
      success: load_selectors,
      dataType: 'json'
    });
});

function load_selectors(all_employees) {
    var all_employees_search = new Array();
    $.each( all_employees, function(email, emp_label){
        all_employees_search.push(emp_label);
    });
    MultiSelectAutoComplete.lookup_dictionary = all_employees;
    MultiSelectAutoComplete.search_list = all_employees_search;

    $.each(MultiSelectAutoComplete.look_up_groups, function(index, group_id){
        // insert 'add element' icons to sections
        var element = $('<img tite="add recipient" alt="add recipient" src="/htdocs/media/img/action_edit_add.png" />')
            .click(function(){
                create_employee_selector(group_id, group_id+'_group');
            });
        $("label[for="+group_id+"]").before(element);
    });
    
    
}



function create_employee_selector(group, append_to) {
    append_to = '#'+append_to.replace(/^#/, '');


    // create the element
    var auto_box = $('<div><input type="text" name="'+group+'_autocomplete" /></div>');
    $(auto_box).children('input').autocomplete(
        MultiSelectAutoComplete.search_list,{matchContains: true}
    ).result(function(event, data, formatted) {
        /*
         * get the email (considered pk) and set it to the hidden input
         */
        var match = formatted ? formatted.match(/\((.*)\)$/) : null;
        $(this).next('input').val(match ? match[1] : null);
        $(this)
            .hide()
            .before('<span>'+formatted+'</span>')
            .remove();
        return false;
    // on blur, invoke search again incase there were edits
    }).blur(function(){
//        $(this).search();
    });
    $(append_to).append(auto_box);
    var closer = $('<img title="remove" alt="remove" src="/htdocs/media/img/action_edit_remove.png" />')
        .click(function(){
            $(this).parent().remove();
        });
    $(auto_box).append('<input type="hidden" name="'+group+'[]" />').append(closer);
    $(auto_box).find('input').focus()

}

