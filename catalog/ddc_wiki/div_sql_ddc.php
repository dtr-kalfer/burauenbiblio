<?php
/* 
 * This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 * 
 * DDC Classification Division Mapping (Level 2)
 * Source: Wikipedia - Dewey Decimal Classification
 * https://en.wikipedia.org/wiki/List_of_Dewey_Decimal_classes
 *
 * This file references materials licensed under the
 * Creative Commons Attribution-ShareAlike 4.0 International License.
 * License details: https://creativecommons.org/licenses/by-sa/4.0/legalcode
 *
 * -- F. Tumulak
 */
$updateSQL = "
    UPDATE extract_ddc
    SET classification_div = CASE
        -- 000s General Works
        WHEN LEFT(ddc, 3) BETWEEN '000' AND '009' THEN 'Computer science, information & general works'
        WHEN LEFT(ddc, 3) BETWEEN '010' AND '019' THEN 'Bibliographies'
        WHEN LEFT(ddc, 3) BETWEEN '020' AND '029' THEN 'Library & information sciences'
        WHEN LEFT(ddc, 3) BETWEEN '030' AND '039' THEN 'Encyclopedias & books of facts'
        WHEN LEFT(ddc, 3) BETWEEN '040' AND '049' THEN 'Unassigned (General Works)'
        WHEN LEFT(ddc, 3) BETWEEN '050' AND '059' THEN 'Magazines, journals & serials'
        WHEN LEFT(ddc, 3) BETWEEN '060' AND '069' THEN 'Associations, organizations & museums'
        WHEN LEFT(ddc, 3) BETWEEN '070' AND '079' THEN 'News media, journalism & publishing'
        WHEN LEFT(ddc, 3) BETWEEN '080' AND '089' THEN 'Quotations & collections'
        WHEN LEFT(ddc, 3) BETWEEN '090' AND '099' THEN 'Manuscripts & rare books'

        -- 100s Philosophy & Psychology
        WHEN LEFT(ddc, 3) BETWEEN '100' AND '109' THEN 'Philosophy'
        WHEN LEFT(ddc, 3) BETWEEN '110' AND '119' THEN 'Metaphysics'
        WHEN LEFT(ddc, 3) BETWEEN '120' AND '129' THEN 'Epistemology, causation & humankind'
        WHEN LEFT(ddc, 3) BETWEEN '130' AND '139' THEN 'Parapsychology & occultism'
        WHEN LEFT(ddc, 3) BETWEEN '140' AND '149' THEN 'Specific philosophical schools'
        WHEN LEFT(ddc, 3) BETWEEN '150' AND '159' THEN 'Psychology'
        WHEN LEFT(ddc, 3) BETWEEN '160' AND '169' THEN 'Philosophical logic'
        WHEN LEFT(ddc, 3) BETWEEN '170' AND '179' THEN 'Ethics (Moral philosophy)'
        WHEN LEFT(ddc, 3) BETWEEN '180' AND '189' THEN 'Ancient, medieval, Eastern philosophy'
        WHEN LEFT(ddc, 3) BETWEEN '190' AND '199' THEN 'Modern Western philosophy'

        -- 200s Religion
        WHEN LEFT(ddc, 3) BETWEEN '200' AND '209' THEN 'Religion'
        WHEN LEFT(ddc, 3) BETWEEN '210' AND '219' THEN 'Philosophy & theory of religion'
        WHEN LEFT(ddc, 3) BETWEEN '220' AND '229' THEN 'Bible & biblical studies'
        WHEN LEFT(ddc, 3) BETWEEN '230' AND '239' THEN 'Christian theology'
        WHEN LEFT(ddc, 3) BETWEEN '240' AND '249' THEN 'Christian practice & observance'
        WHEN LEFT(ddc, 3) BETWEEN '250' AND '259' THEN 'Local Christian church & religious orders'
        WHEN LEFT(ddc, 3) BETWEEN '260' AND '269' THEN 'Social & ecclesiastical theology'
        WHEN LEFT(ddc, 3) BETWEEN '270' AND '279' THEN 'Christian church history'
        WHEN LEFT(ddc, 3) BETWEEN '280' AND '289' THEN 'Denominations & sects'
        WHEN LEFT(ddc, 3) BETWEEN '290' AND '299' THEN 'Other religions'

        -- 300s Social Sciences
        WHEN LEFT(ddc, 3) BETWEEN '300' AND '309' THEN 'Social sciences'
        WHEN LEFT(ddc, 3) BETWEEN '310' AND '319' THEN 'Statistics'
        WHEN LEFT(ddc, 3) BETWEEN '320' AND '329' THEN 'Political science'
        WHEN LEFT(ddc, 3) BETWEEN '330' AND '339' THEN 'Economics'
        WHEN LEFT(ddc, 3) BETWEEN '340' AND '349' THEN 'Law'
        WHEN LEFT(ddc, 3) BETWEEN '350' AND '359' THEN 'Public administration & military science'
        WHEN LEFT(ddc, 3) BETWEEN '360' AND '369' THEN 'Social problems & services; associations'
        WHEN LEFT(ddc, 3) BETWEEN '370' AND '379' THEN 'Education'
        WHEN LEFT(ddc, 3) BETWEEN '380' AND '389' THEN 'Commerce, communications & transport'
        WHEN LEFT(ddc, 3) BETWEEN '390' AND '399' THEN 'Customs, etiquette & folklore'

        -- 400s Language
        WHEN LEFT(ddc, 3) BETWEEN '400' AND '409' THEN 'Language'
        WHEN LEFT(ddc, 3) BETWEEN '410' AND '419' THEN 'Linguistics'
        WHEN LEFT(ddc, 3) BETWEEN '420' AND '429' THEN 'English & Old English'
        WHEN LEFT(ddc, 3) BETWEEN '430' AND '439' THEN 'Germanic languages'
        WHEN LEFT(ddc, 3) BETWEEN '440' AND '449' THEN 'Romance languages'
        WHEN LEFT(ddc, 3) BETWEEN '450' AND '459' THEN 'Italian, Romanian & related languages'
        WHEN LEFT(ddc, 3) BETWEEN '460' AND '469' THEN 'Spanish & Portuguese'
        WHEN LEFT(ddc, 3) BETWEEN '470' AND '479' THEN 'Latin & classical languages'
        WHEN LEFT(ddc, 3) BETWEEN '480' AND '489' THEN 'Greek & Hellenic languages'
        WHEN LEFT(ddc, 3) BETWEEN '490' AND '499' THEN 'Other languages'

        -- 500s Science
        WHEN LEFT(ddc, 3) BETWEEN '500' AND '509' THEN 'Science'
        WHEN LEFT(ddc, 3) BETWEEN '510' AND '519' THEN 'Mathematics'
        WHEN LEFT(ddc, 3) BETWEEN '520' AND '529' THEN 'Astronomy'
        WHEN LEFT(ddc, 3) BETWEEN '530' AND '539' THEN 'Physics'
        WHEN LEFT(ddc, 3) BETWEEN '540' AND '549' THEN 'Chemistry'
        WHEN LEFT(ddc, 3) BETWEEN '550' AND '559' THEN 'Earth sciences & geology'
        WHEN LEFT(ddc, 3) BETWEEN '560' AND '569' THEN 'Fossils & prehistoric life'
        WHEN LEFT(ddc, 3) BETWEEN '570' AND '579' THEN 'Life sciences & biology'
        WHEN LEFT(ddc, 3) BETWEEN '580' AND '589' THEN 'Plants & botany'
        WHEN LEFT(ddc, 3) BETWEEN '590' AND '599' THEN 'Zoology & animal sciences'

        -- 600s Technology
        WHEN LEFT(ddc, 3) BETWEEN '600' AND '609' THEN 'Technology'
        WHEN LEFT(ddc, 3) BETWEEN '610' AND '619' THEN 'Medicine & health'
        WHEN LEFT(ddc, 3) BETWEEN '620' AND '629' THEN 'Engineering'
        WHEN LEFT(ddc, 3) BETWEEN '630' AND '639' THEN 'Agriculture'
        WHEN LEFT(ddc, 3) BETWEEN '640' AND '649' THEN 'Home & family management'
        WHEN LEFT(ddc, 3) BETWEEN '650' AND '659' THEN 'Management & public relations'
        WHEN LEFT(ddc, 3) BETWEEN '660' AND '669' THEN 'Chemical engineering'
        WHEN LEFT(ddc, 3) BETWEEN '670' AND '679' THEN 'Manufacturing'
        WHEN LEFT(ddc, 3) BETWEEN '680' AND '689' THEN 'Manufacture for specific uses'
        WHEN LEFT(ddc, 3) BETWEEN '690' AND '699' THEN 'Construction & buildings'

        -- 700s Arts & Recreation
        WHEN LEFT(ddc, 3) BETWEEN '700' AND '709' THEN 'The arts'
        WHEN LEFT(ddc, 3) BETWEEN '710' AND '719' THEN 'Landscape & area planning'
        WHEN LEFT(ddc, 3) BETWEEN '720' AND '729' THEN 'Architecture'
        WHEN LEFT(ddc, 3) BETWEEN '730' AND '739' THEN 'Sculpture, ceramics & related arts'
        WHEN LEFT(ddc, 3) BETWEEN '740' AND '749' THEN 'Graphic arts & decorative arts'
        WHEN LEFT(ddc, 3) BETWEEN '750' AND '759' THEN 'Painting'
        WHEN LEFT(ddc, 3) BETWEEN '760' AND '769' THEN 'Printmaking & prints'
        WHEN LEFT(ddc, 3) BETWEEN '770' AND '779' THEN 'Photography & computer art'
        WHEN LEFT(ddc, 3) BETWEEN '780' AND '789' THEN 'Music'
        WHEN LEFT(ddc, 3) BETWEEN '790' AND '799' THEN 'Sports, games & entertainment'

        -- 800s Literature
        WHEN LEFT(ddc, 3) BETWEEN '800' AND '809' THEN 'Literature, rhetoric & criticism'
        WHEN LEFT(ddc, 3) BETWEEN '810' AND '819' THEN 'American literature'
        WHEN LEFT(ddc, 3) BETWEEN '820' AND '829' THEN 'English & Old English literatures'
        WHEN LEFT(ddc, 3) BETWEEN '830' AND '839' THEN 'German literature'
        WHEN LEFT(ddc, 3) BETWEEN '840' AND '849' THEN 'French literature'
        WHEN LEFT(ddc, 3) BETWEEN '850' AND '859' THEN 'Italian & Romanian literature'
        WHEN LEFT(ddc, 3) BETWEEN '860' AND '869' THEN 'Spanish & Portuguese literature'
        WHEN LEFT(ddc, 3) BETWEEN '870' AND '879' THEN 'Latin literature'
        WHEN LEFT(ddc, 3) BETWEEN '880' AND '889' THEN 'Greek literature'
        WHEN LEFT(ddc, 3) BETWEEN '890' AND '899' THEN 'Other literatures'

        -- 900s History & Geography
        WHEN LEFT(ddc, 3) BETWEEN '900' AND '909' THEN 'History'
        WHEN LEFT(ddc, 3) BETWEEN '910' AND '919' THEN 'Geography & travel'
        WHEN LEFT(ddc, 3) BETWEEN '920' AND '929' THEN 'Biographies, genealogy & insignia'
        WHEN LEFT(ddc, 3) BETWEEN '930' AND '939' THEN 'Ancient history'
        WHEN LEFT(ddc, 3) BETWEEN '940' AND '949' THEN 'European history'
        WHEN LEFT(ddc, 3) BETWEEN '950' AND '959' THEN 'Asian history'
        WHEN LEFT(ddc, 3) BETWEEN '960' AND '969' THEN 'African history'
        WHEN LEFT(ddc, 3) BETWEEN '970' AND '979' THEN 'North American history'
        WHEN LEFT(ddc, 3) BETWEEN '980' AND '989' THEN 'South American history'
        WHEN LEFT(ddc, 3) BETWEEN '990' AND '999' THEN 'Oceania, polar regions & extraterrestrial worlds'
        
        ELSE 'Unclassified'
    END
    WHERE ddc REGEXP '^[0-9]{1,3}(\\.[0-9]+)?$';
";
