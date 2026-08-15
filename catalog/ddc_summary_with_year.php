<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */


// Nav::node('admin/analytics/ddc_summary', T("ddc_summary"),'../catalog/ddc_summary_with_year.php');	
require_once("../shared/common.php");
$tab = "admin";
$nav = "analytics/ddc_summary";

require_once(REL(__FILE__, "../shared/logincheck.php"));

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

require_once __DIR__ . '/../autoload.php';

use DDC\DDCSummary;

$summary = new DDCSummary();
$rows = $summary->generate_summary();

$title = "<h2>📚 DDC Holdings Summary by Copyright Year</h2>";
?>
<style>
#ddc_summary_section {
    width: 850px;
    background-color: #F5DEB3;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

#ddc_summary_section table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
}

#ddc_summary_section th,
#ddc_summary_section td {
    border: 1px solid #999;
    padding: 6px 10px;
    text-align: center;
}

#ddc_summary_section th {
    background-color: #e6c280;
}

#ddc_summary_section td.subject-col {
    text-align: left;
    font-weight: 500;
}

#ddc_summary_section tr.section-header td {
    background-color: #f0f0f0;
    font-weight: bold;
    text-align: left;
}

#ddc_summary_section tr.total-row {
    font-weight: bold;
    background-color: #fdf2d0;
}

.export-links {
    margin-bottom: 12px;
}

.export-links a {
    display: inline-block;
    padding: 5px 10px;
    border: 1px solid #333;
    background: #fff;
    text-decoration: none;
    color: #000;
    font-weight: bold;
    border-radius: 3px;
}

.export-links a:hover {
    background: #eee;
}
</style>

<section id="ddc_summary_section">
    <?= $title; ?>

    <div class="export-links">
        <a href="./ddc_summary_export.php" target="_blank">📥 Export to CSV</a>
    </div>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th rowspan="2">SUBJECT AREA</th>
                <th colspan="2">BOOK HOLDINGS</th>
                <th colspan="2">Copyrighted within last 10 yrs</th>
                <th colspan="2">Copyrighted within last 5 yrs</th>
            </tr>
            <tr>
                <th>Titles</th>
                <th>Volumes</th>
                <th>Titles</th>
                <th>Volumes</th>
                <th>Titles</th>
                <th>Volumes</th>
            </tr>
        </thead>
        <tbody>
            <tr class="section-header">
                <td colspan="7">I. BOOKS</td>
            </tr>
            <?php
            $tot_titles = 0;
            $tot_vols = 0;
            $tot_t10 = 0;
            $tot_v10 = 0;
            $tot_t5 = 0;
            $tot_v5 = 0;

            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $tot_titles += (int)$row['titles'];
                    $tot_vols   += (int)$row['volumes'];
                    $tot_t10    += (int)$row['titles_last_10_years'];
                    $tot_v10    += (int)$row['volumes_last_10_years'];
                    $tot_t5     += (int)$row['titles_last_5_years'];
                    $tot_v5     += (int)$row['volumes_last_5_years'];
                    ?>
                    <tr>
                        <td class="subject-col"><?= htmlspecialchars($row['subject_area']) ?></td>
                        <td><?= number_format($row['titles']) ?></td>
                        <td><?= number_format($row['volumes']) ?></td>
                        <td><?= number_format($row['titles_last_10_years']) ?></td>
                        <td><?= number_format($row['volumes_last_10_years']) ?></td>
                        <td><?= number_format($row['titles_last_5_years']) ?></td>
                        <td><?= number_format($row['volumes_last_5_years']) ?></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="7" style="text-align:center; font-style:italic;">No records found. Please ensure the DDC table has been processed.</td>
                </tr>
                <?php
            }
            ?>
            <tr class="total-row">
                <td class="subject-col">TOTAL</td>
                <td><?= number_format($tot_titles) ?></td>
                <td><?= number_format($tot_vols) ?></td>
                <td><?= number_format($tot_t10) ?></td>
                <td><?= number_format($tot_v10) ?></td>
                <td><?= number_format($tot_t5) ?></td>
                <td><?= number_format($tot_v5) ?></td>
            </tr>
        </tbody>
    </table>
</section>