<?php

namespace Tagged;

class Tagged extends \ConnectDB
{
    /**
     * Get tagged bibliographic items from cart
     *
     * @return array
     */
    public function getTaggedBibItems(): array
    {
        $sql = "
            SELECT 
                bc.bibid,
                title_field.field_entry AS title,
                author_field.field_entry AS author
            FROM (
                SELECT id AS bibid
                FROM cart
            ) c
            JOIN (
                SELECT *
                FROM biblio_copy bc1
                WHERE (bc1.bibid, bc1.copyid) IN (
                    SELECT bibid, MIN(copyid)
                    FROM biblio_copy
                    GROUP BY bibid
                )
            ) bc ON c.bibid = bc.bibid
            LEFT JOIN (
                SELECT 
                    bibid,
                    GROUP_CONCAT(subfield_data ORDER BY subfield_data SEPARATOR ',') AS field_entry
                FROM biblio_subfield
                WHERE (bibid, fieldid) IN (
                    SELECT bibid, MIN(fieldid) + 2
                    FROM biblio_subfield
                    GROUP BY bibid
                )
                GROUP BY bibid
            ) title_field ON bc.bibid = title_field.bibid
            LEFT JOIN (
                SELECT 
                    bibid,
                    GROUP_CONCAT(subfield_data ORDER BY subfield_data SEPARATOR ',') AS field_entry
                FROM biblio_subfield
                WHERE (bibid, fieldid) IN (
                    SELECT bibid, MIN(fieldid) + 1
                    FROM biblio_subfield
                    GROUP BY bibid
                )
                GROUP BY bibid
            ) author_field ON bc.bibid = author_field.bibid
        ";

        // Uses ConnectDB::select()
        return $this->select($sql);
    }
}
