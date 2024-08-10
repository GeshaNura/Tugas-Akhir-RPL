<?php
session_start();
include "koneksi.php";

function getMenuList() {
    global $conn;
    $sql = "SELECT id_menu, nama_menu, jenis, harga FROM menu";
    $result = $conn->query($sql);

    $menuList = "";
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $menuList .= "<tr class='odd:bg-white even:bg-gray-50 text-center'>";
            $menuList .= "<th scope='row' class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>" . $row["id_menu"] . "</th>";
            $menuList .= "<td class='px-6 py-4'>" . $row["nama_menu"] . "</td>";
            $menuList .= "<td class='px-6 py-4'>" . $row["jenis"] . "</td>";
            $menuList .= "<td class='px-6 py-4'>Rp" . number_format($row["harga"], 2, ',', '.') . "</td>";
            $menuList .= "<td class='px-6 py-4 justify-center gap-6 flex '>";
            $menuList .= "<div><input id='lk_" . $row["id_menu"] . "' type='radio' name='Ketersediaan_" . $row["id_menu"] . "' checked>";
            $menuList .= "<label for='lk_" . $row["id_menu"] . "'>Tersedia</label></div>";
            $menuList .= "<div><input id='pr_" . $row["id_menu"] . "' type='radio' name='Ketersediaan_" . $row["id_menu"] . "'>";
            $menuList .= "<label for='pr_" . $row["id_menu"] . "'>Kosong</label></div>";
            $menuList .= "</td></tr>";
        }
    } else {
        $menuList .= "<tr><td colspan='5'>Tidak ada data</td></tr>";
    }

    $conn->close();
    return $menuList;
}
?>
