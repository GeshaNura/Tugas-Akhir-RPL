<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Menu</title>
    <link rel="stylesheet" href="./../../css/output.css">
</head>
<body class="bg-[#e2e6ed]">
    <div class="h-16 w-screen flex items-center">
        <a href="listmenu.php" class="bg-[#11385a] text-white py-1 px-6 text-xl rounded-full font-semibold ml-9">KEMBALI</a>
    </div>
    <div>
        <form class="flex justify-end px-16" method="GET" action="rekomendasimenu.php">
            <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only">Cari</label>
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="search" id="default-search" name="search" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50" placeholder="Nama" required />
                <button type="submit" class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Cari</button>
            </div>
        </form>
    </div>
    <div class="mt-2 mx-7 h-screen overflow-y-scroll">
        <div class="relative shadow-md">
            <table class="w-full">
                <thead class="uppercase bg-[#11385a] text-white">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Nama Menu</th>
                        <th scope="col" class="px-6 py-3">Harga</th>
                        <th scope="col" class="px-6 py-3">Rekomendasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include 'koneksi.php';

                    $search = isset($_GET['search']) ? $_GET['search'] : '';
                    $sql = "SELECT id_menu, nama_menu, harga FROM menu";
                    if ($search) {
                        $sql .= " WHERE nama_menu LIKE '%$search%'";
                    }

                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $no = 1;
                        while($row = $result->fetch_assoc()) {
                            echo "<tr class='odd:bg-white even:bg-gray-50 text-center'>";
                            echo "<th scope='row' class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>".$no."</th>";
                            echo "<td class='px-6 py-4'>".$row['nama_menu']."</td>";
                            echo "<td class='px-6 py-4'>".$row['harga']."</td>";
                            echo "<td class='px-6 py-4'>";
                            echo "<form action='listmenu.php' method='POST'>";
                            echo "<input type='hidden' name='nama_menu' value='".$row['nama_menu']."'>";
                            echo "<button type='submit' class='font-medium py-1 px-6 rounded-3xl bg-green-600 text-white'>PILIH</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='4' class='px-6 py-4 text-center'>No data available</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
