<!DOCTYPE html>
<html>

<head>

    <?php


    $namaaplikasi = "face recognition";
    $lokasi = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $lokasi_request = $_SERVER['REQUEST_URI'];
    ?>
    <title> <?php echo $namaaplikasi; ?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    -
    <script src="face-api.js"></script>

</head>


<center>
    <h2>
        <img src="admin/data/image/logo/logo.png" width="5%">
        APLIKASI PRESENSI WAJAH
    </h2>

</center>
<br>
<div id="parent1" style="padding-left: 20px;">

    <div class="margin" style="position: relative; float:left; border: 3px solid #fbd84a;
      
      padding-bottom: 12px;
      padding-top: 13px;
      border: 3px solid #fbd84a;
      background-color: black;

      ">

        <video id="vidDisplay" style="height: 500px; width: 100%; display: inline-block; vertical-align: baseline;"
            onloadedmetadata="onPlay(this)" autoplay="true"></video>
        <canvas id="overlay" style="position: absolute; top: 0; left: 0;" width="1200" height="800" />
    </div>


    <div id="parent2" style="
    /*position: relative;*/
    /*float: right;*/
    /*border: 3px solid #fbd84a;*/
    /*padding-right: -1px;*/
    /*margin-right: 20px;*/
    /*background-color: grey;*/
">
        <br>
        <center>
            <?php
            if (isset($_POST['nama'])) {
            ?>
                <button id="register" class="button button1" style="background-color: #0f47a1;"> Pendaftaran Wajah
                </button>
                <button id="login" class="button button1" style="background-color: #0f47a1;"> Pengenalan Wajah</button>
            <?php } else { ?>

                <?php
                if (isset($_POST['id_matapelajaran'])) {
                ?>
                    <input id="id_matapelajaran" value="<?php echo $_POST['id_matapelajaran']; ?>" type="hidden">
                <?php

                } else {
                ?>
                    <script>
                        window.location.href = 'mahasiswa/'
                    </script>
                <?php
                    die();
                }
                ?>

                <button id="login" class="button button1" style="background-color: #0f47a1;"> Pengenalan Wajah</button>
            <?php } ?>
        </center>
        <br>
        <center>
            <img id="prof_img"
                style=" height:200px; width: 200px; border: 3px solid black; border-radius: 10px;">
        </center>
        <br><br>
        <div id="siapa"
            style=" text-align: center; font-size: 23px; font-family:Lucida Console,Monaco,monospace; font-weight: bold; margin-bottom: 50px;">
            ... Deteksi Wajah ...
        </div>
        <div id="reg_disp" style="display: none;">
            <center>
                <input id="fname"
                    style="margin-left:50px; margin-right:50px; width:500px; height: 30px; border-radius: 5px; border:1px solid black;"
                    type="readonly" value="<?php echo $_POST['nama']; ?>" placeholder="Nama : "></input><br></br>

                <button id="capture" class="button button1" style="margin-left: 220px;height:72px;"> Simpan
                    Wajah
                </button>
                <br>
                <div id="tries"
                    style=" text-align: center; font-size: 23px; font-family:Lucida Console,Monaco,monospace; font-weight: bold;">
                    Jumlah :
                </div>
                <br>
            </center>
        </div>

        <div id="log_disp">

            <div id="logname"
                style="font-size: 35px; font-weight: bold; margin-left: 40px; width: 570px; white-space: pre-wrap; text-align: center;">
            </div>

        </div>
    </div>
</div>
</body>
<br>
<br>
<br>
<br>

<?php include 'deteksiwajah.php'; ?>



<input id="lat" type="hidden">
<input id="lng" type="hidden">

<script>
    $(document).ready(function() {
        getLocation();
    });

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            x.innerHTML = "Geolocation is not supported by this browser.";
        }
    }

    function showPosition(position) {

        document.getElementById("lat").value = position.coords.latitude;
        document.getElementById("lng").value = position.coords.longitude;

    }
</script>
<hr>
<center>
    <a href="mahasiswa/" class="btn btn-primary">Masuk Halaman mahasiswa</a>
</center>

</html>