<?php namespace Flag\Frmk\Database;

use PDOStatement;

class InsertResult {

    private PDOStatement $stmt;
    private mixed $id;
    private int $affectedRows;

    public function __construct(mixed $id, PDOStatement $stmt)
    {
        $this->stmt = $stmt;
        $this->id = $id;
        $this->affectedRows = $stmt->rowCount();
    }

    public function getStatement(): PDOStatement {
        return $this->stmt;
    }

    public function getId(): mixed {
        return $this->id;
    }

    public function getAffectedRows(): int {
        return $this->affectedRows;
    }
}