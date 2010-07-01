$(document).ready(function(){
    $.ajax({
      url: URL_BASE+"api/all_employees",
      success: init_selectors,
      dataType: 'json'
    });
});

function init_selectors(all_employees) {
    MultiSelectAutoComplete.load_selectors(all_employees)
}
/**
 *
 */
var MultiSelectAutoComplete = {
    // set by the server these are the group sections for select boxes
    look_up_groups: memebers_autobox_groups,
    /**
     * set by the server, array of groups that were submitted on the
     * prev POST (and their selected values).  Needed so we can repopulate
     * form after validation failure
     */
    groups_posted : memebers_groups_posted,
    // key (email) -> formatted label (what will be searched against)
    lookup_dictionary: {},
    // array of values that autocomplete searches against
    search_list: [],
    /**
     *
     */
    add_autobox: function(group_id, value) {
        if (typeof value == 'undefined' ) value = '';
        this.create_autobox(group_id, group_id+'_group', value);
    },
    /**
     *
     */
    load_selectors: function(all_employees) {
        var that = this;
        var all_employees_search = new Array();
        $.each( all_employees, function(email, emp_label){
            all_employees_search.push(emp_label);
        });
        this.lookup_dictionary = all_employees;
        this.search_list = all_employees_search;
        /**
         * iterate over the lookup groups, add the + icon and check
         * if we need to create and populate any autoboxes (i.e. we
         * are retruning to the form after failed validation
         */
        $.each(this.look_up_groups, function(index, group_id){
            // insert 'add element' icons to sections
            var add_button = $('<div class="button" style="display:inline" tabindex="0"><img tite="add recipient" alt="add recipient" src="'+URL_BASE+'media/img/action_edit_add.png" /></div>')
                .click(function(){
                    that.add_autobox(group_id);
                })
                .keyup(function(e){
                    var code = (e.keyCode ? e.keyCode : e.which);
                    if(code==13) {
                        if( ! that.ignore_enter) {
                            that.add_autobox(group_id);
                        } else {
                            that.ignore_enter = false;
                        }
                    }
                });
            $("label[for="+group_id+"]").before(add_button);
            if(typeof that.groups_posted[group_id]!='undefined') {
                if(that.groups_posted[group_id].length > 0) {
                    $.each(that.groups_posted[group_id], function(index, value){
                        that.add_autobox(group_id, value);
                    });
                }
            }

        });


    },
    /**
     * Creates either the dynamic, auto-complete textbox or the
     * static label of what was selected
     *
     * @param group string Id of the group for these autoboxes
     * @param append_to string Id of the element to append the autobox to
     * @param value string [optional] The selected value.  Used to
     *        rebuild previous selected values when redisplaying the
     *        form (failed validation)
     */
    create_autobox: function(group, append_to, value) {
        var that = this;
        if (typeof value == 'undefined' ) value = '';
        var display_label = '';
        if(value && typeof this.lookup_dictionary[value] != 'undefined') {
            display_label = this.lookup_dictionary[value];
        } else {
            value = '';
        }
        append_to = '#'+append_to.replace(/^#/, '');
        /*
         * if we are supplied a value, we are making the label state, else
         * we are rendering the auto-complete input box
         */
        var auto_box = value
            ? $('<div><span class="selected" >'+display_label+'</span></div>')
            : $('<div><input type="text" name="'+group+'_autocomplete" /></div>');
        $(auto_box).children('input').autocomplete(
            this.search_list,{matchContains: true}
        ).result(function(event, data, formatted) {
            /*
             * get the email (considered pk) and set it to the hidden input
             */
            var match = formatted ? formatted.match(/\((.*)\)$/) : null;
            $(this).next('input').val(match ? match[1] : null);
            // put the focus back on the add button
            // @todo Find a way to stop the event Bubble???
            that.ignore_enter = true;
            $(this).parents('.multi-lookup').find('.button').focus();
            // replace the autobox w/ static label of what was choosen
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
}