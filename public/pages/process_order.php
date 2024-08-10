<?php
include 'koneksi.php';

$data = json_decode(file_get_contents('php://input'), true);

$noMeja = $data['noMeja'];
$namaPelanggan = $data['namaPelanggan']; 
$totalAmount = $data['totalAmount'];
$orderDetails = $data['orderDetails'];

// Insert into pelanggan table
$sqlPelanggan = "INSERT INTO pelanggan (nama_pelanggan) VALUES ('$namaPelanggan')";
$conn->query($sqlPelanggan);
$idPelanggan = $conn->insert_id;

// Insert into pesanan table
$sqlPesanan = "INSERT INTO pesanan (id_pelanggan, id_meja) VALUES ('$idPelanggan', '$noMeja')";
$conn->query($sqlPesanan);
$idPesanan = $conn->insert_id;

// Insert into detail_pesanan table
foreach ($orderDetails as $detail) {
    $id_menu = $detail['id'];
    $jumlah = $detail['quantity'];
    $deskripsi = $detail['note']; // Get description from the frontend

    $sqlDetail = "INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, status, deskripsi_pesanan) VALUES ('$idPesanan', '$id_menu', '$jumlah', 'Masih diproses', '$deskripsi')";
    $conn->query($sqlDetail);
}

echo json_encode(['success' => true]);
?>
    