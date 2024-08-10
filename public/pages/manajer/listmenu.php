<?php
session_start();
include "koneksi.php";

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

// Fungsi untuk mendapatkan total menu
function getTotalMenu($conn)
{
    $query = "SELECT COUNT(id_menu) as total FROM menu";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    } else {
        return 0;
    }
}

// Fungsi untuk mengambil data menu
function getMenuData($conn, $search = '')
{
    $searchParam = '%' . $search . '%';
    $query = "SELECT id_menu, nama_menu, jenis_menu, harga, rekomendasi FROM menu WHERE nama_menu LIKE ? OR jenis_menu LIKE ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $searchParam, $searchParam);
    $stmt->execute();
    return $stmt->get_result();
}

// Fungsi untuk mendapatkan menu terlaris hari ini
function getMostSoldMenuToday($conn)
{
    $today = date('Y-m-d');
    $sql = "
    SELECT 
        menu.nama_menu, 
        SUM(detail_pesanan.jumlah) as total_sold 
    FROM 
        detail_pesanan 
    JOIN 
        pesanan 
    ON 
        detail_pesanan.id_pesanan = pesanan.id_pesanan 
    JOIN 
        menu 
    ON 
        detail_pesanan.id_menu = menu.id_menu 
    WHERE 
        DATE(pesanan.tanggal_waktu_pesanan) = ? 
    GROUP BY 
        menu.id_menu 
    ORDER BY 
        total_sold DESC 
    LIMIT 1";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param('s', $today);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Hapus menu jika ada ID menu di query string
if (isset($_GET['id'])) {
    $id_menu = intval($_GET['id']);

    // Query untuk menghapus data dari tabel menu
    $query = "DELETE FROM menu WHERE id_menu = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("i", $id_menu);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Menu berhasil dihapus.";
    } else {
        $_SESSION['message'] = "Error deleting menu: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: listmenu.php"); // Redirect kembali ke halaman daftar menu
    exit();
}

// Update rekomendasi menu jika ada POST request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rekomendasi'])) {
    $id_rekomendasi = intval($_POST['rekomendasi']);

    // Set semua menu menjadi 'tidak' terlebih dahulu
    $query = "UPDATE menu SET rekomendasi = 'tidak'";
    $conn->query($query);

    // Set menu yang dipilih menjadi 'ya'
    $query = "UPDATE menu SET rekomendasi = 'ya' WHERE id_menu = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_rekomendasi);
    $stmt->execute();

    $_SESSION['message'] = "Menu rekomendasi telah diperbarui.";
    header("Location: listmenu.php"); // Redirect untuk menghindari submit ulang form
    exit();
}

// Ambil parameter pencarian dari query string jika ada
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Query untuk mendapatkan data menu
$menuData = getMenuData($conn, $search);
$totalMenu = getTotalMenu($conn);
$mostSoldMenu = getMostSoldMenuToday($conn);

// Ambil pesan dari session untuk notifikasi
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);

