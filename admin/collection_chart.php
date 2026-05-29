<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details. --F.Tumulak
	 */

require_once("../shared/common.php");

$tab = "admin/analytics";
$nav = "collection_chart";

require_once(REL(__FILE__,
"../shared/logincheck.php"));

Page::header([
    'nav'=>$tab.'/'.$nav,
    'title'=>''
]);

require_once __DIR__ . '/../autoload.php';

use LibraryAnalytics\CollectionAnalytics;

$start = $_GET['start_month'] ?? date('Y-01');
$end   = $_GET['end_month'] ?? date('Y-m');

function isValidMonthFormat($month){
    return preg_match(
        '/^\d{4}-(0[1-9]|1[0-2])$/',
        $month
    );
}

if(
    !isValidMonthFormat($start)
    ||
    !isValidMonthFormat($end)
){
    die("Invalid month format.");
}

$start_date = $start.'-01';
$end_date =
date('Y-m-t',
strtotime($end.'-01'));

$db = new CollectionAnalytics;

$result =
$db->getWeeklyGrowthWithCopies(
    $start_date,
    $end_date
);

// for test purposes
// echo "<pre>";
// print_r($result);
// echo "</pre>";

$labels = [];
$bib_data = [];
$copy_data = [];

foreach($result as $row){

// add a '0' padding for week number less than 10 --F. Tumulak
$labels[] =
    'W'.str_pad(
        $row['week_no'],
        2,
        '0',
        STR_PAD_LEFT
    )
    .' '
    .$row['month_short']
    .' '
    .$row['year_added'];

    $bib_data[] =
        (int)$row['bib_total'];

    $copy_data[] =
        (int)$row['copy_total'];
}
?>
<style>
.chart-container-collection{
    width:900px;
    height:450px;
    margin:auto;
}
</style>
<script src="../circ/js/chart.js"></script>
<form method="GET">

    <label>Start Month:</label>

    <input
        type="month"
        name="start_month"
        value="<?= htmlspecialchars($start) ?>"
        required
    >

    <label>End Month:</label>

    <input
        type="month"
        name="end_month"
        value="<?= htmlspecialchars($end) ?>"
        required
    >

    <button type="submit">
        Update Chart
    </button>

</form>
<section
class="chart-container-collection">

<canvas
id="collectionChart">
</canvas>

</section>

<script>

const ctx =
document
.getElementById(
'collectionChart'
)
.getContext('2d');

const elements = document.getElementsByClassName("mylibname");
// Access the first element
const firstElement = elements[0]; 
var libraryName = firstElement ? firstElement.textContent.trim() : 'Library';

new Chart(ctx, {

    type:'line',

    data:{

        labels:
        <?= json_encode($labels) ?>,

        datasets:[

        {

            label:
            'Bibliographic Records',

            data:
            <?= json_encode($bib_data) ?>,

            borderColor:
            '#3498db',

            tension:0.3
        },

        {

            label:
            'Copies',

            data:
            <?= json_encode($copy_data) ?>,

            borderColor:
            '#2ecc71',

            tension:0.3
        }

        ]
    },

    options:{

        responsive:true,

        plugins:{

            legend:{
                position:'top'
            },

            title:{
                display:true,

                text:
                libraryName + ' Collection Growth'
					
            }

        },

        scales:{

            y:{
                beginAtZero:true,

                title:{
                    display:true,

                    text:
                    'Items Added'
                }
            }

        }

    }

});

</script>
