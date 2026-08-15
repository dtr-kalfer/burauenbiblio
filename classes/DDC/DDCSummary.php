<?php
namespace DDC;

class DDCSummary extends \ConnectDB
{
    public function generate_summary()
    {
        $sql = "
            SELECT
                CASE
                    WHEN LEFT(e.ddc, 3) BETWEEN '000' AND '099' THEN '000-099 Generalities'
                    WHEN LEFT(e.ddc, 3) BETWEEN '100' AND '199' THEN '100-199 Philosophy'
                    WHEN LEFT(e.ddc, 3) BETWEEN '200' AND '299' THEN '200-299 Religion'
                    WHEN LEFT(e.ddc, 3) BETWEEN '300' AND '399' THEN '300-399 Social Science'
                    WHEN LEFT(e.ddc, 3) BETWEEN '400' AND '499' THEN '400-499 Language'
                    WHEN LEFT(e.ddc, 3) BETWEEN '500' AND '599' THEN '500-599 Pure/Natural Science'
                    WHEN LEFT(e.ddc, 3) BETWEEN '600' AND '699' THEN '600-699 Applied Science'
                    WHEN LEFT(e.ddc, 3) BETWEEN '700' AND '799' THEN '700-799 Arts/Recreation'
                    WHEN LEFT(e.ddc, 3) BETWEEN '800' AND '899' THEN '800-899 Literature'
                    WHEN LEFT(e.ddc, 3) BETWEEN '900' AND '999' THEN '900-999 History & Geography'
                END AS subject_area,
                COUNT(DISTINCT e.bibid) AS titles,
                COUNT(e.barcode_nmbr) AS volumes,
                COUNT(DISTINCT CASE WHEN b.publication_year >= YEAR(CURDATE()) - 10 THEN e.bibid END) AS titles_last_10_years,
                COUNT(CASE WHEN b.publication_year >= YEAR(CURDATE()) - 10 THEN e.barcode_nmbr END) AS volumes_last_10_years,
                COUNT(DISTINCT CASE WHEN b.publication_year >= YEAR(CURDATE()) - 5 THEN e.bibid END) AS titles_last_5_years,
                COUNT(CASE WHEN b.publication_year >= YEAR(CURDATE()) - 5 THEN e.barcode_nmbr END) AS volumes_last_5_years
            FROM
                extract_ddc e
            LEFT JOIN (
                SELECT
                    bf.bibid,
                    MAX(CAST(bs.subfield_data AS UNSIGNED)) AS publication_year
                FROM
                    biblio_field bf
                JOIN
                    biblio_subfield bs ON bf.fieldid = bs.fieldid
                WHERE
                    bf.tag = '260' AND bs.subfield_cd = 'c'
                    AND bs.subfield_data REGEXP '^[12][0-9]{3}'
                GROUP BY
                    bf.bibid
            ) AS b ON e.bibid = b.bibid
            WHERE e.ddc REGEXP '^[0-9]{1,3}(\\.[0-9]+)?$'
              AND LEFT(e.ddc, 3) BETWEEN '000' AND '999'
            GROUP BY
                subject_area
            ORDER BY
                MIN(CAST(LEFT(e.ddc, 3) AS UNSIGNED)) ASC;
        ";
        return $this->select($sql);
    }
}