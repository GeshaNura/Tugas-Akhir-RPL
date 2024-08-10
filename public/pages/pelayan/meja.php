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

// Proses pengubahan status meja
if (isset($_POST['action']) && isset($_POST['id_meja'])) {
  $id_meja = $_POST['id_meja'];
  $action = $_POST['action'];

  if ($action == 'tambah_tamu') {
    $status_meja = 'Ditempati';
  } elseif ($action == 'bersihkan') {
    $status_meja = 'Sedia';
  } else {
    die('Aksi tidak valid.');
  }

  // Update status meja
  $updateQuery = "UPDATE meja SET status_meja = ? WHERE id_meja = ?";
  $stmt = $conn->prepare($updateQuery);
  if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
  }
  $stmt->bind_param("si", $status_meja, $id_meja);
  $stmt->execute();

  if ($stmt->affected_rows > 0) {
    echo "<script>alert('Status meja berhasil diperbarui.'); window.location.href = 'meja.php';</script>";
  } else {
    echo "<script>alert('Gagal memperbarui status meja.'); window.location.href = 'meja.php';</script>";
  }
}

// Proses pencarian meja
$kapasitasCari = '';
if (isset($_POST['kapasitas'])) {
  $kapasitasCari = $_POST['kapasitas'];
}

if ($kapasitasCari !== '') {
  // Pencarian berdasarkan kapasitas dan status meja
  $query = "SELECT nomor_meja, kapasitas, status_meja FROM meja WHERE kapasitas LIKE ?";
  $param = "%$kapasitasCari%";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $param);
} else {
  // Tampilkan seluruh data meja jika tidak ada pencarian
  $query = "SELECT nomor_meja, kapasitas, status_meja FROM meja";
  $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();

$mejaData = [];
while ($row = $result->fetch_assoc()) {
  $mejaData[] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Meja</title>
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
        <a href="./meja.php" class="px-4 py-2 my-1 flex flex-row font-semibold bg-[#091c2d]">
          <img src="./../../image/dashboard.png" class="w-8 mr-3" alt="">
          <div class="">MEJA</div>
        </a>
      </nav>
      <div class="flex flex-col mt-auto text-center">
        <button id="logoutButton" class="p-3 font-bold bg-red-500 text-white hover:bg-red-600">Logout</button>
      </div>
    </div>

    <!-- Main content -->
    <div class="flex-1 overflow-hidden h-screen bg-[#e2e6ed] overflow-y-auto">
      <div class="w-full text-3xl my-8 font-semibold mx-10">
        PEMILIHAN MEJA
      </div>
      <div class="grid grid-cols-4 mt-10 mx-7">
        <div class="m-auto ml-0"></div>
        <div></div>
        <div></div>
        <div>
          <div>
            <form method="POST" class="flex justify-end px-">
              <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only">Cari</label>
              <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                  <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                  </svg>
                </div>
                <input type="number" name="kapasitas" id="default-search" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50" placeholder="Kapasitas" value="<?php echo htmlspecialchars($kapasitasCari); ?>" />
                <button type="submit" class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Cari</button>
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
                <th scope="col" class="px-6 py-3">No Meja</th>
                <th scope="col" class="px-6 py-3">Kapasitas</th>
                <th scope="col" class="px-6 py-3">Status</th>
                <th scope="col" class="px-6 py-3">Ubah Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($mejaData as $meja): ?>
              <tr class="odd:bg-white even:bg-gray-50 text-center">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"><?php echo htmlspecialchars($meja['nomor_meja']); ?></th>
                <td class="px-6 py-4"><?php echo htmlspecialchars($meja['kapasitas']); ?></td>
                <td class="px-6 py-4">
                  <?php echo $meja['status_meja'] == 'Sedia' ? 'KOSONG' : 'TERISI'; ?>
                </td>
                <td class="px-6 py-4">
                  <?php if ($meja['status_meja'] == 'Sedia'): ?>
                    <button type="button" class="bg-green-600 px-5 py-1 mx-1 rounded-xl font-medium text-white" onclick="confirmChange('tambah_tamu', '<?php echo htmlspecialchars($meja['nomor_meja']); ?>')">TAMBAH TAMU</button>
                  <?php else: ?>
                    <button type="button" class="bg-yellow-500 px-5 py-1 mx-1 rounded-xl font-medium text-white" onclick="confirmChange('bersihkan', '<?php echo htmlspecialchars($meja['nomor_meja']); ?>')">BERSIHKAN</button>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
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
      event.preventDefault();
    }
  });

  function confirmChange(action, idMeja) {
    var confirmation = confirm("Apakah Anda yakin ingin mengubah status meja?");
    if (confirmation) {
      var form = document.createElement('form');
      form.method = 'POST';
      form.action = 'meja.php'; // Ganti dengan URL yang sesuai jika berbeda

      var inputAction = document.createElement('input');
      inputAction.type = 'hidden';
      inputAction.name = 'action';
      inputAction.value = action;
      form.appendChild(inputAction);

      var inputIdMeja = document.createElement('input');
      inputIdMeja.type = 'hidden';
      inputIdMeja.name = 'id_meja';
      inputIdMeja.value = idMeja;
      form.appendChild(inputIdMeja);

      document.body.appendChild(form);
      form.submit();
    }
  }
</script>
