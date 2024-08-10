<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $kontak = $_POST['kontak'];
    $jabatan = $_POST['jabatan'];
    $alamat = $_POST['alamat'];
    $password_karyawan = password_hash($_POST['password_karyawan'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO karyawan (nama, kontak, jabatan, alamat, password_karyawan) VALUES ('$nama', '$kontak', '$jabatan', '$alamat', '$password_karyawan')";

    if ($conn->query($sql) === TRUE) {
        header('Location: pegawai.php');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pegawai</title>
    <link rel="stylesheet" href="./../../css/output.css">
    <script>
        function confirmSubmission(event) {
            event.preventDefault();
            var form = event.target;

            if (confirm('Apakah Anda yakin ingin menambahkan pegawai ini?')) {
                form.submit();
            }
        }
    </script>
</head>
<body class="bg-[#e2e6ed]">
    <div class="h-16 w-screen flex items-center">
        <a href="./pegawai.php" class="bg-[#11385a] text-white py-1 px-6 text-xl rounded-full font-semibold ml-9">KEMBALI</a>
    </div>
    <div class="m-auto mt-10">
        <form action="" method="POST" class="max-w-md mx-auto bg-white p-12" onsubmit="confirmSubmission(event)">
            <div class="text-2xl font-semibold">TAMBAH PEGAWAI</div>
            <hr class="border-black border-2 w-11 mb-7">
            <div class="relative z-0 w-full mb-5 group">
                <input type="text" name="nama" id="nama_lengkap" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required />
                <label for="nama_lengkap" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Nama Lengkap</label>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <input type="password" name="password_karyawan" id="floating_password" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required />
                <label for="floating_password" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Password</label>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <label for="jabatan" class="block mb-2 text-sm font-medium text-gray-900">Jabatan</label>
                <select name="jabatan" id="jabatan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                    <option>Pelayan</option>
                    <option>Kasir</option>
                    <option>Koki</option>
                </select>
            </div>
            <div class="grid md:grid-cols-2 md:gap-6">
                <div class="relative z-0 w-full mb-5 group">
                    <input type="tel" name="kontak" id="kontak" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required />
                    <label for="kontak" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Kontak</label>
                </div>
                <div class="relative z-0 w-full mb-5 group">
                </div>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <input type="text" name="alamat" id="alamat" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required />
                <label for="alamat" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Alamat</label>
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">TAMBAH PEGAWAI</button>
        </form>  
    </div>
</body>
</html>
