<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 * edit_collection.php — Standalone page for library staff to add, update,
 * or remove collections (e.g., Research Paper, Journal, Magazine).
 *
 * Uses Collections model for DB operations. PHP 8.3 compatible.
 */

declare(strict_types=1);

require_once("../shared/common.php");

$tab = "admin";
$nav = "collections";
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../model/Collections.php"));

// ── State ────────────────────────────────────────────────────────────────
$msg = '';
$msgClass = '';
$editMode = false;
$editData = null;

// ── Handle POST actions ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $collections = new Collections;

  // ── ADD ───────────────────────────────────────────────────────────
  if (($_POST['action'] ?? '') === 'add') {
    $desc = trim($_POST['description'] ?? '');
    $type = $_POST['type'] ?? 'Circulated';
    $defaultFlg = ($_POST['default_flg'] ?? 'N') === 'Y' ? 'Y' : 'N';

    if ($desc === '') {
      $msg = T("Description is required.");
      $msgClass = 'error';
    } else {
      $rec = [
        'description'  => $desc,
        'default_flg'  => $defaultFlg,
        'type'         => $type,
        'days_due_back'=> (int) ($_POST['days_due_back'] ?? 14),
        'minutes_due_back'       => 0,
        'regular_late_fee'       => (float) ($_POST['regular_late_fee'] ?? 0.00),
        'restock_threshold'      => (int) ($_POST['restock_threshold'] ?? 0),
        'due_date_calculator'    => $_POST['due_date_calculator'] ?? 'ask_me',
        'important_date'         => $_POST['important_date'] ?? null,
        'important_date_purpose' => $_POST['important_date_purpose'] ?? 'not enabled',
        'number_of_minutes_between_fee_applications' => 0,
        'number_of_minutes_in_grace_period'          => 0,
        'pre_closing_padding'                        => (int) ($_POST['pre_closing_padding'] ?? 30),
      ];

      [$id, $errors] = $collections->insert_el($rec);

      if (empty($errors)) {
        $msg = T("Collection") . " '" . H($desc) . "' " . T("has been added.");
        $msgClass = 'success';
      } else {
        $msg = is_array($errors) ? implode('; ', $errors) : (string) $errors;
        $msgClass = 'error';
      }
    }
  }

  // ── UPDATE ────────────────────────────────────────────────────────
  if (($_POST['action'] ?? '') === 'update') {
    $code = (int) ($_POST['code'] ?? 0);
    $desc = trim($_POST['description'] ?? '');
    $type = $_POST['type'] ?? 'Circulated';
    $defaultFlg = ($_POST['default_flg'] ?? 'N') === 'Y' ? 'Y' : 'N';

    if ($code === 0 || $desc === '') {
      $msg = T("Code and Description are required.");
      $msgClass = 'error';
    } else {
      $rec = [
        'code'         => $code,
        'description'  => $desc,
        'default_flg'  => $defaultFlg,
        'type'         => $type,
        'days_due_back'=> (int) ($_POST['days_due_back'] ?? 14),
        'minutes_due_back'       => 0,
        'regular_late_fee'       => (float) ($_POST['regular_late_fee'] ?? 0.00),
        'restock_threshold'      => (int) ($_POST['restock_threshold'] ?? 0),
        'due_date_calculator'    => $_POST['due_date_calculator'] ?? 'ask_me',
        'important_date'         => $_POST['important_date'] ?? null,
        'important_date_purpose' => $_POST['important_date_purpose'] ?? 'not enabled',
        'number_of_minutes_between_fee_applications' => 0,
        'number_of_minutes_in_grace_period'          => 0,
        'pre_closing_padding'                        => (int) ($_POST['pre_closing_padding'] ?? 30),
      ];

      $errors = $collections->update_el($rec);

      if (empty($errors)) {
        $msg = T("Collection") . " '" . H($desc) . "' " . T("has been updated.");
        $msgClass = 'success';
      } else {
        $msg = is_array($errors) ? implode('; ', $errors) : (string) $errors;
        $msgClass = 'error';
      }
    }
  }

  // ── DELETE ────────────────────────────────────────────────────────
  if (($_POST['action'] ?? '') === 'delete') {
    $code = (int) ($_POST['code'] ?? 0);
    if ($code > 0) {
      $collections->deleteOne($code);
      $msg = T("Collection") . " #{$code} " . T("has been deleted.");
      $msgClass = 'success';
    }
  }
}

