/**
 *
 */
var MultiSelectAutoComplete = {
    // @todo get this as an xhr call to /api
    look_up_groups: memebers_autobox_groups,
    //
    groups_posted : memebers_groups_posted,
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
    /**
     * iterate over the lookup groups, add the + icon and check
     * if we need to create and populate any autoboxes (i.e. we
     * are retruning to the form after failed validation
     */
    $.each(MultiSelectAutoComplete.look_up_groups, function(index, group_id){
        // insert 'add element' icons to sections
        var element = $('<img tite="add recipient" alt="add recipient" src="'+URL_BASE+'media/img/action_edit_add.png" />')
            .click(function(){
                add_autobox(group_id);
            });
        $("label[for="+group_id+"]").before(element);
        if(typeof MultiSelectAutoComplete.groups_posted[group_id]!='undefined') {
            if(MultiSelectAutoComplete.groups_posted[group_id].length > 0) {
                $.each(MultiSelectAutoComplete.groups_posted[group_id], function(index, value){
                    add_autobox(group_id, value);
                });
            }
        }

    });
    
    
}

function add_autobox(group_id, value) {
    if (typeof value == 'undefined' ) value = '';

    create_employee_selector(group_id, group_id+'_group', value);

}

function create_employee_selector(group, append_to, value) {
    if (typeof value == 'undefined' ) value = '';
    var display_label = '';
    if(value && typeof MultiSelectAutoComplete.lookup_dictionary[value] != 'undefined') {
        display_label = MultiSelectAutoComplete.lookup_dictionary[value];
    } else {
        value = '';
    }
    append_to = '#'+append_to.replace(/^#/, '');


    // create the element
    /*
     * if we are supplied a value, we are making the label state, else
     * we are rendering the auto-complete input box
     */
    var box_element = '<div><input type="text" name="'+group+'_autocomplete" /></div>';
    if(value) {
        box_element = '<div><span class="selected" >'+display_label+'</span></div>';
    }
    var auto_box = $(box_element);
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
            .before('<span class="selected">'+formatted+'</span>')
            .remove();
        return false;
    // 
    }).blur(function(){

    });
    $(append_to).after(auto_box);
    var closer = $('<img class="remove" title="remove" alt="remove" src="'+URL_BASE+'media/img/action_edit_remove.png" />')
        .click(function(){
            $(this).parent().remove();
        });
    $(auto_box)
        .prepend(closer)
        .append('<input value="'+value+'" type="hidden" name="'+group+'[]" />');

    $(auto_box).find('input').focus()

}

