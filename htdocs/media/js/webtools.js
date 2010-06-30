$(document).ready(function(){


  if($().TextAreaResizer)  {
    $('textarea.resizable:not(.processed)').TextAreaResizer();
  }
  
  $("#start_date, #end_date").datepicker();

  /**
   * toggle the text field to fill in "other" location
   */
  $('#location').change(function() {
    toggle_section('location_other_section',$(this).val()=='other');
    $('#location_other_section label').addClass('required');
  });
  toggle_section('location_other_section',$('#location').val()=='other');

  /**
   * toggle the edndate to show if HireType::Intern selected
   */
  $('#hire_type').change(function() {
    toggle_section('end_date_section',$(this).val()=='Intern');
  });
  toggle_section('end_date_section',$('#hire_type').val()=='Intern');

  /**
   * For the two checkboxes, toggle the sections the represent
   */
  $('#mail_needed').click(function() {
    toggle_section('mail_box',$(this).attr('checked'));
    
  });
  $('#machine_needed').click(function() {
    toggle_section('machine_box',$(this).attr('checked'));
    $("#mail_box label[for='machine_needed']").addClass('required');
  });
  toggle_section('mail_box',$('#mail_needed').attr('checked'));
  toggle_section('machine_box',$('#machine_needed').attr('checked'));

  update_default_username_display();

  /**
   * update the default username lable
   * note: this is display only, it is recalculated server-side
   */
  $('#first_name, #last_name').focusout(function() {
    update_default_username_display();
  });

  select_to_autocomplete('manager',{extra_attribs: 'size="60"'});
  select_to_autocomplete('buddy',{extra_attribs: 'size="60"'});

});
/**
 * Wrote some wrapper code that allows Jquery Auto complete to
 * switch out select elements with auto-complete text elements
 *
 * @see http://docs.jquery.com/Plugins/Autocomplete
 *
 * @param element_id
 * @param config
 *
 */
function select_to_autocomplete(element_id, config) {
  element_id = element_id.replace(/^#/,'');
  var jq_element_id = '#'+element_id;
  if($(jq_element_id).length>0) {
    // set config defaults
    config.ignore_first = config.ignore_first == undefined ? true : config.ignore_first;
    config.extra_attribs = config.extra_attribs == undefined ? '' : config.extra_attribs;

    // replace the select with a text box
    $(jq_element_id).hide();
    $(jq_element_id).before('<input type="text" id="'+element_id+'_autocomplete" '+config.extra_attribs+' />');
    // set the value of the replacent input element to the selection in original
    if(config.ignore_first&&$(jq_element_id+' :selected').index()==0) {
        $(jq_element_id+'_autocomplete').val('');
    } else {
        $(jq_element_id+'_autocomplete').val($(jq_element_id+' :selected').text()+' ('+$(jq_element_id+' :selected').val()+')');
    }
    // read all the select options into an array
    var list_items = select_as_array(jq_element_id);
    if(config.ignore_first) {list_items[0]='';}
    // attach the autocomplete to the textbox
    $(jq_element_id+'_autocomplete').autocomplete(
        list_items,{matchContains: true}
    ).result(function(event, data, formatted) {
        var match = formatted?formatted.match(/\((.*)\)$/):null;
        $(jq_element_id).val(match ? match[1] : null);
        return false;
    // on blur, invoke search again incase there were edits
    }).blur(function(){
        $(this).search();
    });
    
  }
}

function select_as_array(select_id) {
    select_id = '#'+select_id.replace(/^#/,'');
    var select_items = new Array();
    // read all the select options into an array
    $(select_id+" option").each(function(index) {
        select_items.push($(this).text()+' ('+$(this).val()+')');
    });
    return select_items;
}

function auto_complete_multi_select() {

}

function toggle_section(section_id, change_to_show) {
  if(change_to_show) {
    $('#'+section_id).show();
  } else {
    $('#'+section_id).hide();
  }
}
function update_default_username_display() {
    if($("#first_name").length>0) {
      var first = $("#first_name").val().length>0?$("#first_name").val()[0].toLowerCase():'';
      $("#default_username").val(
        first + $("#last_name").val().toLowerCase()
      );
    }
}