// ── Edit mode: load existing collection data ─────────────────────────────
if (isset($_GET['edit'])) {
  $editCode = (int) $_GET['edit'];
  $collections = new Collections;
  $dm = $collections->getOne($editCode);
  if ($dm) {
    $editMode = true;
    $editData = $dm;

    // Fetch circ or dist detail fields
    if (($dm['type'] ?? '') === 'Circulated') {
      $circ = new CircCollections;
      $detail = $circ->getOne($editCode);
      if ($detail) {
        $editData = array_merge($editData, $detail);
      }
    } elseif (($dm['type'] ?? '') === 'Distributed') {
      $dist = new DistCollections;
      $detail = $dist->getOne($editCode);
      if ($detail) {
        $editData = array_merge($editData, $detail);
      }
    }
  }
}

// ── Fetch existing collections ───────────────────────────────────────────
$collections = new Collections;
$allCollections = $collections->getAllWithStats();

Page::header(['nav' => $tab . '/' . $nav, 'title' => T('Edit Collections')]);
?>

<h1><?php echo T("Manage Collections"); ?></h1>

<?php if ($msg): ?>
  <p class="<?php echo $msgClass; ?>"><?php echo H($msg); ?></p>
<?php endif; ?>

<!-- ── Add / Edit Collection Form ──────────────────────────────────────── -->
<section style="margin-bottom: 1rem; padding: 0 10px;">
  <h2><?php echo $editMode ? T("Update Collection") . " #" . (int) ($editData['code'] ?? 0) : T("Add New Collection"); ?></h2>

  <form method="post" action="" style="max-width: 600px;">
    <input type="hidden" name="action" value="<?php echo $editMode ? 'update' : 'add'; ?>" />

    <?php if ($editMode): ?>
      <input type="hidden" name="code" value="<?php echo (int) ($editData['code'] ?? 0); ?>" />
    <?php endif; ?>

    <fieldset>
      <legend><?php echo T("Collection Details"); ?></legend>

      <p>
        <label for="description"><?php echo T("Description"); ?>: <span class="reqd">*</span></label><br />
        <input type="text" id="description" name="description" size="40"
          placeholder="<?php echo T("e.g. Research Paper, Journal, Magazine"); ?>"
          value="<?php echo H($editData['description'] ?? ''); ?>"
          required aria-required="true" />
      </p>

      <p>
        <label for="type"><?php echo T("Collection Type"); ?>:</label><br />
        <select id="type" name="type" onchange="toggleTypeFields()">
          <option value="Circulated" <?php echo ($editData['type'] ?? 'Circulated') === 'Circulated' ? 'selected' : ''; ?>>
            <?php echo T("Circulated"); ?>
          </option>
          <option value="Distributed" <?php echo ($editData['type'] ?? '') === 'Distributed' ? 'selected' : ''; ?>>
            <?php echo T("Distributed"); ?>
          </option>
        </select>
      </p>

      <p>
        <label for="default_flg"><?php echo T("Set as Default"); ?>:</label>
        <select id="default_flg" name="default_flg">
          <option value="N" <?php echo ($editData['default_flg'] ?? 'N') === 'N' ? 'selected' : ''; ?>>
            <?php echo T("No"); ?>
          </option>
          <option value="Y" <?php echo ($editData['default_flg'] ?? '') === 'Y' ? 'selected' : ''; ?>>
            <?php echo T("Yes"); ?>
          </option>
        </select>
      </p>

      <!-- Circulation fields -->
      <div id="circFields">
        <p>
          <label for="days_due_back"><?php echo T("Loan Period (days)"); ?>:</label><br />
          <input type="number" id="days_due_back" name="days_due_back"
            value="<?php echo (int) ($editData['days_due_back'] ?? 14); ?>"
            min="0" max="365" size="4" />
        </p>

        <p>
          <label for="regular_late_fee"><?php echo T("Late Fee (per day)"); ?>:</label><br />
          <input type="number" id="regular_late_fee" name="regular_late_fee"
            value="<?php echo number_format((float) ($editData['regular_late_fee'] ?? 0.00), 2, '.', ''); ?>"
            min="0" max="99.99" step="0.01" size="6" />
        </p>

        <p>
          <label for="due_date_calculator"><?php echo T("Due Date Rule"); ?>:</label><br />
          <select id="due_date_calculator" name="due_date_calculator">
            <?php
            $calc = $editData['due_date_calculator'] ?? 'ask_me';
            $calcs = [
              'simple'           => T("Simple (add days)"),
              'at_midnight'      => T("At Midnight"),
              'before_we_close'  => T("Before We Close"),
              'ask_me'           => T("Manual Entry"),
            ];
            foreach ($calcs as $val => $label):
            ?>
              <option value="<?php echo $val; ?>" <?php echo $calc === $val ? 'selected' : ''; ?>>
                <?php echo $label; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </p>

        <p>
          <label for="important_date_purpose"><?php echo T("Important Date Rule"); ?>:</label><br />
          <select id="important_date_purpose" name="important_date_purpose">
            <?php
            $idp = $editData['important_date_purpose'] ?? 'not enabled';
            $purposes = [
              'not enabled'   => T("Not Enabled"),
              'ceiling_date'  => T("Ceiling Date"),
              'specific_date' => T("Specific Date"),
            ];
            foreach ($purposes as $val => $label):
            ?>
              <option value="<?php echo $val; ?>" <?php echo $idp === $val ? 'selected' : ''; ?>>
                <?php echo $label; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </p>

        <p>
          <label for="pre_closing_padding"><?php echo T("Minutes Before Closing"); ?>:</label><br />
          <input type="number" id="pre_closing_padding" name="pre_closing_padding"
            value="<?php echo (int) ($editData['pre_closing_padding'] ?? 30); ?>"
            min="0" max="240" size="4" />
        </p>
      </div>

      <!-- Distribution fields -->
      <div id="distFields" style="display: <?php echo ($editData['type'] ?? '') === 'Distributed' ? '' : 'none'; ?>;">
        <p>
          <label for="restock_threshold"><?php echo T("Restock Threshold"); ?>:</label><br />
          <input type="number" id="restock_threshold" name="restock_threshold"
            value="<?php echo (int) ($editData['restock_threshold'] ?? 0); ?>"
            min="0" size="4" />
        </p>
      </div>
			
    </fieldset>

    <p>
      <?php if ($editMode): ?>
        <input type="submit" value="<?php echo T("Update Collection"); ?>" class="button" />
        <a href="edit_collection.php" class="button" style="margin-left: 0.5rem;">
          <?php echo T("Cancel"); ?>
        </a>
      <?php else: ?>
        <input type="submit" value="<?php echo T("Add Collection"); ?>" class="button" />
      <?php endif; ?>
    </p>
  </form>
