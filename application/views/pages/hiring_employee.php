
<p><a href="index.php">Home</a></p>
<h2>
    <?php echo html::image('media/img/employee.png'); ?>Employee/Intern New Hire Request
</h2>
<p><strong>*</strong> denotes a required field</p>
<form method="post" action="" id="newHireForm" accept-charset="utf-8">

    <?php
    echo form::auto_label('hire_type');
    echo form::dropdown('hire_type',$lists['hire_type'],$form['hire_type']);
    client::validation('hire_type');
    
    echo form::auto_label('first_name');
    echo form::input('first_name', $form['first_name'], 'size="20"');
    client::validation('first_name');
    
    echo form::auto_label('last_name');
    echo form::input('last_name', $form['last_name'], 'size="20"');
    client::validation('last_name');

    echo form::auto_label('email_address','Email Address (<em>an existing email for this person</em>)');
    echo form::input('email_address', $form['email_address'], 'size="30"');
    client::validation('email_address');
    
    echo form::auto_label('start_date');
    echo form::input('start_date', $form['start_date'], 'size="10"');
    client::validation('start_date');?>
    
    <div id="end_date_section">
    <?php echo form::auto_label('end_date');
    echo form::input('end_date', $form['end_date'], 'size="10"');
    client::validation('end_date');?>
    </div>
    
    <?php echo form::auto_label('manager');
    echo form::dropdown('manager',$lists['manager'],$form['manager']);
    client::validation('manager');

    echo form::auto_label('buddy');
    echo form::dropdown('buddy',$lists['buddy'],$form['buddy']);
    client::validation('buddy');

    echo form::auto_label('location');
    echo form::dropdown('location',$lists['location'],$form['location']);
    client::validation('location');?>

    <div id="location_other_section">
    <?php echo form::auto_label('location_other','Specify other location');
    echo form::input('location_other', $form['location_other'], 'size="20"');
    client::validation('location_other'); ?>
    </div>

    <p>
        <?php
        echo form::checkbox('mail_needed', '1',$this->input->post('mail_needed')==1);
        echo form::auto_label('mail_needed','Will this user need a mail account?');
        ?>
    </p>


    <div id="mail_box" class="section" style="display: none;">
        <p><i>User accounts are created in the form of &lt;first letter of first
                name&gt;&lt;full last name&gt; (example: John Doe would be "jdoe").</i></p>

        <label for="default_username">Default username</label>
        <input type="text" id="default_username" size="20" disabled />

        <p><i>Mailing aliases are <strong>optional</strong>! Only fill this out if
                you want a username in ADDITION to the default.</i></p>
        <?php
        echo form::auto_label('mail_alias','Mailing Alias');
        echo form::input('mail_alias', $form['mail_alias'], 'size="20"'); ?>
        <?php client::validation('mail_alias'); ?>

        <p><i>Besides "all" and any location-based lists, are there any mailing
                lists should this user be a member of? (optional)</i></p>
        <?php
        echo form::auto_label('mail_lists','Mailing Lists');
        echo form::input('mail_lists', $form['mail_lists'], 'size="30"'); ?>
        <?php client::validation('mail_lists'); ?>

        <?php
        echo form::auto_label('other_comments');
        client::validation('other_comments'); ?>
        <?php echo form::textarea('other_comments', $form['other_comments'],'rows="5" cols="40"'); ?>

    </div>

    <p>
        <?php
        echo form::checkbox('machine_needed', '1',$this->input->post('machine_needed')==1);
        echo form::auto_label('machine_needed','Will this user need a machine?');
        ?>
    </p>


    <div id="machine_box" class="section" style="display: none;">
        <?php
        echo form::auto_label('machine_type','Type of machine needed');
        echo form::dropdown('machine_type',$lists['machine_type'],$form['machine_type']);
        client::validation('machine_type'); ?>

        <?php
        echo form::auto_label('machine_special_requests','Special Requests (software/hardware/setup)');
        client::validation('machine_special_requests');
        echo form::textarea('machine_special_requests', $form['machine_special_requests'],'rows="5" cols="40"'); ?>
    </div>

    <input type="submit" id="submit" name="submit" value="Submit Request" />
</form>
