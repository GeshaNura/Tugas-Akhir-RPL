<?php
session_start();
include "../koneksi.php";

// Periksa apakah id_karyawan ada di session
if (!isset($_SESSION['id_karyawan'])) {
    die("ID Karyawan tidak ditemukan dalam session.");
}

$id_karyawan = $_SESSION['id_karyawan'];

// Periksa apakah koneksi berhasil
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Mengambil data dari form pembayaran
$id_pesanan = $_POST['id_pesanan']; // Pastikan Anda mengirimkan id_pesanan melalui form pembayaran
$total_harga = $_POST['total_harga'];
$tanggal_waktu_pembayaran = date("Y-m-d H:i:s");

// Menyimpan data ke dalam tabel pembayaran
$query = "INSERT INTO pembayaran (id_pesanan, total_harga, tanggal_waktu_pembayaran) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("ids", $id_pesanan, $total_harga, $tanggal_waktu_pembayaran);

if ($stmt->execute()) {
    // Update status pesanan menjadi "Selesai"
    $query_update = "UPDATE pesanan SET status = 'Selesai' WHERE id_pesanan = ?";
    $stmt_update = $conn->prepare($query_update);

    if ($stmt_update === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt_update->bind_param("i", $id_pesanan);
    $stmt_update->execute();

    // Redirect ke halaman dashboard atau halaman sukses
    header("Location: dashboard.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
