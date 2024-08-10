<?php
session_start();
include "koneksi.php";

// Memeriksa apakah id_karyawan ada di session
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

// Logika Pencarian
$search = '';
$searchResults = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
  $search = $conn->real_escape_string($_POST['search']);
  $sql = "SELECT * FROM karyawan WHERE nama LIKE '%$search%' OR jabatan LIKE '%$search%'";
} else {
  $sql = "SELECT * FROM karyawan";
}
$result = $conn->query($sql);
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
        <a href="./listmenu.php" class="px-4 py-2 flex my-1 flex-row font-semibold  hover:bg-[#091c2d]">
          <img src="./../../image/menu.png  " class="w-8 mr-3" alt="">
          <div class="font-semibold  ">MENU</div>
        </a>
        <a href="./pegawai.php" class="px-4 py-2 flex my-1 flex-row font-semibold  bg-[#091c2d]">
          <img src="./../../image/karyawan.png" class="w-8 mr-3" alt="">
          <div class="font-semibold  ">PEGAWAI</div>
        </a>
        <a href="./listmeja.php" class="px-4 py-2 flex my-1 flex-row font-semibold  hover:bg-[#091c2d]">
          <img src="./../../image/meja.png" class="w-8 mr-3" alt="">
          <div class="font-semibold  ">MEJA</div>
        </a>
      </nav>
      <div class="flex flex-col mt-auto text-center">
        <a href="../logout.php" class="p-3 font-bold bg-red-500 text-white hover:bg-red-600">Logout</a>
      </div>
    </div>

    <!-- Main content -->
    <div class="flex-1 overflow-hidden h-screen bg-[#e2e6ed] overflow-y-auto">
      <div class="w-full text-3xl my-8 font-semibold mx-10">
        LIST KARYAWAN
      </div>

      <div class="grid grid-cols-4 mt-10 mx-7">
        <div class="m-auto ml-0">
          <a href="./tambahpegawai.php"
            class="bg-[#11385a] text-white font-semibold px-3 py-2 rounded-md uppercase">TAMBAH PEGAWAI</a>
        </div>
        <div></div>
        <div></div>
        <div>
          <div>
            <form class="flex justify-end px-16" method="POST" action="">
              <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                  <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                  </svg>
                </div>
                <input type="search" id="default-search" name="search"
                  class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50"
                  placeholder="Nama atau Jabatan" required />
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
                <th scope="col" class="px-6 py-3">Nama Karyawan</th>
                <th scope="col" class="px-6 py-3">Jabatan</th>
                <th scope="col" class="px-6 py-3">Edit</th>
                <th scope="col" class="px-6 py-3">Hapus</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0): ?>
                <?php $no = 1;
                while ($row = $result->fetch_assoc()): ?>
                  <tr class="odd:bg-white even:bg-gray-50 text-center">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"><?php echo $no++; ?></th>
                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['jabatan']); ?></td>
                    <td class="px-6 py-4">
                      <a href="edit.php?id=<?php echo $row['id_karyawan']; ?>"
                        class="font-medium text-blue-600 hover:underline">EDIT</a>
                    </td>
                    <td class="px-6 py-4">
                      <a href="hapus.php?id=<?php echo $row['id_karyawan']; ?>"
                        class="font-medium text-red-600 hover:underline"
                        onclick="return confirm('Yakin ingin menghapus?')">HAPUS</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="px-6 py-4 text-center">Tidak ada data karyawan</td>
                </tr>
              <?php endif; ?>
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

<?php $conn->close(); ?>