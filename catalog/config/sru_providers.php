<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 * BurauenBiblio SRU Provider Configuration
 * Private configuration - do not expose through HTTP. 
 * you may add your own compatible SRU config here, be sure to 
 * match the host_id found on the 'Online hosts' nav. menu -- F. Tumulak
 */

return json_decode(<<<'JSON'
{
    "providers": {
        "library_of_congress": {
            "host_id": 27,
            "name": "Library of Congress",
            "enabled": true,
            "protocol": "SRU",
            "transport": "CURL",
            "endpoint": "https://lx2.loc.gov/sru/lcdb",
            "version": "1.1",
            "recordSchema": "marcxml",
            "indexes": {
                "title": "dc.title",
                "author": "dc.author",
                "creator": "dc.creator",
                "subject": "dc.subject",
                "publisher": "dc.publisher",
                "publication_location": "bath.geographicName",
                "isbn": "bath.isbn",
                "issn": "bath.issn",
                "lccn": "bath.lccn",
                "keyword": "cql.anywhere",
                "record": null,
                "media": null
            },
            "relations": {
                "default": "=",
                "title": "=",
                "author": "=",
                "creator": "=",
                "subject": "=",
                "publisher": "=",
                "publication_location": "=",
                "isbn": "=",
                "issn": "=",
                "lccn": "=",
                "keyword": "="
            },
            "request": {
                "startRecord": 1,
                "maximumRecords": 10,
                "timeout": 30
            },
            "capabilities": {
                "supports_explain": true,
                "supports_marcxml": true,
                "supports_isbn": true,
                "supports_issn": true,
                "supports_authority": false,
                "supports_pagination": true
            }
        },
        "deutsche_nationalbibliothek": {
            "host_id": 29,
            "name": "Deutsche Nationalbibliothek (DNB)",
            "enabled": true,
            "protocol": "SRU",
            "transport": "CURL",
            "endpoint": "https://services.dnb.de/sru/dnb",
            "version": "1.1",
            "recordSchema": "MARC21-xml",
            "indexes": {
                "title": "tit",
                "author": "aut",
                "creator": "aut",
                "subject": "sw",
                "publisher": "pub",
                "publication_location": "pla",
                "isbn": "isbn",
                "issn": "issn",
                "lccn": null,
                "keyword": "woe",
                "record": null,
                "media": null
            },
            "relations": {
                "default": "=",
                "title": "=",
                "author": "=",
                "creator": "=",
                "subject": "=",
                "publisher": "=",
                "publication_location": "=",
                "isbn": "=",
                "issn": "=",
                "lccn": "=",
                "keyword": "="
            },
            "request": {
                "startRecord": 1,
                "maximumRecords": 10,
                "timeout": 30
            },
            "capabilities": {
                "supports_explain": true,
                "supports_marcxml": true,
                "supports_isbn": true,
                "supports_issn": true,
                "supports_authority": false,
                "supports_pagination": true
            }
        },
        "national_library_of_finland_melinda": {
            "host_id": 30,
            "name": "National Library of Finland / Melinda",
            "enabled": true,
            "protocol": "SRU",
            "transport": "CURL",
            "endpoint": "https://sru.api.melinda.kansalliskirjasto.fi/bib",
            "version": "1.2",
            "recordSchema": "marcxml",
            "indexes": {
                "title": "dc.title",
                "author": "dc.creator",
                "creator": "dc.creator",
                "subject": "dc.subject",
                "publisher": "dc.publisher",
                "publication_location": null,
                "isbn": "bath.isbn",
                "issn": "bath.issn",
                "lccn": null,
                "keyword": "cql.anywhere",
                "record": "rec.id",
                "media": null
            },
            "relations": {
                "default": "=",
                "title": "=",
                "author": "=",
                "creator": "=",
                "subject": "=",
                "publisher": "=",
                "publication_location": "=",
                "isbn": "=",
                "issn": "=",
                "lccn": "=",
                "keyword": "="
            },
            "request": {
                "startRecord": 1,
                "maximumRecords": 10,
                "timeout": 30
            },
            "capabilities": {
                "supports_explain": true,
                "supports_marcxml": true,
                "supports_isbn": true,
                "supports_issn": true,
                "supports_authority": false,
                "supports_pagination": true
            }
        },
        "k10plus_union_catalogue": {
            "host_id": 31,
            "name": "K10plus Union Catalogue",
            "enabled": true,
            "protocol": "SRU",
            "transport": "CURL",
            "endpoint": "https://sru.k10plus.de/opac-de-627",
            "version": "1.2",
            "recordSchema": "marcxml",
            "indexes": {
                "title": "pica.tit",
                "author": "pica.per",
                "creator": "pica.per",
                "subject": "pica.swd",
                "publisher": "pica.pub",
                "publication_location": "pica.ort",
                "isbn": "pica.isb",
                "issn": "pica.iss",
                "lccn": null,
                "keyword": "pica.all",
                "record": null,
                "media": null
            },
            "relations": {
                "default": "=",
                "title": "=",
                "author": "=",
                "creator": "=",
                "subject": "=",
                "publisher": "=",
                "publication_location": "=",
                "isbn": "=",
                "issn": "=",
                "lccn": "=",
                "keyword": "="
            },
            "request": {
                "startRecord": 1,
                "maximumRecords": 10,
                "timeout": 30
            },
            "capabilities": {
                "supports_explain": true,
                "supports_marcxml": true,
                "supports_isbn": true,
                "supports_issn": true,
                "supports_authority": false,
                "supports_pagination": true
            }
        }
    }
}
JSON
, true);



