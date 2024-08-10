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
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $search = '%' . $search . '%'; // Prepare search term for SQL LIKE query

    $query = "
        SELECT pembayaran.id_pesanan, pelanggan.nama_pelanggan, pembayaran.total_harga, pembayaran.tanggal_waktu_pembayaran, pesanan.id_meja
        FROM pembayaran
        INNER JOIN pesanan ON pembayaran.id_pesanan = pesanan.id_pesanan
        INNER JOIN pelanggan ON pesanan.id_pelanggan = pelanggan.id_pelanggan
        WHERE pelanggan.nama_pelanggan LIKE ?
    ";
    $stmt = $conn->prepare($query);

    // Periksa apakah statement berhasil dipersiapkan
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

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

    // Periksa apakah statement berhasil dipersiapkan
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("i", $id_pesanan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $details = [];
        $subtotal = 0; // Inisialisasi subtotal
        while ($row = $result->fetch_assoc()) {
            $row['total'] = $row['harga'] * $row['jumlah'];
            $subtotal += $row['total']; // Tambah total ke subtotal
            $details[] = $row;
        }
        $pajak = $subtotal * 0.10; // Hitung pajak 10%
        $total = $subtotal + $pajak; // Hitung total setelah pajak

        return [
            'details' => $details,
            'subtotal' => $subtotal,
            'pajak' => $pajak,
            'total' => $total
        ];
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
    <style>
        /* Modal Styles */
        #detailModal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        #detailModal.hidden {
            opacity: 0;
            visibility: hidden;
        }

        #detailModal .modal-content {
            background: white;
            border-radius: 8px;
            padding: 20px;
            width: 80%;
            max-width: 600px;
            position: relative;
        }

        #detailModal .modal-content button {
            position: absolute;
            top: 10px;
            right: 10px;
            background: transparent;
            border: none;
            font-size: 24px;
            cursor: pointer;
        }

        .summary {
            margin-top: 20px;
            font-size: 16px;
        }

        .summary div {
            margin-bottom: 10px;
        }
    </style>
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
                <form class="flex justify-end px-16" method="get">
                    <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only">Cari</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.25 12.5a5 5 0 1 0-1.75 1.75m0-1.75L18 18"></path></svg>
                        </div>
                        <input type="text" id="search" name="search" class="block p-2.5 w-full ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari pelanggan" value="<?php echo htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
                    </div>
                    <button type="submit" class="ms-2 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">Cari</button>
                </form>
            </div>

            <div class="px-16 py-8">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID Pesanan</th>
                            <th scope="col" class="px-6 py-3">Nama Pelanggan</th>
                            <th scope="col" class="px-6 py-3">Total Harga</th>
                            <th scope="col" class="px-6 py-3">Tanggal Pembayaran</th>
                            <th scope="col" class="px-6 py-3">NO Meja</th>
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($riwayatPembayaran as $item): ?>
                            <tr class="bg-white border-b">
                                <td class="px-6 py-4"><?php echo htmlspecialchars($item['id_pesanan']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($item['nama_pelanggan']); ?></td>
                                <td class="px-6 py-4"><?php echo number_format($item['total_harga'], 0, ',', '.'); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($item['tanggal_waktu_pembayaran']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($item['id_meja']); ?></td>
                                <td class="px-6 py-4">
                                    <button class="show-details-btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" data-id="<?php echo htmlspecialchars($item['id_pesanan']); ?>">Detail</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Detail Modal -->
            <div id="detailModal" class="hidden">
                <div class="modal-content">
                    <button class="close-modal">&times;</button>
                    <h2 class="text-xl font-bold mb-4">Detail Pesanan</h2>
                    <div id="modalDetails"></div>
                    <div class="summary">
                        <div><strong>Subtotal:</strong> <span id="subtotal">0</span></div>
                        <div><strong>Pajak (10%):</strong> <span id="pajak">0</span></div>
                        <div><strong>Total:</strong> <span id="total">0</span></div>
                    </div>
                </div>
            </div>

            <script>
                document.getElementById('logoutButton').addEventListener('click', function(event) {
                    var confirmation = confirm("Apakah Anda yakin ingin logout?");
                    if (confirmation) {
                        window.location.href = '../logout.php';
                    } else {
                        event.preventDefault();
                    }
                });
                document.addEventListener('DOMContentLoaded', function() {
                    const detailButtons = document.querySelectorAll('.show-details-btn');
                    const detailModal = document.getElementById('detailModal');
                    const modalContent = document.getElementById('modalDetails');
                    const subtotalElem = document.getElementById('subtotal');
                    const pajakElem = document.getElementById('pajak');
                    const totalElem = document.getElementById('total');

                    detailButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            const idPesanan = this.getAttribute('data-id');
                            fetch(`riwayat.php?detail=true&id_pesanan=${idPesanan}`)
                                .then(response => response.json())
                                .then(data => {
                                    let detailsHtml = '<ul>';
                                    data.details.forEach(item => {
                                        detailsHtml += `<li>${item.nama_menu} - ${item.jumlah} x ${item.harga} = ${item.total}</li>`;
                                    });
                                    detailsHtml += '</ul>';
                                    
                                    modalContent.innerHTML = detailsHtml;

                                    subtotalElem.textContent = data.subtotal;
                                    pajakElem.textContent = data.pajak;
                                    totalElem.textContent = data.total;

                                    detailModal.classList.remove('hidden');
                                });
                        });
                    });

                    document.querySelector('.close-modal').addEventListener('click', function() {
                        detailModal.classList.add('hidden');
                    });
                });
            </script>
        </div>
    </div>
</body>
</html>
