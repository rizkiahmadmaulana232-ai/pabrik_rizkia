<?php

include '../config_rizkia/koneksi_rizkia.php';

session_start();
$id_operator_rizkia = $_SESSION['user_rizkia']['id_rizkia'];

$data_rizkia = mysqli_query($conn_rizkia,"SELECT * FROM scheduling_rizkia 
WHERE operator_id_rizkia='$id_operator_rizkia'");

while($d = mysqli_fetch_array($data_rizkia)){
    echo "Jadwal: ".$d['waktu_mulai_rizkia']."<br>";

    echo "<form method='POST'>
    <input type='hidden' name='id' value='".$d['id_rizkia']."'>
    <button name='selesai_rizkia'>Selesai</button>
    </form>";
}

if(isset($_POST['selesai_rizkia'])){
    mysqli_query($conn_rizkia,"UPDATE scheduling_rizkia 
    SET status_rizkia='Selesai' 
    WHERE id_rizkia='$_POST[id]'");
}
?>