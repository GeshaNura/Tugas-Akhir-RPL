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

// Function to get the total number of tables
function getTotalTables($conn)
{
  $query = "SELECT COUNT(nomor_meja) as total_tables FROM meja";
  $result = $conn->query($query);

  if ($result === false) {
    die("Error executing query: " . $conn->error);
  }

  $row = $result->fetch_assoc();
  return $row['total_tables'];
}

$totalTables = getTotalTables($conn);

// Function to get table data
function getTableData($conn, $searchTerm = null)
{
  $searchTerm = $searchTerm ? trim($searchTerm) : '';

  $query = "SELECT nomor_meja, kapasitas, status_meja FROM meja";

  if ($searchTerm !== '') {
    if ($searchTerm === 'Bersih') {
      $searchTerm = 'Sedia';
    }
    $query .= " WHERE nomor_meja LIKE ? OR kapasitas LIKE ? OR status_meja LIKE ?";
  }

  $stmt = $conn->prepare($query);
  if ($searchTerm !== '') {
    $likeSearchTerm = '%' . $searchTerm . '%';
    $stmt->bind_param("sss", $likeSearchTerm, $likeSearchTerm, $likeSearchTerm);
  }
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result === false) {
    die("Error executing query: " . $conn->error);
  }

  $tableData = [];
  while ($row = $result->fetch_assoc()) {
    // Change status_meja from 'Sedia' to 'Bersih'
    if ($row['status_meja'] == 'Sedia') {
      $row['status_meja'] = 'Bersih';
    }
    $tableData[] = $row;
  }
  return $tableData;
}

// Handle search form submission
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$tableData = getTableData($conn, $searchTerm);

// Handle delete form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nomor_meja'])) {
  $nomor_meja = $_POST['nomor_meja'];

  // Menyiapkan query untuk menghapus meja
  $query = "DELETE FROM meja WHERE nomor_meja = ?";
  $stmt = $conn->prepare($query);

  // Periksa apakah statement berhasil dipersiapkan
  if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
  }

  $stmt->bind_param("i", $nomor_meja);

  if ($stmt->execute()) {
    // Redirect ke halaman listmeja.php setelah penghapusan berhasil
    header("Location: listmeja.php");
    exit();
  } else {
    die("Error executing query: " . $stmt->error);
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>List Meja</title>
  <link href="./../../css/output.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script>
    function confirmDelete() {
      return confirm('Apakah Anda yakin ingin menghapus meja ini?');
    }
  </script>
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
          <img src="./../../image/dashboard.png" class="w-8 mr-3" alt="">
          <div class="">DASHBOARD</div>
        </a>
        <a href="./listmenu.php" class="px-4 py-2 flex my-1 flex-row font-semibold hover:bg-[#091c2d]">
          <img src="./../../image/menu.png" class="w-8 mr-3" alt="">
          <div class="font-semibold">MENU</div>
        </a>
        <a href="./pegawai.php" class="px-4 py-2 flex my-1 flex-row font-semibold hover:bg-[#091c2d]">
          <img src="./../../image/karyawan.png" class="w-8 mr-3" alt="">
          <div class="font-semibold">PEGAWAI</div>
        </a>
        <a href="./listmeja.php" class="px-4 py-2 flex my-1 flex-row font-semibold bg-[#091c2d]">
          <img src="./../../image/meja.png" class="w-8 mr-3" alt="">
          <div class="font-semibold">MEJA</div>
        </a>
      </nav>
      <div class="flex flex-col mt-auto text-center">
        <button id="logoutButton" class="p-3 font-bold bg-red-500 text-white hover:bg-red-600">Logout</button>
      </div>
    </div>

    <!-- Main menu -->
    <div class="flex-1 overflow-hidden h-screen bg-[#e2e6ed] overflow-y-auto">
      <div class="w-full text-3xl my-8 font-semibold mx-10">
        LIST MEJA
      </div>
      <div class="grid grid-cols-4 h-52 gap-6 mx-7">
        <div class="grid grid-rows-4 grid-cols-1 p-3 shadow-xl text-white bg-[#11385a]">
          <div class="flex items-center justify-between">
            <h3 class="font-semibold uppercase">jumlah meja</h3>
            <img src="./../../image/meja.png" alt="" class="w-10">
          </div>
          <div class="flex justify-center items-center row-span-2">
            <p class="text-3xl font-semibold"><?php echo $totalTables; ?> MEJA</p>
          </div>
          <div class="text-sm pt-5">
          </div>
        </div>
      </div>
      <div class="grid grid-cols-4 mt-10 mx-7">
        <div class="m-auto ml-0">
          <a href="./tambahmeja.php" class="bg-[#11385a] text-white font-semibold px-3 py-2 rounded-md">TAMBAH MEJA</a>
        </div>
        <div></div>
        <div></div>
        <div>
          <div>
            <form class="flex justify-end px-16" method="GET">
              <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                  <svg class="w-4 h-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                  </svg>
                </div>
                <input type="text" id="default-search" name="search"
                  class="block p-2.5 w-full ps-10 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Cari Meja">
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto mx-7 mt-6">
        <table class="w-full">
          <thead class="uppercase bg-[#11385a] text-white">
            <tr>
              <th scope="col" class="px-6 py-3">Nomor Meja</th>
              <th scope="col" class="px-6 py-3">Kapasitas</th>
              <th scope="col" class="px-6 py-3">Status</th>
              <th scope="col" class="px-6 py-3">Edit</th>
              <th scope="col" class="px-6 py-3">Hapus</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($tableData as $row): ?>
              <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 text-center">
                <td class="px-6 py-4"><?php echo htmlspecialchars($row['nomor_meja']); ?></td>
                <td class="px-6 py-4"><?php echo htmlspecialchars($row['kapasitas']); ?></td>
                <td class="px-6 py-4"><?php echo htmlspecialchars($row['status_meja']); ?></td>
                <td class="px-6 py-4">
                  <a href="./editmeja.php?id=<?php echo htmlspecialchars($row['nomor_meja']); ?>"
                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">EDIT</a>
                </td>
                <td class="px-6 py-4">
                  <form action="./listmeja.php" method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                    <input type="hidden" name="nomor_meja" value="<?php echo htmlspecialchars($row['nomor_meja']); ?>">
                    <button type="submit"
                      class="font-medium text-red-600 dark:text-red-500 hover:underline">HAPUS</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
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