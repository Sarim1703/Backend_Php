<?php
header('Content-Type: application/json');
try {
    $pdo = new PDO("mysql:host=localhost;dbname=test_db", "root", "Champ@102938");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['name']) && !empty($data['name'])) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE name = :name");
            $stmt->execute(['name' => $data['name']]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['error' => 'Name already exists']);
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (name) VALUES (:name)");
                $stmt->execute(['name' => $data['name']]);
                echo json_encode(['message' => 'User saved: ' . $data['name']]);
            }
        } else {
            echo json_encode(['error' => 'Name is required']);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['id']) && !empty($data['id'])) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute(['id' => $data['id']]);
            if ($stmt->rowCount() > 0) {
                echo json_encode(['message' => 'User deleted']);
            } else {
                echo json_encode(['error' => 'User not found']);
            }
        } else {
            echo json_encode(['error' => 'ID is required']);
        }
    } else {
        $stmt = $pdo->query("SELECT * FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
}
?>