<?php
session_start();
include "../koneksi.php";

// Fungsi untuk menghasilkan nama file unik
function generateUniqueFilename($originalName) {
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid() . '.' . $ext;
}

// Mendapatkan ID menu dari parameter URL
$menu_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Mengambil data menu dari database
if ($menu_id > 0) {
    $query = "SELECT * FROM menu WHERE id_menu = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $menu_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $menu = $result->fetch_assoc();
    } else {
        die("Menu not found.");
    }
    $stmt->close();
} else {
    die("Invalid menu ID.");
}

// Proses update data menu
if (isset($_POST['submit'])) {
    $nama_menu = $_POST['nama_menu'] ?? '';
    $jenis_menu = $_POST['jenis_menu'] ?? '';
    $deskripsi_menu = $_POST['deskripsi_menu'] ?? '';
    $harga = $_POST['harga'] ?? '';
    $kapasitas = $_POST['kapasitas'] ?? '';

    // Validasi data
    if (!empty($nama_menu) && !empty($jenis_menu) && !empty($deskripsi_menu) && !empty($harga) && !empty($kapasitas)) {
        $file = $_FILES['file'] ?? null;

        if ($file && $file['error'] == UPLOAD_ERR_NO_FILE) {
            // Tidak ada file yang diupload, gunakan gambar lama
            $fileName = $menu['gambar_menu'];
        } elseif ($file && $file['error'] == UPLOAD_ERR_OK) {
            // Validasi tipe file
            $allowedTypes = ['image/png', 'image/jpeg'];
            $allowedSize = 25 * 1024 * 1024; // 25 MB
            $fileSize = $file['size'];
            $fileType = mime_content_type($file['tmp_name']);

            if ($fileSize <= $allowedSize && in_array($fileType, $allowedTypes)) {
                $fileName = generateUniqueFilename($file['name']);
                $target_dir = __DIR__ . "/../../image/"; // Path ke folder image
                $target_file = $target_dir . $fileName;

                // Upload file ke folder
                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    // File berhasil di-upload
                } else {
                    echo "Sorry, there was an error uploading your file.";
                    exit;
                }
            } else {
                echo "Invalid file type or size. Only PNG and JPG files up to 25MB are allowed.";
                exit;
            }
        } else {
            // Jika tidak ada file dan gambar lama, gunakan gambar lama
            $fileName = $menu['gambar_menu'];
        }

        // Menyimpan data ke dalam database
        $query = "UPDATE menu SET nama_menu = ?, jenis_menu = ?, harga = ?, kapasitas_menu = ?, deskripsi_menu = ?, gambar_menu = ? WHERE id_menu = ?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("ssisssi", $nama_menu, $jenis_menu, $harga, $kapasitas, $deskripsi_menu, $fileName, $menu_id);

        if ($stmt->execute()) {
            header("Location: listmenu.php");
            exit;
        } else {
            echo "Error updating menu: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Please fill all fields.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu</title>
    <link rel="stylesheet" href="./../../css/output.css">
    <script>
        function confirmSubmit() {
            return confirm("Apakah Anda yakin ingin mengubah menu ini?");
        }

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('preview_img');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</head>
<body class="bg-[#e2e6ed]">
    <div class="h-16 w-screen flex items-center">
        <a href="./listmenu.php" class="bg-[#11385a] text-white py-1 px-6 text-xl rounded-full font-semibold ml-9">KEMBALI</a>
    </div>
    <div class="m-auto mt-10">
        <form class="max-w-md mx-auto bg-white p-12" method="post" enctype="multipart/form-data" onsubmit="return confirmSubmit()">
            <div class="text-2xl font-semibold">EDIT MENU</div>
            <hr class="border-black border-2 w-11 mb-7">
            <div class="relative z-0 w-full mb-5 group">
                <input type="text" name="nama_menu" id="nama_menu" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " value="<?php echo htmlspecialchars($menu['nama_menu'] ?? ''); ?>" required />
                <label for="nama_menu" class="peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Nama Menu</label>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <label for="jenis_menu" class="block mb-2 text-sm font-medium text-gray-900">Jenis Menu</label>
                <select name="jenis_menu" id="jenis_menu" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
                    <option value="Makanan" <?php echo ($menu['jenis_menu'] ?? '') == 'Makanan' ? 'selected' : ''; ?>>Makanan</option>
                    <option value="Minuman" <?php echo ($menu['jenis_menu'] ?? '') == 'Minuman' ? 'selected' : ''; ?>>Minuman</option>
                    <option value="Penutup" <?php echo ($menu['jenis_menu'] ?? '') == 'Penutup' ? 'selected' : ''; ?>>Penutup</option>
                    <option value="Kudapan" <?php echo ($menu['jenis_menu'] ?? '') == 'Kudapan' ? 'selected' : ''; ?>>Kudapan</option>
                </select>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <label for="deskripsi_menu" class="block mb-2 text-sm font-medium text-gray-900">Deskripsi Singkat</label>
                <textarea name="deskripsi_menu" id="deskripsi_menu" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300" placeholder="Tentang Makanan..." required><?php echo htmlspecialchars($menu['deskripsi_menu'] ?? ''); ?></textarea>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <input type="number" name="harga" id="harga" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " value="<?php echo htmlspecialchars($menu['harga'] ?? ''); ?>" required />
                <label for="harga" class="peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Harga</label>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <input type="number" name="kapasitas" id="kapasitas" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " value="<?php echo htmlspecialchars($menu['kapasitas_menu'] ?? ''); ?>" required />
                <label for="kapasitas" class="peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Kapasitas</label>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <label class="block mb-2 text-sm font-medium text-gray-900">Upload Gambar</label>
                <img id="preview_img" src="data:image/jpeg;base64,<?php echo base64_encode($menu['gambar_menu'] ?? ''); ?>" alt="Preview Gambar" class="w-32 h-32 object-cover mb-3" />
                <input type="file" name="file" id="file" accept="image/*" onchange="previewImage(event)" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <button type="submit" name="submit" class="py-3 px-5 bg-[#11385a] text-white text-xl rounded-full font-semibold">Ubah Menu</button>
            </div>
        </form>
    </div>
</body>
</html>
