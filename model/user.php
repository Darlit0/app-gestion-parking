<?php

class User {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, family_name, email, password, phone, admin) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");        return $stmt->execute([
            $data['name'],
            $data['family_name'], 
            $data['email'],
            $data['password'],
            $data['phone'] ?? null,
            $data['admin'] ?? 0
        ]);
    }
      public function update($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE users 
            SET name = ?, family_name = ?, email = ?, phone = ?, admin = ? 
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['name'],
            $data['family_name'],
            $data['email'],
            $data['phone'],
            $data['admin'] ?? 0,
            $id
        ]);
    }
    
    public function updatePassword($id, $newPassword) {
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $id]);
    }
    
    public function isAdmin($id) {
        $stmt = $this->pdo->prepare("SELECT admin FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user && $user['admin'];
    }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllWithPagination($limit, $offset) {
        $stmt = $this->pdo->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTotalCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getPdo() {
        return $this->pdo;
    }
}
