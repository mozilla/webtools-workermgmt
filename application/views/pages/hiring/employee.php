
<p><a href="/">Home</a></p>
<h2>
    <?php echo html::image('media/img/employee.png'); ?>Employee/Intern New Hire Request
</h2>
<p><strong>*</strong> denotes a required field</p>
<form class="app_form" method="post" action="" id="newHireForm" accept-charset="utf-8">

    <?php
    echo Form::csrf_token();

    echo form::auto_label('hire_type');
    client::validation('hire_type');
    echo form::select('hire_type=id',$lists['hire_type'],$form['hire_type']);
    
    
    echo form::auto_label('first_name');
    client::validation('first_name');
    echo form::input('first_name=id', $form['first_name'], array('size'=>'20'));
    
    
    echo form::auto_label('last_name');
    client::validation('last_name');
    echo form::input('last_name=id', $form['last_name'], array('size'=>'20'));
    

    echo form::auto_label('email_address','Email Address (<em>an existing email for this person</em>)');
    client::validation('email_address');
    echo form::input('email_address=id', $form['email_address'], array('size'=>'30'));
    
    
    echo form::auto_label('start_date');
    client::validation('start_date');
    echo form::input('start_date=id', $form['start_date'], array('size'=>'10'));
    ?>
    
    <div id="end_date_section">
    <?php echo form::auto_label('end_date');
    client::validation('end_date');
    echo form::input('end_date=id', $form['end_date'], array('size'=>'10'));
    ?>
    </div>
    
    <?php echo form::auto_label('manager');
    client::validation('manager');
    echo form::select('manager=id',$lists['manager'],$form['manager']);
    

    echo form::auto_label('buddy');
    client::validation('buddy');
    echo form::select('buddy=id',$lists['buddy'],$form['buddy']);
    

    echo form::auto_label('location');
    client::validation('location');
    echo form::select('location=id',$lists['location'],$form['location']);
    ?>

    <div id="location_other_section">
    <?php echo form::auto_label('location_other','Specify other location');
    client::validation('location_other');
    echo form::input('location_other=id', $form['location_other'], array('size'=>'20'));
    ?>
    </div>

    <p>
        <?php
        echo form::checkbox('mail_needed=id', '1', Arr::get($_POST,'mail_needed')==1);
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
        client::validation('mail_alias');
        echo form::input('mail_alias=id', $form['mail_alias'], array('size'=>'20')); ?>

        <p><i>Besides "all" and any location-based lists, are there any mailing
                lists should this user be a member of? (optional)</i></p>
        <?php
        echo form::auto_label('mail_lists','Mailing Lists');
        client::validation('mail_lists');
        echo form::input('mail_lists=id', $form['mail_lists'], array('size'=>'30')); ?>

        <?php
        echo form::auto_label('other_comments');
        client::validation('other_comments'); ?>
        <?php echo form::textarea(
                'other_comments=id',
                $form['other_comments'],
                array('rows'=>"5", 'cols'=>"40"));
        ?>

    </div>

    <p>
        <?php
        echo form::checkbox('machine_needed=id', '1', Arr::get($_POST,'machine_needed')==1);
        echo form::auto_label('machine_needed','Will this user need a machine?');
        ?>
    </p>


    <div id="machine_box" class="section" style="display: none;">
        <?php
        echo form::auto_label('machine_type','Type of machine needed');
        client::validation('machine_type');
        echo form::select('machine_type=id',$lists['machine_type'],$form['machine_type']);
        ?>

        <?php
        echo form::auto_label('machine_special_requests','Special Requests (software/hardware/setup)');
        client::validation('machine_special_requests');
        echo form::textarea(
                'machine_special_requests=id',
                $form['machine_special_requests'],
                array('rows'=>"5", 'cols'=>"40"));
        ?>
    </div>

    <input type="submit" id="submit" name="submit" value="Submit Request" />
</form>
