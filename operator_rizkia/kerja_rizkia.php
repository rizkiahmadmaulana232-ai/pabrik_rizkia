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

function ada_kolom_scheduling($conn_rizkia, $nama_kolom){
    static $cache_kolom = [];
    if(isset($cache_kolom[$nama_kolom])){
        return $cache_kolom[$nama_kolom];
    }

    $nama_kolom_aman = mysqli_real_escape_string($conn_rizkia, $nama_kolom);
    $q = mysqli_query($conn_rizkia, "SHOW COLUMNS FROM scheduling_rizkia LIKE '$nama_kolom_aman'");
    $cache_kolom[$nama_kolom] = $q && mysqli_num_rows($q) > 0;
    return $cache_kolom[$nama_kolom];
}

function sinkron_status_job($conn_rizkia, $job_id){
    $job_id = (int)$job_id;
    if($job_id <= 0){
        return;
    }

    $stmt = mysqli_prepare($conn_rizkia, "SELECT COUNT(*) AS total, SUM(CASE WHEN status_rizkia='Selesai' THEN 1 ELSE 0 END) AS selesai FROM scheduling_rizkia WHERE job_id_rizkia=?");
    mysqli_stmt_bind_param($stmt, "i", $job_id);
    mysqli_stmt_execute($stmt);
    $r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    $total = (int)($r['total'] ?? 0);
    $selesai = (int)($r['selesai'] ?? 0);

    if($total > 0 && $selesai === $total){
        $status_job = 'Selesai';
    }elseif($total > 0){
        $status_job = 'Proses';
    }else{
        $status_job = 'Menunggu';
    }

    $stmt_update_job = mysqli_prepare($conn_rizkia, "UPDATE jobs_rizkia SET status_rizkia=? WHERE id_rizkia=?");
    mysqli_stmt_bind_param($stmt_update_job, "si", $status_job, $job_id);
    mysqli_stmt_execute($stmt_update_job);
}

