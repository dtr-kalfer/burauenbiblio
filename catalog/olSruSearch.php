<?php

/**
 * BurauenBiblio SRU Search Handler
 *
 * Coordinates provider lookup, CQL construction,
 * cURL execution, and MARCXML parsing.
 *
 * This is the cURL-based SRU implementation.
 */

/**
 * Search one SRU host.
 *
 * @param array $host     lookup_hosts database row
 * @param array $postVars Search criteria from the frontend
 * @param int|null $maxHits Optional maximum records override
 *
 * @return array
 */
function doSruHostSearch(
    array $host,
    array $postVars,
    ?int $maxHits = null
): array {

    $result = [
        'success'    => false,
        'query'      => '',
        'hits'       => 0,
        'total_hits' => 0,
        'records'    => [],
        'diagnostics'=> [],
        'warnings'   => [],
        'error'      => null
    ];

    try {

        /*
         * 1. Identify the SRU provider.
         *
         * The provider JSON uses lookup_hosts.id
         * as its host_id.
         */

        $hostId = (int)($host['id'] ?? 0);

        if ($hostId <= 0) {
            throw new RuntimeException(
                'Invalid SRU host ID.'
            );
        }

        $provider = getSruProviderByHostId($hostId);

        if (!$provider) {
            throw new RuntimeException(
                'No enabled SRU provider configuration found for host ID ' .
                $hostId
            );
        }

        /*
         * 2. Build provider-specific CQL.
         */

				// error_log(
						// 'SRU Incoming POST Criteria: ' .
						// print_r([
								// 'lookupVal'  => $_POST['lookupVal'] ?? null,
								// 'srchBy'     => $_POST['srchBy'] ?? null,
								// 'lookupVal2' => $_POST['lookupVal2'] ?? null,
								// 'srchBy2'    => $_POST['srchBy2'] ?? null,
								// 'lookupVal3' => $_POST['lookupVal3'] ?? null,
								// 'srchBy3'    => $_POST['srchBy3'] ?? null,
								// 'lookupVal4' => $_POST['lookupVal4'] ?? null,
								// 'srchBy4'    => $_POST['srchBy4'] ?? null,
								// 'lookupVal5' => $_POST['lookupVal5'] ?? null,
								// 'srchBy5'    => $_POST['srchBy5'] ?? null,
						// ], true)
				// );

        $queryResult = buildSruQuery(
            $postVars,
            $provider
        );

				// error_log(
						// 'SRU Query Builder Debug: ' .
						// print_r([
								// 'provider' => $provider['name'] ?? '',
								// 'post_keys' => array_keys($postVars),
								// 'query_result' => $queryResult,
						// ], true)
				// );

        $query = trim($queryResult['query'] ?? '');

        $result['query'] = $query;
        $result['warnings'] = $queryResult['warnings'] ?? [];

        if ($query === '') {
            throw new RuntimeException(
                'Unable to construct an SRU query.'
            );
        }

        /*
         * 3. Apply the configured request limit.
         *
         * The global maxHits option can override
         * the provider's JSON maximumRecords value.
         */

        if ($maxHits !== null && $maxHits > 0) {

            if (!isset($provider['request'])) {
                $provider['request'] = [];
            }

            $provider['request']['maximumRecords'] = $maxHits;
        }

        /*
         * 4. Execute the SRU request using cURL.
         */

        $response = executeSruRequest(
            $provider,
            $query
        );

        if (empty($response['success'])) {

            throw new RuntimeException(
                $response['error'] ??
                'SRU request failed.'
            );
        }

        if (empty($response['body'])) {
            throw new RuntimeException(
                'The SRU provider returned an empty response.'
            );
        }

        /*
         * 5. Parse the SRU MARCXML response.
         *
         * Return format:
         * [parsed count, records, total hits, diagnostics]
         */

        list(
            $parsedCount,
            $records,
            $totalHits,
            $diagnostics
        ) = get_marc_fields_from_sru(
            $response['body']
        );

        $result['hits'] = $parsedCount;
        $result['records'] = $records;
        $result['total_hits'] = $totalHits;
        $result['diagnostics'] = $diagnostics;

        /*
         * 6. Consider a response with SRU diagnostics
         * and no records a failed search.
         */

        if ($parsedCount === 0 && !empty($diagnostics)) {

            $result['error'] = implode(
                '; ',
                $diagnostics
            );

            return $result;
        }

        $result['success'] = true;

    } catch (Throwable $e) {

        $result['error'] = $e->getMessage();

    }

    return $result;
}


/*
 * Load the SRU components.
 */

require_once __DIR__ . '/olSruConfig.php';
require_once __DIR__ . '/olSruQuery.php';
require_once __DIR__ . '/olSruRequest.php';
require_once __DIR__ . '/olSruParser.php';

?>