// Ambil menu rekomendasi yang sedang aktif
$query = "SELECT nama_menu FROM menu WHERE rekomendasi = 'ya'";
$result = $conn->query($query);
$recommendedMenu = $result->num_rows > 0 ? $result->fetch_assoc()['nama_menu'] : 'Tak ada menu yang direkomendasikan.';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="./../../css/output.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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
            <nav class="flex flex-col mt-6 ">
                <a href="./dashboard.php" class="px-4 py-2 my-1 flex flex-row font-semibold hover:bg-[#091c2d]">
                    <img src="./../../image/dashboard.png" class="w-8 mr-3" alt="">
                    <div class="">DASHBOARD</div>
                </a>
                <a href="./listmenu.php" class="px-4 py-2 flex my-1 flex-row font-semibold  bg-[#091c2d]">
                    <img src="./../../image/menu.png  " class="w-8 mr-3" alt="">
                    <div class="font-semibold  ">MENU</div>
                </a>
                <a href="./pegawai.php" class="px-4 py-2 flex my-1 flex-row font-semibold  hover:bg-[#091c2d]">
                    <img src="./../../image/karyawan.png" class="w-8 mr-3" alt="">
                    <div class="font-semibold  ">PEGAWAI</div>
                </a>
                <a href="./listmeja.php" class="px-4 py-2 flex my-1 flex-row font-semibold  hover:bg-[#091c2d]">
                    <img src="./../../image/meja.png" class="w-8 mr-3" alt="">
                    <div class="font-semibold  ">MEJA</div>
                </a>
            </nav>
            <div class="flex flex-col mt-auto text-center">
                <button id="logoutButton" class="p-3 font-bold bg-red-500 text-white hover:bg-red-600">Logout</button>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 overflow-hidden h-screen bg-[#e2e6ed] overflow-y-auto">
            <div class="w-full text-3xl my-8 font-semibold mx-10">LIST MENU</div>
            <div class="grid grid-cols-4 h-52 gap-6 mx-7">
                <div class="grid grid-rows-4 grid-cols-1 p-3 shadow-xl text-white bg-[#11385a]">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold uppercase">Jumlah Menu</h3>
                        <img src="./../../image/menu.png" alt="" class="w-10">
                    </div>
                    <div class="flex justify-center items-center row-span-2">
                        <p class="text-3xl font-semibold"><?php echo $totalMenu; ?> MENU</p>
                    </div>
                </div>
                <div class="grid grid-rows-4 grid-cols-1 p-3 shadow-xl bg-white">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold uppercase">Menu Terlaris Hari Ini</h3>
                        <img src="./../../image/bintang.png" alt="" class="w-10">
                    </div>
                    <div class="flex justify-center items-center row-span-2">
                        <?php
                        if ($mostSoldMenu) {
                            echo "<p class='text-3xl font-semibold'>" . htmlspecialchars($mostSoldMenu['nama_menu']) . "</p>";
                        } else {
                            echo "<p class='text-3xl font-semibold'>Tidak ada data penjualan hari ini.</p>";
                        }
                        ?>
                    </div>
                    <div class="text-sm pt-5">
                        <?php
                        if ($mostSoldMenu) {
                            echo "Terjual sebanyak: " . htmlspecialchars($mostSoldMenu['total_sold']);
                        }
                        ?>
                    </div>
                </div>
                <div class="col-span-2 grid grid-rows-4 grid-cols-1 p-3 shadow-xl bg-white">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold uppercase">Menu Rekomendasi</h3>
                        <img src="./../../image/thumb.png" alt="" class="w-10">
                    </div>
                    <div class="flex justify-center items-center row-span-2">
                        <p class='text-3xl font-semibold'><?php echo htmlspecialchars($recommendedMenu); ?></p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-4 mt-10 mx-7">
                <div class="m-auto ml-0">
                    <a href="./tambahmenu.php" class="bg-[#11385a] text-white font-semibold px-3 py-2 rounded-md">TAMBAH
                        MENU</a>
                </div>
                <div></div>
                <div></div>
                <div>
                    <div>
                        <form class="flex justify-end px-16" method="GET">
                            <label for="default-search"
                                class="mb-2 text-sm font-medium text-gray-900 sr-only">Cari</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                    </svg>
                                </div>
                                <input type="search" id="default-search" name="search"
                                    class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50"
                                    placeholder="Cari Menu" value="<?php echo htmlspecialchars($search); ?>" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="mt-2 mx-7 h-screen overflow-y-scroll">
                <div class="relative shadow-md">
                    <table class="w-full">
                        <thead class="uppercase bg-[#11385a] text-white">
                            <tr>
                                <th scope="col" class="px-6 py-3">No</th>
                                <th scope="col" class="px-6 py-3">Nama Menu</th>
                                <th scope="col" class="px-6 py-3">Jenis</th>
                                <th scope="col" class="px-6 py-3">Harga</th>
                                <th scope="col" class="px-6 py-3">Menu Rekomendasi</th>
                                <th scope="col" class="px-6 py-3">Edit</th>
                                <th scope="col" class="px-6 py-3">Hapus</th> <!-- Tambahkan kolom Hapus -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Inisialisasi nomor urut
                            $no = 1;

                            // Loop melalui data menu dan tampilkan dalam tabel
                            while ($row = mysqli_fetch_assoc($menuData)) {
                                $checked = $row['rekomendasi'] === 'ya' ? 'checked' : '';
                                echo "<tr class='odd:bg-white even:bg-gray-50 text-center'>";
                                echo "<th scope='row' class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap'>{$no}</th>";
                                echo "<td class='px-6 py-4'>{$row['nama_menu']}</td>";
                                echo "<td class='px-6 py-4'>{$row['jenis_menu']}</td>";
                                echo "<td class='px-6 py-4'>Rp" . number_format($row['harga'], 0, ',', '.') . "</td>";
                                echo "<td class='px-6 py-4'>
                                        <form method='POST' action='listmenu.php'>
                                            <input type='hidden' name='rekomendasi' value='{$row['id_menu']}'>
                                            <button type='submit' class='bg-blue-500 text-white p-2 rounded'>Set Rekomendasi</button>
                                        </form>
                                      </td>";
                                echo "<td class='px-6 py-4'><a href='./edit_menu.php?id={$row['id_menu']}' class='font-medium text-blue-600 hover:underline'>EDIT</a></td>";
                                echo "<td class='px-6 py-4'><a href='listmenu.php?id={$row['id_menu']}' class='font-medium text-red-600 hover:underline' onclick='return confirm(\"Are you sure you want to delete this item?\")'>HAPUS</a></td>";
                                echo "</tr>";

                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    document.getElementById('logoutButton').addEventListener('click', function (event) {
        var confirmation = confirm("Apakah Anda yakin ingin logout?");
        if (confirmation) {
            window.location.href = '../logout.php';
        } else {
            // Pengguna menekan "Tidak," tidak melakukan apa-apa
            event.preventDefault();
        }
    });
</script>
