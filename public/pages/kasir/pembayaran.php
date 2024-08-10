<?php
session_start();
include "../koneksi.php";

// Pastikan variabel $koneksi telah didefinisikan
if (!isset($conn)) {
    die("Koneksi database gagal. Pastikan koneksi.php menginisialisasi variabel \$koneksi.");
}

// Ambil id_pesanan dari query string
$id_pesanan = $_GET['id_pesanan'] ?? 1; // Ganti dengan cara Anda mendapatkan id_pesanan

// Query untuk mengambil data pesanan
$query = "
    SELECT 
        m.nomor_meja, 
        pl.nama_pelanggan, 
        (
            SELECT SUM(menu.harga * detail_pesanan.jumlah)
            FROM detail_pesanan
            JOIN menu ON detail_pesanan.id_menu = menu.id_menu
            WHERE detail_pesanan.id_pesanan = p.id_pesanan AND detail_pesanan.status = 'Selesai'
        ) AS subtotal
    FROM pesanan p 
    INNER JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan 
    INNER JOIN meja m ON p.id_meja = m.id_meja 
    WHERE p.id_pesanan = ?
";

// Siapkan statement query
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Statement query gagal: " . $conn->error);
}
$stmt->bind_param("i", $id_pesanan);
$stmt->execute();
$result = $stmt->get_result();

// Periksa apakah ada hasil
if ($result->num_rows > 0) {
    // Ambil data hasil query
    $data = $result->fetch_assoc();
    $no_meja = htmlspecialchars($data['nomor_meja']);
    $nama_pelanggan = htmlspecialchars($data['nama_pelanggan']);
    $subtotal = number_format($data['subtotal'], 0, ',', '.');
    // Hitung total (misalnya ada biaya tambahan atau pajak, tambahkan di sini)
    $total = number_format($data['subtotal'] * 1.1, 0, ',', '.'); // Contoh: 10% pajak
} else {
    die('Data pesanan tidak ditemukan.');
}
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uang = str_replace(['Rp', '.'], '', $_POST['uang']); // Hapus format Rp dan titik
    $total_harga = str_replace(['Rp', '.'], '', $total); // Hapus format Rp dan titik
    $kembalian = $uang - $total_harga;

    // Simpan data pembayaran ke dalam tabel pembayaran
    $query_insert = "
        INSERT INTO pembayaran (id_pesanan, total_harga, tanggal_waktu_pembayaran) 
        VALUES (?, ?, NOW())
    ";
    $stmt_insert = $conn->prepare($query_insert);
    if ($stmt_insert === false) {
        die("Statement query gagal: " . $conn->error);
    }
    $stmt_insert->bind_param("ii", $id_pesanan, $total_harga);
    $stmt_insert->execute();
    $stmt_insert->close();

    // Update status pembayaran di database
    $query_update = "UPDATE pesanan SET status_pembayaran = 'selesai' WHERE id_pesanan = ?";
    $stmt_update = $conn->prepare($query_update);
    if ($stmt_update === false) {
        die("Statement query gagal: " . $conn->error);
    }
    $stmt_update->bind_param("i", $id_pesanan);
    $stmt_update->execute();
    $stmt_update->close();

    // Redirect atau tampilkan pesan sukses
    echo "<script>alert('Pembayaran berhasil!'); window.location.href='dashboard.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <link rel="stylesheet" href="./../../css/output.css">
</head>
<body class="bg-[#e2e6ed]">
    <div class="h-16 w-screen flex items-center">
        <a href="./dashboard.php" class="bg-[#11385a] text-white py-1 px-6 text-xl rounded-full font-semibold ml-9">KEMBALI</a>
    </div>
    <div class="grid grid-cols-2">
        <div class="m-auto mt-10 shadow-xl">
            <form method="POST" class="max-w-md mx-auto bg-white p-12">
                <div class="text-2xl font-semibold">PEMBAYARAN</div>
                <hr class="border-black border-2 w-11 mb-7">
                <div class="relative z-0 w-full mb-5 group">
                    <input type="text" name="no_meja" id="no_meja" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required disabled value="<?php echo $no_meja; ?>" />
                    <label for="no_meja" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">No Meja</label>
                </div>
                <div class="relative z-0 w-full mb-5 group">
                    <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required disabled value="<?php echo $nama_pelanggan; ?>" />
                    <label for="nama_pelanggan" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Nama Pelanggan</label>
                </div>
                <div class="grid md:grid-cols-2 md:gap-6">
                    <div class="relative z-0 w-full mb-5 group">
                        <input type="text" name="subtotal" id="subtotal" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required value="Rp<?php echo $subtotal; ?>" disabled />
                        <label for="subtotal" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">SubTotal</label>
                    </div>
                    <div class="relative z-0 w-full mb-5 group">
                        <input type="text" name="total" id="total" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required value="Rp<?php echo $total; ?>" disabled />
                        <label for="total" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Total</label>
                    </div>
                </div>
                <div class="relative z-0 w-full mb-5 group">
                    <input type="text" name="uang" id="uang" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required />
                    <label for="uang" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Uang Tunai</label>
                </div>
                <div class="relative z-0 w-full mb-5 group">
                    <input type="text" name="kembalian" id="kembalian" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required disabled />
                    <label for="kembalian" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Kembalian</label>
                </div>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Bayar</button>
            </form>
        </div>
        <div class="mt-10 bg-white p-12 mr-44 shadow-xl">
            <table class="w-full">
                <thead class="uppercase bg-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Kuantitas</th>
                        <th scope="col" class="px-6 py-3">Nama Menu</th>
                        <th scope="col" class="px-6 py-3">Harga</th>
                        <th scope="col" class="px-6 py-3">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query untuk mengambil detail pesanan
                    $query_detail = "
                        SELECT 
                            menu.nama_menu, 
                            detail_pesanan.jumlah, 
                            menu.harga, 
                            (menu.harga * detail_pesanan.jumlah) AS total
                        FROM detail_pesanan
                        JOIN menu ON detail_pesanan.id_menu = menu.id_menu
                        WHERE detail_pesanan.id_pesanan = ? AND detail_pesanan.status = 'Selesai'
                    ";
                    $stmt_detail = $conn->prepare($query_detail);
                    $stmt_detail->bind_param("i", $id_pesanan);
                    $stmt_detail->execute();
                    $result_detail = $stmt_detail->get_result();

                    while ($row = $result_detail->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td class='px-6 py-3'>" . htmlspecialchars($row['jumlah']) . "</td>";
                        echo "<td class='px-6 py-3'>" . htmlspecialchars($row['nama_menu']) . "</td>";
                        echo "<td class='px-6 py-3'>Rp" . number_format($row['harga'], 0, ',', '.') . "</td>";
                        echo "<td class='px-6 py-3'>Rp" . number_format($row['total'], 0, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
    document.getElementById('uang').addEventListener('input', function() {
        var uang = parseFloat(this.value.replace(/Rp|[,.]/g, '')) || 0;
        var total = parseFloat(document.getElementById('total').value.replace(/Rp|[,.]/g, '')) || 0;
        var kembalian = uang - total;
        document.getElementById('kembalian').value = 'Rp' + kembalian.toLocaleString('id-ID');
    });
    </script>
</body>
</html>
