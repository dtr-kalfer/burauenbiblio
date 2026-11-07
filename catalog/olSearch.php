<?php

require_once(REL(__FILE__, '../catalog/olSrchVals.php'));
// Merge submitted search criteria with server-generated data.
// Server-generated values take precedence over POST values.
$searchVars = array_merge($_POST, $postVars);

/*
 * ---------------------------------------------------------
 * 1. Initialize search variables
 * ---------------------------------------------------------
 */

$numHosts = (int)($postVars['numHosts'] ?? 0);

$hosts = $postVars['hosts'] ?? [];

$temp = $postVars['opts'] ?? [];
$opts = $temp[0] ?? [];

$maxHits = (int)($opts['maxHits'] ?? 25);

if ($maxHits <= 0) {
    $maxHits = 25;
}

$ttlHits = 0;

$hits = [];
$id = [];
$errMsg = [];
$rslt = [];

$hasYazHosts = false;
$hasSruHosts = false;


/*
 * ---------------------------------------------------------
 * 2. Determine which search engines are needed
 * ---------------------------------------------------------
 */

for ($h = 0; $h < $numHosts; $h++) {

    $host = $hosts[$h] ?? [];

    $service = strtoupper(
        trim($host['service'] ?? '')
    );

    if ($service === 'Z3950') {
        $hasYazHosts = true;

    } elseif ($service === 'SRU') {
        $hasSruHosts = true;
    }
}


/*
 * ---------------------------------------------------------
 * 3. Load and execute the Z39.50 search engine
 * ---------------------------------------------------------
 */

if ($hasYazHosts) {

    if (function_exists('yaz_connect')) {

        require_once(
            REL(__FILE__, '../catalog/olYazSrch.php')
        );

    } else {

        // SRU remains usable even if PHP YAZ is unavailable.
        for ($h = 0; $h < $numHosts; $h++) {

            $host = $hosts[$h] ?? [];

            $service = strtoupper(
                trim($host['service'] ?? '')
            );

            if ($service === 'Z3950') {
                $errMsg[$h] =
                    'PHP YAZ extension is not available.';
            }
        }
    }
}


/*
 * ---------------------------------------------------------
 * 4. Load the new cURL SRU search engine
 * ---------------------------------------------------------
 */

if ($hasSruHosts) {

    require_once(
        REL(__FILE__, '../catalog/olSruSearch.php')
    );
}


/*
 * ---------------------------------------------------------
 * 5. Process each host independently
 * ---------------------------------------------------------
 */

for ($h = 0; $h < $numHosts; $h++) {

    $host = $hosts[$h] ?? [];

    $service = strtoupper(
        trim($host['service'] ?? '')
    );


    /*
     * Z39.50 host
     */

    if ($service === 'Z3950') {

        if (function_exists('yaz_connect')) {

            $rslt[$h] = doOneHost(
                $h,
                $hits,
                $id
            );

        } else {

            $rslt[$h] = [
                1 => [
                    'err' => $errMsg[$h] ??
                        'PHP YAZ extension is not available.'
                ]
            ];
        }

        continue;
    }


    /*
     * SRU host
     */

    if ($service === 'SRU') {

        $sruResult = doSruHostSearch($host, $searchVars, $maxHits);
				
				

        // Add server-reported total hits only once.
        $ttlHits += (int)$sruResult['total_hits'];

        if ($sruResult['success']) {

            $rslt[$h] = $sruResult['records'];

            // A valid search with no matching records.
            if (empty($rslt[$h])) {
                $rslt[$h] = [
                    1 => [
                        'err' => 'No records returned by SRU provider.'
                    ]
                ];
            }

        } else {

            $error = $sruResult['error'] ??
                'SRU search failed.';

            $errMsg[$h] = $error;

            $rslt[$h] = [
                1 => [
                    'err' => $error
                ]
            ];
        }

        continue;
    }


    /*
     * Unsupported service
     */

    $error = 'Unsupported search service: ' .
        ($service !== '' ? $service : 'Unknown');

    $errMsg[$h] = $error;

    $rslt[$h] = [
        1 => [
            'err' => $error
        ]
    ];
}


/*
 * ---------------------------------------------------------
 * 6. Prepare common response metadata
 * ---------------------------------------------------------
 */

$rcd = [];

$rcd['ttlHits'] = $ttlHits;

$srch1 = [
    'byName' => $srchByName ?? 0,
    'lookupVal' => $lookupVal ?? ''
];

$srch2 = [
    'byName' => $srchByName2 ?? 0,
    'lookupVal' => $lookupVal2 ?? ''
];

$rcd['srch1'] = json_encode($srch1);
$rcd['srch2'] = json_encode($srch2);


/*
 * ---------------------------------------------------------
 * 7. No results
 * ---------------------------------------------------------
 */

if ($ttlHits === 0) {

    $collectedErrors = [];

    for ($i = 0; $i < $numHosts; $i++) {

        if (!empty($errMsg[$i])) {

            $hName = $hosts[$i]['name'] ??
                "Host #$i";

            $collectedErrors[] =
                $hName . ': ' . $errMsg[$i];
        }
    }

    if (!empty($collectedErrors)) {

        $rcd['msg'] = implode(
            '<br />',
            $collectedErrors
        );

    } else {

        $rcd['msg'] = T('Nothing Found');
    }

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($rcd);
    exit;
}


/*
 * ---------------------------------------------------------
 * 8. Too many results
 * ---------------------------------------------------------
 */

if ($ttlHits > $maxHits) {

    $rcd['msg'] = T('lookup_tooManyHits');
    $rcd['msg2'] = T('lookup_refineSearch');

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($rcd);
    exit;
}


/*
 * ---------------------------------------------------------
 * 9. Successful search response
 * ---------------------------------------------------------
 */

$_POST['data'] = $rslt;
$_POST['ttlHits'] = $ttlHits;
$_POST['numHosts'] = $numHosts;


/*
 * ---------------------------------------------------------
 * 10. JSON encoding
 * ---------------------------------------------------------
 */

$json = json_encode($_POST);

if ($json === false) {

    error_log(
        'BurauenBiblio JSON error: ' .
        json_last_error_msg()
    );

    header('Content-Type: text/plain; charset=UTF-8');

    echo 'JSON encoding failed: ';
    echo json_last_error_msg();

    exit;
}

header('Content-Type: application/json; charset=UTF-8');

echo $json;
exit;