</section>

<!-- ── Existing Collections ────────────────────────────────────────────── -->
<section style="width: 700px; padding: 10px;">
  <h2><?php echo T("Existing Collections"); ?></h2>
  <?php
  $rows = [];
  foreach ($allCollections as $row) {
    $rows[] = $row;
  }
  if (empty($rows)):
  ?>
    <p><?php echo T("No collections have been defined."); ?></p>
  <?php else: ?>
    <table class="striped" style="width: 100%;">
      <thead>
        <tr>
          <th><?php echo T("Code"); ?></th>
          <th><?php echo T("Description"); ?></th>
          <th><?php echo T("Type"); ?></th>
          <th><?php echo T("Items"); ?></th>
          <th><?php echo T("Default"); ?></th>
          <th colspan="2"><?php echo T("Actions"); ?></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($rows as $row): ?>
        <tr>
          <td><?php echo (int) $row['code']; ?></td>
          <td><?php echo H($row['description'] ?? ''); ?></td>
          <td><?php echo H($row['type'] ?? ''); ?></td>
          <td><?php echo (int) ($row['count'] ?? 0); ?></td>
          <td><?php echo ($row['default_flg'] ?? 'N') === 'Y' ? T("Yes") : T("No"); ?></td>
          <td>
            <a href="edit_collection.php?edit=<?php echo (int) $row['code']; ?>"
              class="button small"><?php echo T("Edit"); ?></a>
          </td>
          <td>
            <form method="post" action=""
              onsubmit="return confirm('<?php echo T("Are you sure you want to delete"); ?>: <?php echo H(addslashes($row['description'] ?? '')); ?>?');"
              style="display: inline;">
              <input type="hidden" name="action" value="delete" />
              <input type="hidden" name="code" value="<?php echo (int) $row['code']; ?>" />
              <input type="submit" value="<?php echo T("Delete"); ?>" class="button small"
                style="background: #c33; color: #fff;" />
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<script>
function toggleTypeFields() {
  var type = document.getElementById('type').value;
  document.getElementById('circFields').style.display = (type === 'Circulated') ? '' : 'none';
  document.getElementById('distFields').style.display = (type === 'Distributed') ? '' : 'none';
}
// Run on load
toggleTypeFields();
</script>

<?php
require_once(REL(__FILE__, '../shared/footer.php'));