<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['certificate'])) {
        $file = $_FILES['certificate'];
        $md5_hash = md5_file($file['tmp_name']);
        
        $stmt = $conn->prepare("SELECT * FROM valid_certificates WHERE md5_hash = ?");
        $stmt->bind_param("s", $md5_hash);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode(['status' => 'valid', 'hash' => $md5_hash]);
        } else {
            echo json_encode(['status' => 'invalid', 'hash' => $md5_hash]);
        }
        
        $stmt->close();
    }
}
$conn->close();
?>