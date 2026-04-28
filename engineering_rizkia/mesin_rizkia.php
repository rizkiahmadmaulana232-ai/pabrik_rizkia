<?php

session_start();
include '../config_rizkia/koneksi_rizkia.php';
date_default_timezone_set("Asia/Jakarta");

/* ROLE CHECK ENGINEERING */
if(!isset($_SESSION['user_rizkia']['role_rizkia']) || $_SESSION['user_rizkia']['role_rizkia'] != 'engineering'){
    die("Akses ditolak. Hanya Engineering.");
}

/* UPDATE STATUS MESIN */
if(isset($_POST['update_rizkia'])){
    $id_mesin = mysqli_real_escape_string($conn_rizkia, $_POST['id_mesin']);
    $status = mysqli_real_escape_string($conn_rizkia, $_POST['status']);

    mysqli_query($conn_rizkia,"UPDATE mesin_rizkia 
    SET status_rizkia='$status' 
    WHERE id_rizkia='$id_mesin'");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Engineering Dashboard - MachinaFlow</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background:linear-gradient(135deg,#eef2f7,#dfe9f3);
    color:#2c3e50;
    overflow-x:hidden;
}

/* ANIMATION */
@keyframes fadeUp{
    from{
        opacity:0;
        transform:translateY(18px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

@keyframes fadeScale{
    from{
        opacity:0;
        transform:scale(0.96);
    }
    to{
        opacity:1;
        transform:scale(1);
    }
}

@keyframes floatBg{
    0%,100%{ transform:translateY(0px); }
    50%{ transform:translateY(-12px); }
}

/* BACKGROUND DECOR */
.bg1,.bg2,.bg3{
    position:fixed;
    border-radius:50%;
    background:rgba(44,62,80,0.05);
    z-index:0;
    animation:floatBg 8s ease-in-out infinite;
}

.bg1{
    width:260px;
    height:260px;
    top:-90px;
    left:-80px;
}

.bg2{
    width:180px;
    height:180px;
    right:40px;
    top:120px;
    animation-delay:2s;
}

.bg3{
    width:140px;
    height:140px;
    right:180px;
    bottom:40px;
    animation-delay:4s;
}

/* HEADER */
.header{
    height:80px;
    margin-left:220px;
    background:linear-gradient(135deg,#1f2d3a,#2c3e50,#34495e);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
    font-weight:bold;
    letter-spacing:1px;
    box-shadow:0 4px 15px rgba(0,0,0,0.15);
    position:sticky;
    top:0;
    z-index:10;
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
    box-shadow:4px 0 15px rgba(0,0,0,0.12);
    z-index:11;
}

.brand{
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
    background:#e74c3c;
    text-align:center;
    padding:12px;
    border-radius:10px;
    font-weight:bold;
    color:white !important;
    text-decoration:none;
    transition:0.3s;
}

.logout:hover{
    background:#c0392b !important;
    transform:none !important;
}

/* CONTENT */
.content{
    margin-left:220px;
    padding:25px;
    position:relative;
    z-index:1;
}

/* CARD */
.card{
    background:rgba(255,255,255,0.96);
    backdrop-filter:blur(8px);
    border-radius:18px;
    padding:22px;
    margin-bottom:22px;
    box-shadow:0 8px 24px rgba(0,0,0,0.08);
    border:1px solid #e6ebf1;
    animation:fadeUp 0.5s ease;
}

.card h3{
    margin-bottom:18px;
    font-size:22px;
    color:#2c3e50;
    border-bottom:2px solid #eef2f7;
    padding-bottom:10px;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    border-radius:14px;
    overflow:hidden;
    background:white;
}

th{
    background:#2c3e50;
    color:white;
    padding:14px;
    font-size:14px;
    text-align:center;
}

td{
    padding:14px;
    text-align:center;
    border-bottom:1px solid #eef2f7;
    font-size:14px;
}

tr:hover{
    background:#f8fbff;
}

/* BADGE */
.badge{
    display:inline-block;
    padding:7px 12px;
    border-radius:999px;
    color:white;
    font-size:12px;
    font-weight:bold;
}

.normal{ background:#27ae60; }
.rusak{ background:#e74c3c; }
.perbaikan{ background:#f39c12; }
.diproses{ background:#3498db; }
.selesai{ background:#8e44ad; }

/* BUTTON */
button{
    padding:9px 14px;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-size:13px;
    font-weight:bold;
    transition:0.3s;
}

.btn-primary{
    background:#3498db;
    color:white;
}

.btn-primary:hover{
    background:#2980b9;
}

.btn-danger{
    background:#e74c3c;
    color:white;
}

.btn-danger:hover{
    background:#c0392b;
}

/* MODAL */
#modal{
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
    border-radius:18px;
    box-shadow:0 15px 40px rgba(0,0,0,0.2);
    animation:fadeScale 0.3s ease;
}

.modal-box h3{
    margin-bottom:16px;
    color:#2c3e50;
}

.modal-box select{
    width:100%;
    padding:11px 12px;
    margin-top:10px;
    border:1px solid #dcdde1;
    border-radius:10px;
    outline:none;
    background:#fbfcfe;
    font-size:14px;
}

.modal-box select:focus{
    border-color:#3498db;
    box-shadow:0 0 8px rgba(52,152,219,0.15);
    background:white;
}

.modal-action{
    display:flex;
    gap:10px;
    margin-top:18px;
}
</style>

</head>
<body>

<div class="bg1"></div>
<div class="bg2"></div>
<div class="bg3"></div>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="brand">MACHINAFLOW</div>

    <div class="menu">
        <a href="dashboard_rizkia.php">Dashboard</a>
        <a href="mesin_rizkia.php" class="active">Kelola Mesin</a>
    </div>

    <a class="logout" href="../auth_rizkia/logout_rizkia.php">Logout</a>
</div>

<!-- HEADER -->
<div class="header">
    Engineering Control Panel
</div>

<!-- CONTENT -->
<div class="content">

<div class="card">
<h3>Status Mesin Produksi</h3>

<table>
<tr>
<th>ID</th>
<th>Nama Mesin</th>
<th>Status</th>
<th>Aksi</th>
</tr>

<?php
$data = mysqli_query($conn_rizkia,"SELECT * FROM mesin_rizkia");
while($d=mysqli_fetch_array($data)){

    $badge = strtolower($d['status_rizkia']);
?>
<tr>
    <td><?= $d['id_rizkia']; ?></td>
    <td><?= $d['nama_mesin_rizkia']; ?></td>
    <td><span class="badge <?= $badge; ?>"><?= $d['status_rizkia']; ?></span></td>
    <td>
        <button class="btn-primary" onclick="openModal('<?= $d['id_rizkia']; ?>')">Update</button>
    </td>
</tr>
<?php } ?>
</table>

</div>

</div>

<!-- FORM HIDDEN -->
<form method="POST" id="formUpdate" style="display:none;">
    <input type="hidden" name="id_mesin" id="id_mesin">
    <input type="hidden" name="status" id="status">
    <button name="update_rizkia">submit</button>
</form>

<!-- MODAL -->
<div id="modal">
    <div class="modal-box">
        <h3>Update Status Mesin</h3>

        <select id="popup_status">
            <option value="Normal">Normal</option>
            <option value="Rusak">Rusak</option>
            <option value="Perbaikan">Perbaikan</option>
            <option value="Diproses">Diproses</option>
            <option value="Selesai">Selesai</option>
        </select>

        <div class="modal-action">
            <button class="btn-primary" onclick="submitUpdate()">Update</button>
            <button class="btn-danger" onclick="closeModal()">Batal</button>
        </div>
    </div>
</div>

<script>
let selectedId = null;

function openModal(id){
    selectedId = id;
    document.getElementById('modal').style.display = 'flex';
}

function closeModal(){
    document.getElementById('modal').style.display = 'none';
}

function submitUpdate(){
    document.getElementById('id_mesin').value = selectedId;
    document.getElementById('status').value = document.getElementById('popup_status').value;
    document.getElementById('formUpdate').submit();
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