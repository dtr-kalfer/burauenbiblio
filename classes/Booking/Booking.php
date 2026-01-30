<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F. Tumulak 1/30/2026
 */
 
namespace Booking;

class Booking extends \ConnectDB
{
    /**
     * Get booked or reserved items from cart
     *
     * @return array
     */
    public function getBookedItems(): array
    {
        $sql = "
				SELECT
						bh.*,
						CONCAT(m.last_name, ', ', m.first_name) AS member,
						bc.barcode_nmbr,
						bsh.status_cd,
						bk.due_dt,
						stat.description AS status,
						DATE_FORMAT(bh.hold_begin_dt, '%m/%d/%Y') AS hold_begin,
						IFNULL(DATE_FORMAT(bk.due_dt, '%m/%d/%Y'), 'N/A') AS due,
						CONCAT(ts.subfield_data, ' ', IFNULL(sub.subfield_data, '')) AS title
				FROM biblio_hold bh
				JOIN member m ON m.mbrid = bh.mbrid
				JOIN biblio_copy bc ON bc.copyid = bh.copyid
				JOIN biblio_status_hist bsh ON bsh.histid = bc.histid
				JOIN biblio_status_dm stat ON stat.code = bsh.status_cd
				JOIN biblio_field tf ON tf.bibid = bh.bibid AND tf.tag = '245'
				JOIN biblio_subfield ts ON ts.fieldid = tf.fieldid AND ts.subfield_cd = 'a'
				LEFT JOIN biblio_subfield sub ON sub.fieldid = tf.fieldid AND sub.subfield_cd = 'b'
				LEFT JOIN booking bk ON bk.out_histid = bsh.histid
				WHERE ( @bibid IS NULL OR bh.bibid = @bibid )
					AND ( @mbrid IS NULL OR bh.mbrid = @mbrid )
				ORDER BY bh.hold_begin_dt ASC;
        ";

        // Uses ConnectDB::select()
        return $this->select($sql);
    }
}
