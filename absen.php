<?php 
include 'admin/include/koneksi/koneksi.php';

if (!(isset($_GET['proses'])))
{
	die();
}

$nim =  $_GET['proses'];
$lat =  $_GET['lat'];
$lng =  $_GET['lng'];
$id_matapelajaran =  $_GET['id_matapelajaran'];

$arr = explode("-", $nim, 2);
$nim = $arr[0];

function getDistanceBetweenPoints($lat1, $lon1, $lat2, $lon2) {
    $theta = $lon1 - $lon2;
    $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
    $miles = acos($miles);
    $miles = rad2deg($miles);
    $miles = $miles * 60 * 1.1515;
    $feet  = $miles * 5280;
    $yards = $feet / 3;
    $kilometers = $miles * 1.609344;
    $meters = $kilometers * 1000;
    return round($meters,2);
}


date_default_timezone_set('Asia/Jakarta');

$id_absensi=id_otomatis("data_absensi","id_absensi","10");
$tanggal=date('Y-m-d');
$jam=date('H:i:s');
$id_mahasiswa=baca_database('','id_mahasiswa',"select * from data_mahasiswa where nim='$nim'");
$status="hadir";

$cek = cek_database("","","","select * from data_absensi where tanggal='$tanggal' and id_mahasiswa='$id_mahasiswa' and id_matapelajaran='$id_matapelajaran'");


$lat_wilayah =baca_database('','lat',"select * from data_wilayah limit 0,1");
$lng_wilayah =baca_database('','lng',"select * from data_wilayah limit 0,1");
$radius_wilayah =baca_database('','radius',"select * from data_wilayah limit 0,1");



$point1 = array("lat" => $lat, "long" => $lng);
$point2 = array("lat" => $lat_wilayah, "long" => $lng_wilayah);
$distance = getDistanceBetweenPoints($point1['lat'], $point1['long'], $point2['lat'], $point2['long']);


if ($cek == "nggak")
{


	if ($id_mahasiswa == "")

	{

	}
	else
	{


        if ($distance > $radius_wilayah)
        {
            //jauh
        }
        else
        {
            $query=mysql_query("insert into data_absensi values (
            '$id_absensi'
             ,'$tanggal'
             ,'$jam'
             ,'$id_mahasiswa'
             ,'$id_matapelajaran'
             ,'$status'
              ,'$lat'
            ,'$lng'
            )");
        }

	}
}
else
{
	//echo "SUDAH";
}





if ($distance > $radius_wilayah)
{
echo "JAUH";
}




//BACA DATABASE
function baca_database($tabel,$field,$query)
{
	
	if ($query=="")
	{
		$sql = 'SELECT * FROM '.$tabel;
	}
	else
	{
		$sql = $query;
	}
	
	$querytabelualala=$sql;
	$prosesulala = mysql_query($querytabelualala);
	$datahasilpemrosesanquery = mysql_fetch_array($prosesulala);
	$hasiltermantab = $datahasilpemrosesanquery[$field];
	return $hasiltermantab;
}

//CEK DATABASE
function cek_database($tabel,$field,$value,$query)
{
	if ($query=="")
	{
		$sql = "SELECT * FROM ".$tabel." WHERE ".$field." ='".$value."'";
	}
	else
	{
		$sql = $query;
	}
	
	$cek_user=mysql_num_rows(mysql_query($sql));
	if ($cek_user > 0) 
	{   
		$hasiltermantab = "ada";
	}
	else
	{
		$hasiltermantab = "nggak";
	}
	return $hasiltermantab;
}

//KODE OTOMATIS	 	
function id_otomatis($nama_tabel,$id_nama_tabel,$panjang_id)
{
	$cek = mysql_query("SELECT * FROM $nama_tabel");
	$rowcek = mysql_num_rows($cek);
	
	
		$kodedepan = strtoupper($nama_tabel);
		$kodedepan = str_replace("DATA_","",$kodedepan);
		$kodedepan = str_replace("DATA","",$kodedepan);
		$kodedepan = str_replace("TABEL_","",$kodedepan);
		$kodedepan = str_replace("TABEL","",$kodedepan);
		$kodedepan = str_replace("TABLE_","",$kodedepan);
		$kodedepan = strtoupper(substr($kodedepan,0,3));
		$id_tabel_otomatis = $kodedepan.date('YmdHis');
		$min = pow($panjang_id, 3 - 1);
		$max = pow($panjang_id, 3) - 1;
		
		$kodeakhir = mt_rand($min, $max);
	    return $id_tabel_otomatis.$kodeakhir;
}



?>