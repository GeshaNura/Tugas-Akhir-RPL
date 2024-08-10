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
        echo "File Path: " . realpath($file['tmp_name']) . "<br>";
        echo "Target Path: " . realpath($target_file) . "<br>";

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
