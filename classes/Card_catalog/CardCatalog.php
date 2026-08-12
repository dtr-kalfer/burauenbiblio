<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details.
	 * This is an add-on feature for Burauenbiblio developed by Ferdinand Tumulak
	 * For bibid card catalog printing use.
	 * it now uses prepared statements and built-in class functions.
	 */
namespace Card_catalog;

class CardCatalog extends \ConnectDB 
{    
    /**
     * Get a single MARC subfield value for a given bibid, tag, and subfield code.
     */
		 
		public function get_book_details($bibid) {
				return [
						'call_no'   => $this->getMarcSubfield($bibid, '099', 'a'),
						'author'    => $this->getMarcSubfield($bibid, '100', 'a'),
						'title'     => $this->getMarcSubfield($bibid, '245', 'a'),
						'sub_title' => $this->getMarcSubfield($bibid, '245', 'b'),
						'stmt_resp' => $this->getMarcSubfield($bibid, '245', 'c'),
						'place'     => $this->getMarcSubfield($bibid, '260', 'a'),
						'publisher' => $this->getMarcSubfield($bibid, '260', 'b'),
						'year'      => $this->getMarcSubfield($bibid, '260', 'c'),
						'extent'    => $this->getMarcSubfield($bibid, '300', 'a'),
						'other_det' => $this->getMarcSubfield($bibid, '300', 'b'),
						'dimension' => $this->getMarcSubfield($bibid, '300', 'c'),
						'note'      => $this->getMarcSubfield($bibid, '504', 'a'),
						'isbn'      => $this->getMarcSubfield($bibid, '020', 'a'),
						'subjects'  => $this->getMarcSubfield($bibid, '650', 'a')
				];
		}
		 
    public function getMarcSubfield(int $bibid, string $tag, string $subfield_cd): string {
        $sql = "SELECT s.subfield_data
                  FROM biblio_field AS f
                  JOIN biblio_subfield AS s ON f.fieldid = s.fieldid
                 WHERE f.bibid = ?
                   AND f.tag = ?
                   AND s.subfield_cd = ?
              ORDER BY f.fieldid, s.subfieldid
                 LIMIT 1";

        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $bibid, $tag, $subfield_cd);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row ? $row['subfield_data'] : '';
    }

    /**
     * Get all barcodes for a bibid, trimmed to last 6 characters.
     */
    public function get_trimmed_barcodes(int $bibid): array {
        $sql = "SELECT RIGHT(barcode_nmbr, 6) AS barcode_short
                  FROM biblio_copy
                 WHERE bibid = ?
              ORDER BY barcode_nmbr ASC";

        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $bibid);
        $stmt->execute();
        $result = $stmt->get_result();

        $barcodes = [];
        while ($row = $result->fetch_assoc()) {
            $barcodes[] = $row['barcode_short'];
        }
        $stmt->close();

        return $barcodes;
    }

    /**
     * Get all subfields for a bibid.
     */
    public function getSubfields(int $bibid): array {
        $sql = "SELECT subfield_data
                  FROM biblio_subfield
                 WHERE bibid = ?
              ORDER BY fieldid";

        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $bibid);
        $stmt->execute();
        $result = $stmt->get_result();

        $subfields = [];
        while ($row = $result->fetch_assoc()) {
            $subfields[] = $row['subfield_data'];
        }
        $stmt->close();

        return $subfields;
    }

    /**
     * Get all barcodes for a bibid.
     */
    public function getBarcodes(int $bibid): array {
        $sql = "SELECT bibid, barcode_nmbr
                  FROM biblio_copy
                 WHERE bibid = ?";

        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $bibid);
        $stmt->execute();
        $result = $stmt->get_result();

        $barcodes = [];
        while ($row = $result->fetch_assoc()) {
            $barcodes[] = $row;
        }
        $stmt->close();

        return $barcodes;
    }

    /**
     * Check if a bibid exists.
     */
    public function hasBibid(int $bibid): bool {
        $sql = "SELECT 1 FROM biblio_subfield WHERE bibid = ? LIMIT 1";

        $conn = $this->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $bibid);
        $stmt->execute();
        $result = $stmt->get_result();
        $found = $result->num_rows > 0;
        $stmt->close();

        return $found;
    }

    /**
     * Safe array access for barcodes (replaces safe_barcode()).
     */
    public function safe_barcode(array $barcodes, int $index): string {
        return $barcodes[$index] ?? '';
    }
}
