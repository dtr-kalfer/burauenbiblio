<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. -- F. Tumulak
 *
 * Convert existing BurauenBiblio search form
 * fields into generic SRU search criteria.
 */
function getSruSearchCriteria(array $input): array
{
    $fieldMap = [
        '4'    => 'title',
        '7'    => 'isbn',
        '8'    => 'issn',
        '9'    => 'lccn',
        '1004' => 'author',
        '1016' => 'keyword',
        '1018' => 'publisher',
        '1031' => 'media',
        '59'   => 'publication_location',
        '31'   => 'publication_date',
    ];

    $criteria = [];

    for ($i = 1; $i <= 5; $i++) {

        $valueKey = $i === 1
            ? 'lookupVal'
            : 'lookupVal' . $i;

        $fieldKey = $i === 1
            ? 'srchBy'
            : 'srchBy' . $i;

        $value = trim($input[$valueKey] ?? '');
        $field = (string)($input[$fieldKey] ?? '');

        if ($value === '' || !isset($fieldMap[$field])) {
            continue;
        }

        $criteria[] = [
            'field' => $fieldMap[$field],
            'term'  => $value,
            'operator' => 'AND'
        ];
    }

    return $criteria;
}