<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
require_once("../shared/common.php");
$tab = "circulation";
$nav = "analytics/missingbooks";

require_once(REL(__FILE__, "../shared/logincheck.php"));

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

require_once("../catalog/class/Qtest.php");

$mypass = new Qtest;

try {
    $dsn = 'mysql:host=' . $mypass->getDSN2("host") . ';dbname=' . $mypass->getDSN2("database") . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $mypass->getDSN2("username"), $mypass->getDSN2("pwd"));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}

$sql = "
    SELECT
        bc.bibid,
        bc.barcode_nmbr,
        bc.copy_desc,
        MAX(CASE WHEN bf.tag='099' AND bs.subfield_cd='a' THEN bs.subfield_data END) AS call_number,
        MAX(CASE WHEN bf.tag='100' AND bs.subfield_cd='a' THEN bs.subfield_data END) AS author,
        MAX(CASE WHEN bf.tag='245' AND bs.subfield_cd='a' THEN bs.subfield_data END) AS title
    FROM biblio_copy bc
    LEFT JOIN biblio_field bf ON bf.bibid = bc.bibid
    LEFT JOIN biblio_subfield bs ON bs.fieldid = bf.fieldid
    WHERE ucase(bc.copy_desc) = 'ITEM-IS-MISSING'
    GROUP BY bc.bibid, bc.barcode_nmbr, bc.copy_desc
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['export']) && $_GET['export'] === 'json') {
    header('Content-Type: application/json');
    echo json_encode($rows, JSON_PRETTY_PRINT);
    exit;
}
?>
<style>
/* Clickable bib link styling from taggedItems.php */
.tagged-link {
    color: #1a56db;
    text-decoration: none;
    font-weight: 500;
    border-bottom: 1px dashed #1a56db;
    transition: color 0.15s, border-color 0.15s;
}
.tagged-link:hover {
    color: #0d3b9e;
    border-bottom-style: solid;
}
.tagged-link::after {
    content: " ↗";
    font-size: 0.75em;
    opacity: 0.5;
}
</style>

<section>
  <h2>Missing Books Report</h2>

  <a href="missing_books_export.php" target="_blank">
    Export to JSON
  </a>

  <table id="missingBooksTable">
    <thead>
      <tr>
        <th>bibid</th>
        <th>barcode_nmbr</th>
        <th>copy_desc</th>
        <th>call_number</th>
        <th>author</th>
        <th>title</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $row):
                $bibid   = $row['bibid'] ?? '';
                $srchUrl = "../catalog/srchForms.php?bibid=" . urlencode((string) $bibid);
            ?>
        <tr>
          <td>
                        <a href="<?= $srchUrl ?>" class="tagged-link" title="View full record">
                            <?= htmlspecialchars($bibid) ?>
                        </a>
                    </td>
          <td><?= htmlspecialchars($row['barcode_nmbr']) ?></td>
          <td><?= htmlspecialchars(strtoupper($row['copy_desc'])) ?></td>
          <td><?= htmlspecialchars($row['call_number']) ?></td>
          <td><?= htmlspecialchars($row['author']) ?></td>
          <td>
                        <a href="<?= $srchUrl ?>" class="tagged-link" title="View full record">
                            <?= htmlspecialchars($row['title']) ?>
                        </a>
                    </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>