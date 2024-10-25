<?php

namespace Backend\Models;

use PDO;

class User
{
    private PDO $conn;
    private string $table_name = "users";

    public int $id;
    public string $username;
    public string $email;
    public string $birth_date;
    public string $password;
    public int $role_id;
    public string $avatar_url;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function register(): bool
    {
        $query = "INSERT INTO " . $this->table_name . " (username, email, birth_date, password, role_id, avatar_url)
                  VALUES (:username, :email, :birth_date, :password, :role_id, :avatar_url)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $this->username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindParam(':birth_date', $this->birth_date, PDO::PARAM_STR);
        $stmt->bindParam(':password', $this->password, PDO::PARAM_STR);
        $stmt->bindParam(':role_id', $this->role_id, PDO::PARAM_INT);
        $stmt->bindParam(':avatar_url', $this->avatar_url, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function emailExists(string $email): bool
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getUserByEmail(string $email): ?array
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function getPasswordByEmail(string $email): ?string
    {
        $user = $this->getUserByEmail($email);
        return $user['password'] ?? null;
    }

    public function getIdByEmail(string $email): ?int
    {
        $user = $this->getUserByEmail($email);
        return $user['id'] ?? null;
    }

    public function getUsernameByEmail(string $email): ?string
    {
        $user = $this->getUserByEmail($email);
        return $user['username'] ?? null;
    }

    public function updateUser(int $userId, string $username, string $birthDate, ?string $avatarUrl = null): bool
    {
        $query = "UPDATE " . $this->table_name . " 
              SET username = :username, birth_date = :birth_date" .
            ($avatarUrl ? ", avatar_url = :avatar_url" : "") .
            " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':birth_date', $birthDate, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        if ($avatarUrl) {
            $stmt->bindParam(':avatar_url', $avatarUrl, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }

    public function getUserById(int $userId): ?array
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function storeRememberToken(int $userId, string $token): bool
    {
        $query = "UPDATE " . $this->table_name . " SET remember_token = :token WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getUserByRememberToken(string $token): ?array
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE remember_token = :token LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function getAvatarByEmail(string $email): ?string
    {
        $query = "SELECT avatar_url FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user['avatar_url'] ?? '/uploads/avatars/default_avatar.jpg';
    }

    public function getRoleIdByEmail(string $email): ?int
    {
        $query = "SELECT role_id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? (int)$user['role_id'] : null;
    }

    public function updatePassword(int $userId, string $newPassword): bool
    {
        $query = "UPDATE " . $this->table_name . " SET password = :password WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $newPassword, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteRememberToken(string $token): bool
    {
        $query = "UPDATE " . $this->table_name . " SET remember_token = NULL WHERE remember_token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function getAllUsers(): array
    {
        $query = "
            SELECT users.id, users.avatar_url, users.username, users.email, users.birth_date, roles.name as role
            FROM users
            JOIN roles ON users.role_id = roles.id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}