/* ACTION */
if(isset($_POST['aksi_rizkia'])){
    $id = (int)$_POST['jadwal_id_rizkia'];
    $aksi = $_POST['aksi_rizkia'];
    $kendala = trim($_POST['kendala_rizkia'] ?? '');
    $qty_total = (int)($_POST['qty_total_rizkia'] ?? 0);
    $qty_reject = (int)($_POST['qty_reject_rizkia'] ?? 0);
    $qty_ok = max(0, $qty_total - $qty_reject);
    $job_id = 0;

    $stmt_job = mysqli_prepare($conn_rizkia, "SELECT job_id_rizkia FROM scheduling_rizkia WHERE id_rizkia=? AND operator_id_rizkia=? LIMIT 1");
    mysqli_stmt_bind_param($stmt_job, "ii", $id, $id_operator);
    mysqli_stmt_execute($stmt_job);
    $jadwal_ref = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_job));
    $job_id = (int)($jadwal_ref['job_id_rizkia'] ?? 0);

    if($aksi == 'mulai' && $job_id > 0){
        mysqli_query($conn_rizkia,"
        UPDATE scheduling_rizkia 
        SET status_rizkia='Berjalan',
            actual_mulai_rizkia=COALESCE(actual_mulai_rizkia,'$now')
        WHERE id_rizkia='$id' AND operator_id_rizkia='$id_operator'
        ");

        mysqli_query($conn_rizkia,"UPDATE jobs_rizkia SET status_rizkia='Proses' WHERE id_rizkia='$job_id'");
    }

    if($aksi == 'tunda' && $job_id > 0){
        mysqli_query($conn_rizkia,"
        UPDATE scheduling_rizkia 
        SET status_rizkia='Tertunda',
            kendala_rizkia='$kendala'
        WHERE id_rizkia='$id' AND operator_id_rizkia='$id_operator'
        ");

        mysqli_query($conn_rizkia,"UPDATE jobs_rizkia SET status_rizkia='Proses' WHERE id_rizkia='$job_id'");
    }

    if($aksi == 'selesai' && $job_id > 0){
        if($qty_total < 0){
            $qty_total = 0;
        }
        if($qty_reject < 0){
            $qty_reject = 0;
        }
        if($qty_reject > $qty_total){
            $qty_reject = $qty_total;
        }
        $qty_ok = $qty_total - $qty_reject;
        $catatan = "Total:$qty_total | OK:$qty_ok | Reject:$qty_reject | $kendala";

        mysqli_query($conn_rizkia,"
        UPDATE scheduling_rizkia 
        SET status_rizkia='Selesai',
            actual_selesai_rizkia='$now',
            waktu_selesai_rizkia='$now',
            qty_selesai_rizkia='$qty_ok',
            qty_reject_rizkia='$qty_reject',
            catatan_operator_rizkia='$catatan'
        WHERE id_rizkia='$id' AND operator_id_rizkia='$id_operator'
        ");

        sinkron_status_job($conn_rizkia, $job_id);
    }
}

/* DATA */
$data = mysqli_query($conn_rizkia,"
SELECT s.*, j.nama_job_rizkia, j.jumlah_rizkia, m.nama_mesin_rizkia
FROM scheduling_rizkia s
LEFT JOIN jobs_rizkia j ON s.job_id_rizkia=j.id_rizkia
LEFT JOIN mesin_rizkia m ON s.mesin_id_rizkia=m.id_rizkia
WHERE s.operator_id_rizkia='$id_operator'
ORDER BY FIELD(s.status_rizkia,'Berjalan','Tertunda','Dijadwalkan','Selesai'), s.waktu_mulai_rizkia ASC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Work Center Operator - MachinaFlow</title>

<style>
/* CSS TETAP - TIDAK DIUBAH SAMA SEKALI */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    background:linear-gradient(135deg,#eef2f7,#dfe9f3);
    color:#2c3e50;
}

@keyframes fadeUp{
    from{opacity:0; transform:translateY(18px);}
    to{opacity:1; transform:translateY(0);}
}

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
    font-size:14px;
    font-weight:bold;
    transition:.3s;
}

.sidebar a:hover{
    background:rgba(255,255,255,.08);
    transform:translateX(4px);
}

.logout{
    margin:12px;
    background:#e74c3c;
    text-align:center;
    padding:12px;
    border-radius:10px;
    font-weight:bold;
    color:white;
}

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
}

.content{
    margin-left:220px;
    padding:25px;
}

.card{
    background:rgba(255,255,255,.96);
    border-radius:18px;
    padding:20px;
    margin-bottom:20px;
    box-shadow:0 8px 24px rgba(0,0,0,.08);
    animation:fadeUp .4s ease;
}

.status{
    padding:6px 10px;
    border-radius:999px;
    color:white;
    font-size:12px;
}

.Dijadwalkan{background:#f39c12;}
.Berjalan{background:#27ae60;}
.Tertunda{background:#e67e22;}
.Selesai{background:#3498db;}

input,textarea{
    width:100%;
    padding:8px;
    margin-top:6px;
    margin-bottom:10px;
    border-radius:8px;
    border:1px solid #ccc;
}

.btn{
    padding:8px 12px;
    border:none;
    border-radius:8px;
    color:white;
    cursor:pointer;
}

.mulai{background:#2980b9;}
.tunda{background:#f39c12;}
.selesai{background:#27ae60;}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:10px;
    margin-top:10px;
}
</style>
</head>

<body>

<div class="sidebar">
    <div class="brand">MACHINAFLOW</div>
    <div class="menu">
        <a href="dashboard_rizkia.php">Dashboard</a>
        <a href="kerja_rizkia.php">Work Center</a>
    </div>
    <a class="logout" href="../auth_rizkia/logout_rizkia.php">Logout</a>
</div>

<div class="header">
    <h2>Work Center Operator</h2>
    <span>Halo, <?= $nama_operator ?></span>
</div>

<div class="content">

<?php while($d = mysqli_fetch_assoc($data)){ ?>

<div class="card">

    <div style="display:flex;justify-content:space-between;">
        <h3><?= $d['nama_job_rizkia'] ?> - <?= $d['nama_mesin_rizkia'] ?></h3>
        <span class="status <?= $d['status_rizkia'] ?>"><?= $d['status_rizkia'] ?></span>
    </div>

    <div class="grid">
        <div><b>Mulai</b><br><?= $d['waktu_mulai_rizkia'] ?></div>
        <div><b>Actual Mulai</b><br><?= ($d['actual_mulai_rizkia'] ?? '') ?: '-' ?></div>
        <div><b>Target Produksi</b><br><?= (int)($d['jumlah_rizkia'] ?? 0) ?></div>
        <div><b>Selesai</b><br>
            <?= ($d['waktu_selesai_rizkia'] ?? '') ?: (($d['actual_selesai_rizkia'] ?? '') ?: '-') ?>
        </div>
    </div>

    <?php if($d['status_rizkia'] != 'Selesai'){ ?>

    <form method="POST">

        <input type="hidden" name="jadwal_id_rizkia" value="<?= $d['id_rizkia'] ?>">

        <label>Total Produksi</label>
        <input type="number" name="qty_total_rizkia" min="0" placeholder="Total unit yang diproduksi">

        <label>Qty Reject</label>
        <input type="number" name="qty_reject_rizkia" min="0" placeholder="Unit cacat / ditolak">

        <label>Kendala</label>
        <textarea name="kendala_rizkia"></textarea>

        <div style="display:flex;gap:8px;flex-wrap:wrap">

            <?php if($d['status_rizkia']=='Dijadwalkan'){ ?>
            <button class="btn mulai" name="aksi_rizkia" value="mulai">Mulai</button>
            <?php } ?>

            <?php if($d['status_rizkia']=='Berjalan'){ ?>
            <button class="btn tunda" name="aksi_rizkia" value="tunda">Tunda</button>
            <?php } ?>

            <button class="btn selesai" name="aksi_rizkia" value="selesai">Selesai</button>

        </div>

    </form>

    <?php } else { ?>

        <p><b>Hasil:</b> OK <?= $d['qty_selesai_rizkia'] ?? 0 ?> | Reject <?= $d['qty_reject_rizkia'] ?? 0 ?></p>
        <p><b>Catatan:</b> <?= $d['catatan_operator_rizkia'] ?? '-' ?></p>

    <?php } ?>

</div>

<?php } ?>

</div>

</body>
</html>
