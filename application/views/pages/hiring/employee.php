<?php

	$sHireType = (isset($form['employee_type']) && $form['employee_type']) 
		? $form['employee_type']
		: ((isset($_GET['employee_type']) && in_array($_GET['employee_type'], $lists['employee_type']))
			? $_GET['employee_type']
			: ''
		);
?>
<style>
	legend {
		color: #333 !important;
		font-weight: normal !important;
		font-size: 12px;
	}
	fieldset {
		margin: 0 0 20px 0 !important;
		padding: 10px 20px 20px !important;
		border-width: 1px !important;
		-moz-border-radius: 5px;
		border-radius: 5px;
		width: 550px;
	}
	.form-row {
		padding: 0;
		margin: 0;
		display: block;
		clear: both;
	}
	.form-col {
		padding: 0;
		margin: 2px 5px;
		float: left;
	}
	#department {
		width: 30px;
		margin-right: 200px;
	}
	#notification_email {
		margin: 10px 10px 0 20px;
	}
	#employee_type {
		width: 140px;
	}
	#first_name, #last_name {
		width: 187px;
	}
	#address_street, #address_billing_street {
		width: 390px;
	}
	#address_city, #address_billing_city {
		width: 187px;
	}
	#address_province, #address_billing_province {
		width: 187px;
	}
	#address_postal_code, #address_billing_postal_code {
		width: 85px;
	}
	#address_country, #address_billing_country {
		width: 285px;
	}
	#start_date, #end_date {
		width: 187px;
	}
</style>

<h2>
    <?php echo html::image('media/img/app_emps.png'); ?>New Hire Request Form
