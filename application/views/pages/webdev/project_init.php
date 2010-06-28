
<p><a href="/">Home</a></p>
<h2>
    Project Initialization Form
</h2>
<p><strong>*</strong> denotes a required field</p>
<form class="app_form" method="post" action="" id="project_init_form" accept-charset="utf-8">

    <?php
    echo Form::csrf_token();

    echo form::auto_label('name');
    client::validation('name');
    echo form::input('name=id',$form['name']);
    ?>

    <fieldset>
        <legend>Team Members</legend>
        <?php
        echo form::auto_label('members.it','IT');
        client::validation('members.it');
        echo form::textarea(
                'members.it=id',
                $form['members.it'],
                array('class' => 'resizable'));

        echo form::auto_label('members.product_driver', 'Product/Driver (you)?');
        client::validation('members.product_driver');
        echo form::textarea(
                'members.product_driver=id',
                $form['members.product_driver'],
                array('class' => 'resizable'));

        echo form::auto_label('members.l10n', 'L10n');
        client::validation('members.l10n');
        echo form::textarea(
                'members.it=l10n',
                $form['members.l10n'],
                array('class' => 'resizable'));

        echo form::auto_label('members.marketing', 'Marketing');
        client::validation('members.marketing');
        echo form::textarea(
                'members.marketing=id',
                $form['members.marketing'],
                array('class' => 'resizable'));

        echo form::auto_label('members.qa', 'QA');
        client::validation('members.qa');
        echo form::textarea(
                'members.qa=id',
                $form['members.qa'],
                array('class' => 'resizable'));

        echo form::auto_label('members.security', 'Security');
        client::validation('members.security');
        echo form::textarea(
                'members.security=id',
                $form['members.security'],
                array('class' => 'resizable'));

        echo form::auto_label('members.webdev', 'Webdev');
        client::validation('members.webdev');
        echo form::textarea(
                'members.webdev=id',
                $form['members.webdev'],
                array('class' => 'resizable'));

        echo form::auto_label('members.other', 'Other');
        client::validation('members.other');
        echo form::textarea(
                'members.other=id',
                $form['members.other'],
                array('class' => 'resizable'));
        ?>


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
        echo form::auto_label('dependencies.legal','Legal');
        client::validation('dependencies.legal');
        echo form::textarea(
                'dependencies.legal=id',
                $form['dependencies.legal'],
                array('class' => 'resizable'));

        echo form::auto_label('dependencies.security', 'Security (infra and/or client)');
        client::validation('dependencies.security');
        echo form::textarea(
                'dependencies.security=id',
                $form['dependencies.security'],
                array('class' => 'resizable'));

        echo form::auto_label('dependencies.analytics', 'Analytics');
        client::validation('dependencies.analytics');
        echo form::textarea(
                'dependencies.it=analytics',
                $form['dependencies.analytics'],
                array('class' => 'resizable'));

        echo form::auto_label('dependencies.finance', 'Finance/Payments');
        client::validation('dependencies.finance');
        echo form::textarea(
                'dependencies.finance=id',
                $form['dependencies.finance'],
                array('class' => 'resizable'));

        echo form::auto_label('dependencies.app', 'App');
        client::validation('dependencies.app');
        echo form::textarea(
                'dependencies.app=id',
                $form['dependencies.app'],
                array('class' => 'resizable'));

        echo form::auto_label('dependencies.other', 'Other');
        client::validation('dependencies.other');
        echo form::textarea(
                'dependencies.other=id',
                $form['dependencies.other'],
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
