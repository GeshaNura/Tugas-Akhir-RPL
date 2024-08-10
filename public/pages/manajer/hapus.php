<?php
include 'koneksi.php';

$id = $_GET['id'];

$sql = "DELETE FROM karyawan WHERE id_karyawan='$id'";

if ($conn->query($sql) === TRUE) {
    header('Location: pegawai.php');
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
