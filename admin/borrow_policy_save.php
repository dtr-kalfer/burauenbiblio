<?php
require_once("../shared/common.php");
require_once("../shared/logincheck.php");

	require_once __DIR__ . '/../autoload.php';
	use Collection\MyCollection;

$code = trim($_POST['code'] ?? '');
$days = $_POST['days_due_back'] ?? null;
$fee  = $_POST['regular_late_fee'] ?? null;

if ($code === '' || $days === null || $fee === null) {
    header("Location: borrow_policy.php?msg=Missing+required+fields");
    exit;
}

try {
    $collection = new MyCollection();

    $affected = $collection->updateBorrowPolicy(
        $code,
        (int)$days,
        (float)$fee
    );

    if ($affected === 0) {
        header("Location: borrow_policy.php?msg=No+collection+updated+(invalid+code?)");
    } else {
        header("Location: borrow_policy.php?msg=Policy+update+successful!");
    }

} catch (Exception $e) {
    // You can log this instead if you prefer
    header("Location: borrow_policy.php?msg=Error:+".$e->getMessage());
}
