<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F. Tumulak 1/30/2026
 */
 
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
						c.id AS bibid,
						cn.subfield_data AS call_number,
						CONCAT(
								t.subfield_data,
								IFNULL(CONCAT(' ', tb.subfield_data), '')
						) AS title,
						a.subfield_data AS author

				FROM cart c

				LEFT JOIN biblio_field cf
						ON cf.bibid = c.id
					 AND cf.tag = '099'

				LEFT JOIN biblio_subfield cn
						ON cn.fieldid = cf.fieldid
					 AND cn.subfield_cd = 'a'

				JOIN biblio_field tf
						ON tf.bibid = c.id
					 AND tf.tag = '245'

				JOIN biblio_subfield t
						ON t.fieldid = tf.fieldid
					 AND t.subfield_cd = 'a'

				LEFT JOIN biblio_subfield tb
						ON tb.fieldid = tf.fieldid
					 AND tb.subfield_cd = 'b'

				LEFT JOIN biblio_field af
						ON af.bibid = c.id
					 AND af.tag = '100'

				LEFT JOIN biblio_subfield a
						ON a.fieldid = af.fieldid
					 AND a.subfield_cd = 'a'

				ORDER BY title;
        ";

        // Uses ConnectDB::select()
        return $this->select($sql);
    }
}
