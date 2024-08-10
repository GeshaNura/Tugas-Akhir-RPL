<?php
session_start();
include "../koneksi.php";

// Fungsi untuk menghasilkan nama file unik
function generateUniqueFilename($originalName) {
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid() . '.' . $ext;
}

// Proses upload file
if (isset($_POST['submit'])) {
    $nama_menu = $_POST['nama_menu'];
    $jenis_menu = $_POST['jenis_menu'];
    $deskripsi_menu = $_POST['message'];
    $harga = $_POST['harga'];
    $kapasitas = $_POST['kapasitas'];

    // Validasi data
    if (!empty($nama_menu) && !empty($jenis_menu) && !empty($deskripsi_menu) && isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file'];

        // Validasi tipe file
        $allowedTypes = ['image/png', 'image/jpeg'];
        $fileType = mime_content_type($file['tmp_name']);
        $fileName = generateUniqueFilename($file['name']);
        $target_dir = __DIR__ . "/../../image/"; // Path ke folder image
        $target_file = $target_dir . $fileName;

        // Debug: Tampilkan path file yang akan digunakan
        echo "Target directory: " . realpath($target_dir) . "<br>";
        echo "Target file path: " . realpath($target_file) . "<br>";

        if (in_array($fileType, $allowedTypes)) {
            // Upload file ke folder
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                // Menyimpan data ke dalam database
                $query = "INSERT INTO menu (nama_menu, jenis_menu, harga, kapasitas_menu, deskripsi_menu, gambar_menu) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);

                if ($stmt === false) {
                    die("Error preparing statement: " . $conn->error);
                }

                $stmt->bind_param("ssisis", $nama_menu, $jenis_menu, $harga, $kapasitas, $deskripsi_menu, $fileName);

                if ($stmt->execute()) {
                    header("Location: listmenu.php");
                    exit;
                } else {
                    echo "Error adding menu: " . $stmt->error;
                }

                $stmt->close();
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "Invalid file type. Only PNG and JPG files are allowed.";
        }
    } else {
        echo "Please fill all fields and select a file.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu</title>
    <link rel="stylesheet" href="./../../css/output.css">
    <script>
        function confirmSubmit() {
            return confirm("Apakah Anda yakin ingin menambahkan menu ini?");
        }
    </script>
</head>
<body class="bg-[#e2e6ed]">
    <div class="h-16 w-screen flex items-center">
        <a href="./listmenu.php" class="bg-[#11385a] text-white py-1 px-6 text-xl rounded-full font-semibold ml-9">KEMBALI</a>
    </div>
    <div class="m-auto mt-10">
        <form class="max-w-md mx-auto bg-white p-12" method="post" enctype="multipart/form-data" onsubmit="return confirmSubmit()">
            <div class="text-2xl font-semibold">TAMBAH MENU</div>
            <hr class="border-black border-2 w-11 mb-7">
            <div class="relative z-0 w-full mb-5 group">
                <input type="text" name="nama_menu" id="nama_menu" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required />
                <label for="nama_menu" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Nama Menu</label>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <label for="jenis_menu" class="block mb-2 text-sm font-medium text-gray-900 ">Jenis Menu</label>
                <select name="jenis_menu" id="jenis_menu" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
                    <option value="Makanan">Makanan</option>
                    <option value="Minuman">Minuman</option>
                    <option value="Penutup">Penutup</option>
                    <option value="Kudapan">Kudapan</option>
                </select>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <label for="message" class="block mb-2 text-sm font-medium text-gray-900 ">Deskripsi Singkat</label>
                <textarea name="message" id="message" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300" placeholder="Tentang Makanan..." required></textarea>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <input type="number" name="harga" id="harga" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required />
                <label for="harga" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Harga</label>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <input type="number" name="kapasitas" id="kapasitas" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required />
                <label for="kapasitas" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Kapasitas</label>
            </div>
            <div class="relative z-0 w-full mb-10 group flex items-center space-x-6">
                <div class="shrink-0">
                    <img id='preview_img' class="h-16 w-16 object-cover rounded-full" src="./../../image/KUDAPAN.png" />
                </div>
                <label class="block">
                    <span class="sr-only">Pilih Foto Makanan</span>
                    <input type="file" name="file" onchange="loadFile(event)" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#11385a] file:text-white hover:file:bg-violet-100" accept=".png, .jpg, .jpeg" required />
                </label>
            </div>
            <button type="submit" name="submit" class="py-3 px-5 bg-[#11385a] text-white text-xl rounded-full font-semibold">Tambahkan Menu</button>
        </form>  
    </div>

<script>
    var loadFile = function(event) {
        var input = event.target;
        var file = input.files[0];
        var output = document.getElementById('preview_img');
        output.src = URL.createObjectURL(file);
        output.onload = function() {
            URL.revokeObjectURL(output.src); // free memory
        }
    };

    function confirmSubmit() {
        return confirm("Apakah Anda yakin ingin menambahkan menu ini?");
    }
</script>
</body>
</html>
