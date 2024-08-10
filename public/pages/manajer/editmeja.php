<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data meja dari database
    $query = "SELECT * FROM meja WHERE nomor_meja = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nomor_meja = $row['nomor_meja'];
        $kapasitas = $row['kapasitas'];
    } else {
        echo "Meja tidak ditemukan.";
        exit();
    }
} else {
    echo "ID meja tidak disediakan.";
    exit();
}

include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_meja = $_POST['id_meja'];
    $no_meja = $_POST['no_meja'];
    $kapasitas = $_POST['Kapasitas'];

    // Update data meja di database
    $query = "UPDATE meja SET nomor_meja = ?, kapasitas = ? WHERE nomor_meja = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $no_meja, $kapasitas, $id_meja);
    
    if ($stmt->execute()) {
        // Redirect ke halaman listmeja.php setelah update berhasil
        header("Location: listmeja.php");
        exit();
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Meja</title>
    <link rel="stylesheet" href="./../../css/output.css">
    <script>
        function confirmUpdate() {
            return confirm('Apakah Anda yakin ingin mengubah data meja ini?');
        }
    </script>
</head>
<body class="bg-[#e2e6ed]">
    <div class="h-16 w-screen flex items-center">
        <a href="./listmeja.php" class="bg-[#11385a] text-white py-1 px-6 text-xl rounded-full font-semibold ml-9">KEMBALI</a>
    </div>
    <div class="m-auto mt-10">
        <form class="max-w-md mx-auto bg-white p-12" method="POST" onsubmit="return confirmUpdate();" action="">
            <div class="text-2xl font-semibold">EDIT MEJA</div>
            <hr class="border-black border-2 w-11 mb-7">
            <input type="hidden" name="id_meja" value="<?php echo htmlspecialchars($nomor_meja); ?>" />
            <div class="relative z-0 w-full mb-5 group">
                <input type="number" name="no_meja" id="no_meja" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " value="<?php echo htmlspecialchars($nomor_meja); ?>" required />
                <label for="no_meja" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 ">No Meja</label>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <input type="number" name="Kapasitas" id="Kapasitas" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " value="<?php echo htmlspecialchars($kapasitas); ?>" required />
                <label for="Kapasitas" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 ">Kapasitas</label>
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">UBAH</button>
        </form>
    </div>
</body>
</html>
