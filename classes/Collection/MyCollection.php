<?php
namespace Collection;

class MyCollection extends \ConnectDB
{
    /**
     * Update borrow policy for a collection
     */
    public function updateBorrowPolicy(
        string $code,
        int $daysDueBack,
        float $lateFee
    ): int {
        $sql = "
            UPDATE collection_circ
            SET
                days_due_back = ?,
                regular_late_fee = ?
            WHERE code = ?
        ";

        // i = int, d = double, s = string
        return $this->execute(
            $sql,
            "ids",
            [$daysDueBack, $lateFee, $code],
            true // return affected rows
        );
    }

    /**
     * Optional: fetch current policy (useful for validation / UI)
     */
    public function getBorrowPolicy(string $code): ?array
    {
        $sql = "
            SELECT
                code,
                days_due_back,
                regular_late_fee
            FROM collection_circ
            WHERE code = ?
        ";

        return $this->selectOne($sql, "s", [$code]);
    }
		
		/**
     * Get collections with circulation policy summary
     */
		public function getBorrowPolicyList(): array
		{
				$sql = "
						SELECT
								dm.code,
								dm.description,
								COUNT(DISTINCT b.bibid) AS item_count,
								circ.days_due_back,
								circ.regular_late_fee
						FROM collection_dm dm
						LEFT JOIN biblio b
								ON b.collection_cd = dm.code
						LEFT JOIN collection_circ circ
								ON circ.code = dm.code
						GROUP BY
								dm.code,
								dm.description,
								circ.days_due_back,
								circ.regular_late_fee
						ORDER BY dm.description
				";

				return $this->select($sql);
		}
	
}
