<?php
include 'koneksi.php';

// Function to check if a table number already exists
function doesTableNumberExist($conn, $no_meja) {
    $query = "SELECT COUNT(*) FROM meja WHERE nomor_meja = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $no_meja);
    $stmt->execute();
    $stmt->bind_result($count);
    if ($stmt->fetch()) {
        $stmt->close();
        return $count > 0;
    } else {
        $stmt->close();
        return false;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $no_meja = $_POST['no_meja'];
    $kapasitas = $_POST['Kapasitas'];

    // Check if the table number already exists
    if (doesTableNumberExist($conn, $no_meja)) {
        echo '<script>alert("Nomor meja sudah ada. Silakan input nomor meja lain.");</script>';
    } else {
        // Prepare and execute the insert query
        $query = "INSERT INTO meja (nomor_meja, kapasitas, status_meja) VALUES (?, ?, 'Sedia')";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("ii", $no_meja, $kapasitas);

        if ($stmt->execute()) {
            echo '<script>
                if (confirm("Data berhasil ditambahkan. Apakah Anda ingin melanjutkan?")) {
                    window.location.href = "listmeja.php";
                }
            </script>';
        } else {
            echo '<script>alert("Terjadi kesalahan saat menambahkan data.");</script>';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Meja</title>
    <link rel="stylesheet" href="./../../css/output.css">
    <script>
        function confirmSubmit(event) {
            event.preventDefault();
            if (confirm("Apakah Anda yakin ingin menambahkan data ini?")) {
                event.target.submit();
            }
        }
    </script>
</head>
<body class="bg-[#e2e6ed]">
    <div class="h-16 w-screen flex items-center">
        <a href="./listmeja.php" class="bg-[#11385a] text-white py-1 px-6 text-xl rounded-full font-semibold ml-9">KEMBALI</a>
    </div>
    <div class="m-auto mt-10">
        <form class="max-w-md mx-auto bg-white p-12" method="POST" onsubmit="confirmSubmit(event)">
            <div class="text-2xl font-semibold">TAMBAH MEJA</div>
            <hr class="border-black border-2 w-11 mb-7">
            <div class="relative z-0 w-full mb-5 group">
                <input type="number" name="no_meja" id="no_meja" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required />
                <label for="no_meja" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">No Meja</label>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <input type="number" name="Kapasitas" id="Kapasitas" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required />
                <label for="Kapasitas" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Kapasitas</label>
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Tambah</button>
        </form>  
    </div>
</body>
</html>
