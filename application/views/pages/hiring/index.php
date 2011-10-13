<style>
	#decision_window h3 {
		font-size: 12px;
		padding: 0;
		margin: 0;
	}
	#decision_window table tr td {
		padding: 0;
		margin: 2px;
		height: 35px;
	}
	#decision_window .button {
		border: 1px solid #CCC;
		-moz-border-radius: 3px;
	    -webkit-border-radius: 3px;
		border-radius: 3px;
		cursor: pointer;
	}
</style>

<div id="landing_content">
    <div class="item">
        <h2>
            <?php echo html::file_anchor('hiring/employee?employee_type=Employee', html::image('media/img/app_emps.png'));?>
            <div><?php echo html::anchor('hiring/employee?employee_type=Employee','Employee'); ?></div>
        </h2>
        New Hire Notification:<ul><li>Files a facility request.</li><li>Files account/hardware requests.</li></ul>
    </div>
	<div class="item">
        <h2>
            <?php echo html::file_anchor('hiring/employee?employee_type=Intern', html::image('media/img/app_emps.png'));?>
			<div><?php echo html::anchor('hiring/employee?employee_type=Intern','Intern'); ?></div>
        </h2>
        New Hire Notification:<ul><li>Files a facility request.</li><li>Files account/hardware requests.</li></ul>
    </div>
	<div class="item">
        <h2>
            <?php echo html::file_anchor('hiring/employee?employee_type=Seasonal', html::image('media/img/app_emps.png'));?>
			<div><?php echo html::anchor('hiring/employee?employee_type=Seasonal','Seasonal'); ?></div>
        </h2>
        New Hire Notification:<ul><li>Files a facility request.</li><li>Files account/hardware requests.</li></ul>
    </div>
    <div class="item">
        <h2>
            <a href="javascript:showDecisionWindow()"><?php echo html::image('media/img/app_person.png');?></a>
            <div><a href="javascript:showDecisionWindow()">Contractor</a></div>
        </h2>
        Contractor Request:<ul><li>Files a Mozilla Corporation Consulting bug.</li></ul>
    </div>
</div>

<div id="decision_window" style="display: none;" title="Please answer the following questions:">
		<table>
			<tr>
				<td><h3>Will this contractor work in any Mozilla office?</h3></td>
				<td><input class="button" type="button" onclick="redirectToDestination(1)" value="Yes" /></td>
			</tr>
			<tr>
				<td><h3>Will this contractor be using any Mozilla equipment? (ex. computer)</h3></td>
				<td><input class="button" type="button" onclick="redirectToDestination(1)" value="Yes" /></td>
			</tr>
			<tr>
				<td><h3>Is Mozilla their only client?</h3></td>
				<td><input class="button" type="button" onclick="redirectToDestination(1)" value="Yes" /></td>
			</tr>
			<tr>
				<td><h3>Does this contractor require supervision?</h3></td>
				<td><input class="button" type="button" onclick="redirectToDestination(1)" value="Yes" /></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: center">
					<input class="button" type="button" value="None of the above" onclick="redirectToDestination(0)" />
				</td>
			</tr>
		</table>
</div>

<script>
	var sUrlToContractor = '<?php echo URL::base().'hiring/contractor'; ?>';
	var sUrlToEmployee   = '<?php echo URL::base().'hiring/employee?employee_type=Seasonal'; ?>';
	function showDecisionWindow() {
		$("#decision_window").dialog('open');
	};
	function redirectToDestination(nValue) {
		if (nValue) {
			location.href = sUrlToEmployee;
			return;
		}
		location.href = sUrlToContractor;
	};
	$('document').ready(function() {
		$("#decision_window").dialog({ 
			autoOpen: false,
			width: 500
		});
	});
</script>