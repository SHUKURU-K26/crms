<?php
include '../web_db/connection.php'; // your DB connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    $code = $_POST['code'];

    // Basic validation: 6 digits
    if (!preg_match('/^[0-9]{6}$/', $code)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid code']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO registration_codes (code, status) VALUES (?, 'Unused')");
    $stmt->bind_param("s", $code);
    

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    $stmt->close();
    $conn->close();
}
