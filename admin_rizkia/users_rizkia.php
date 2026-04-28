<?php

include '../config_rizkia/koneksi_rizkia.php';
session_start();

/* ROLE CHECK ADMIN */
if(!isset($_SESSION['user_rizkia']['role_rizkia']) || $_SESSION['user_rizkia']['role_rizkia'] != 'admin'){
    die("Akses ditolak. Hanya Admin.");
}

/* HAPUS USER */
if(isset($_POST['hapus_rizkia'])){
    mysqli_query($conn_rizkia,"DELETE FROM users_rizkia 
    WHERE id_rizkia='$_POST[id]'");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Users - MachinaFlow</title>
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
            box-shadow:0 8px 20px rgba(0,0,0,0.06);
            animation:fadeIn 0.5s ease;
        }

        .card h3{
            margin-bottom:18px;
            font-size:20px;
            color:#2c3e50;
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

        /* ROLE BADGE */
        .badge{
            padding:6px 12px;
            border-radius:20px;
            font-size:12px;
            font-weight:bold;
            display:inline-block;
            text-transform:capitalize;
        }

        .badge.admin{
            background:#eaf2ff;
            color:#2563eb;
        }

        .badge.operator{
            background:#eafaf1;
            color:#27ae60;
        }

        .badge.engineering{
            background:#fff6e5;
            color:#f39c12;
        }

        /* BUTTON */
        .btn-hapus{
            padding:9px 14px;
            border:none;
            border-radius:10px;
            background:#e74c3c;
            color:white;
            font-size:13px;
            font-weight:bold;
            cursor:pointer;
            transition:0.3s;
        }

        .btn-hapus:hover{
            background:#c0392b;
            transform:translateY(-1px);
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
        <a href="users_rizkia.php" class="active">Users</a>
        <a href="laporan_rizkia.php">Laporan</a>
        <a href="monitoring_rizkia.php">Monitoring</a>
    </div>

    <a class="logout" href="../auth_rizkia/logout_rizkia.php">Logout</a>
</div>

<div class="header">
    User Management
</div>

<div class="content">

    <div class="card">
        <h3>Data Users</h3>

        <div class="table-wrap">
            <table>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>

                <?php
                $data = mysqli_query($conn_rizkia,"SELECT * FROM users_rizkia");
                while($d=mysqli_fetch_array($data)){
                ?>
                <tr>
                    <td><?= $d['username_rizkia'] ?></td>
                    <td>
                        <span class="badge <?= strtolower($d['role_rizkia']) ?>">
                            <?= $d['role_rizkia'] ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                            <input type="hidden" name="id" value="<?= $d['id_rizkia'] ?>">
                            <button class="btn-hapus" name="hapus_rizkia">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

</div>

</body>
</html>