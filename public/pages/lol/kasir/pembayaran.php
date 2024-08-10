<?php
session_start();
include "../koneksi.php";


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./../../css/output.css">
</head>
<body class="bg-[#e2e6ed]">
    <div class="h-16 w-screen  flex items-center">
        <a href="./dashboard.php" class="bg-[#11385a] text-white py-1 px-6 text-xl rounded-full font-semibold ml-9">KEMBALI</a>
    </div>
    <div class="grid grid-cols-2">
      <div class=" m-auto mt-10 shadow-xl">
        <form class="max-w-md mx-auto bg-white p-12">
            <div class="text-2xl font-semibold">PEMBAYARAN</div>
            <hr class="border-black border-2 w-11  mb-7">
            <div class="relative z-0 w-full mb-5 group">
                <input type="text" name="no_meja" id="no_meja" class="block py-2.5 px-0 w-full text-sm  bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required disabled value="01" />
                <label for="no_meja" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">No Meja</label>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="block py-2.5 px-0 w-full text-sm  bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required value="Dimas" disabled />
                <label for="nama_pelanggan" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Nama Pelanggan</label>
            </div>
            <div class="grid md:grid-cols-2 md:gap-6">
              <div class="relative z-0 w-full mb-5 group">
                <input type="text" name="subtotal" id="subtotal" class="block py-2.5 px-0 w-full text-sm  bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required value="Rp700.000" disabled />
                <label for="subtotal" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">SubTotal</label>
              </div>
              <div class="relative z-0 w-full mb-5 group">
                <input type="text" name="total" id="total" class="block py-2.5 px-0 w-full text-sm  bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required value="Rp770.000" disabled />
                <label for="total" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Total</label>
              </div>
              
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <input type="text" name="uang" id="uang" class="block py-2.5 px-0 w-full text-sm  bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required />
                <label for="uang" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Uang Tunai</label>
            </div>
            <div class="relative z-0 w-full mb-5 group">
              <input type="text" name="kembalian" id="kembalian" class="block py-2.5 px-0 w-full text-sm  bg-transparent border-0 border-b-2 appearance-none outline-none border-gray-600 peer" placeholder=" " required disabled value="Rp30.000" />
              <label for="kembalian" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Kembalian</label>
          </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Bayar</button>
        </form>  
      </div>
      <div class=" mt-10 bg-white p-12 mr-44 shadow-xl">
        <table class="w-full">
          <thead class="uppercase bg-gray-400">
              <tr>
                  <th scope="col" class="px-6 py-3">
                      Kuantitas
                  </th>
                  <th scope="col" class="px-6 py-3">
                      Nama Menu
                  </th>
                  <th scope="col" class="px-6 py-3">
                      Harga
                  </th>
              </tr>
          </thead>
          <tbody>
              <tr class="odd:bg-white even:bg-gray-50 text-center">
                  <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                      1
                  </th>
                  <td class="px-6 py-4">
                      Basmut
                  </td>
                  <td class="px-6 py-4">
                      Rp700.000
                  </td>
              </tr>
              <tr class="odd:bg-white even:bg-gray-50 text-center">
                  <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                      1
                  </th>
                  <td class="px-6 py-4">
                      orang 1
                  </td>
                  <td class="px-6 py-4">
                      Rp.7.000.000
                  </td> 
              </tr>
          </tbody>
      </table>
      </div>
    </div>
</body>
</html>