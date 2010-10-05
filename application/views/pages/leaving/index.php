<h2>
    <?php echo html::image('media/img/app_emps.png'); ?>Employment Termination Request
</h2>
<p><strong>*</strong> denotes a required field</p>
<form class="app_form" method="post" action="" id="newHireForm" accept-charset="utf-8">

    <?php
        echo Form::csrf_token();

        echo form::auto_label('full_name');
        client::validation('full_name');
        echo form::input('full_name=id', $form['full_name'], array('size'=>'20'));

        echo form::auto_label('reason_for_leaving');
        client::validation('reason_for_leaving');
        echo form::input('reason_for_leaving=id', $form['reason_for_leaving'], array('size'=>'20'));
    
        echo form::auto_label('manager');
        client::validation('manager');
        echo form::select('manager=id', $lists['manager'], $form['manager']);
    
        echo form::auto_label('date_of_notice');
        client::validation('date_of_notice');
        echo form::input('date_of_notice=id', $form['date_of_notice'], array('size'=>'20'));
    
        echo form::auto_label('date_of_last_day');
        client::validation('date_of_last_day');
        echo form::input('date_of_last_day=id', $form['date_of_last_day'], array('size'=>'20'));
    
        echo form::auto_label('equipment_to_return');
        client::validation('equipment_to_return');
        echo form::input('equipment_to_return=id', $form['equipment_to_return'], array('size'=>'20'));
    
        echo form::auto_label('do_notify_managers');
        client::validation('do_notify_managers');
        echo form::checkbox('do_notify_managers=id', $form['do_notify_managers'], true);
    ?>
    
    <br /><br />
    <input type="submit" id="submit" name="submit" value="Submit Request" />
</form>
