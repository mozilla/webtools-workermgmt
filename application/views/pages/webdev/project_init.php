
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

    <h2>Team Members</h2>
    
    <?php
    echo form::auto_label('overview');
    client::validation('overview');
    echo form::textarea(
            'overview=id',
            $form['overview'],
            array('rows'=>"5", 'cols'=>"40"));
    ?>

    <?php
    echo form::auto_label('scope');
    client::validation('scope');
    echo form::textarea(
            'scope=id',
            $form['scope'],
            array('rows'=>"5", 'cols'=>"40"));
    ?>

    <h2>Dependencies</h2>

    <?php
    echo form::auto_label('assumptions');
    client::validation('assumptions');
    echo form::textarea(
            'assumptions=id',
            $form['assumptions'],
            array('rows'=>"5", 'cols'=>"40"));
    ?>

    <?php
    echo form::auto_label('deliverables');
    client::validation('deliverables');
    echo form::textarea(
            'deliverables=id',
            $form['deliverables'],
            array('rows'=>"5", 'cols'=>"40"));
    ?>
    

    <input type="submit" id="submit" name="submit" value="Submit Request" />
</form>
