<?php

include '../config_rizkia/koneksi_rizkia.php';
session_start();
date_default_timezone_set("Asia/Jakarta");

/* ROLE CHECK */
if(!isset($_SESSION['user_rizkia'])){
    header("Location: ../auth_rizkia/login_rizkia.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>MachinaFlow - Monitoring Produksi</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial, sans-serif;
        }

        body{
            background:linear-gradient(135deg,#eef2f7,#dfe9f3);
            min-height:100vh;
            overflow-x:hidden;
            color:#2c3e50;
        }

        @keyframes fadeInUp{
            from{ opacity:0; transform:translateY(20px); }
            to{ opacity:1; transform:translateY(0); }
        }

        @keyframes pulseGreen{
            0%{ box-shadow:0 0 0 rgba(39,174,96,0.4); }
            50%{ box-shadow:0 0 18px rgba(39,174,96,0.7); }
            100%{ box-shadow:0 0 0 rgba(39,174,96,0.4); }
        }

        @keyframes pulseYellow{
            0%{ box-shadow:0 0 0 rgba(243,156,18,0.4); }
            50%{ box-shadow:0 0 18px rgba(243,156,18,0.7); }
            100%{ box-shadow:0 0 0 rgba(243,156,18,0.4); }
        }

        @keyframes pulseRed{
            0%{ box-shadow:0 0 0 rgba(231,76,60,0.4); }
            50%{ box-shadow:0 0 18px rgba(231,76,60,0.7); }
            100%{ box-shadow:0 0 0 rgba(231,76,60,0.4); }
        }

        @keyframes floatBg{
            0%,100%{ transform:translateY(0px); }
            50%{ transform:translateY(-12px); }
        }

        .bg-circle1,.bg-circle2,.bg-circle3{
            position:fixed;
            border-radius:50%;
            background:rgba(44,62,80,0.05);
            z-index:0;
            animation:floatBg 8s ease-in-out infinite;
        }

        .bg-circle1{
            width:260px;
            height:260px;
            top:-80px;
            left:-80px;
        }

        .bg-circle2{
            width:180px;
            height:180px;
            bottom:40px;
            right:50px;
            animation-delay:2s;
        }

        .bg-circle3{
            width:120px;
            height:120px;
            top:200px;
            right:250px;
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
            position:sticky;
            top:0;
            z-index:10;
        }

        /* SIDEBAR (DISAMAKAN DENGAN LAPORAN) */
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
            animation:fadeInUp 0.8s ease;
        }

        .card{
            background:rgba(255,255,255,0.96);
            backdrop-filter:blur(8px);
            border-radius:16px;
            padding:22px;
            margin-bottom:22px;
            box-shadow:0 8px 20px rgba(0,0,0,0.06);
            animation:fadeInUp 0.5s ease;
        }

        .card h3{
            margin-bottom:18px;
            font-size:20px;
            color:#2c3e50;
        }

        .summary-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
            gap:18px;
            margin-bottom:20px;
        }

        .summary-box{
            padding:22px;
            border-radius:16px;
            color:white;
            box-shadow:0 8px 20px rgba(0,0,0,0.08);
            animation:fadeInUp 0.8s ease;
        }

        .summary-box h4{
            font-size:14px;
            margin-bottom:8px;
            opacity:0.9;
        }

        .summary-box h2{
            font-size:30px;
            font-weight:bold;
        }

        .blue{ background:linear-gradient(135deg,#3498db,#2980b9); }
        .green{ background:linear-gradient(135deg,#27ae60,#1e8449); }
        .orange{ background:linear-gradient(135deg,#f39c12,#d68910); }
        .red{ background:linear-gradient(135deg,#e74c3c,#c0392b); }

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
            padding:14px;
            text-align:center;
            border-bottom:1px solid #ecf0f1;
            font-size:14px;
            color:#2c3e50;
            background:white;
        }

        tr:hover td{
            background:#f8fafc;
        }

        .badge{
            display:inline-block;
            padding:8px 14px;
            border-radius:999px;
            color:white;
            font-size:12px;
            font-weight:bold;
            min-width:140px;
        }

        .status-hijau{ background:#27ae60; animation:pulseGreen 2s infinite; }
        .status-kuning{ background:#f39c12; animation:pulseYellow 2s infinite; }
        .status-merah{ background:#e74c3c; animation:pulseRed 2s infinite; }
        .status-abu{ background:#7f8c8d; }

        .empty{
            padding:20px;
            text-align:center;
            color:#7f8c8d;
            font-style:italic;
            background:#f8f9fa;
            border-radius:12px;
        }
    </style>
</head>

<body>

<div class="bg-circle1"></div>
<div class="bg-circle2"></div>
<div class="bg-circle3"></div>

<div class="sidebar">
    <div class="logo">MachinaFlow</div>

    <div class="menu">
        <a href="dashboard_rizkia.php">Dashboard</a>
        <a href="jobs_rizkia.php">Jobs</a>
        <a href="sparepart_rizkia.php">Sparepart</a>
        <a href="scheduling_rizkia.php">Scheduling</a>
        <a href="mesin_rizkia.php">Mesin</a>
        <a href="users_rizkia.php">Users</a>
        <a href="laporan_rizkia.php">Laporan</a>
        <a href="monitoring_rizkia.php" class="active">Monitoring</a>
    </div>

    <a class="logout" href="../auth_rizkia/logout_rizkia.php">Logout</a>
</div>

<div class="header">Monitoring Produksi</div>

<div class="content">

<?php
$now_rizkia = date("Y-m-d H:i:s");

$total_mesin    = mysqli_fetch_assoc(mysqli_query($conn_rizkia,"SELECT COUNT(*) as total FROM mesin_rizkia"))['total'];
$aktif          = mysqli_fetch_assoc(mysqli_query($conn_rizkia,"SELECT COUNT(*) as total FROM scheduling_rizkia WHERE status_rizkia='Dijadwalkan'"))['total'];
$selesai        = mysqli_fetch_assoc(mysqli_query($conn_rizkia,"SELECT COUNT(*) as total FROM scheduling_rizkia WHERE status_rizkia='Selesai'"))['total'];
$operator_aktif = mysqli_fetch_assoc(mysqli_query($conn_rizkia,"SELECT COUNT(DISTINCT operator_id_rizkia) as total FROM scheduling_rizkia WHERE status_rizkia='Dijadwalkan'"))['total'];
?>

<div class="summary-grid">
    <div class="summary-box blue">
        <h4>Total Mesin</h4>
        <h2><?= $total_mesin ?></h2>
    </div>
    <div class="summary-box green">
        <h4>Produksi Aktif</h4>
        <h2><?= $aktif ?></h2>
    </div>
    <div class="summary-box orange">
        <h4>Operator Aktif</h4>
        <h2><?= $operator_aktif ?></h2>
    </div>
    <div class="summary-box red">
        <h4>Job Selesai</h4>
        <h2><?= $selesai ?></h2>
    </div>
</div>

<div class="card">
    <h3>Monitoring Produksi</h3>

    <?php
    $data_rizkia = mysqli_query($conn_rizkia,"
    SELECT s.*, j.nama_job_rizkia, m.nama_mesin_rizkia, u.username_rizkia
    FROM scheduling_rizkia s
    LEFT JOIN jobs_rizkia j ON s.job_id_rizkia = j.id_rizkia
    LEFT JOIN mesin_rizkia m ON s.mesin_id_rizkia = m.id_rizkia
    LEFT JOIN users_rizkia u ON s.operator_id_rizkia = u.id_rizkia
    ORDER BY s.waktu_mulai_rizkia ASC
    ");

    if(mysqli_num_rows($data_rizkia) == 0){
        echo "<div class='empty'>Tidak ada data monitoring saat ini.</div>";
    }else{
    ?>

    <table>
        <tr>
            <th>Job</th>
            <th>Mesin</th>
            <th>Operator</th>
            <th>Mulai</th>
            <th>Selesai</th>
            <th>Kondisi</th>
        </tr>

        <?php
        while($d = mysqli_fetch_array($data_rizkia)){

            $class = "status-abu";
            $kondisi = "IDLE";

            if($d['status_rizkia'] == 'Selesai'){
                $class = "status-merah";
                $kondisi = "SELESAI";
            }else{
                $selisih = (strtotime($d['waktu_mulai_rizkia']) - strtotime($now_rizkia)) / 60;

                if($selisih <= 60 && $selisih > 0){
                    $class = "status-kuning";
                    $kondisi = "AKAN MULAI";
                }elseif($now_rizkia >= $d['waktu_mulai_rizkia'] && $now_rizkia <= $d['waktu_selesai_rizkia']){
                    $class = "status-hijau";
                    $kondisi = "SEDANG BERJALAN";
                }
            }

            echo "<tr>
                <td>".$d['nama_job_rizkia']."</td>
                <td>".$d['nama_mesin_rizkia']."</td>
                <td>".$d['username_rizkia']."</td>
                <td>".$d['waktu_mulai_rizkia']."</td>
                <td>".$d['waktu_selesai_rizkia']."</td>
                <td><span class='badge $class'>$kondisi</span></td>
            </tr>";
        }
        ?>
    </table>

    <?php } ?>
</div>

</div>

</body>
</html>