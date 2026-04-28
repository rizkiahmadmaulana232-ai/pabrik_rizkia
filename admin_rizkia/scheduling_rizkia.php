<?php

include '../config_rizkia/koneksi_rizkia.php';
session_start();
date_default_timezone_set("Asia/Jakarta");

/* ROLE CHECK */
if(!isset($_SESSION['user_rizkia'])){
    header("Location: ../auth_rizkia/login_rizkia.php");
    exit;
}

/* CEK BENTROK */
function cek_bentrok($conn, $mesin, $operator, $mulai, $selesai, $exclude_id = null){

    $filter_id = "";
    if($exclude_id){
        $filter_id = "AND id_rizkia != '$exclude_id'";
    }

    $cek_mesin = mysqli_query($conn,"
    SELECT * FROM scheduling_rizkia
    WHERE mesin_id_rizkia='$mesin'
    AND status_rizkia='Dijadwalkan'
    AND ('$mulai' < waktu_selesai_rizkia AND '$selesai' > waktu_mulai_rizkia)
    $filter_id
    ");

    $cek_operator = mysqli_query($conn,"
    SELECT * FROM scheduling_rizkia
    WHERE operator_id_rizkia='$operator'
    AND status_rizkia='Dijadwalkan'
    AND ('$mulai' < waktu_selesai_rizkia AND '$selesai' > waktu_mulai_rizkia)
    $filter_id
    ");

    if(mysqli_num_rows($cek_mesin) > 0){
        return "Mesin sedang dipakai!";
    }

    if(mysqli_num_rows($cek_operator) > 0){
        return "Operator sedang bekerja!";
    }

    return false;
}

/* TAMBAH */
if(isset($_POST['jadwal_rizkia'])){

    $job      = $_POST['job'];
    $mesin    = $_POST['mesin'];
    $operator = $_POST['operator'];
    $waktu    = $_POST['waktu'];

    $job_data = mysqli_fetch_array(mysqli_query($conn_rizkia,
        "SELECT * FROM jobs_rizkia WHERE id_rizkia='$job'"));
    $jumlah = $job_data['jumlah_rizkia'];

    $mesin_data = mysqli_fetch_array(mysqli_query($conn_rizkia,
        "SELECT * FROM mesin_rizkia WHERE id_rizkia='$mesin'"));
    $durasi = $mesin_data['durasi_produksi_rizkia'];

    $total = $jumlah * $durasi;

    $mulai   = date("Y-m-d H:i:s", strtotime($waktu));
    $selesai = date("Y-m-d H:i:s", strtotime("+$total minutes", strtotime($waktu)));

    $error = cek_bentrok($conn_rizkia, $mesin, $operator, $mulai, $selesai);

    if(!$error){
        mysqli_query($conn_rizkia,"INSERT INTO scheduling_rizkia 
        (job_id_rizkia, mesin_id_rizkia, operator_id_rizkia, waktu_mulai_rizkia, waktu_selesai_rizkia, status_rizkia)
        VALUES('$job','$mesin','$operator','$mulai','$selesai','Dijadwalkan')");
    } else {
        echo "<script>alert('$error');</script>";
    }
}

/* UPDATE */
if(isset($_POST['update'])){

    $id       = $_POST['id'];
    $job      = $_POST['job'];
    $mesin    = $_POST['mesin'];
    $operator = $_POST['operator'];
    $waktu    = $_POST['waktu'];

    $job_data = mysqli_fetch_array(mysqli_query($conn_rizkia,
        "SELECT * FROM jobs_rizkia WHERE id_rizkia='$job'"));
    $jumlah = $job_data['jumlah_rizkia'];

    $mesin_data = mysqli_fetch_array(mysqli_query($conn_rizkia,
        "SELECT * FROM mesin_rizkia WHERE id_rizkia='$mesin'"));
    $durasi = $mesin_data['durasi_produksi_rizkia'];

    $total = $jumlah * $durasi;

    $mulai   = date("Y-m-d H:i:s", strtotime($waktu));
    $selesai = date("Y-m-d H:i:s", strtotime("+$total minutes", strtotime($waktu)));

    $error = cek_bentrok($conn_rizkia, $mesin, $operator, $mulai, $selesai, $id);

    if(!$error){
        mysqli_query($conn_rizkia,"UPDATE scheduling_rizkia SET
        job_id_rizkia='$job',
        mesin_id_rizkia='$mesin',
        operator_id_rizkia='$operator',
        waktu_mulai_rizkia='$mulai',
        waktu_selesai_rizkia='$selesai'
        WHERE id_rizkia='$id'");
    } else {
        echo "<script>alert('$error');</script>";
    }
}

/* DELETE */
if(isset($_POST['hapus'])){
    mysqli_query($conn_rizkia,"DELETE FROM scheduling_rizkia WHERE id_rizkia='$_POST[id]'");
}

/* DATA */
$s = mysqli_query($conn_rizkia,"
SELECT s.*, j.nama_job_rizkia, m.nama_mesin_rizkia, u.username_rizkia
FROM scheduling_rizkia s
JOIN jobs_rizkia j ON s.job_id_rizkia = j.id_rizkia
JOIN mesin_rizkia m ON s.mesin_id_rizkia = m.id_rizkia
JOIN users_rizkia u ON s.operator_id_rizkia = u.id_rizkia
ORDER BY s.id_rizkia DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Scheduling - MachinaFlow</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background:#eef2f7;
    color:#2c3e50;
}

/* HEADER */
.header{
    height:80px;
    margin-left:220px;
    background:linear-gradient(135deg,#1f2d3a,#2c3e50);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
    font-weight:bold;
    letter-spacing:1px;
    box-shadow:0 4px 12px rgba(0,0,0,0.15);
}

/* SIDEBAR */
.sidebar{
    width:220px;
    height:100vh;
    background:linear-gradient(180deg,#2c3e50,#34495e);
    position:fixed;
    top:0;
    left:0;
    display:flex;
    flex-direction:column;
    box-shadow:4px 0 15px rgba(0,0,0,0.08);
}

.sidebar .logo{
    padding:24px 20px;
    font-size:22px;
    font-weight:bold;
    color:white;
    text-align:center;
    border-bottom:1px solid rgba(255,255,255,0.08);
    letter-spacing:1px;
}

.menu{
    flex:1;
    padding:18px 12px;
    overflow-y:auto;
}

.sidebar a{
    display:block;
    color:#ecf0f1;
    text-decoration:none;
    padding:12px 14px;
    margin-bottom:8px;
    border-radius:10px;
    transition:0.3s;
    font-size:14px;
    font-weight:bold;
}

.sidebar a:hover{
    background:rgba(255,255,255,0.08);
    transform:translateX(4px);
}

.sidebar a.active{
    background:rgba(255,255,255,0.12);
}

/* LOGOUT */
.logout{
    margin:12px;
    background:#e74c3c !important;
    text-align:center;
    border-radius:10px;
}

.logout:hover{
    background:#c0392b !important;
    transform:none !important;
}

/* CONTENT */
.content{
    margin-left:220px;
    padding:25px;
}

/* CARD */
.card{
    background:white;
    border-radius:16px;
    padding:22px;
    margin-bottom:22px;
    box-shadow:0 8px 20px rgba(0,0,0,0.06);
    animation:fadeIn 0.5s ease;
}

.card h3{
    margin-bottom:18px;
    font-size:20px;
    color:#2c3e50;
}

/* FORM */
label{
    display:block;
    margin-bottom:8px;
    font-size:14px;
    font-weight:bold;
    color:#2c3e50;
}

input,select{
    width:100%;
    padding:11px 12px;
    margin-bottom:14px;
    border:1px solid #dcdde1;
    border-radius:10px;
    outline:none;
    transition:0.3s;
    background:#fbfcfe;
    font-size:14px;
}

input:focus,select:focus{
    border-color:#3498db;
    box-shadow:0 0 8px rgba(52,152,219,0.15);
    background:white;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    border-radius:12px;
    overflow:hidden;
}

th{
    background:#2c3e50;
    color:white;
    padding:14px;
    font-size:14px;
    text-align:center;
}

td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #ecf0f1;
    font-size:14px;
}

tr:hover{
    background:#f8fafc;
}

/* BUTTON */
button,.btn{
    padding:8px 14px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-size:13px;
    font-weight:bold;
    transition:0.3s;
    background:#3498db;
    color:white;
}

button:hover,.btn:hover{
    background:#2980b9;
}

.btn-hapus,.btn-batal{
    background:#e74c3c;
}

.btn-hapus:hover,.btn-batal:hover{
    background:#c0392b;
}

/* MODAL */
.modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.45);
    justify-content:center;
    align-items:center;
    z-index:999;
}

.modal-box{
    background:white;
    width:380px;
    padding:25px;
    border-radius:16px;
    box-shadow:0 15px 40px rgba(0,0,0,0.2);
    animation:fadeInScale 0.3s ease;
}

.modal-box h3{
    margin-bottom:16px;
    color:#2c3e50;
}

/* ANIMATION */
@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(12px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

@keyframes fadeInScale{
    from{
        opacity:0;
        transform:scale(0.95);
    }
    to{
        opacity:1;
        transform:scale(1);
    }
}
</style>
</head>

<body>

<div class="sidebar">
    <div class="logo">MachinaFlow</div>

    <div class="menu">
        <a href="dashboard_rizkia.php">Dashboard</a>
        <a href="jobs_rizkia.php">Jobs</a>
        <a href="sparepart_rizkia.php">Sparepart</a>
        <a href="scheduling_rizkia.php" class="active">Scheduling</a>
        <a href="mesin_rizkia.php">Mesin</a>
        <a href="users_rizkia.php">Users</a>
        <a href="laporan_rizkia.php">Laporan</a>
        <a href="monitoring_rizkia.php">Monitoring</a>
    </div>

    <a class="logout" href="../auth_rizkia/logout_rizkia.php">Logout</a>
</div>

<div class="header">Schedule Management</div>

<div class="content">

<div class="card">
<h3>Tambah Jadwal</h3>

<form method="POST">

<label>Job</label>
<select name="job">
<?php
$j = mysqli_query($conn_rizkia,"SELECT * FROM jobs_rizkia");
while($d = mysqli_fetch_array($j)){
    echo "<option value='$d[id_rizkia]'>$d[nama_job_rizkia]</option>";
}
?>
</select>

<label>Mesin</label>
<select name="mesin">
<?php
$m = mysqli_query($conn_rizkia,"SELECT * FROM mesin_rizkia");
while($d = mysqli_fetch_array($m)){
    echo "<option value='$d[id_rizkia]'>$d[nama_mesin_rizkia]</option>";
}
?>
</select>

<label>Operator</label>
<select name="operator">
<?php
$o = mysqli_query($conn_rizkia,"SELECT * FROM users_rizkia WHERE role_rizkia='operator'");
while($d = mysqli_fetch_array($o)){
    echo "<option value='$d[id_rizkia]'>$d[username_rizkia]</option>";
}
?>
</select>

<label>Waktu</label>
<input type="datetime-local" name="waktu" required>

<button name="jadwal_rizkia">Tambah</button>
</form>
</div>

<div class="card">
<h3>Data Jadwal</h3>

<table>
<tr>
<th>Job</th>
<th>Mesin</th>
<th>Operator</th>
<th>Mulai</th>
<th>Selesai</th>
<th>Status</th>
<th>Aksi</th>
</tr>

<?php while($d = mysqli_fetch_array($s)){ ?>
<tr>
<td><?= $d['nama_job_rizkia'] ?></td>
<td><?= $d['nama_mesin_rizkia'] ?></td>
<td><?= $d['username_rizkia'] ?></td>
<td><?= $d['waktu_mulai_rizkia'] ?></td>
<td><?= $d['waktu_selesai_rizkia'] ?></td>
<td>
<?php if($d['status_rizkia'] == 'Selesai'){ ?>
    <span style="color:green;font-weight:bold;">✔ Selesai</span>
<?php } else { ?>
    <span style="color:orange;font-weight:bold;">⏳ Dijadwalkan</span>
<?php } ?>
</td>
<td>

<button type="button" onclick="openEdit(
'<?= $d['id_rizkia'] ?>',
'<?= $d['job_id_rizkia'] ?>',
'<?= $d['mesin_id_rizkia'] ?>',
'<?= $d['operator_id_rizkia'] ?>',
'<?= date('Y-m-d\TH:i', strtotime($d['waktu_mulai_rizkia'])) ?>'
)">Edit</button>

<form method="POST" style="display:inline">
    <input type="hidden" name="id" value="<?= $d['id_rizkia'] ?>">
    <button class="btn-hapus" name="hapus">Hapus</button>
</form>

</td>
</tr>
<?php } ?>

</table>
</div>

</div>

<div class="modal" id="modal">
<div class="modal-box">

<h3>Edit Jadwal</h3>

<form method="POST">

<input type="hidden" name="id" id="id">

<label>Job</label>
<select name="job" id="job">
<?php
$j = mysqli_query($conn_rizkia,"SELECT * FROM jobs_rizkia");
while($d = mysqli_fetch_array($j)){
    echo "<option value='$d[id_rizkia]'>$d[nama_job_rizkia]</option>";
}
?>
</select>

<label>Mesin</label>
<select name="mesin" id="mesin">
<?php
$m = mysqli_query($conn_rizkia,"SELECT * FROM mesin_rizkia");
while($d = mysqli_fetch_array($m)){
    echo "<option value='$d[id_rizkia]'>$d[nama_mesin_rizkia]</option>";
}
?>
</select>

<label>Operator</label>
<select name="operator" id="operator">
<?php
$o = mysqli_query($conn_rizkia,"SELECT * FROM users_rizkia WHERE role_rizkia='operator'");
while($d = mysqli_fetch_array($o)){
    echo "<option value='$d[id_rizkia]'>$d[username_rizkia]</option>";
}
?>
</select>

<label>Waktu</label>
<input type="datetime-local" name="waktu" id="waktu" required>

<button name="update">Update</button>
<button type="button" class="btn-batal" onclick="closeModal()">Batal</button>

</form>

</div>
</div>

<script>
function openEdit(id,job,mesin,operator,waktu){
    document.getElementById('modal').style.display='flex';
    document.getElementById('id').value=id;
    document.getElementById('job').value=job;
    document.getElementById('mesin').value=mesin;
    document.getElementById('operator').value=operator;
    document.getElementById('waktu').value=waktu;
}

function closeModal(){
    document.getElementById('modal').style.display='none';
}

window.onclick = function(event){
    let modal = document.getElementById('modal');
    if(event.target == modal){
        modal.style.display = "none";
    }
}
</script>
</body>
</html>