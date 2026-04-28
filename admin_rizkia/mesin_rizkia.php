<?php

include '../config_rizkia/koneksi_rizkia.php';
session_start();

/* ROLE CHECK ADMIN */
if(!isset($_SESSION['user_rizkia']['role_rizkia']) || $_SESSION['user_rizkia']['role_rizkia'] != 'admin'){
    die("Akses ditolak. Hanya Admin.");
}

/* TAMBAH MESIN */
if(isset($_POST['tambah_rizkia'])){
    $nama = mysqli_real_escape_string($conn_rizkia, $_POST['nama']);
    $durasi = mysqli_real_escape_string($conn_rizkia, $_POST['durasi']);

    mysqli_query($conn_rizkia,"INSERT INTO mesin_rizkia 
    VALUES(NULL,'$nama','Normal','$durasi')");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Mesin - MachinaFlow</title>
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
    background:linear-gradient(135deg,#1f2d3a,#2c3e50);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    padding-left:220px;
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
.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr auto;
    gap:12px;
    align-items:end;
}

.input-group{
    display:flex;
    flex-direction:column;
}

.input-group label{
    margin-bottom:6px;
    font-size:13px;
    font-weight:bold;
    color:#34495e;
}

input{
    width:100%;
    padding:12px;
    border:1px solid #dcdde1;
    border-radius:10px;
    font-size:14px;
    transition:0.3s;
}

input:focus{
    outline:none;
    border-color:#2980b9;
    box-shadow:0 0 0 3px rgba(41,128,185,0.15);
}

/* BUTTON */
button{
    padding:12px 18px;
    border:none;
    border-radius:10px;
    background:linear-gradient(135deg,#2c3e50,#34495e);
    color:white;
    font-size:14px;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
    box-shadow:0 6px 14px rgba(44,62,80,0.18);
}

button:hover{
    transform:translateY(-2px);
    background:linear-gradient(135deg,#1f2d3a,#2c3e50);
}

/* TABLE */
.table-wrap{
    overflow-x:auto;
}

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
    padding:13px;
    text-align:center;
    border-bottom:1px solid #ecf0f1;
    font-size:14px;
}

tr:hover td{
    background:#f8fafc;
}

.status{
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
    display:inline-block;
}

.status.normal{
    background:#eafaf1;
    color:#27ae60;
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
</style>
</head>

<body>

<div class="sidebar">
    <div class="logo">MachinaFlow</div>

    <div class="menu">
        <a href="dashboard_rizkia.php">Dashboard</a>
        <a href="jobs_rizkia.php">Jobs</a>
        <a href="sparepart_rizkia.php">Sparepart</a>
        <a href="scheduling_rizkia.php">Scheduling</a>
        <a href="mesin_rizkia.php" class="active">Mesin</a>
        <a href="users_rizkia.php">Users</a>
        <a href="laporan_rizkia.php">Laporan</a>
        <a href="monitoring_rizkia.php">Monitoring</a>
    </div>

    <a class="logout" href="../auth_rizkia/logout_rizkia.php">Logout</a>
</div>

<div class="header">
    Machine Management
</div>

<div class="content">

    <div class="card">
        <h3>Tambah Mesin</h3>

        <form method="POST" class="form-grid">
            <div class="input-group">
                <label>Nama Mesin</label>
                <input type="text" name="nama" placeholder="Contoh: CNC Lathe" required>
            </div>

            <div class="input-group">
                <label>Durasi Produksi (menit)</label>
                <input type="number" name="durasi" placeholder="Contoh: 30" required>
            </div>

            <button name="tambah_rizkia">Tambah</button>
        </form>
    </div>

    <div class="card">
        <h3>Data Mesin</h3>

        <div class="table-wrap">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nama Mesin</th>
                    <th>Durasi Produksi</th>
                    <th>Status</th>
                </tr>

                <?php
                $data = mysqli_query($conn_rizkia,"SELECT * FROM mesin_rizkia");
                while($d=mysqli_fetch_array($data)){
                ?>
                <tr>
                    <td><?= $d['id_rizkia'] ?></td>
                    <td><?= $d['nama_mesin_rizkia'] ?></td>
                    <td><?= $d['durasi_produksi_rizkia'] ?> menit</td>
                    <td><span class="status normal"><?= $d['status_rizkia'] ?></span></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

</div>

</body>
</html>