<?php

include '../config_rizkia/koneksi_rizkia.php';
session_start();

/* ROLE CHECK */
if(!isset($_SESSION['user_rizkia'])){
    header("Location: ../auth_rizkia/login_rizkia.php");
    exit;
}

/* SIMPAN LAPORAN */
if(isset($_POST['simpan_rizkia'])){
    $isi = mysqli_real_escape_string($conn_rizkia, $_POST['isi']);

    mysqli_query($conn_rizkia,"INSERT INTO laporan_rizkia 
    VALUES(NULL,'$isi',CURDATE())");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan - MachinaFlow</title>
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
        textarea{
            width:100%;
            min-height:140px;
            resize:vertical;
            padding:14px;
            border:1px solid #dcdde1;
            border-radius:12px;
            font-size:14px;
            margin-bottom:14px;
            transition:0.3s;
        }

        textarea:focus{
            outline:none;
            border-color:#2980b9;
            box-shadow:0 0 0 3px rgba(41,128,185,0.15);
        }

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
            padding:14px;
            border-bottom:1px solid #ecf0f1;
            font-size:14px;
            vertical-align:top;
        }

        td:first-child{
            text-align:left;
            line-height:1.6;
        }

        td:last-child{
            text-align:center;
            white-space:nowrap;
        }

        tr:hover td{
            background:#f8fafc;
        }

        /* DATE BADGE */
        .date-badge{
            display:inline-block;
            padding:6px 12px;
            border-radius:20px;
            background:#eaf2ff;
            color:#2563eb;
            font-size:12px;
            font-weight:bold;
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
        <a href="mesin_rizkia.php">Mesin</a>
        <a href="users_rizkia.php">Users</a>
        <a href="laporan_rizkia.php" class="active">Laporan</a>
        <a href="monitoring_rizkia.php">Monitoring</a>
    </div>

    <a class="logout" href="../auth_rizkia/logout_rizkia.php">Logout</a>
</div>

<div class="header">
    Production Reports
</div>

<div class="content">

    <div class="card">
        <h3>Buat Laporan</h3>

        <form method="POST">
            <textarea name="isi" placeholder="Tulis laporan produksi hari ini..." required></textarea>
            <button name="simpan_rizkia">Simpan Laporan</button>
        </form>
    </div>

    <div class="card">
        <h3>Data Laporan</h3>

        <div class="table-wrap">
            <table>
                <tr>
                    <th>Isi Laporan</th>
                    <th>Tanggal</th>
                </tr>

                <?php
                $data = mysqli_query($conn_rizkia,"SELECT * FROM laporan_rizkia ORDER BY id_rizkia DESC");
                while($d=mysqli_fetch_array($data)){
                ?>
                <tr>
                    <td><?= $d['isi_rizkia'] ?></td>
                    <td><span class="date-badge"><?= $d['tanggal_rizkia'] ?></span></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

</div>

</body>
</html>