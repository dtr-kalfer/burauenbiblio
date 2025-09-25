<?php
namespace Todo_List;

class Todo_List extends \ConnectDB {

    public function addTask(string $task): bool {
        $sql = "INSERT INTO todos (task) VALUES (?)";
        return $this->execute($sql, "s", [$task]);
    }

    public function deleteTask(int $id): bool {
        $sql = "DELETE FROM todos WHERE id = ?";
        return $this->execute($sql, "i", [$id]);
    }

    public function updateTask(int $id, bool $done): bool {
        $sql = "UPDATE todos SET is_done = ? WHERE id = ?";
        return $this->execute($sql, "ii", [$done ? 1 : 0, $id]);
    }

    public function getAllTasks(): array {
        $sql = "SELECT * FROM todos ORDER BY id ASC";
        return $this->select($sql);
    }
}
