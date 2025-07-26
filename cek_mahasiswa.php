<?php
include 'admin/include/koneksi/koneksi.php';

// Direktori lokasi file foto
$base_url = "https://localhost.go-web.my.id/2025-aplikasi-absensi-egrand/admin/upload/";

// Query untuk mengambil id_karyawan, nama, dan semua foto
$query = "SELECT id_mahasiswa, nama, nim, foto_1, foto_2, foto_3, foto_4, foto_5, foto_6, foto_7, foto_8, foto_9, foto_10 FROM data_mahasiswa";
$result = mysql_query($query);

// Array untuk menampung hasil output
$output = [];

while ($row = mysql_fetch_assoc($result)) {
    // Membuat array output untuk setiap karyawan
    $output[] = [
        "id_mahasiswa" => $row['id_mahasiswa'],
        "nama" => $row['nama'],
        "nim" => $row['nim'],
        "out_1" => $row['foto_1'] ? $base_url . $row['foto_1'] : '',
        "out_2" => $row['foto_2'] ? $base_url . $row['foto_2'] : '',
        "out_3" => $row['foto_3'] ? $base_url . $row['foto_3'] : '',
        "out_4" => $row['foto_4'] ? $base_url . $row['foto_4'] : '',
        "out_5" => $row['foto_5'] ? $base_url . $row['foto_5'] : '',
        "out_6" => $row['foto_6'] ? $base_url . $row['foto_6'] :'',
        "out_7" => $row['foto_7'] ? $base_url . $row['foto_7'] : '',
        "out_8" => $row['foto_8'] ? $base_url . $row['foto_8'] : '',
        "out_9" => $row['foto_9'] ? $base_url . $row['foto_9'] :'',
        "out_10" => $row['foto_10'] ? $base_url . $row['foto_10'] : ''
    ];
}

// Output the result as JSON
header('Content-Type: application/json');
echo json_encode($output);
?>
