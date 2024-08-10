<?php
session_start();
include "../koneksi.php";
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

function fetchOrderDetails($conn) {
    $sql = "SELECT dp.id_pesanan, dp.id_menu, m.nama_menu, dp.deskripsi_pesanan, dp.jumlah, dp.status 
            FROM detail_pesanan dp
            JOIN menu m ON dp.id_menu = m.id_menu
            WHERE status = 'Masih Diproses'
            ORDER BY dp.id_pesanan";
    $result = $conn->query($sql);

    $orders = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[$row['id_pesanan']][] = $row;
        }
    }
    return $orders;
}

function completeOrder($conn, $orderId, $menuId) {
  $sql = "UPDATE detail_pesanan SET status = 'Selesai' WHERE id_pesanan = ? AND id_menu = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $orderId, $menuId);
  $stmt->execute();
  $stmt->close();
}
function cancelOrder($conn, $orderId, $menuId) {
  $sql = "UPDATE detail_pesanan SET status = 'Tidak dapat dibuat' WHERE id_pesanan = ? AND id_menu = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $orderId, $menuId);
  $stmt->execute();
  $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['complete'])) {
        completeOrder($conn, $_POST['order_id'], $_POST['menu_id']);
    } elseif (isset($_POST['delete'])) {
        cancelOrder($conn, $_POST['order_id'], $_POST['menu_id']);
    }
    echo "<script>window.location.href=window.location.href;</script>";
    exit();
}
$orders = fetchOrderDetails($conn);
function displayOrderDetails($orders) {
    foreach ($orders as $orderId => $orderDetails) {
        echo "
        <div class='p-3 shadow-xl h-[700px] bg-[#11385a]'>
            <div class='grid-rows-1'>
                <h3 class='font-semibold text-center text-7xl text-white'>$orderId</h3>
                <hr class='border-white'>
            </div>
            <div class='bg-white overflow-y-auto max-h-[550px] mt-3 p-3'>";

        foreach ($orderDetails as $order) {
            $menuName = $order['nama_menu'];
            $menuDescription = $order['deskripsi_pesanan'];
            $quantity = $order['jumlah'];
            $status = $order['status'];
            $statusClass = $status == 'Selesai' ? 'text-gray-400' : '';
            $buttonDisabled = $status == 'Selesai' ? 'disabled' : '';
            $buttonClass = $status == 'Selesai' ? 'bg-green-300 text-white' : 'bg-green-600 text-white';
            $readyMessage = $status == 'Selesai' ? "<div class='ml-2 text-green-600 font-bold'>hidangan siap</div>" : '';

            echo "
                <div class='grid grid-cols-6 pt-2'>
                    <div class='$statusClass'>$quantity X</div>
                    <div class='col-span-3 $statusClass'>$menuName</div>
                    <div class='col-span-2 justify-between flex'>
                        <form method='POST' action=''>
                            <input type='hidden' name='order_id' value='$orderId'>
                            <input type='hidden' name='menu_id' value='{$order['id_menu']}'>
                            <button type='submit' name='complete' class='$buttonClass py-1 px-4 uppercase rounded-xl' $buttonDisabled>selesai</button>
                        </form>
                        <form method='POST' action=''>
                            <input type='hidden' name='order_id' value='$orderId'>
                            <input type='hidden' name='menu_id' value='{$order['id_menu']}'>
                            <button type='submit' name='delete' class='bg-red-600 text-white py-1 px-4 uppercase rounded-xl'>hapus</button>
                        </form>
                        $readyMessage
                    </div>
                </div>
                <div class='text-gray-500 ml-10 mb-3'>$menuDescription</div>";
        }

        echo "
            </div>
        </div>";
    }
}
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
    <div class="w-72 bg-[#11385a] text-white flex flex-col h-screen">
      <div class="flex items-center justify-center p-6 border-b border-[#355e91]">
        <div class="text-center">
          <div class="w-24 h-24 bg-white rounded-full mx-auto shadow-2xl"></div>
          <h2 class="mt-4 text-xl font-semibold"><?php echo htmlspecialchars($nama); ?></h2>
          <p><?php echo htmlspecialchars($jabatan); ?></p>
        </div>
      </div>
      <nav class="flex flex-col mt-6 ">
        <a href="./listpesanan.php" class="px-4 py-2 my-1 flex flex-row font-semibold bg-[#091c2d]">
          <img src="./../../image/list.png" class="w-8 mr-3" alt="">
          <div>LIST PESANAN</div>
        </a>
        <a href="./ketersediaanmenu.php" class="px-4 py-2 flex my-1 flex-row font-semibold hover:bg-[#091c2d]">
          <img src="./../../image/menu.png" class="w-8 mr-3" alt="">
          <div class="font-semibold">MENU</div>
        </a>    
      </nav>
      <div class="flex flex-col mt-auto text-center">
        <button id="logoutButton" class="p-3 font-bold bg-red-500 text-white hover:bg-red-600">Logout</button>
      </div>
    </div>
    <div class="flex-1 overflow-hidden h-screen bg-[#e2e6ed] overflow-y-auto">
      <div class="w-full text-3xl my-8 font-semibold mx-10">
        LIST PESANAN
      </div>
      <div class="grid grid-cols-2 h-auto gap-6 mx-7 bg-white">
        <?php displayOrderDetails($orders); ?>
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
