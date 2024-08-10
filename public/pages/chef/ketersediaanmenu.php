<?php
session_start();
include_once "koneksi.php";
if (!isset($_SESSION['id_karyawan'])) {
    die("ID Karyawan tidak ditemukan dalam session.");
  }
  
  $id_karyawan = $_SESSION['id_karyawan'];
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }
  $query = "SELECT nama, jabatan FROM karyawan WHERE id_karyawan = ?";
  $stmt = $conn->prepare($query);
  if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
  }
  $stmt->bind_param("i", $id_karyawan);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    $pegawai = $result->fetch_assoc();
    if (isset($pegawai['nama']) && isset($pegawai['jabatan'])) {
      $nama = $pegawai['nama'];
      $jabatan = $pegawai['jabatan'];
    } else {
      die('Data pegawai tidak lengkap.');
    }
  } else {
    die('Data pegawai tidak ditemukan.');
  }
function getMenuList($searchTerm = "") {
    global $conn;
    $sql = "SELECT id_menu, nama_menu, jenis_menu, harga, status_menu FROM menu";
    if (!empty($searchTerm)) {
        $sql .= " WHERE nama_menu LIKE ?";
    }
    $stmt = $conn->prepare($sql);
    if (!empty($searchTerm)) {
        $searchTerm = "%" . $searchTerm . "%";
        $stmt->bind_param("s", $searchTerm);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $menuList = "";
    if ($result->num_rows > 0) {
        $no = 1;
        while($row = $result->fetch_assoc()) {
            $menuList .= "<tr class='odd:bg-white even:bg-gray-50 text-center'>";
            $menuList .= "<th scope='row' class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>" . $no++ . "</th>";
            $menuList .= "<td class='px-6 py-4'>" . $row["nama_menu"] . "</td>";
            $menuList .= "<td class='px-6 py-4'>" . $row["jenis_menu"] . "</td>";
            $menuList .= "<td class='px-6 py-4'>Rp" . number_format($row["harga"], 2, ',', '.') . "</td>";
            $menuList .= "<td class='px-6 py-4 justify-center gap-6 flex '>";
            $menuList .= "<div><input id='lk_" . $row["id_menu"] . "' type='radio' name='Ketersediaan[" . $row["id_menu"] . "]' value='Sedia' " . ($row["status_menu"] == 'Sedia' ? 'checked' : '') . " onchange='updateStatus(" . $row["id_menu"] . ", \"Sedia\")'>";
            $menuList .= "<label for='lk_" . $row["id_menu"] . "'>Tersedia</label></div>";
            $menuList .= "<div><input id='pr_" . $row["id_menu"] . "' type='radio' name='Ketersediaan[" . $row["id_menu"] . "]' value='Kosong' " . ($row["status_menu"] == '' ? 'checked' : '') . " onchange='updateStatus(" . $row["id_menu"] . ", \"Kosong\")'>";
            $menuList .= "<label for='pr_" . $row["id_menu"] . "'>Kosong</label></div>";
            $menuList .= "</td></tr>";
        }
    } else {
        $menuList .= "<tr><td colspan='5'>Tidak ada data</td></tr>";
    }

    return $menuList;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ketersediaan Menu</title>
  <link href="./../../css/output.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    function updateStatus(menuId, status) {
        $.ajax({
            url: 'update_status_menu.php',
            type: 'POST',
            data: { id_menu: menuId, status_menu: status },
            success: function(response) {
                const res = JSON.parse(response);
                if (res.status === 'success') {
                    console.log('Status updated successfully');
                } else {
                    console.log('Failed to update status');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error: ' + error);
            }
        });
    }
  </script>
</head>
<body class="bg-gray-100 font-sans antialiased">
  <div class="flex">
    <div class="w-72 bg-[#11385a] text-white flex flex-col h-screen">
        <div class="flex items-center justify-center p-6 border-b border-[#355e91]">
            <div class="text-center">
                <div class="w-24 h-24 bg-white rounded-full mx-auto shadow-2xl"></div>
                <h2 class="mt-4 text-xl font-semibold"><?php echo htmlspecialchars($nama); ?></h2>
          <p><?php echo htmlspecialchars($jabatan); ?></p>
            </div>
        </div>
        <nav class="flex flex-col mt-6 ">
            <a href="./listpesanan.php" class="px-4 py-2 my-1 flex flex-row font-semibold hover:bg-[#091c2d]">
                <img src="./../../image/list.png" class="w-8 mr-3" alt="">
                <div class="">LIST PESANAN</div>
            </a>
            <a href="./ketersediaanmenu.php" class="px-4 py-2 flex my-1 flex-row font-semibold  bg-[#091c2d]">
                <img src="./../../image/menu.png" class="w-8 mr-3" alt="">
                <div class="font-semibold  ">MENU</div>
            </a>    
        </nav>
        <div class="flex flex-col mt-auto text-center">
        <button id="logoutButton" class="p-3 font-bold bg-red-500 text-white hover:bg-red-600">Logout</button>
      </div>
    </div>
    <div class="flex-1 overflow-hidden h-screen bg-[#e2e6ed] overflow-y-auto">
        <div class="w-full text-3xl my-8 font-semibold mx-10">
            LIST MENU
        </div>
        <div class="grid grid-cols-4 mt-10 mx-7">
          <div class="m-auto ml-0"></div>
          <div></div>
          <div></div>
          <div>
            <div>
              <form class="flex justify-end px-16" method="GET" action="">   
                  <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only">Cari</label>
                  <div class="relative">
                      <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                          <svg class="w-4 h-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                          </svg>
                      </div>
                      <input type="search" id="default-search" name="search" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 " placeholder="Nama" required />
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
                            <th scope="col" class="px-6 py-3">no</th>
                            <th scope="col" class="px-6 py-3">Nama menu</th>
                            <th scope="col" class="px-6 py-3">jenis</th>
                            <th scope="col" class="px-6 py-3">harga</th>
                            <th scope="col" class="px-6 py-3">status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $searchTerm = isset($_GET['search']) ? $_GET['search'] : "";
                        echo getMenuList($searchTerm);
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
            event.preventDefault();
        }
    });
</script>
