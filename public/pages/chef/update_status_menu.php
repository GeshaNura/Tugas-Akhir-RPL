<?php
include_once "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menuId = $_POST['id_menu'];
    $status = $_POST['status_menu'];

    // Tambahkan ini untuk debugging
    file_put_contents('debug_log.txt', "menuId: $menuId, status: $status\n", FILE_APPEND);

    $sql = "UPDATE menu SET status_menu = ? WHERE id_menu = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $menuId);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Status updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update status"]);
    }
    $stmt->close();
    $conn->close();
}

