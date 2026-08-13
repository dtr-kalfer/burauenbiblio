<?php
namespace DDC;

class DDCSummary extends \ConnectDB
{
    public function generate_summary()
    {
        $sql = "
            SELECT
                e.classification AS subject_area,
                COUNT(DISTINCT e.bibid) AS titles,
                COUNT(e.barcode_nmbr) AS volumes,
                COUNT(DISTINCT CASE WHEN b.publication_year >= YEAR(CURDATE()) - 9 THEN e.bibid END) AS titles_last_10_years,
                COUNT(CASE WHEN b.publication_year >= YEAR(CURDATE()) - 9 THEN e.barcode_nmbr END) AS volumes_last_10_years,
                COUNT(DISTINCT CASE WHEN b.publication_year >= YEAR(CURDATE()) - 4 THEN e.bibid END) AS titles_last_5_years,
                COUNT(CASE WHEN b.publication_year >= YEAR(CURDATE()) - 4 THEN e.barcode_nmbr END) AS volumes_last_5_years
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
                    AND bs.subfield_data REGEXP '^[12][0-9]{3}' -- Ensure it looks like a year
                GROUP BY
                    bf.bibid
            ) AS b ON e.bibid = b.bibid
            WHERE e.classification IS NOT NULL AND e.classification != 'Unclassified'
            GROUP BY
                e.classification
            ORDER BY
                e.classification;
        ";
        return $this->select($sql);
    }
}