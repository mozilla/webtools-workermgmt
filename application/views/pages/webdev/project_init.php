<h2>
    Project Initialization Form
</h2>
<p><strong>*</strong> denotes a required field</p>
<form class="app_form" method="post" action="" id="project_init_form" accept-charset="utf-8">

    <?php
    echo Form::csrf_token();

    echo form::auto_label('name');
    client::validation('name');
    echo form::input('name=id',$form['name'],array('class'=>'first_focus'));
    ?>

    <fieldset>
        <legend>Team Members</legend>

        <div class="multi-lookup">
        <?php
        echo form::auto_label('members_it', 'IT', array('id'=>'members_it_group'));
        client::validation('members_it');?>
        </div>
        <div class="multi-lookup">
        <?php
        echo form::auto_label('members_product_driver', 'Product/Driver (you)?', array('id'=>'members_product_driver_group'));
        client::validation('members_product_driver');?>
        </div>
        <div class="multi-lookup">
        <?php
        echo form::auto_label('members_l10n', 'L10n', array('id'=>'members_l10n_group'));
        client::validation('members_l10n');?>
        </div>
        <div class="multi-lookup">
        <?php
        echo form::auto_label('members_marketing', 'Marketing', array('id'=>'members_marketing_group'));
        client::validation('members_marketing');?>
        </div>
        <div class="multi-lookup">
        <?php
        echo form::auto_label('members_qa', 'QA', array('id'=>'members_qa_group'));
        client::validation('members_qa');?>
        </div>
        <div class="multi-lookup">
        <?php
        echo form::auto_label('members_security', 'Security', array('id'=>'members_security_group'));
        client::validation('members_security');?>
        </div>
        <div class="multi-lookup">
        <?php
        echo form::auto_label('members_webdev', 'Webdev', array('id'=>'members_webdev_group'));
        client::validation('members_webdev');?>
        </div>
        <div class="multi-lookup">
        <?php
        echo form::auto_label('members_other', 'Other', array('id'=>'members_other_group'));
        client::validation('members_other');?>
        </div>
        
    </fieldset>
    
    <?php
    echo form::auto_label('overview');
    client::validation('overview');
    echo form::textarea(
            'overview=id',
            $form['overview'],
            array('class' => 'resizable'));
    ?>

    <?php
    echo form::auto_label('scope');
    client::validation('scope');
    echo form::textarea(
            'scope=id',
            $form['scope'],
            array('class' => 'resizable'));
    ?>

    <fieldset>
        <legend>Dependencies</legend>
        
        <?php
        echo form::auto_label('dependencies_legal','Legal');
        client::validation('dependencies_legal');
        echo form::textarea(
                'dependencies_legal=id',
                $form['dependencies_legal'],
                array('class' => 'resizable'));

        echo form::auto_label('dependencies_security', 'Security (infra and/or client)');
        client::validation('dependencies_security');
        echo form::textarea(
                'dependencies_security=id',
                $form['dependencies_security'],
                array('class' => 'resizable'));

        echo form::auto_label('dependencies_analytics', 'Analytics');
        client::validation('dependencies_analytics');
        echo form::textarea(
                'dependencies_it=analytics',
                $form['dependencies_analytics'],
                array('class' => 'resizable'));

        echo form::auto_label('dependencies_finance', 'Finance/Payments');
        client::validation('dependencies_finance');
        echo form::textarea(
                'dependencies_finance=id',
                $form['dependencies_finance'],
                array('class' => 'resizable'));

        echo form::auto_label('dependencies_app', 'App');
        client::validation('dependencies_app');
        echo form::textarea(
                'dependencies_app=id',
                $form['dependencies_app'],
                array('class' => 'resizable'));

        echo form::auto_label('dependencies_other', 'Other');
        client::validation('dependencies_other');
        echo form::textarea(
                'dependencies_other=id',
                $form['dependencies_other'],
                array('class' => 'resizable'));

        ?>


    </fieldset>

    <?php
    echo form::auto_label('assumptions');
    client::validation('assumptions');
    echo form::textarea(
            'assumptions=id',
            $form['assumptions'],
            array('class' => 'resizable'));
    ?>

    <?php
    echo form::auto_label('deliverables');
    client::validation('deliverables');
    echo form::textarea(
            'deliverables=id',
            $form['deliverables'],
            array('class' => 'resizable'));
    ?>
    

    <input type="submit" id="submit" name="submit" value="Submit Request" />
</form>
