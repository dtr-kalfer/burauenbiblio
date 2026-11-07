<?php

/**
 * Parse an SRU searchRetrieve response containing MARCXML records.
 *
 * Return format:
 * [
 *     0 => number of records parsed,
 *     1 => normalized MARC records,
 *     2 => total records reported by the SRU server,
 *     3 => diagnostic messages
 * ]
 */
function get_marc_fields_from_sru(string $xml): array
{
    $marc = [];
    $totalHits = 0;
    $diagnostics = [];

    if (trim($xml) === '') {
        return [0, [], 0, ['Empty SRU response.']];
    }

    $previousErrorMode = libxml_use_internal_errors(true);

    try {
        $dom = new DOMDocument();

        if (!$dom->loadXML($xml, LIBXML_NONET)) {
            $errors = libxml_get_errors();

            foreach ($errors as $error) {
                $diagnostics[] = trim($error->message);
            }

            return [0, [], 0, $diagnostics];
        }

        $xpath = new DOMXPath($dom);

        // SRU response metadata.
        $countNode = $xpath->query(
            '//*[local-name()="numberOfRecords"]'
        )->item(0);

        if ($countNode) {
            $totalHits = (int)trim($countNode->textContent);
        }

        // SRU diagnostics, if any.
        $diagnosticNodes = $xpath->query(
            '//*[local-name()="diagnostic"]'
        );

        foreach ($diagnosticNodes as $diagnostic) {
            $uri = '';
            $message = '';

            foreach ($diagnostic->childNodes as $child) {
                if ($child->nodeType !== XML_ELEMENT_NODE) {
                    continue;
                }

                switch ($child->localName) {
                    case 'uri':
                        $uri = trim($child->textContent);
                        break;

                    case 'message':
                        $message = trim($child->textContent);
                        break;
                }
            }

            $diagnostics[] = trim($uri . ' ' . $message);
        }

        // Locate the SRU records, not echoedSearchRetrieveRequest.
        $recordNodes = $xpath->query(
            '//*[local-name()="records"]/*[local-name()="record"]'
        );

        foreach ($recordNodes as $recordNode) {

            // The actual MARCXML record is inside recordData.
            $marcNode = null;

            foreach ($recordNode->childNodes as $child) {
                if (
                    $child->nodeType === XML_ELEMENT_NODE &&
                    $child->localName === 'recordData'
                ) {
                    foreach ($child->childNodes as $dataChild) {
                        if ($dataChild->nodeType === XML_ELEMENT_NODE) {
                            $marcNode = $dataChild;
                            break;
                        }
                    }
                }

                if ($marcNode !== null) {
                    break;
                }
            }

            if (!$marcNode) {
                continue;
            }

            $record = [];
            $subjectCount = 0;
            $lastSubjectKey = null;
            $fallback700a = '';

            foreach ($marcNode->childNodes as $field) {

                if ($field->nodeType !== XML_ELEMENT_NODE) {
                    continue;
                }

                // MARC leader.
                if ($field->localName === 'leader') {
                    $record['LDR'] = $field->textContent;
                    continue;
                }

                // MARC control fields: 001, 005, 008, etc.
                if ($field->localName === 'controlfield') {
                    $tag = $field->getAttribute('tag');

                    if ($tag !== '') {
                        $record[$tag] = $field->textContent;
                    }

                    continue;
                }

                if ($field->localName !== 'datafield') {
                    continue;
                }

                $tag = $field->getAttribute('tag');

                if ($tag === '') {
                    continue;
                }

                // Each 650 field gets its own subject key.
                if ($tag === '650') {
                    $lastSubjectKey = '650a' . $subjectCount;
                    $subjectCount++;
                }

                foreach ($field->childNodes as $subfield) {

                    if (
                        $subfield->nodeType !== XML_ELEMENT_NODE ||
                        $subfield->localName !== 'subfield'
                    ) {
                        continue;
                    }

                    $code = $subfield->getAttribute('code');

                    if ($code === '') {
                        continue;
                    }

$value = trim($subfield->textContent);

                    if ($value === '') {
                        continue;
                    }

                    $fieldId = $tag . $code;

                    // Preserve the existing 700$a author fallback.
                    if ($fieldId === '700a') {
                        if ($fallback700a === '') {
                            $fallback700a = $value;
                        }
                    }

                    // 538$a is treated as 520$a by the legacy parser.
                    if ($tag === '538' && $code === 'a') {
                        $fieldId = '520a';
                    }

                    // Subject subdivisions belong to their 650$a.
                    if ($tag === '650') {

                        if ($code === 'a') {
                            $record[$lastSubjectKey] =
                                isset($record[$lastSubjectKey])
                                ? $record[$lastSubjectKey] . '; ' . $value
                                : $value;

                        } elseif ($lastSubjectKey !== null) {
                            $record[$lastSubjectKey] .= ', ' . $value;
                        }

                        continue;
                    }

                    // Preserve ordinary repeated-subfield behavior.
                    if (!isset($record[$fieldId])) {
                        $record[$fieldId] = $value;
                    } else {
                        $record[$fieldId] .= '; ' . $value;
                    }
                }
            }

            // Use 700$a only when the record has no 100$a.
            if (
                empty($record['100a']) &&
                $fallback700a !== ''
            ) {
                $record['100a'] = $fallback700a;
            }

            // Fallback for call numbers.
            if (empty($record['099a'])) {

                if (!empty($record['090a'])) {
                    $record['099a'] = $record['090a'];

                    if (!empty($record['090b'])) {
                        $record['099a'] .= ' ' . $record['090b'];
                    }

                } elseif (!empty($record['050a'])) {
                    $record['099a'] = $record['050a'];

                    if (!empty($record['050b'])) {
                        $record['099a'] .= ' ' . $record['050b'];
                    }

                } elseif (!empty($record['852h'])) {
                    $record['099a'] = $record['852h'];
                }
            }

            $marc[] = $record;
        }

        return [
            count($marc),
            $marc,
            $totalHits,
            $diagnostics
        ];

    } finally {
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorMode);
    }
}
