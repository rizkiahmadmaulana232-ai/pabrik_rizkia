<?php

session_start();
include '../config_rizkia/koneksi_rizkia.php';
date_default_timezone_set("Asia/Jakarta");

if(!isset($_SESSION['user_rizkia']) || $_SESSION['user_rizkia']['role_rizkia'] != 'operator'){
    header("Location: ../auth_rizkia/login_rizkia.php");
    exit;
}

$id_operator   = $_SESSION['user_rizkia']['id_rizkia'];
$nama_operator = $_SESSION['user_rizkia']['username_rizkia'];
$now = date("Y-m-d H:i:s");

/* =========================
   NOTIF H-1 JAM
========================= */
$data_notif = mysqli_query($conn_rizkia,"
SELECT s.*, j.nama_job_rizkia, m.nama_mesin_rizkia
FROM scheduling_rizkia s
JOIN jobs_rizkia j ON s.job_id_rizkia = j.id_rizkia
JOIN mesin_rizkia m ON s.mesin_id_rizkia = m.id_rizkia
WHERE s.operator_id_rizkia = '$id_operator'
AND s.status_rizkia = 'Dijadwalkan'
");

$notif = [];

while($d = mysqli_fetch_array($data_notif)){
    $selisih = (strtotime($d['waktu_mulai_rizkia']) - strtotime($now)) / 60;

    if($selisih <= 60 && $selisih > 0 && $d['notif_rizkia'] == 0){
        $notif[] = $d;

        mysqli_query($conn_rizkia,"
        UPDATE scheduling_rizkia 
        SET notif_rizkia = 1 
        WHERE id_rizkia = '".$d['id_rizkia']."'
        ");
    }
}

/* =========================
   DASHBOARD STATS
========================= */
$total_jadwal = mysqli_fetch_array(mysqli_query($conn_rizkia,"
SELECT COUNT(*) as total 
FROM scheduling_rizkia 
WHERE operator_id_rizkia='$id_operator'
"));

$jadwal_aktif = mysqli_fetch_array(mysqli_query($conn_rizkia,"
SELECT COUNT(*) as total 
FROM scheduling_rizkia 
WHERE operator_id_rizkia='$id_operator'
AND status_rizkia='Dijadwalkan'
"));

$jadwal_selesai = mysqli_fetch_array(mysqli_query($conn_rizkia,"
SELECT COUNT(*) as total 
FROM scheduling_rizkia 
WHERE operator_id_rizkia='$id_operator'
AND status_rizkia='Selesai'
"));

$jadwal_hari_ini = mysqli_fetch_array(mysqli_query($conn_rizkia,"
SELECT COUNT(*) as total 
FROM scheduling_rizkia 
WHERE operator_id_rizkia='$id_operator'
AND DATE(waktu_mulai_rizkia)=CURDATE()
"));

/* =========================
   DATA JADWAL
========================= */
$data2 = mysqli_query($conn_rizkia,"
SELECT s.*, j.nama_job_rizkia, m.nama_mesin_rizkia
FROM scheduling_rizkia s
JOIN jobs_rizkia j ON s.job_id_rizkia = j.id_rizkia
JOIN mesin_rizkia m ON s.mesin_id_rizkia = m.id_rizkia
WHERE s.operator_id_rizkia='$id_operator'
ORDER BY s.waktu_mulai_rizkia ASC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard Operator - MachinaFlow</title>
<meta http-equiv="refresh" content="30">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    background:linear-gradient(135deg,#eef2f7,#dfe9f3);
    color:#2c3e50;
    overflow-x:hidden;
}

/* ANIMATION */
@keyframes fadeUp{
    from{opacity:0; transform:translateY(18px);}
    to{opacity:1; transform:translateY(0);}
}

@keyframes fadeScale{
    from{opacity:0; transform:scale(.96);}
    to{opacity:1; transform:scale(1);}
}

@keyframes floatBg{
    0%,100%{transform:translateY(0);}
    50%{transform:translateY(-12px);}
}

/* BG */
.bg1,.bg2,.bg3{
    position:fixed;
    border-radius:50%;
    background:rgba(44,62,80,.05);
    z-index:0;
    animation:floatBg 8s ease-in-out infinite;
}
.bg1{width:260px;height:260px;top:-90px;left:-80px;}
.bg2{width:180px;height:180px;right:40px;top:120px;animation-delay:2s;}
.bg3{width:140px;height:140px;right:180px;bottom:40px;animation-delay:4s;}

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
    box-shadow:4px 0 15px rgba(0,0,0,.12);
    z-index:11;
}

.brand{
    padding:24px 20px;
    font-size:22px;
    font-weight:bold;
    color:white;
    text-align:center;
    border-bottom:1px solid rgba(255,255,255,.08);
    letter-spacing:1px;
}

.menu{
    flex:1;
    padding:18px 12px;
}

.sidebar a{
    display:block;
    color:#ecf0f1;
    text-decoration:none;
    padding:12px 14px;
    margin-bottom:8px;
    border-radius:10px;
    transition:.3s;
    font-size:14px;
    font-weight:bold;
}

.sidebar a:hover{
    background:rgba(255,255,255,.08);
    transform:translateX(4px);
}

.sidebar a.active{
    background:rgba(255,255,255,.12);
}

.logout{
    margin:12px;
    background:#e74c3c;
    text-align:center;
    padding:12px;
    border-radius:10px;
    font-weight:bold;
    color:white !important;
}

.logout:hover{
    background:#c0392b !important;
    transform:none !important;
}

/* HEADER */
.header{
    height:80px;
    margin-left:220px;
    background:linear-gradient(135deg,#1f2d3a,#2c3e50,#34495e);
    color:white;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 30px;
    box-shadow:0 4px 15px rgba(0,0,0,.15);
    position:sticky;
    top:0;
    z-index:10;
}

.header h2{
    font-size:24px;
}

.header span{
    font-size:14px;
    opacity:.9;
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
    background:rgba(255,255,255,.96);
    backdrop-filter:blur(8px);
    border-radius:18px;
    padding:22px;
    margin-bottom:22px;
    box-shadow:0 8px 24px rgba(0,0,0,.08);
    border:1px solid #e6ebf1;
    animation:fadeUp .5s ease;
}

.card h3{
    margin-bottom:18px;
    font-size:22px;
    color:#2c3e50;
    border-bottom:2px solid #eef2f7;
    padding-bottom:10px;
}

/* STATS */
.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:18px;
}

.stat-box{
    background:linear-gradient(135deg,#ffffff,#f8fbff);
    border:1px solid #e4edf5;
    border-radius:16px;
    padding:20px;
    box-shadow:0 4px 12px rgba(0,0,0,.05);
    transition:.3s;
}

.stat-box:hover{ transform:translateY(-5px); }

.stat-box h4{
    font-size:13px;
    color:#7f8c8d;
    margin-bottom:10px;
    text-transform:uppercase;
}

.stat-box .number{
    font-size:34px;
    font-weight:bold;
}

.stat-blue{ border-left:5px solid #3498db; }
.stat-green{ border-left:5px solid #27ae60; }
.stat-orange{ border-left:5px solid #f39c12; }
.stat-purple{ border-left:5px solid #8e44ad; }

/* NOTIF */
.notif{
    background:linear-gradient(135deg,#f39c12,#e67e22);
    color:white;
    padding:16px;
    border-radius:14px;
    margin-bottom:14px;
    box-shadow:0 8px 18px rgba(243,156,18,.25);
    animation:fadeScale .4s ease;
    line-height:1.6;
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
    vertical-align:middle;
}

tr:hover{ background:#f8fbff; }

/* BADGE */
.badge{
    display:inline-block;
    padding:7px 12px;
    border-radius:999px;
    color:white;
    font-size:12px;
    font-weight:bold;
}

.pending{ background:#f39c12; }
.done{ background:#27ae60; }
</style>
</head>
<body>

<div class="bg1"></div>
<div class="bg2"></div>
<div class="bg3"></div>

<div class="sidebar">
    <div class="brand">MACHINAFLOW</div>

    <div class="menu">
        <a href="dashboard_rizkia.php" class="active">Dashboard</a>
        <a href="jadwal_rizkia.php">Jadwal Saya</a>
        <a href="riwayat_rizkia.php">Riwayat Produksi</a>
    </div>

    <a class="logout" href="../auth_rizkia/logout_rizkia.php">Logout</a>
</div>

<div class="header">
    <h2>Dashboard Operator</h2>
    <span>Halo, <?= $nama_operator; ?></span>
</div>

<div class="content">

<?php if(count($notif) > 0){ ?>
    <?php foreach($notif as $n){ ?>
        <div class="notif">
            🔔 Jadwal akan segera dimulai<br>
            <b>Job:</b> <?= $n['nama_job_rizkia']; ?><br>
            <b>Mesin:</b> <?= $n['nama_mesin_rizkia']; ?><br>
            <b>Mulai:</b> <?= $n['waktu_mulai_rizkia']; ?>
        </div>
    <?php } ?>
<?php } ?>

<div class="card">
    <h3>Ringkasan Operator</h3>

    <div class="stats">
        <div class="stat-box stat-blue">
            <h4>Total Jadwal</h4>
            <div class="number"><?= $total_jadwal['total']; ?></div>
        </div>

        <div class="stat-box stat-orange">
            <h4>Jadwal Aktif</h4>
            <div class="number"><?= $jadwal_aktif['total']; ?></div>
        </div>

        <div class="stat-box stat-green">
            <h4>Selesai</h4>
            <div class="number"><?= $jadwal_selesai['total']; ?></div>
        </div>

        <div class="stat-box stat-purple">
            <h4>Hari Ini</h4>
            <div class="number"><?= $jadwal_hari_ini['total']; ?></div>
        </div>
    </div>
</div>

<div class="card">
    <h3>Jadwal Saya</h3>

    <table>
        <tr>
            <th>Job</th>
            <th>Mesin</th>
            <th>Mulai</th>
            <th>Selesai (Real)</th>
            <th>Status</th>
        </tr>

        <?php while($d = mysqli_fetch_array($data2)){ ?>
        <tr>
            <td><?= $d['nama_job_rizkia']; ?></td>
            <td><?= $d['nama_mesin_rizkia']; ?></td>
            <td><?= $d['waktu_mulai_rizkia']; ?></td>
            <td><?= $d['waktu_selesai_rizkia'] ? $d['waktu_selesai_rizkia'] : '-'; ?></td>
            <td>
                <span class="badge <?= ($d['status_rizkia'] == 'Selesai') ? 'done' : 'pending'; ?>">
                    <?= $d['status_rizkia']; ?>
                </span>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</div>
</body>
</html>