
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
	#currency_symbol {
		margin-top: 14px;
	}
		#currency_symbol h2 {
			font-size: 16px;
			margin: 0;
			padding: 0;
		}
		#currency_symbol span {
			font-size: 10px;
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
	#start_date, #end_date, #hours_per_week, #payment_limit {
		width: 187px;
	}
	#pay_rate {
		width: 187px;
	}
	#payment_schedule {
		width: 190px;
	}
	
</style>

<h2>
    <?php echo html::image('media/img/app_person.png'); ?>Contractor New Hire Request
</h2>
<p><strong>*</strong> denotes a required field</p>
<form class="app_form" method="post" action="" id="newHireForm" accept-charset="utf-8">
	
	<fieldset>
	    <legend>Personal Information</legend>
		<div class="form-row">
			<div class="form-col">

    <?php
    echo Form::csrf_token();
    echo form::hidden('employee_type','Contractor');
	echo form::hidden('currency','USD (U.S.Dollars)');
	?>
	
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
    echo form::auto_label('org_name', 'Organization name');
    client::validation('org_name');
    echo form::input('org_name=id', $form['org_name'], array('size'=>'20'));
	?>
	
	<?php
    echo form::auto_label('phone_number');
    client::validation('phone_number');
    echo form::input('phone_number=id', $form['phone_number'], array('size'=>'30'));
	?>
    
	<?php
    echo form::auto_label('email_address');
    client::validation('email_address');
    echo form::input('email_address=id', $form['email_address'], array('size'=>'30'));
	?>
	
			</div>
		</div>
	</fieldset>
    
	<?php /*  W O R K   A D D R E S S  */ ?>
	
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
	echo form::select('address_country=id',$lists['country'],$form['address_country']);
	?>
	
			</div>
		</div>
	</fieldset>
		
	
	<?php /*  B I L L I N G   A D D R E S S  */ ?>
	
	
	<fieldset>
	    <legend>Billing Address</legend>
			<div class="form-row">
				<div class="form-col">

	<?php
	echo form::auto_label('same as work address');
    client::validation('address_billing_same');
    echo form::checkbox('address_billing_same=id', '1', Arr::get($_POST,'address_billing_same')==1);
	?>

			</div>
		</div>
		
		<div id="billing_address_container">
			<div class="form-row">
				<div class="form-col">

		<?php
		echo form::auto_label('address_billing_street', 'Street Address');
	    client::validation('address_billing_street');
	    echo form::input('address_billing_street=id', $form['address_billing_street'], array('size'=>'200'));
		?>
	
				</div>
			</div>
			<div class="form-row">
				<div class="form-col">
		
		<?php
		echo form::auto_label('address_billing_city', 'City');
	    client::validation('address_billing_city');
	    echo form::input('address_billing_city=id', $form['address_billing_city'], array('size'=>'20'));
		?>
	
				</div>
				<div class="form-col">
	
		<?php
		echo form::auto_label('address_billing_province', 'Province/State');
	    client::validation('address_billing_province');
	    echo form::input('address_billing_province=id', $form['address_billing_province'], array('size'=>'20'));
		?>
	
				</div>
			</div>
			<div class="form-row">
				<div class="form-col">
	
		<?php
		echo form::auto_label('address_billing_postal_code', 'Postal Code');
	    client::validation('address_billing_postal_code');
	    echo form::input('address_billing_postal_code=id', $form['address_billing_postal_code'], array('size'=>'10'));
		?>
	
				</div>
				<div class="form-col">
	
		<?php
	    echo form::auto_label('address_billing_country', 'Country');
	    client::validation('address_billing_country');
		echo form::select('address_billing_country=id',$lists['country'],$form['address_billing_country']);
		?>
	
				</div>
			</div>
		</div>
		
	</fieldset>
		
	<?php /*  O T H E R   I N F O  */ ?>
	
	<fieldset>
		<legend>Position Information</legend>	
		<div class="form-row">
			<div class="form-col">
    
	<?php
    echo form::auto_label('contract_type','New/Extension');
    client::validation('contract_type');
    echo form::radio('contract_type', 'New',       Arr::get($_POST,'contract_type')=='New');?>New contract<?php 
    echo form::radio('contract_type', 'Extension', Arr::get($_POST,'contract_type')=='Extension'); ?>Extension of existing contract


    <?php
    echo form::auto_label('contractor_category','Category');
    client::validation('contractor_category');
    echo form::radio('contractor_category', 'Independent',  Arr::get($_POST,'contractor_category')=='Independent'); ?>Independent<?php 
    echo form::radio('contractor_category', 'Corp to Corp', Arr::get($_POST,'contractor_category')=='Corp to Corp'); ?>Corp to Corp
	
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
	$aPaymentSchedules = array(
		'Semi-Monthly'	=> 'Semi-Monthly',
		'Monthly'		=> 'Monthly',
		'Hourly'		=> 'Hourly',
		'Flat'			=> 'Flat'
	);
    echo form::auto_label('payment_schedule', 'Pay Schedule');
    client::validation('payment_schedule');
    echo form::select('payment_schedule=id', $aPaymentSchedules, $form['payment_schedule']);
	?>			
				
			</div>
			<div class="form-col">
	
	<?php
    echo form::auto_label('pay_rate', 'Rate of pay');
    client::validation('pay_rate');
    echo form::input('pay_rate=id', $form['pay_rate'], array('size'=>'10'));
	?>
	
			</div>
			<div class="form-col">
				<div id="currency_symbol"><h2>USD</h2><span>U.S. Dollars</span></div>
			</div>
		</div>
		
		<div class="form-row">
			<div class="form-col">
				<?php
			    echo form::auto_label('hours_per_week', 'Hours Per Week');
			    client::validation('hours_per_week');
			    echo form::input('hours_per_week=id', $form['hours_per_week'], array('size'=>'10'));
				?>
			</div>
			<div class="form-col">
				<?php
			    echo form::auto_label('payment_limit', 'Total payment limitation');
			    client::validation('payment_limit');
			    echo form::input('payment_limit=id', $form['payment_limit'], array('size'=>'10'));
				?>
			</div
		</div>
		
		<div class="form-row">
			<div class="form-col">
				
	    
	<?php
    echo form::auto_label('manager');
    client::validation('manager');
    echo form::select('manager=id',$lists['manager'],$form['manager']);
	?>
    
	<?php /*
    echo form::auto_label('location');
    client::validation('location');
    echo form::select('location=id',$lists['location'],$form['location']);
    */ ?>

    <div id="location_other_section">
	<?php echo form::auto_label('location_other','Specify other location');
    client::validation('location_other');
    echo form::input('location_other=id', $form['location_other'], array('size'=>'20'));
    ?>
    </div>

    <?php echo form::auto_label('statement_of_work');
    client::validation('statement_of_work');
    echo form::textarea('statement_of_work=id',$form['statement_of_work'],array('rows'=>"8", 'cols'=>"60"));
    ?>

    <!--p>
        <?php
        //echo form::checkbox('mail_needed=id', '1', Arr::get($_POST,'mail_needed')==1);
        //echo form::auto_label('mail_needed','Will this user need an LDAP account?');
        ?>
    </p-->

	<?php
    echo form::auto_label('mail_needed','Will this contractor need an LDAP account?');
    client::validation('mail_needed');
    echo form::radio('mail_needed', '1', Arr::get($_POST,'mail_needed')=='1');?>Yes<?php 
	echo form::radio('mail_needed', '0', Arr::get($_POST,'mail_needed')=='0'); ?>No


    			<div id="mail_box" class="section" style="display: none;">
			        <p>
						<i>User accounts are created in the form of &lt;first letter of first
			                name&gt;&lt;full last name&gt; (example: John Doe would be "jdoe").
						</i>
					</p>

			        <label for="default_username">Default username</label>
			        <input type="text" id="default_username" size="20" disabled />

			        <p>
						<i>Mailing aliases are <b>optional</b>! Only fill this out if you want a username in ADDITION to the default.</i>
					</p>
		
			        <?php
			        echo form::auto_label('mail_alias','Mailing Alias');
			        client::validation('mail_alias');
			        echo form::input('mail_alias=id', $form['mail_alias'], array('size'=>'20')); 
					?>

			        <p><i>Besides "all" and any location-based lists, are there any mailing
			                lists should this user be a member of? (optional)</i></p>
			        <?php
			        echo form::auto_label('mail_lists','Mailing Lists');
			        client::validation('mail_lists');
			        echo form::input('mail_lists=id', $form['mail_lists'], array('size'=>'30')); 
					?>

			        <?php
			        echo form::auto_label('other_comments');
			        client::validation('other_comments'); ?>
			        <?php echo form::textarea('other_comments=id',$form['other_comments'],array('rows'=>"5", 'cols'=>"40"));
			        ?>

			    </div>
			</div>
		</div>
		<div class="form-row">
   			<input type="submit" id="submit" name="submit" value="Submit Request" style="float: right; margin-right: 30px;" />
		</div>
	</fieldset>
