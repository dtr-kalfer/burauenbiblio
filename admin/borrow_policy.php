<?php
require_once("../shared/common.php");
Page::header(['nav' => 'admin/collections', 'title' => T("Collection Setup: Borrow Time Limit Policy")]);

require_once __DIR__ . '/../autoload.php';

use Collection\MyCollection;

$collection = new MyCollection();
$policies = $collection->getBorrowPolicyList();
?>
<form method="post" action="borrow_policy_save.php">
  <fieldset>
    <label>
      <?php echo T("Collection Code"); ?>
      <label>
				<?php echo T("Collection"); ?>
				<select id="rpt_c_code" name="code" required>
					<option value=""><?php echo T("-- Select Collection --"); ?></option>
					<?php foreach ($policies as $row): ?>
						<option
							value="<?php echo H($row['code']); ?>"
							data-days="<?php echo H($row['days_due_back']); ?>"
							data-fee="<?php echo H($row['regular_late_fee']); ?>"
						>
							<?php
								echo H($row['code']) . ' – ' . H($row['description']);
							?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>
    </label>

    <label>
      <?php echo T("Allowed number of days"); ?>
      <input id="rpt_c_days_due_back" name="days_due_back" type="number" min="0" max="365" required>
    </label>

    <label>
      <?php echo T("Late fee rate"); ?>
      <input id="rpt_c_regular_late_fee" name="regular_late_fee" type="text" required>
    </label>

    <input type="submit" class="button" value="<?php echo T("Update Policy"); ?>">
  </fieldset>
</form>

<h3><?php echo T("Current Borrow Policies"); ?></h3>

<table class="striped">
    <thead>
        <tr>
            <th><?php echo T("Code"); ?></th>
            <th><?php echo T("Description"); ?></th>
            
            <th><?php echo T("Days Due Back"); ?></th>
            <th><?php echo T("Late fee rate"); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($policies)): ?>
        <tr>
            <td colspan="5"><?php echo T("No collections found."); ?></td>
        </tr>
    <?php else: ?>
        <?php foreach ($policies as $row): ?>
            <tr>
                <td><?php echo H($row['code']); ?></td>
                <td><?php echo H($row['description']); ?></td>

                <td style="text-align:center">
                    <?php echo H($row['days_due_back']); ?>
                </td>
                <td style="text-align:right">
                    <?php echo number_format((float)$row['regular_late_fee'], 2); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<?php require_once("../shared/footer.php"); ?>
<script>
document.getElementById('rpt_c_code').addEventListener('change', function () {
    const opt = this.selectedOptions[0];
    if (!opt) return;

    const days = opt.dataset.days;
    const fee  = opt.dataset.fee;

    if (days !== undefined) {
        document.getElementById('rpt_c_days_due_back').value = days;
    }
    if (fee !== undefined) {
        document.getElementById('rpt_c_regular_late_fee').value = fee;
    }
});
</script>

