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

// Function untuk mengambil data riwayat pembayaran
function getRiwayatPembayaran($conn) {
    $query = "
        SELECT pembayaran.id_pesanan, pelanggan.nama_pelanggan, pembayaran.total_harga, pembayaran.tanggal_waktu_pembayaran
        FROM pembayaran
        INNER JOIN pesanan ON pembayaran.id_pesanan = pesanan.id_pesanan
        INNER JOIN pelanggan ON pesanan.id_pelanggan = pelanggan.id_pelanggan
    ";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    } else {
        return [];
    }
}

// Function untuk mengambil detail pesanan
function getDetailPesanan($conn, $id_pesanan) {
    $query = "
        SELECT menu.nama_menu, menu.harga, detail_pesanan.jumlah
        FROM detail_pesanan
        INNER JOIN menu ON detail_pesanan.id_menu = menu.id_menu
        WHERE detail_pesanan.id_pesanan = ?
    ";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("i", $id_pesanan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $details = [];
        while ($row = $result->fetch_assoc()) {
            $details[] = $row;
        }
        return $details;
    } else {
        return [];
    }
}

// Mengambil data riwayat pembayaran
$riwayatPembayaran = getRiwayatPembayaran($conn);

// Jika permintaan AJAX untuk detail pesanan
if (isset($_GET['detail']) && $_GET['detail'] == 'true' && isset($_GET['id_pesanan'])) {
    header('Content-Type: application/json');
    $id_pesanan = $_GET['id_pesanan'];
    $detailPesanan = getDetailPesanan($conn, $id_pesanan);
    echo json_encode($detailPesanan);
    exit();
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
                <a href="./dashboard.php" class="px-4 py-2 my-1 flex flex-row font-semibold hover:bg-[#091c2d]">
                    <img src="./../../image/bayar.png" class="w-8 mr-3" alt="">
                    <div>Pembayaran</div>
                </a>
                <a href="./riwayat.php" class="px-4 py-2 flex my-1 flex-row font-semibold bg-[#091c2d]">
                    <img src="./../../image/history.png" class="w-8 mr-3" alt="">
                    <div>Riwayat Pembayaran</div>
                </a>
            </nav>
            <div class="flex flex-col mt-auto text-center">
                <button id="logoutButton" class="p-3 font-bold bg-red-500 text-white hover:bg-red-600">Logout</button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-hidden h-screen bg-[#e2e6ed]">
            <div class="text-5xl font-bold text-center p-8">RIWAYAT PEMBAYARAN</div>
            <div>
                <form class="flex justify-end px-16">
                    <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only">Cari</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="search" id="default-search" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50" placeholder="Nama" required />
                    </div>
                </form>
            </div>
            <div class="bg-white p-8 mx-16 h-[80%] mb-6 overflow-x-auto rounded-xl mt-3">
                <div class="relative overflow-x-auto shadow-md">
                    <table class="w-full">
                        <thead class="uppercase bg-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">No</th>
                                <th scope="col" class="px-6 py-3">Pembeli</th>
                                <th scope="col" class="px-6 py-3">Total Bayar</th>
                                <th scope="col" class="px-6 py-3">Tanggal</th>
                                <th scope="col" class="px-6 py-3">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($riwayatPembayaran) > 0): ?>
                                <?php foreach ($riwayatPembayaran as $index => $pembayaran): ?>
                                    <tr class="odd:bg-white even:bg-gray-50 text-center">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"><?php echo $index + 1; ?></th>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($pembayaran['nama_pelanggan']); ?></td>
                                        <td class="px-6 py-4">Rp.<?php echo number_format($pembayaran['total_harga'], 0, ',', '.'); ?></td>
                                        <td class="px-6 py-4"><?php echo date('d-m-Y', strtotime($pembayaran['tanggal_waktu_pembayaran'])); ?></td>
                                        <td class="px-6 py-4">
                                            <a href="javascript:void(0);" onclick="showDetailModal(<?php echo $pembayaran['id_pesanan']; ?>);" class="font-medium text-blue-600 hover:underline">Detail</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center">Tidak ada data riwayat pembayaran.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-1/2">
            <h3 class="text-2xl mb-4">Detail Pesanan</h3>
            <div id="detailContent"></div>
            <div id="totalJumlah" class="mt-4 text-lg font-semibold"></div>
            <button onclick="closeDetailModal();" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg">Tutup</button>
        </div>
    </div>

    <script>
        function showDetailModal(idPesanan) {
            fetch(`riwayat.php?detail=true&id_pesanan=${idPesanan}`)
                .then(response => response.json())
                .then(data => {
                    let detailContent = document.getElementById('detailContent');
                    let totalJumlah = document.getElementById('totalJumlah');
                    detailContent.innerHTML = '';
                    let total = 0;
                    if (data.length > 0) {
                        let table = '<table class="w-full"><thead><tr><th>Nama Menu</th><th>Harga</th><th>Jumlah</th></tr></thead><tbody>';
                        data.forEach(item => {
                            table += `<tr><td>${item.nama_menu}</td><td>Rp.${Number(item.harga).toLocaleString()}</td><td>${item.jumlah}</td></tr>`;
                            total += item.jumlah;
                        });
                        table += '</tbody></table>';
                        detailContent.innerHTML = table;
                        totalJumlah.innerHTML = `Jumlah Total: ${total}`;
                    } else {
                        detailContent.innerHTML = 'Tidak ada detail pesanan.';
                        totalJumlah.innerHTML = '';
                    }
                    document.getElementById('detailModal').classList.remove('hidden');
                });
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>
</body>
</html>
