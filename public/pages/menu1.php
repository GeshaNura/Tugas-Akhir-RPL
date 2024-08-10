<?php
include 'koneksi.php';

$sql = "SELECT * FROM menu WHERE jenis_menu = 'Makanan';";
$makanan = $conn->query($sql);

$sql1 = "SELECT * FROM menu WHERE jenis_menu = 'Minuman';";
$minuman = $conn->query($sql1);

$sql2 = "SELECT * FROM menu WHERE jenis_menu = 'Penutup';";
$penutup = $conn->query($sql2);

$sql3 = "SELECT * FROM menu WHERE jenis_menu = 'Kudapan';";
$kudapan = $conn->query($sql3);

// Query untuk mengambil gambar menu yang direkomendasikan
$sql4 = "SELECT gambar_menu FROM menu WHERE rekomendasi = 'ya'";
$rekomendasi = $conn->query($sql4);

$queryMeja = "SELECT nomor_meja FROM meja";
$resultMeja = $conn->query($queryMeja);

// Query untuk mengambil data rekomendasi dari tabel menu
$query = "SELECT nama_menu, deskripsi_menu, gambar_menu FROM menu WHERE rekomendasi = 'ya'";
$result = mysqli_query($conn, $query);

// Ambil data dari hasil query
if ($result) {
    $menu = mysqli_fetch_assoc($result);
} else {
    die("Query error: " . mysqli_error($conn));
}

// Tutup koneksi database
mysqli_close($conn);

