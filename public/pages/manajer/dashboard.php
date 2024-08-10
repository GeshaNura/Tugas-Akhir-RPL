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

// Query untuk menghitung total pendapatan
$totalPendapatanQuery = 'SELECT SUM(total_harga) AS total FROM pembayaran';
$result = $conn->query($totalPendapatanQuery);
if ($result === false) {
  die("Error executing query: " . $conn->error);
}
$totalPendapatan = $result->fetch_assoc()['total'];

// Ambil PENDAPATAN HARI INI
$tanggalHariIni = date('Y-m-d');
$pendapatanHariIniQuery = 'SELECT SUM(total_harga) AS total FROM pembayaran WHERE DATE(tanggal_waktu_pembayaran) = ?';
$stmt = $conn->prepare($pendapatanHariIniQuery);
if ($stmt === false) {
  die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("s", $tanggalHariIni);
$stmt->execute();
$result = $stmt->get_result();
if ($result === false) {
  die("Error executing query: " . $stmt->error);
}
$pendapatanHariIni = $result->fetch_assoc()['total'];

// Ambil pendapatan per bulan
$pendapatanPerBulanQuery = "
    SELECT MONTH(tanggal_waktu_pembayaran) AS bulan, SUM(total_harga) AS total
    FROM pembayaran
    GROUP BY MONTH(tanggal_waktu_pembayaran)
";
$result = $conn->query($pendapatanPerBulanQuery);
if ($result === false) {
  die("Error executing query: " . $conn->error);
}
$pendapatanPerBulan = array_fill(1, 12, 0); // Inisialisasi array untuk 12 bulan

while ($row = $result->fetch_assoc()) {
  $pendapatanPerBulan[(int) $row['bulan']] = (int) $row['total'];
}

// Ambil data bulan dan jumlah pengunjung
$jumlahPengunjungQuery = "
  SELECT MONTH(p.tanggal_waktu_pesanan) AS bulan, SUM(m.kapasitas) AS jumlah_pengunjung
  FROM pesanan p
  JOIN meja m ON p.id_meja = m.id_meja
  GROUP BY MONTH(p.tanggal_waktu_pesanan)
";
$result = $conn->query($jumlahPengunjungQuery);
if ($result === false) {
  die("Error executing query: " . $conn->error);
}
$jumlahPengunjungPerBulan = array_fill(1, 12, 0); // Inisialisasi array untuk 12 bulan

while ($row = $result->fetch_assoc()) {
  $jumlahPengunjungPerBulan[(int) $row['bulan']] = (int) $row['jumlah_pengunjung'];
}


// Query untuk menghitung jumlah item yang terjual
$totalItemsSoldQuery = "SELECT COUNT(id_menu) AS total_items_sold FROM detail_pesanan";
$result = $conn->query($totalItemsSoldQuery);
if ($result === false) {
  die("Error executing query: " . $conn->error);
}
$totalItemsSold = $result->fetch_assoc()['total_items_sold'];

// Function untuk mengambil jumlah item terjual berdasarkan jenis menu
function getTotalItemsSoldByJenis($jenis, $conn)
{
  $query = "SELECT COUNT(dp.id_menu) AS total_items_sold
              FROM detail_pesanan dp
              JOIN menu m ON dp.id_menu = m.id_menu
              WHERE m.jenis_menu = ?";
  $stmt = $conn->prepare($query);
  if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
  }
  $stmt->bind_param("s", $jenis);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result === false) {
    die("Error executing query: " . $stmt->error);
  }
  return $result->fetch_assoc()['total_items_sold'];
}

// Ambil jumlah item terjual berdasarkan jenis menu
$jenisMenu = ['Makanan', 'Minuman', 'Penutup', 'Kudapan'];
$itemsSoldByJenis = [];
foreach ($jenisMenu as $jenis) {
  $itemsSoldByJenis[$jenis] = getTotalItemsSoldByJenis($jenis, $conn);
}

// Format angka untuk tampilan
function formatRupiah($angka)
{
  return 'Rp.' . number_format($angka, 0, ',', '.');
}

