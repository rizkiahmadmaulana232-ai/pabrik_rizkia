<?php

session_start();
include '../config_rizkia/koneksi_rizkia.php';
date_default_timezone_set("Asia/Jakarta");

/* ROLE CHECK ENGINEERING */
if(!isset($_SESSION['user_rizkia']) || $_SESSION['user_rizkia']['role_rizkia'] != 'engineering'){
    header("Location: ../auth_rizkia/login_rizkia.php");
    exit;
}

$now = date("Y-m-d H:i:s");

/* =========================
   AUTO UPDATE STATUS SCHEDULING
========================= */
mysqli_query($conn_rizkia,"
UPDATE scheduling_rizkia 
SET status_rizkia='Selesai'
WHERE status_rizkia IN ('Dijadwalkan','Berjalan')
AND waktu_selesai_rizkia <= '$now'
");

mysqli_query($conn_rizkia,"
UPDATE jobs_rizkia j
SET j.status_rizkia = CASE
    WHEN EXISTS (
        SELECT 1 FROM scheduling_rizkia s1
        WHERE s1.job_id_rizkia = j.id_rizkia
    ) AND NOT EXISTS (
        SELECT 1 FROM scheduling_rizkia s2
        WHERE s2.job_id_rizkia = j.id_rizkia
        AND s2.status_rizkia != 'Selesai'
    ) THEN 'Selesai'
    WHEN EXISTS (
        SELECT 1 FROM scheduling_rizkia s3
        WHERE s3.job_id_rizkia = j.id_rizkia
    ) THEN 'Proses'
    ELSE COALESCE(j.status_rizkia, 'Menunggu')
END
");

/* =========================
   DASHBOARD DATA
========================= */
$total_mesin = mysqli_fetch_array(mysqli_query($conn_rizkia,"
SELECT COUNT(*) as total FROM mesin_rizkia
"));

$normal = mysqli_fetch_array(mysqli_query($conn_rizkia,"
SELECT COUNT(*) as total FROM mesin_rizkia WHERE status_rizkia='Normal'
"));

$rusak = mysqli_fetch_array(mysqli_query($conn_rizkia,"
SELECT COUNT(*) as total FROM mesin_rizkia WHERE status_rizkia='Rusak'
"));

$perbaikan = mysqli_fetch_array(mysqli_query($conn_rizkia,"
SELECT COUNT(*) as total FROM mesin_rizkia WHERE status_rizkia='Perbaikan'
"));

$aktif = mysqli_fetch_array(mysqli_query($conn_rizkia,"
SELECT COUNT(*) as total 
FROM scheduling_rizkia 
WHERE status_rizkia='Dijadwalkan'
"));

/* =========================
   LIVE MACHINE STATUS
========================= */
$mesin = mysqli_query($conn_rizkia,"
SELECT * FROM mesin_rizkia
ORDER BY id_rizkia ASC
");

/* =========================
   LIVE SCHEDULING
========================= */
$jadwal = mysqli_query($conn_rizkia,"
SELECT s.*, j.nama_job_rizkia, m.nama_mesin_rizkia, u.username_rizkia
FROM scheduling_rizkia s
LEFT JOIN jobs_rizkia j ON s.job_id_rizkia = j.id_rizkia
LEFT JOIN mesin_rizkia m ON s.mesin_id_rizkia = m.id_rizkia
LEFT JOIN users_rizkia u ON s.operator_id_rizkia = u.id_rizkia
ORDER BY s.waktu_mulai_rizkia ASC
LIMIT 6
");
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

/* BG */
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
    box-shadow:0 4px 12px rgba(0,0,0,0.05);
    transition:0.3s;
}

.stat-box:hover{
    transform:translateY(-5px);
}

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
.stat-red{ border-left:5px solid #e74c3c; }
.stat-orange{ border-left:5px solid #f39c12; }

/* GRID */
.grid-2{
    display:grid;
    grid-template-columns:1.4fr 1fr;
    gap:20px;
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

.live{ background:#27ae60; }
.wait{ background:#f39c12; }
.done{ background:#e74c3c; }
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
        <a href="mesin_rizkia.php">Kelola Mesin</a>
    </div>

    <a class="logout" href="../auth_rizkia/logout_rizkia.php">Logout</a>
</div>

<div class="header">
    Engineering Dashboard
</div>

<div class="content">

<div class="card">
    <h3>Engineering Overview</h3>

    <div class="stats">
        <div class="stat-box stat-blue">
            <h4>Total Mesin</h4>
            <div class="number"><?= $total_mesin['total'] ?></div>
        </div>

        <div class="stat-box stat-green">
            <h4>Mesin Normal</h4>
            <div class="number"><?= $normal['total'] ?></div>
        </div>

        <div class="stat-box stat-red">
            <h4>Mesin Rusak</h4>
            <div class="number"><?= $rusak['total'] ?></div>
        </div>

        <div class="stat-box stat-orange">
            <h4>Perbaikan</h4>
            <div class="number"><?= $perbaikan['total'] ?></div>
        </div>

        <div class="stat-box stat-blue">
            <h4>Job Aktif</h4>
            <div class="number"><?= $aktif['total'] ?></div>
        </div>
    </div>
</div>

<div class="grid-2">

    <div class="card">
        <h3>Status Mesin</h3>

        <table>
            <tr>
                <th>ID</th>
                <th>Nama Mesin</th>
                <th>Status</th>
            </tr>

            <?php while($m = mysqli_fetch_array($mesin)){ ?>
            <tr>
                <td><?= $m['id_rizkia'] ?></td>
                <td><?= $m['nama_mesin_rizkia'] ?></td>
                <td><span class="badge <?= strtolower($m['status_rizkia']) ?>"><?= $m['status_rizkia'] ?></span></td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <div class="card">
        <h3>Live Activity</h3>

        <table>
            <tr>
                <th>Mesin</th>
                <th>Job</th>
                <th>Status</th>
            </tr>

            <?php while($d = mysqli_fetch_array($jadwal)){ 
                $badge = "wait";
                $text  = "Menunggu";

                if($d['status_rizkia'] == 'Selesai'){
                    $badge = "done";
                    $text  = "Selesai";
                }elseif($now >= $d['waktu_mulai_rizkia'] && $now <= $d['waktu_selesai_rizkia']){
                    $badge = "live";
                    $text  = "Berjalan";
                }
            ?>
            <tr>
                <td><?= $d['nama_mesin_rizkia'] ?></td>
                <td><?= $d['nama_job_rizkia'] ?></td>
                <td><span class="badge <?= $badge ?>"><?= $text ?></span></td>
            </tr>
            <?php } ?>
        </table>
    </div>

</div>

</div>

</body>
</html>
