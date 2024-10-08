<?php
session_start();
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $user = $_POST['username'];
        $password = $_POST['password'];
        $op = isset($_GET['op']) ? $_GET['op'] : '';

        if ($op == "in") {
            $stmt = $conn->prepare("SELECT * FROM karyawan WHERE nama = ?");
            if ($stmt === false) {
                die("Error preparing statement: " . $conn->error);
            }
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $qry = $result->fetch_assoc();
                $hashedPassword = $qry['password_karyawan'];

                // Cek apakah password cocok dengan hash
                if (password_verify($password, $hashedPassword) || $password === $hashedPassword) {
                    $_SESSION['id_karyawan'] = $qry['id_karyawan'];
                    $_SESSION['nama'] = $qry['nama'];
                    $_SESSION['jabatan'] = $qry['jabatan'];

                    switch ($qry['jabatan']) {
                        case "manager":
                            header("location:manajer/dashboard.php");
                            break;
                        case "koki":
                            header("location:chef/ketersediaanmenu.php");
                            break;
                        case "pelayan":
                            header("location:pelayan/meja.php");
                            break;
                        case "kasir":
                            header("location:kasir/dashboard.php");
                            break;
                        default:
                            header("location:index.php");
                            break;
                    }
                } else {
                    echo '<script language="JavaScript">
                            alert("Username atau Password tidak sesuai. Silahkan diulang kembali!");
                            document.location="index.php";
                          </script>';
                }
            } else {
                echo '<script language="JavaScript">
                        alert("Username atau Password tidak sesuai. Silahkan diulang kembali!");
                        document.location="index.php";
                      </script>';
            }

            $stmt->close();
        }
    }
} else if (isset($_GET['op']) && $_GET['op'] == "out") {
    unset($_SESSION['id_karyawan']);
    unset($_SESSION['nama']);
    unset($_SESSION['jabatan']);
    header("location:index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./../css/output.css">
</head>
<body>
    <div class="w-screen h-screen flex flex-col justify-center items-center">
        <a href="" class="text-6xl font-bold pb-3 text-ungu">RESTO UNIKOM</a>
        <div class="w-1/4 h-auto bg-langit rounded-lg py-10">
            <div class="p-6">
                <h1 class="text-ungu font-bold text-5xl">LOGIN</h1>
                <form action="index.php?op=in" method="post">
                    <div class="pt-6">
                        <label for="username" class="text-ungu font-semibold text-xl">Username</label>
                        <input type="text" name="username" id="username" class="bg-white border border-gray-300 text-gray-900 rounded-lg block w-full p-2.5" placeholder="Username Anda">
                    </div>
                    <div class="pt-6">
                        <label for="password" class="text-ungu font-semibold text-xl">Password</label>
                        <input type="password" name="password" id="password" class="bg-white border border-gray-300 text-gray-900 rounded-lg block w-full p-2.5" placeholder="••••••••">
                    </div>
                    <div class="flex justify-center">
                        <button type="submit" class="mt-6 w-1/2 font-bold text-xl text-langit bg-ungu hover:bg-white hover:text-black rounded-lg px-5 py-2.5 text-center">LOGIN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
