<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details. --F.Tumulak
	 */
	 
namespace LibraryAnalytics;

class CollectionAnalytics extends \ConnectDB {

    public function getWeeklyGrowth($start_date, $end_date) {

        $sql = "
        SELECT
				
            YEAR(create_dt) AS year_added,
            WEEK(create_dt,1) AS week_no,
            COUNT(*) AS total
        FROM biblio
        WHERE DATE(create_dt) BETWEEN ? AND ?
        GROUP BY
            YEAR(create_dt),
            WEEK(create_dt,1)
        ORDER BY
            year_added,
            week_no
        ";

        return $this->select(
            $sql,
            "ss",
            [$start_date, $end_date]
        );
    }

    public function getWeeklyGrowthWithCopies($start_date, $end_date) {

        $sql = "
        SELECT
						
				YEAR(b.create_dt) AS year_added,
				WEEK(b.create_dt,1) AS week_no,
				UPPER(DATE_FORMAT(b.create_dt,'%b')) AS month_short,

				COUNT(DISTINCT b.bibid) AS bib_total,
				COUNT(DISTINCT c.copyid) AS copy_total				

        FROM biblio b

        LEFT JOIN biblio_copy c
            ON c.bibid = b.bibid

        WHERE DATE(b.create_dt) BETWEEN ? AND ?

        GROUP BY
            YEAR(b.create_dt),
            WEEK(b.create_dt,1)

        ORDER BY
            year_added,
            week_no
        ";

        return $this->select(
            $sql,
            "ss",
            [$start_date,$end_date]
        );
    }
}