</h2>
<p><strong>*</strong> denotes a required field</p>
<form class="app_form" method="post" action="" id="newHireForm" accept-charset="utf-8">

    <?php
    echo Form::csrf_token();
	?>
	<fieldset>
	    <legend>Basic info</legend>
	
			<div class="form-row">
				<div class="form-col">
					<div id="end_date_section">
				
			<?php 
			//echo form::auto_label('end_date');
		    //client::validation('end_date');
		    //echo form::input('end_date=id', $form['end_date'], array('size'=>'10'));
		    ?>
	
					</div>
				</div>
			</div>
			<div class="form-row">
				<div class="form-col">

			<?php
		    echo form::auto_label('first_name');
		    client::validation('first_name');
		    echo form::input('first_name=id', $form['first_name'], array('size'=>'20'));
			?>

				</div>
				<div class="form-col">
    
		    <?php
		    echo form::auto_label('last_name');
		    client::validation('last_name');
		    echo form::input('last_name=id', $form['last_name'], array('size'=>'20'));
			?>
    
				</div>
			</div>
			<div class="form-row">
				<div class="form-col">

			<?php
		    echo form::auto_label('email_address','Email Address (<em>personal</em>)');
		    client::validation('email_address');
		    echo form::input('email_address=id', $form['email_address'], array('size'=>'30'));
			?>
	
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-col">

			<?php
		    echo form::auto_label('employee_type', 'Employee Type');
		    client::validation('employee_type');
		    echo form::select('employee_type=id',$lists['employee_type'], $sHireType ,array('class'=>'first_focus'));
		    ?>
    
				</div>
				<div class="form-col" style="width: 300px; padding-top: 18px;">
					
			<?php
	        echo form::checkbox('notification_email=id', '1', Arr::get($_POST,'notification_email')==1);
	        echo form::auto_label('notification_email','Send notification email?');
	        ?>

				</div>
			</div>
	</fieldset>
	
	<?php /*  J O B   I N F O  */  ?>
		
	<fieldset>
	    <legend>Job info</legend>
			
			<div class="form-row">
				<div class="form-col">

			<?php
		    echo form::auto_label('position_title');
		    client::validation('position_title');
		    echo form::input('position_title=id', $form['position_title'], array('size'=>'10'));
		    ?>

				</div>
				<div class="form-col">

			<?php 
			echo form::auto_label('department');
		    client::validation('department');
		    echo form::input('department=id', $form['department'], array('size'=>'10', 'maxlength'=>3));
		    ?>

				</div>
			</div>
	
			<div class="form-row">
				<div class="form-col">
				
			<?php
		    echo form::auto_label('start_date');
		    client::validation('start_date');
		    echo form::input('start_date=id', $form['start_date'], array('size'=>'10', 'autocomplete'=>'off'));
		    ?>

				</div>
				<div class="form-col">

			<?php 
			echo form::auto_label('end_date');
		    client::validation('end_date');
		    echo form::input('end_date=id', $form['end_date'], array('size'=>'10', 'autocomplete'=>'off'));
		    ?>
			
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-col">
				
			<?php
		    echo form::auto_label('hire_type');
		    client::validation('hire_type');
		    echo form::select('hire_type=id', $lists['hire_type'], array('size'=>'3'));
		    ?>

				</div>
				<div class="form-col">

			
			
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-col">
				
			<?php
	        echo form::checkbox('relocation=id', '1', Arr::get($_POST,'relocation')==1);
	        echo form::auto_label('relocation','Relocation', array('style'=>'display:inline'));
	        ?>

				</div>
				<div class="form-col">

			<?php
	        echo form::checkbox('immigration=id', '1', Arr::get($_POST,'immigration')==1);
	        echo form::auto_label('immigration','Immigration', array('style'=>'display:inline'));
	        ?>
	
				</div>
			</div>
			
	</fieldset>
    
	<?php /*  W O R K   A D D R E S S  */  ?>
	
	<fieldset>
	    <legend>Work Address</legend>
			<div class="form-row">
				<div class="form-col">
	
	<?php
	echo form::auto_label('address_street', 'Street Address');
    client::validation('address_street');
    echo form::input('address_street=id', $form['address_street'], array('size'=>'200'));
	?>
	
			</div>
		</div>
		<div class="form-row">
			<div class="form-col">
		
	<?php
	echo form::auto_label('address_city', 'City');
    client::validation('address_city');
    echo form::input('address_city=id', $form['address_city'], array('size'=>'20'));
	?>
	
			</div>
			<div class="form-col">
	
	<?php
	echo form::auto_label('address_province', 'Province/State');
    client::validation('address_province');
    echo form::input('address_province=id', $form['address_province'], array('size'=>'20'));
	?>
	
			</div>
		</div>
		<div class="form-row">
			<div class="form-col">
	
	<?php
	echo form::auto_label('address_postal_code', 'Postal Code');
    client::validation('address_postal_code');
    echo form::input('address_postal_code=id', $form['address_postal_code'], array('size'=>'10'));
	?>
	
			</div>
			<div class="form-col">
	
	<?php
    echo form::auto_label('address_country', 'Country');
    client::validation('address_country');
	echo form::select('address_country=id', $lists['country'], $form['address_country']);
	?>
	
			</div>
		</div>
	</fieldset>
	
	<?php /*  OTHER STUFF */ ?>
	
	<fieldset>
	    <legend>Office info</legend>

		<div class="form-row">
			<div class="form-col">
				
	<?php
    echo form::auto_label('Office Contact');
    client::validation('office_contact');
    echo form::select('office_contact=id',array(),$form['office_contact']);
    ?>
			</div>
		</div>
		<div class="form-row">
			<div class="form-col">

	<?php echo form::auto_label('manager');
    client::validation('manager');
    echo form::select('manager=id',$lists['manager'],$form['manager']);
    ?>

			</div>
		</div>
		<div class="form-row">
			<div class="form-col">

	<?php
    echo form::auto_label('buddy');
    client::validation('buddy');
    echo form::select('buddy=id',$lists['buddy'],$form['buddy']);
    ?>

			</div>
		</div>
		<div class="form-row">
			<div class="form-col">

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
			
			</div>
		</div>
	</fieldset>
    <input type="submit" id="submit" name="submit" value="Submit Request" />
</form>

<script>
	/*
	USA: Karen Prescott
	Canada: Hilary Hall
	France: Tristan Nitot
	New Zealand: Robert O'Callahan
	Other: Karen Prescott
	*/
	var aOfficeContacts = [
		'Karen Prescott <karen@mozilla.com>',
		'Robert O\'Callahan <rocallahan@mozilla.com>',
		'Tristan Nitot <tnitot@mozilla.com>',
		'Hilary Hall <hhall@mozilla.com>'
	];
	
	function setOfficeContact() {
		var sOfficeContact;
		switch ($('#address_country')[0].value) {
			case 'Canada':
				sOfficeContact = aOfficeContacts[3];
				break;
			case 'France':
				sOfficeContact = aOfficeContacts[2];
				break;
			case 'New Zealand':
				sOfficeContact = aOfficeContacts[1];
				break;
			case 'United States':
			default:
				sOfficeContact = aOfficeContacts[0];
		}
		$('#office_contact')[0].value = sOfficeContact;
	}
	
	$(document).ready(function() {
		for (sKey in aOfficeContacts) {
			$('#office_contact').append($.OPTION(
				{'value' : aOfficeContacts[sKey]}, aOfficeContacts[sKey]
			));
		}
		$('#address_country').change(function() {
			setOfficeContact();
		});
		setOfficeContact();
	});
</script>

