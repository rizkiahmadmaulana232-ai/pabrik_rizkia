<?php

session_start();
include '../config_rizkia/koneksi_rizkia.php';
date_default_timezone_set("Asia/Jakarta");

if(!isset($_SESSION['user_rizkia']) || $_SESSION['user_rizkia']['role_rizkia'] != 'admin'){
    header("Location: ../auth_rizkia/login_rizkia.php");
    exit;
}

/* =========================
   DASHBOARD DATA
========================= */
$job = mysqli_fetch_array(mysqli_query($conn_rizkia,"SELECT COUNT(*) as total FROM jobs_rizkia"));
$mesin = mysqli_fetch_array(mysqli_query($conn_rizkia,"SELECT COUNT(*) as total FROM mesin_rizkia"));
$jadwal = mysqli_fetch_array(mysqli_query($conn_rizkia,"SELECT COUNT(*) as total FROM scheduling_rizkia"));

$aktif = mysqli_fetch_array(mysqli_query($conn_rizkia,"
    SELECT COUNT(*) as total 
    FROM scheduling_rizkia 
    WHERE status_rizkia='Dijadwalkan'
"));

$selesai = mysqli_fetch_array(mysqli_query($conn_rizkia,"
    SELECT COUNT(*) as total 
    FROM scheduling_rizkia 
    WHERE status_rizkia='Selesai'
"));

$operator = mysqli_fetch_array(mysqli_query($conn_rizkia,"
    SELECT COUNT(*) as total 
    FROM users_rizkia 
    WHERE role_rizkia='operator'
"));

$now_rizkia = date("Y-m-d H:i:s");

$status_labels = ['Dijadwalkan','Berjalan','Tertunda','Selesai'];
$status_counts = [];
$status_total = 0;
foreach($status_labels as $status_label){
    $status_aman = mysqli_real_escape_string($conn_rizkia, $status_label);
    $q_status = mysqli_fetch_array(mysqli_query($conn_rizkia, "SELECT COUNT(*) AS total FROM scheduling_rizkia WHERE status_rizkia='$status_aman'"));
    $jumlah_status = (int)($q_status['total'] ?? 0);
    $status_counts[$status_label] = $jumlah_status;
    $status_total += $jumlah_status;
}

/* RECENT REPORT */
$laporan = mysqli_query($conn_rizkia,"
SELECT * FROM laporan_rizkia
ORDER BY id_rizkia DESC
LIMIT 5
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard Admin - MachinaFlow</title>

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

@keyframes pulseGlow{
    0%{ box-shadow:0 0 0 rgba(52,152,219,0.25); }
    50%{ box-shadow:0 0 20px rgba(52,152,219,0.35); }
    100%{ box-shadow:0 0 0 rgba(52,152,219,0.25); }
}

@keyframes floatBg{
    0%,100%{ transform:translateY(0px); }
    50%{ transform:translateY(-12px); }
}

/* BG DECOR */
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
    z-index:11;
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
    position:relative;
    z-index:1;
}

/* CARD */
.card{
    background:rgba(255,255,255,0.95);
    backdrop-filter:blur(8px);
    padding:22px;
    margin-bottom:20px;
    border-radius:18px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
    border:1px solid #e6ebf1;
    animation:fadeUp 0.6s ease;
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
    margin-top:10px;
}

.stat-box{
    background:linear-gradient(135deg,#ffffff,#f8fbff);
    border:1px solid #e4edf5;
    border-radius:16px;
    padding:20px;
    box-shadow:0 4px 12px rgba(0,0,0,0.05);
    transition:0.3s;
    animation:fadeUp 0.7s ease;
}

.stat-box:hover{
    transform:translateY(-5px);
    box-shadow:0 10px 20px rgba(0,0,0,0.08);
}

.stat-box h4{
    font-size:13px;
    color:#7f8c8d;
    margin-bottom:10px;
    text-transform:uppercase;
    letter-spacing:1px;
}

.stat-box .number{
    font-size:34px;
    font-weight:bold;
    color:#2c3e50;
}

.stat-blue{ border-left:5px solid #3498db; }
.stat-green{ border-left:5px solid #27ae60; }
.stat-orange{ border-left:5px solid #f39c12; }
.stat-red{ border-left:5px solid #e74c3c; animation:pulseGlow 2.5s infinite; }

/* GRID */
.grid-2{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:20px;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
    background:white;
    border-radius:12px;
    overflow:hidden;
}

th{
    background:#2c3e50;
    color:white;
    font-size:13px;
}

th, td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #eef2f7;
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

.green{ background:#27ae60; }
.orange{ background:#f39c12; }
.red{ background:#e74c3c; }
.gray{ background:#7f8c8d; }

/* REPORT */
.report-item{
    padding:12px;
    border-bottom:1px solid #eef2f7;
    font-size:14px;
}

.report-item:last-child{
    border-bottom:none;
}

.report-date{
    font-size:12px;
    color:#7f8c8d;
    margin-top:4px;
}

.chart-list{
    display:flex;
    flex-direction:column;
    gap:14px;
}
.chart-row{
    display:grid;
    grid-template-columns:120px 1fr 50px;
    align-items:center;
    gap:10px;
}
.chart-bar-wrap{
    height:14px;
    border-radius:999px;
    background:#e9eef5;
    overflow:hidden;
}
.chart-bar{
    height:100%;
    border-radius:999px;
    background:linear-gradient(135deg,#3498db,#2c3e50);
}
.chart-bar[data-status="Berjalan"]{ background:linear-gradient(135deg,#27ae60,#1e8449); }
.chart-bar[data-status="Dijadwalkan"]{ background:linear-gradient(135deg,#f39c12,#d68910); }
.chart-bar[data-status="Tertunda"]{ background:linear-gradient(135deg,#e67e22,#ca6f1e); }
.chart-bar[data-status="Selesai"]{ background:linear-gradient(135deg,#3498db,#2e86c1); }
</style>
</head>
<body>

<div class="bg1"></div>
<div class="bg2"></div>
<div class="bg3"></div>

<div class="sidebar">
    <div class="logo">MachinaFlow</div>

    <div class="menu">
        <a href="dashboard_rizkia.php" class="active">Dashboard</a>
        <a href="jobs_rizkia.php">Jobs</a>
        <a href="sparepart_rizkia.php">Sparepart</a>
        <a href="scheduling_rizkia.php">Scheduling</a>
        <a href="mesin_rizkia.php">Mesin</a>
        <a href="users_rizkia.php">Users</a>
        <a href="laporan_rizkia.php">Laporan</a>
        <a href="monitoring_rizkia.php">Monitoring</a>
    </div>

    <a class="logout" href="../auth_rizkia/logout_rizkia.php">Logout</a>
</div>

<div class="header">Production Control Dashboard</div>

<div class="content">

<div class="card">
    <h3>Dashboard Overview</h3>

    <div class="stats">
        <div class="stat-box stat-blue">
            <h4>Total Jobs</h4>
            <div class="number"><?= $job['total'] ?></div>
        </div>

        <div class="stat-box stat-green">
            <h4>Total Mesin</h4>
            <div class="number"><?= $mesin['total'] ?></div>
        </div>

        <div class="stat-box stat-orange">
            <h4>Total Operator</h4>
            <div class="number"><?= $operator['total'] ?></div>
        </div>

        <div class="stat-box stat-red">
            <h4>Job Aktif</h4>
            <div class="number"><?= $aktif['total'] ?></div>
        </div>

        <div class="stat-box stat-blue">
            <h4>Total Scheduling</h4>
            <div class="number"><?= $jadwal['total'] ?></div>
        </div>

        <div class="stat-box stat-green">
            <h4>Job Selesai</h4>
            <div class="number"><?= $selesai['total'] ?></div>
        </div>
    </div>
</div>

<div class="grid-2">

    <div class="card">
        <h3>Distribusi Status Scheduling</h3>
        <div class="chart-list">
            <?php foreach($status_labels as $status_label){ 
                $jumlah_status = (int)($status_counts[$status_label] ?? 0);
                $persen = $status_total > 0 ? round(($jumlah_status / $status_total) * 100) : 0;
            ?>
            <div class="chart-row">
                <strong><?= $status_label ?></strong>
                <div class="chart-bar-wrap">
                    <div class="chart-bar" data-status="<?= $status_label ?>" style="width:<?= $persen ?>%"></div>
                </div>
                <span><?= $jumlah_status ?></span>
            </div>
            <?php } ?>
        </div>
    </div>

    <div class="card">
        <h3>Laporan Terbaru</h3>

        <?php while($l=mysqli_fetch_array($laporan)){ ?>
            <div class="report-item">
                <?= $l['isi_rizkia'] ?>
                <div class="report-date"><?= $l['tanggal_rizkia'] ?></div>
            </div>
        <?php } ?>
    </div>

</div>

</div>

</body>
</html>