$stmt->close();
$conn->close();
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
        <a href="./dashboard.php" class="px-4 py-2 my-1 flex flex-row font-semibold bg-[#091c2d]">
          <img src="./../../image/dashboard.png" class="w-8 mr-3" alt="">
          <div class="">DASHBOARD</div>
        </a>
        <a href="./listmenu.php" class="px-4 py-2 flex my-1 flex-row font-semibold  hover:bg-[#091c2d]">
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

    <!-- Main menu -->
    <div class="flex-1 overflow-hidden h-screen bg-[#e2e6ed] overflow-y-auto">
      <div class="w-full text-3xl my-8 font-semibold mx-10">
        DASHBOARD
      </div>
      <div class="grid grid-cols-4 h-52 gap-6 mx-7">
        <div class="grid grid-rows-4 grid-cols-1 p-3 shadow-xl text-white bg-[#11385a]">
          <div class="flex items-center justify-between">
            <h3 class="font-semibold">TOTAL PENDAPATAN</h3>
            <img src="./../../image/pendapatan.png" alt="" class="w-10">
          </div>
          <div class="flex justify-center items-center row-span-2">
            <p class="text-3xl font-semibold"><?php echo formatRupiah($totalPendapatan); ?></p>
          </div>
          <div class="text-sm pt-5">
            sampai tanggal : <?php echo date('d-m-Y'); ?>
          </div>
        </div>
        <div class="grid grid-rows-4 grid-cols-1 p-3 shadow-xl bg-white">
          <div class="flex items-center justify-between">
            <h3 class="font-semibold">PENDAPATAN HARI INI</h3>
            <img src="./../../image/pendapatan1.png" alt="" class="w-10">
          </div>
          <div class="flex justify-center items-center row-span-2">
            <p class="text-3xl font-semibold"><?php echo formatRupiah($pendapatanHariIni); ?></p>
          </div>
          <div class="text-sm pt-5">
            tanggal : <?php echo date('d-m-Y'); ?>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-5 h-52 gap-6 mx-7 mt-10">
        <div class="grid grid-rows-4 grid-cols-1 p-3 shadow-xl text-white bg-[#11385a]">
          <div class="flex items-center justify-between">
            <h3 class="font-semibold uppercase">Penjualan seluruh item</h3>
            <img src="./../../image/menu.png" alt="" class="w-10">
          </div>
          <div class="flex justify-center items-center row-span-2">
            <p class="text-3xl font-semibold"><?php echo $totalItemsSold; ?> ITEM</p>
          </div>
          <div class="text-sm pt-5">
            tanggal : <?php echo date('d-m-Y'); ?>
          </div>
        </div>
        <?php foreach ($itemsSoldByJenis as $jenis => $total): ?>
          <div class="grid grid-rows-4 grid-cols-1 p-3 shadow-xl bg-white">
            <div class="flex items-center justify-between">
              <h3 class="font-semibold uppercase"><?php echo htmlspecialchars($jenis); ?></h3>
            </div>
            <div class="flex justify-center items-center row-span-2">
              <p class="text-3xl font-semibold"><?php echo $total; ?> ITEM</p>
            </div>
            <div class="text-sm pt-5">
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="grid grid-cols-2 gap-6 mx-7 my-10">
        <div class="p-3 shadow-xl bg-white">
          <div class="flex items-center justify-between">
            <h3 class="font-semibold">GRAFIK PENDAPATAN</h3>
          </div>
          <div id="chart"></div>
        </div>
        <div class="p-3 shadow-xl bg-white">
          <div class="flex items-center justify-between">
            <h3 class="font-semibold">GRAFIK PELANGGAN</h3>
          </div>
          <div id="pelangganChart"></div>
        </div>
      </div>
    </div>
  </div>
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

    document.addEventListener('DOMContentLoaded', function () {
      var pendapatanPerBulan = <?php echo json_encode(array_values($pendapatanPerBulan)); ?>;
      var jumlahPengunjungPerBulan = <?php echo json_encode(array_values($jumlahPengunjungPerBulan)); ?>;

      var optionsPendapatan = {
        chart: {
          type: 'line'
        },
        series: [{
          name: 'Pendapatan',
          data: pendapatanPerBulan
        }],
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        }
      };

      var chartPendapatan = new ApexCharts(document.querySelector("#chart"), optionsPendapatan);
      chartPendapatan.render();

      var optionsPelanggan = {
        chart: {
          type: 'line'
        },
        series: [{
          name: 'Jumlah Pelanggan',
          data: jumlahPengunjungPerBulan
        }],
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        }
      };

      var chartPelanggan = new ApexCharts(document.querySelector("#pelangganChart"), optionsPelanggan);
      chartPelanggan.render();
    });
  </script>
</body>

</html>
