<?php
include 'admin/include/koneksi/koneksi.php';

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




$lat_wilayah =baca_database('','lat',"select * from data_wilayah limit 0,1");
$lng_wilayah =baca_database('','lng',"select * from data_wilayah limit 0,1");
$radius_wilayah =baca_database('','radius',"select * from data_wilayah limit 0,1");



$point1 = array("lat" => $_GET['lat'], "long" => $_GET['lng']);
$point2 = array("lat" => $lat_wilayah , "long" => $lng_wilayah );
$distance = getDistanceBetweenPoints($point1['lat'], $point1['long'], $point2['lat'], $point2['long']);
?>
<h2>
    <center>
        <br>
        <br>
        <br>
        <br>
        <br>
        GAGAL PRESENSI <br><br>
    Jarak Anda dengan lokasi presensi :
    (<?php echo $distance;?> Meter), Lebih dari radius yang diizinkan presensi yaitu :  (<?php echo $radius_wilayah;?> Meter)
    </center>
    <br>
    <center>
    <a href="admin/../">Kembali absensi</a>
    </center>
</h2>

<?php



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


?>


