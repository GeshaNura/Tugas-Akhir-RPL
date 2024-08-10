<?php
include_once "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ketersediaan = $_POST['Ketersediaan'];

    foreach ($ketersediaan as $id_menu => $status_menu) {
        $sql = "UPDATE menu SET status_menu = ? WHERE id_menu = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $status_menu, $id_menu);
        $stmt->execute();
    }

    header("Location: ketersediaanmenu.php");
    exit;
}
?>
