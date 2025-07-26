<?php
include 'admin/include/koneksi/koneksi.php';
header('Content-Type: application/json');
if (!(isset($_GET['id_mahasiswa']))) {
    die();
}

$id_mahasiswa=  $_GET['id_mahasiswa'];



date_default_timezone_set('Asia/Jakarta');

// Array untuk mapping hari dalam Bahasa Indonesia
$hari_indo = array(
    'Sunday'    => 'minggu',
    'Monday'    => 'senin',
    'Tuesday'   => 'selasa',
    'Wednesday' => 'rabu',
    'Thursday'  => 'kamis',
    'Friday'    => 'jumat',
    'Saturday'  => 'sabtu'
);

// Mendapatkan hari ini
$hari_ini = date("l");

// Mengganti hari dalam format Indonesia
$id_kelas = baca_database("", "id_kelas", "select * from data_mahasiswa where id_mahasiswa='$id_mahasiswa'");
$id_semester = baca_database("", "id_semester", "select * from data_mahasiswa where id_mahasiswa='$id_mahasiswa'");
$id_prodi = baca_database("", "id_prodi", "select * from data_mahasiswa where id_mahasiswa='$id_mahasiswa'");
$id_tahun_ajaran = baca_database("", "id_tahun_ajaran", "select * from data_mahasiswa where id_mahasiswa='$id_mahasiswa'");

$hari_ini_indo = $hari_indo[$hari_ini];
$jam_sekarang = date('H:i:s');

 $cekJadwal = baca_database("", "jam", "SELECT * FROM data_jadwal WHERE hari = '$hari_ini_indo' AND id_kelas='$id_kelas' AND id_semester='$id_semester' AND id_prodi='$id_prodi' AND id_tahun_ajaran='$id_tahun_ajaran'");


if (empty($cekJadwal)) {
    die("Tidak ada jadwal pada hari ini.");
}

$cekJam = date('H:i');



$id_absensi = id_otomatis("data_absen", "id_absensi", "10");
$tanggal = date('Y-m-d');
$jam= date('H:i:s');
 $id_matapelajaran = baca_database('', 'id_matapelajaran', "select * from data_jadwal where id_kelas='$id_kelas' AND id_semester='$id_semester' AND id_prodi='$id_prodi' AND id_tahun_ajaran='$id_tahun_ajaran' AND hari = '$hari_ini_indo' ");


 $cek = cek_database("", "", "", "select * from data_absensi where tanggal='$tanggal' and id_mahasiswa='$id_mahasiswa' AND id_matapelajaran='$id_matapelajaran' AND jam = '$cekJadwal' ");

if ($cek == "nggak") {
    // Pastikan format waktu yang digunakan
    $timeAbsensi = strtotime($jam);         // Konversi jam absen ke timestamp
    $timeJadwal  = strtotime($cekJadwal);   // Konversi jam jadwal ke timestamp

    // Debugging: Cek hasil konversi waktu
    // file_put_contents('debug.txt', "Jam Absen: $jam ($timeAbsensi), Jam Jadwal: $cekJadwal ($timeJadwal)\n", FILE_APPEND);

    // Cek apakah jam absen lebih besar dari jam jadwal (terlambat)
    if ($timeAbsensi > $timeJadwal) { 
        $status = 'terlambat';
    } else {
        $status = 'hadir';
    }

    // Query untuk memasukkan data ke database
    $query = "INSERT INTO data_absensi (id_absensi, tanggal, jam, id_mahasiswa, id_matapelajaran, status) 
              VALUES ('$id_absensi', '$tanggal', '$jam', '$id_mahasiswa', '$id_matapelajaran', '$status')";

    $result = mysql_query($query); // Eksekusi query

    // Periksa hasil query dan tampilkan response JSON
    if ($result) {
        $response = array(
            "status" => "success",
            "message" => "Data berhasil disimpan.",
            "stored_status" => $status // Tambahkan status yang disimpan untuk debug
        );
    } else {
        $response = array(
            "status" => "error",
            "message" => "Terjadi kesalahan dalam penyimpanan data.",
            "error"   => mysql_error() // Debugging jika query error
        );
    }

    echo json_encode($response); // Menampilkan response JSON
}
//BACA DATABASE
function baca_database($tabel, $field, $query)
{
    if ($query == "") {
        $sql = 'SELECT * FROM ' . $tabel;
    } else {
        $sql = $query;
    }

    $querytabelualala = $sql;
    $prosesulala = mysql_query($querytabelualala);
    $datahasilpemrosesanquery = mysql_fetch_array($prosesulala);
    $hasiltermantab = $datahasilpemrosesanquery[$field];
    return $hasiltermantab;
}

//CEK DATABASE
function cek_database($tabel, $field, $value, $query)
{
    if ($query == "") {
        $sql = "SELECT * FROM " . $tabel . " WHERE " . $field . " ='" . $value . "'";
    } else {
        $sql = $query;
    }

    $cek_user = mysql_num_rows(mysql_query($sql));
    if ($cek_user > 0) {
        $hasiltermantab = "ada";
    } else {
        $hasiltermantab = "nggak";
    }
    return $hasiltermantab;
}

//KODE OTOMATIS
function id_otomatis($nama_tabel, $id_nama_tabel, $panjang_id)
{
    $cek = mysql_query("SELECT * FROM $nama_tabel");
    $rowcek = mysql_num_rows($cek);

    $kodedepan = strtoupper($nama_tabel);
    $kodedepan = str_replace("DATA_", "", $kodedepan);
    $kodedepan = str_replace("DATA", "", $kodedepan);
    $kodedepan = str_replace("TABEL_", "", $kodedepan);
    $kodedepan = str_replace("TABEL", "", $kodedepan);
    $kodedepan = str_replace("TABLE_", "", $kodedepan);
    $kodedepan = strtoupper(substr($kodedepan, 0, 3));
    $id_tabel_otomatis = $kodedepan . date('YmdHis');
    $min = pow($panjang_id, 3 - 1);
    $max = pow($panjang_id, 3) - 1;

    $kodeakhir = mt_rand($min, $max);
    return $id_tabel_otomatis . $kodeakhir;
}
?>
