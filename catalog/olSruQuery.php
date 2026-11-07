<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. -- F. Tumulak
 *
 * Convert BurauenBiblio search field IDs into canonical SRU fields.
 */
function getSruFieldMap(): array
{
    return [
        '4'    => 'title',
        '7'    => 'isbn',
        '8'    => 'issn',
        '9'    => 'lccn',
        '1004' => 'author',
        '1016' => 'keyword',
        '1018' => 'publisher',
        '1031' => 'media',
        '59'   => 'publication_location',
        '31'   => 'publication_date'
    ];
}

/**
 * Build CQL from the existing BurauenBiblio search form.
 *
 * Returns:
 * [
 *   'query' => 'dc.title any "plants"',
 *   'criteria' => [...],
 *   'warnings' => [...]
 * ]
 */
function buildSruQuery(array $post, array $provider): array
{
    $fieldMap = getSruFieldMap();

    $criteria = [];
    $warnings = [];

    for ($i = 1; $i <= 5; $i++) {

        $valueKey = $i === 1
            ? 'lookupVal'
            : 'lookupVal' . $i;

        $fieldKey = $i === 1
            ? 'srchBy'
            : 'srchBy' . $i;

        $value = trim($post[$valueKey] ?? '');
        $fieldId = (string)($post[$fieldKey] ?? '');

        if ($value === '' || $fieldId === '') {
            continue;
        }

        if (!isset($fieldMap[$fieldId])) {
            $warnings[] = "Unknown search field: " . $fieldId;
            continue;
        }

        $canonical = $fieldMap[$fieldId];

        $index = $provider['indexes'][$canonical] ?? null;

        if (empty($index)) {
            $warnings[] = "Unsupported provider field: " . $canonical;
            continue;
        }

        $relation = $provider['relations'][$canonical]
            ?? $provider['relations']['default']
            ?? 'any';

        // Escape CQL special characters inside quoted strings.
        $value = str_replace(
            ['\\', '"'],
            ['\\\\', '\\"'],
            $value
        );

        $criteria[] = $index . ' ' . $relation . ' "' . $value . '"';
    }

    return [
        'query' => implode(' and ', $criteria),
        'criteria' => $criteria,
        'warnings' => $warnings
    ];
}