function displayMenu($result)
{
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Format harga menjadi Rupiah
            $formattedPrice = number_format($row["harga"], 0, ',', '.');

            echo '<button class="openModalBtn w-[200px] h-[330px] mt-8 my-12 show-modal" ';
            echo 'data-name="' . htmlspecialchars($row["nama_menu"]) . '" ';
            echo 'data-price="' . $row["harga"] . '" ';
            echo 'data-image="./../image/' . htmlspecialchars($row["gambar_menu"]) . '" ';
            echo 'data-description="' . htmlspecialchars($row["deskripsi_Menu"]) . '" ';
            echo 'data-id ="' . htmlspecialchars($row["id_menu"]) . '"';
            echo '>';
            echo '<div class="flex justify-center items-center w-auto top-16">';
            echo '<img class="w-64 z-30" src="./../image/' . htmlspecialchars($row["gambar_menu"]) . '" alt="">';
            echo '</div>';
            echo '<div class="w-52 h-56 absolute -mt-16 rounded-lg bg-white -z-0 text-center shadow-2xl">';
            echo '<div class="mt-16 font-semibold">' . htmlspecialchars($row["nama_menu"]) . '</div>';
            echo '<div class="text-[10px] text-gray-400 pt-9">' . htmlspecialchars($row["kapasitas_menu"]) . '</div>';
            echo '<hr class="border-black mx-3">';
            echo '<div class="font-bold">Rp.' . $formattedPrice . '</div>';
            echo '<div>' . htmlspecialchars($row["id_menu"]) . '</div>';
            echo '</div>';
            echo '</button>';
        }
    } else {
        echo "No menu items found.";
    }
}
// Fungsi baru untuk menampilkan gambar menu yang direkomendasikan
function displayRecommendedImages($result)
{
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<img src="./../image/' . htmlspecialchars($row["gambar_menu"]) . '" alt="Recommended Image" class="w-[400px] h-[400px]">';
        }
    } else {
        echo "No recommended images found.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coba Tailwind</title>
    <link rel="stylesheet" href="./../css/output.css">

</head>

<body class="w-screen overflow-x-hidden bg-[#e2e6ed]">
    <nav class="shadow flex items-center justify-between opacity-100 mx-auto h-14 sticky top-0 z-50 bg-white">
        <div>
            <div>
                <!-- <div class="absolute h-14 mn:w-24 md:w-36 mn:-top-10 sm:top-[2px] z-10">-->
                <div><img src="./../image/logo.png" class="absolute z-10 h-44 -top-9" alt="logo"></div>
                <div class="ml-40 text-2xl font-semibold">Pak Resto Unikom</div>
            </div>
        </div>
        <div>
            <button id="keranjang"
                class="openModalP h-full bg-[#007BFF] px-44 py-[8px] text-2xl text-white font-bold grid grid-cols-2">
                <div>PESAN</div>
                <img class="w-10 " src="./../image/CART.png" alt="">
            </button>
        </div>
    </nav>
    <!-- ini buat rekomednsai makanan -->
    <div class="bg-langit w-screen h-[620px] overflow-hidden overflow-x-hidden">
        <div class="text-center m-auto text-6xl font-bold p-4 text-ungu">
            REKOMENDASI
        </div>
        <div class="flex flex-row items-center h-[528px]">
            <div class="basis-1/12">
                <img class="w-96 absolute z-0 -left-7" src="./../image/MINT.png" alt="">
            </div>
            <div class="basis-5/12 grid grid-cols-2">
                <div class="text-ungu font-bold text-5xl my-2 col-start-2">
                    <?php echo htmlspecialchars($menu['nama_menu']); ?>
                </div>
                <div class="w-[350px] my-2 col-start-2">
                    <?php echo htmlspecialchars($menu['deskripsi_menu']); ?>
                </div>
                
            </div>
            <div class="grid-cols-2 grid basis-5/12">
                <?php displayRecommendedImages($rekomendasi); ?>
                
            </div>
            <div class="basis-1/12">
                <img class="w-96 absolute top-1 right-0" src="./../image/MINT.png" alt="">
            </div>
        </div>
    </div>
    <!-- ini buat sub menu -->
    <div class="grid grid-cols-4 gap-6 py-6 z-50">
        <div class="">
            <div class="grid grid-cols-2 grid-rows-2 bg-white shadow-xl">
                <div class="text-center text-2xl font-bold mt-24">MAKANAN</div>
                <div class="row-span-2 center flex justify-center items-center">
                    <img class="w-40" src="./../image/salmon panggang.png" alt="">
                </div>
                <div class="text-center">
                    <hr class="border-2 border-ungu">
                    ini makanan
                </div>
            </div>
        </div>
        <div class="">
            <div class="grid grid-cols-2 grid-rows-2 bg-white shadow-xl">
                <div class="text-center text-2xl font-bold mt-24">MINUMAN</div>
                <div class="row-span-2 center flex justify-center items-center">
                    <img class="w-40" src="./../image/MINUMAN.png" alt="">
                </div>
                <div class="text-center">
                    <hr class="border-2 border-[#DBEDF7]">
                    ini makanan
                </div>
            </div>
        </div>
        <div class="">
            <div class="grid grid-cols-2 grid-rows-2 bg-white shadow-xl">
                <div class="text-center text-2xl font-bold mt-24">PENUTUP</div>
                <div class="row-span-2 center flex justify-center items-center">
                    <img class="w-40" src="./../image/PENUTUP.png" alt="">
                </div>
                <div class="text-center">
                    <hr class="border-2 border-[#FACCB2]">
                    ini makanan
                </div>
            </div>
        </div>
        <div class="">
            <div class="grid grid-cols-2 grid-rows-2 bg-white shadow-xl">
                <div class="text-center text-2xl font-bold mt-24">KUDAPAN</div>
                <div class="row-span-2 center flex justify-center items-center">
                    <img class="w-40" src="./../image/KUDAPAN.png" alt="">
                </div>
                <div class="text-center">
                    <hr class="border-2 border-[#FF8551]">
                    ini makanan
                </div>
            </div>
        </div>
    </div>

    <!-- ini menu -->
    <div class="bg-ungu w-screen min-h-screen max-h-screen overflow-hidden">
        <div class="text-center m-auto text-6xl font-bold py-8 text-white">
            MAKANAN
        </div>
        <hr class="mx-[700px] border-t-4 mb-8">
        <div class="grid grid-cols-7 w-screen justify-content-center mx-20">
            <?php displayMenu($makanan); ?>
        </div>
    </div>

    <div class="bg-[#DBEDF7] w-screen min-h-screen overflow-hidden">
        <div class="text-center m-auto text-6xl font-bold py-8 text-ungu">
            MINUMAN
        </div>
        <hr class="mx-[700px] border-t-4 mb-8">
        <div class="grid grid-cols-7 w-screen justify-content-center mx-20">
            <?php displayMenu($minuman); ?>
        </div>
    </div>

    <div class="bg-[#FACCB2] w-screen min-h-screen overflow-hidden">
        <div class="text-center m-auto text-6xl font-bold py-8 text-white">
            PENUTUP
        </div>
        <hr class="mx-[700px] border-t-4 mb-8">
        <div class="grid grid-cols-7 w-screen justify-content-center mx-20">
            <?php displayMenu($penutup); ?>
        </div>
    </div>

    <div class="bg-[#FF8551] w-screen min-h-screen overflow-hidden">
        <div class="text-center m-auto text-6xl font-bold py-8 text-white">
            KUDAPAN
        </div>
        <hr class="mx-[700px] border-t-4 mb-8">
        <div class="grid grid-cols-7 w-screen justify-content-center mx-20">
            <?php displayMenu($kudapan); ?>
        </div>
    </div>

    <!-- INI MODAL YANG POPUP -->
    <div id="modalmenu"
        class="z-50 modal min-h-screen w-full fixed left-0 top-0 flex justify-center items-center bg-black bg-opacity-50 hidden">
        <div class="w-1/2 min-h-screen -z-20">
            <div class="flex justify-center items-center w-auto top-16">
                <img class="w-64" src="" al ">
            </div>
            <div class="bg-langit w-1/2 h-auto absolute -mt-16 rounded-lg -z-10 text-center shadow-2xl">
                <div class="mt-16 font-bold text-5xl text-ungu">Nama Menu</div>
                <div class="mx-2">
                    <p>Deskripsi Menu</p>
                </div>
                <hr class="border-ungu border-t-4 mx-3 mt-7">
                <form class="max-w-xl mx-auto">
                    <div class="relative flex items-center py-5 max-w-xs m-auto">
                        <button type="button" id="decrement-button" data-input-counter-decrement="quantity-input"
                            class="bg-gray-100 rounded-s-lg p-3 h-11">
                            <svg class="w-3 h-3 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 18 2">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M1 1h16" />
                            </svg>
                        </button>
                        <input type="number" id="quantity-input" data-input-counter
                            aria-describedby="helper-text-explanation"
                            class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm block w-full py-2.5 "
                            value="1" />
                        <button type="button" id="increment-button" data-input-counter-increment="quantity-input"
                            class="bg-gray-100 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                            <svg class="w-3 h-3 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 18 18">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M9 1v16M1 9h16" />
                            </svg>
                        </button>
                    </div>
                    <div class="font-bold pb-5 text-ungu">Rp.65.000</div>
                    <div class="relative flex items-center pb-5">
                        <input type="text" id="order-note"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5"
                            placeholder="CATATAN PESANAN">
                    </div>
                    <button type="button" id="closemodal1"
                        class="bg-red-600 text-white font-semibold px-6 py-1 rounded-full text-xl mx-3 shadow-2xl w-32">
                        BATAL
                    </button>
                    <button type="button" id="order-button"
                        class="bg-ungu text-white font-semibold px-6 py-1 rounded-full text-xl mx-3 shadow-2xl mb-8 w-32">
                        PESAN
                    </button>

                </form>
            </div>
        </div>
    </div>


    <!-- INI MODAL UNTUK LIST PESANAN -->
    <div id="modalPesan"
        class="z-50 modal min-h-screen w-full fixed left-0 top-0 flex justify-center items-center bg-black bg-opacity-50 hidden">
        <div class="w-1/2 h-[650px] bg-white rounded-3xl border shadow-xl">
            <div class="text-3xl font-bold text-center my-2">LIST PESANAN</div>
            <div class="bg-white mx-16 h-[500px] mb-6 rounded-3xl shadow-xl border over">
                <div class="bg-white mx-3 h-[400px] mt-2 overflow-scroll" id="order-list">
                    <!-- Pesanan akan ditambahkan di sini melalui JavaScript -->
                </div>
                <div class="px-9 flex items-center justify-between mx-auto font-semibold text-lg my-4">
                    <div>TOTAL</div>
                    <div id="total-price">Rp.0</div>
                </div>
                <div class="px-9 flex items-center justify-between mx-auto font-semibold text-lg my-4">
                    <div>NO MEJA</div>
                    <select name="table_number" id="table-number" class="border rounded px-2 py-1" required>
                        <option value="">Pilih NO MEJA</option>
                        <?php while ($row = $resultMeja->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['nomor_meja']); ?>">
                                <?php echo htmlspecialchars($row['nomor_meja']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="px-9 flex items-center justify-between mx-auto font-semibold text-lg my-4">
                    <div>NAMA PELANGGAN</div>
                    <input type="text" id="customer-name"> <!-- Tambahkan ID -->
                </div>

            </div>
            <div class="text-center">
                <button type="button" id="closemodal"
                    class="bg-red-600 text-white font-semibold px-6 py-1 rounded-full text-xl mx-3 shadow-2xl w-32">
                    BATAL
                </button>
                <button type="button" id="confirm-order-button"
                    class="bg-ungu text-white font-semibold px-6 py-1 rounded-full text-xl mx-3 shadow-2xl w-32">
                    PESAN
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.querySelector("#modalmenu");
            const closeModal = document.querySelector("#closemodal1");
            const openModalButtons = document.querySelectorAll(".openModalBtn");
            const modalImage = modal.querySelector("img");
            const modalName = modal.querySelector(".text-ungu");
            const modalDescription = modal.querySelector("p");
            const modalPrice = modal.querySelector(".font-bold.pb-5.text-ungu");
            const quantityInput = document.querySelector("#quantity-input");
            const orderButton = document.querySelector("#order-button"); // Tombol PESAN
            const orderList = document.querySelector("#order-list");
            const totalPriceElement = document.querySelector("#total-price");
            const confirmOrderButton = document.querySelector("#confirm-order-button");
            const orderNote = document.querySelector("#order-note");
            const modalPesan = document.querySelector("#modalPesan");

            let orderData = [];
            let totalAmount = 0;

            const formatRupiah = (amount) => {
                return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            };

            openModalButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.getAttribute("data-id");
                    const name = button.getAttribute("data-name");
                    const price = parseInt(button.getAttribute("data-price"));
                    const image = button.getAttribute("data-image");
                    const description = button.getAttribute("data-description");

                    modalImage.src = image;
                    modalName.textContent = name;
                    modalDescription.textContent = description;
                    modalPrice.textContent = `Rp.${formatRupiah(price)}`;
                    modal.style.display = "flex";

                    // Simpan ID menu di modal untuk digunakan saat menambah pesanan
                    orderButton.setAttribute("data-id", id);
                });
            });

            closeModal.addEventListener('click', () => {
                modal.style.display = "none";
            });

            orderButton.addEventListener('click', () => {
                const id = orderButton.getAttribute("data-id");
                const name = modalName.textContent;
                const price = parseInt(modalPrice.textContent.replace('Rp.', '').replace(/\./g, ''));
                const quantity = parseInt(quantityInput.value);
                const note = orderNote.value;

                const existingOrder = orderData.find(order => order.id === id);

                if (existingOrder) {
                    existingOrder.quantity += quantity;
                    existingOrder.note = note;
                } else {
                    orderData.push({ id, name, price, quantity, note });
                }

                updateOrderList();
                modal.style.display = "none";
                quantityInput.value = 1;
                orderNote.value = "";
            });

            const updateOrderList = () => {
                orderList.innerHTML = "";

                orderData.forEach((order, index) => {
                    const orderItem = document.createElement("div");
                    orderItem.className = "flex justify-between items-center px-4 py-2";
                    orderItem.innerHTML = `
                <div>
                    <div class="font-bold">${order.name}</div>
                    <div class="text-sm">Note: ${order.note}</div>
                </div>
                <div class="flex items-center">
                    <button class="decrease-quantity" data-index="${index}">-</button>
                    <div class="text-lg font-semibold mx-2">Rp.${formatRupiah(order.price * order.quantity)}</div>
                    <button class="increase-quantity" data-index="${index}">+</button>
                    <div class="text-sm ml-2">(${order.quantity})</div>
                    <button class="remove-order" data-index="${index}">Remove</button>
                </div>
            `;
                    orderList.appendChild(orderItem);
                });

                totalAmount = orderData.reduce((acc, order) => acc + order.price * order.quantity, 0);
                totalPriceElement.textContent = `Rp.${formatRupiah(totalAmount)}`;

                document.querySelectorAll(".increase-quantity").forEach(button => {
                    button.addEventListener('click', () => {
                        const index = button.getAttribute("data-index");
                        orderData[index].quantity += 1;
                        updateOrderList();
                    });
                });

                document.querySelectorAll(".decrease-quantity").forEach(button => {
                    button.addEventListener('click', () => {
                        const index = button.getAttribute("data-index");
                        if (orderData[index].quantity > 1) {
                            orderData[index].quantity -= 1;
                            updateOrderList();
                        }
                    });
                });

                document.querySelectorAll(".remove-order").forEach(button => {
                    button.addEventListener('click', () => {
                        const index = button.getAttribute("data-index");
                        orderData.splice(index, 1);
                        updateOrderList();
                    });
                });
            };

            confirmOrderButton.addEventListener('click', () => {
                const tableNumber = document.querySelector("select#table-number").value;
                const customerName = document.querySelector("input#customer-name").value;

                if (!tableNumber || !customerName) {
                    alert("Please fill out both the table number and customer name.");
                    return;
                }

                const orderPayload = {
                    noMeja: tableNumber,
                    namaPelanggan: customerName,
                    totalAmount: totalAmount,
                    orderDetails: orderData
                };

                fetch('process_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderPayload)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log(data.message);
                            orderData = [];
                            totalAmount = 0;
                            updateOrderList();
                            modalPesan.style.display = "none";
                            window.location.href = 'tunggu.php';
                        } else {
                            console.error(data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });



            document.querySelector("#closemodal").addEventListener('click', () => {
                document.querySelector("#modalPesan").style.display = "none";
            });

            document.querySelector("#keranjang").addEventListener('click', () => {
                document.querySelector("#modalPesan").style.display = "flex";
            });

            const incrementButton = document.querySelector("#increment-button");
            const decrementButton = document.querySelector("#decrement-button");

            incrementButton.addEventListener('click', () => {
                quantityInput.value = parseInt(quantityInput.value) + 1;
            });

            decrementButton.addEventListener('click', () => {
                if (quantityInput.value > 1) {
                    quantityInput.value = parseInt(quantityInput.value) - 1;
                }
            });
        });
    </script>

</body>

</html>