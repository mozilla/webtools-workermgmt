
<p><a href="index.php">Home</a></p>
<h2>
    <?php echo html::image('media/img/contractor.png'); ?>Contractor New Hire Request
</h2>
<p><strong>*</strong> denotes a required field</p>
<form method="post" action="" id="newHireForm" accept-charset="utf-8">

    <?php
    echo form::hidden('hire_type','Contractor');

    echo form::auto_label('contract_type','New/Extension');
    echo form::radio('contract_type', 'New', $this->input->post('contract_type')=='New');?>New contract
    <?php echo form::radio('contract_type', 'Extension', $this->input->post('contract_type')=='Extension'); ?>Extension of existing contract
    <?php client::validation('contract_type'); ?>

    <?php
    echo form::auto_label('contractor_category','Category');
    echo form::radio('contractor_category', 'Independent', $this->input->post('contractor_category')=='Independent'); ?>Independent
    <?php echo form::radio('contractor_category', 'Corp to Corp', $this->input->post('contractor_category')=='Corp to Corp'); ?>Corp to Corp
    <?php client::validation('contractor_category'); ?>


    <?php
    echo form::auto_label('first_name');
    echo form::input('first_name', $form['first_name'], 'size="20"');
    client::validation('first_name');

    echo form::auto_label('last_name');
    echo form::input('last_name', $form['last_name'], 'size="20"');
    client::validation('last_name');

    echo form::auto_label('org_name', 'Organization name');
    echo form::input('org_name', $form['org_name'], 'size="20"');
    client::validation('org_name');

    echo form::auto_label('address');
    echo form::input('address', $form['address'], 'size="30"');
    client::validation('address');

    echo form::auto_label('phone_number');
    echo form::input('phone_number', $form['phone_number'], 'size="30"');
    client::validation('phone_number');

    echo form::auto_label('email_address');
    echo form::input('email_address', $form['email_address'], 'size="30"');
    client::validation('email_address');

    echo form::auto_label('start_date');
    echo form::input('start_date', $form['start_date'], 'size="10"');
    client::validation('start_date');

    echo form::auto_label('end_date');
    echo form::input('end_date', $form['end_date'], 'size="10"');
    client::validation('end_date');

    echo form::auto_label('pay_rate', 'Rate of pay');
    echo form::input('pay_rate', $form['pay_rate'], 'size="10"');
    client::validation('pay_rate');

    echo form::auto_label('payment_limit', 'Total payment limitation');
    echo form::input('payment_limit', $form['payment_limit'], 'size="10"');
    client::validation('payment_limit');

    echo form::auto_label('manager');
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

    <?php echo form::auto_label('statement_of_work_label');
    client::validation('statement_of_work_label');
    echo form::textarea('statement_of_work', $form['statement_of_work'],'rows="8" cols="60"');
    ?>

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
    
    <input type="submit" id="submit" name="submit" value="Submit Request" />
</form>