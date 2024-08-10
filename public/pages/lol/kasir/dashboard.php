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

// Menyiapkan query untuk mengambil data pegawai berdasarkan id_karyawan
$query = "SELECT nama, jabatan FROM karyawan WHERE id_karyawan = ?";
$stmt = $conn->prepare($query);

// Periksa apakah statement berhasil dipersiapkan
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $id_karyawan);
$stmt->execute();
$result = $stmt->get_result();

// Periksa apakah hasil query ada
if ($result->num_rows > 0) {
    $pegawai = $result->fetch_assoc();

    // Periksa apakah data pegawai ada
    if (isset($pegawai['nama']) && isset($pegawai['jabatan'])) {
        $nama = $pegawai['nama'];
        $jabatan = $pegawai['jabatan'];
    } else {
        die('Data pegawai tidak lengkap.');
    }
} else {
    die('Data pegawai tidak ditemukan.');
}

function getPesananData($conn) {
    // Query untuk mengambil data pesanan
    $query_pesanan = "
    SELECT 
        pesanan.id_pesanan,
        pelanggan.nama_pelanggan AS AN,
        pesanan.tanggal_waktu_pesanan AS TANGGAL,
        (
            SELECT SUM(menu.harga * detail_pesanan.jumlah)
            FROM detail_pesanan
            JOIN menu ON detail_pesanan.id_menu = menu.id_menu
            WHERE detail_pesanan.id_pesanan = pesanan.id_pesanan AND detail_pesanan.status = 'Selesai'
        ) AS TOTAL
    FROM 
        pesanan
    JOIN 
        pelanggan ON pesanan.id_pelanggan = pelanggan.id_pelanggan
    WHERE 
        pesanan.status_pembayaran = 'belum'
    ORDER BY 
        pesanan.tanggal_waktu_pesanan DESC
    ";

    $result_pesanan = $conn->query($query_pesanan);

    if ($result_pesanan === false) {
        die("Error executing query: " . $conn->error);
    }

    return $result_pesanan;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="./../../css/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-72 bg-[#11385a] text-white flex flex-col h-screen">
            <div class="flex items-center justify-center p-6 border-b border-[#355e91]">
                <div class="text-center">
                    <div class="w-24 h-24 bg-white rounded-full mx-auto shadow-2xl"></div>
                    <h2 class="mt-4 text-xl font-semibold"><?php echo htmlspecialchars($nama); ?></h2>
                    <p><?php echo htmlspecialchars($jabatan); ?></p>
                </div>
            </div>
            <nav class="flex flex-col mt-6">
                <a href="./dashboard.php" class="px-4 py-2 my-1 flex flex-row font-semibold bg-[#091c2d]">
                    <img src="./../../image/bayar.png" class="w-8 mr-3" alt="">
                    <div class="">Pembayaran</div>
                </a>
                <a href="./riwayat.php" class="px-4 py-2 flex my-1 flex-row font-semibold hover:bg-[#091c2d]">
                    <img src="./../../image/history.png" class="w-8 mr-3" alt="">
                    <div class="font-semibold">Riwayat Pembayaran</div>
                </a>    
            </nav>
            <div class="flex flex-col mt-auto text-center">
                <button id="logoutButton" class="p-3 font-bold bg-red-500 text-white hover:bg-red-600">Logout</button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-hidden h-screen bg-[#e2e6ed]">
            <div class="text-5xl font-bold text-center p-8">LIST PESANAN</div>
            <div class="p-8 mx-16 h-[100%] mb-6">
                <div class="mx-3 mt-2 h-[80%] overflow-scroll grid grid-cols-4 gap-5">
                    <?php
                    $result_pesanan = getPesananData($conn);
                    while ($pesanan = $result_pesanan->fetch_assoc()): ?>
                    <a href="pembayaran.php?id_pesanan=<?php echo $pesanan['id_pesanan']; ?>" class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-2xl font-bold mb-2 text-center"><?php echo htmlspecialchars($pesanan['id_pesanan']); ?></h3>
                        <hr class="border-slate-950 mb-2">
                        <div class="grid grid-cols-2 items-center mb-12">
                            <div class="basis-1/2">AN</div>
                            <div class="basis-1/2 text-right"><?php echo htmlspecialchars($pesanan['AN']); ?></div>
                            <div class="basis-1/2">TANGGAL</div>
                            <div class="basis-1/2 text-right"><?php echo htmlspecialchars(date('d/m/Y', strtotime($pesanan['TANGGAL']))); ?></div>
                            <div class="basis-1/2">SUB TOTAL</div>
                            <div class="basis-1/2 text-right">Rp<?php echo number_format($pesanan['TOTAL'], 0, ',', '.'); ?></div>
                        </div>
                        <p class="text-xs text-slate-400 text-center">TEKAN UNTUK PROSES</p>
                    </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<script>
document.getElementById('logoutButton').addEventListener('click', function(event) {
    var confirmation = confirm("Apakah Anda yakin ingin logout?");
    if (confirmation) {
        window.location.href = '../logout.php';
    } else {
        // Pengguna menekan "Tidak," tidak melakukan apa-apa
        event.preventDefault();
    }
});
</script>
