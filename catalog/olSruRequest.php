<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. -- F. Tumulak
 *
 * Execute an SRU searchRetrieve request.
 *
 * Returns:
 * [
 *     'success' => bool,
 *     'http_code' => int,
 *     'body' => string|null,
 *     'error' => string|null
 * ]
 */
function executeSruRequest(
    array $provider,
    string $query
): array {

    if (trim($query) === '') {
        return [
            'success' => false,
            'http_code' => 0,
            'body' => null,
            'error' => 'Empty SRU query.'
        ];
    }

    $endpoint = $provider['endpoint'] ?? '';

    if (!filter_var($endpoint, FILTER_VALIDATE_URL)) {
        return [
            'success' => false,
            'http_code' => 0,
            'body' => null,
            'error' => 'Invalid SRU endpoint.'
        ];
    }

    $request = $provider['request'] ?? [];

    $params = [
        'version' => $provider['version'] ?? '1.1',
        'operation' => 'searchRetrieve',
        'query' => $query,
        'startRecord' => $request['startRecord'] ?? 1,
        'maximumRecords' => $request['maximumRecords'] ?? 10,
        'recordSchema' => $provider['recordSchema'] ?? 'marcxml'
    ];

    $url = $endpoint . '?' . http_build_query(
        $params,
        '',
        '&',
        PHP_QUERY_RFC3986
    );

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => (int)($request['timeout'] ?? 30),
        CURLOPT_HTTPHEADER => [
            'Accept: application/xml'
        ],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2
    ]);

    $body = curl_exec($ch);

    $httpCode = (int)curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

    $error = curl_error($ch);

    curl_close($ch);

    if ($body === false) {
        return [
            'success' => false,
            'http_code' => $httpCode,
            'body' => null,
            'error' => $error ?: 'Unknown cURL error.'
        ];
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        return [
            'success' => false,
            'http_code' => $httpCode,
            'body' => $body,
            'error' => 'SRU server returned HTTP ' . $httpCode
        ];
    }

    return [
        'success' => true,
        'http_code' => $httpCode,
        'body' => $body,
        'error' => null
    ];
}
