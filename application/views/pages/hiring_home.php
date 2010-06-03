    <table id="employee-selector">
    <tr>
    <td class="employee-type">
        <?php echo html::file_anchor('hiring/employee', html::image('media/img/employee.png'));?>
        <br/>
        <?php echo html::anchor('hiring/employee','Employee or Intern'); ?>

    </td>
    <td class="employee-type">
        <?php echo html::file_anchor('hiring/contractor', html::image('media/img/contractor.png'));?>
        <br/>
        <?php echo html::anchor('hiring/contractor','Contractor'); ?>
    </td>
    </tr>
    <tr class="employee-description">
    <td valign="top">New Hire Notification:<ul><li>Files a facility request.</li><li>Files account/hardware requests.</li></ul></td>

    <td valign="top">Contractor Request:<ul><li>Files a Mozilla Corporation Consulting bug.</li></ul></td>
    </tr>
    </table>