</form>

<script>
	$ = jQuery;
	var oCountryCurrencyList = <?php echo json_encode($lists['country_currency']); ?>;
	function setBillingAddress() {
		if ($('#address_billing_same')[0].checked) {
			$('#billing_address_container').hide();
			$('#address_billing_street')[0].value 		= $('#address_street')[0].value;
			$('#address_billing_city')[0].value 		= $('#address_city')[0].value;
			$('#address_billing_province')[0].value 	= $('#address_province')[0].value;
			$('#address_billing_postal_code')[0].value 	= $('#address_postal_code')[0].value;
			$('#address_billing_country')[0].value 		= $('#address_country')[0].value;
		} else {
			$('#billing_address_container').show();
		}
	}
	function setMailBox() {
		if ($('input[name=mail_needed]')[0].checked) {
			$('#mail_box').show();
		} else {
			$('#mail_box').hide();
		}
	}
	function setCurrencySymbolByCountry() {
		var sCountry = $('#address_country')[0].value;
		// Set currency label
		$('#currency_symbol').html($.DIV({},
			$.H2({},   oCountryCurrencyList[sCountry][1]),
			$.SPAN({}, oCountryCurrencyList[sCountry][0])
		));
		// Set currency hidden value
		$('input[name=currency]')[0].value = oCountryCurrencyList[sCountry][1] + ' (' +
			oCountryCurrencyList[sCountry][0] + ')';
	}
	$(document).ready(function() {
		$('#address_billing_same').click(function(oEvent) {
			setBillingAddress();
		});
		$('input[name=mail_needed]').change(function() {
			setMailBox();
		});
		$('#address_country').change(function() {
			setCurrencySymbolByCountry();
		});
		$('#newHireForm').submit(function() {
			setBillingAddress();
		});
		setMailBox();
		setBillingAddress();
		setCurrencySymbolByCountry();
	});
</script>