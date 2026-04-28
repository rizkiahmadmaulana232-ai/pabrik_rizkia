<?php

include '../config_rizkia/koneksi_rizkia.php';
session_start();
include '../config_rizkia/security_rizkia.php';
date_default_timezone_set('Asia/Jakarta');

if(!isset($_SESSION['user_rizkia']) || $_SESSION['user_rizkia']['role_rizkia'] !== 'operator'){
    header('Location: ../auth_rizkia/login_rizkia.php');
    exit;
}

$id_operator_rizkia = (int)$_SESSION['user_rizkia']['id_rizkia'];

mysqli_query($conn_rizkia, "ALTER TABLE scheduling_rizkia ADD COLUMN IF NOT EXISTS actual_mulai_rizkia DATETIME NULL");
mysqli_query($conn_rizkia, "ALTER TABLE scheduling_rizkia ADD COLUMN IF NOT EXISTS actual_selesai_rizkia DATETIME NULL");
mysqli_query($conn_rizkia, "ALTER TABLE scheduling_rizkia ADD COLUMN IF NOT EXISTS qty_selesai_rizkia INT NOT NULL DEFAULT 0");
mysqli_query($conn_rizkia, "ALTER TABLE scheduling_rizkia ADD COLUMN IF NOT EXISTS qty_reject_rizkia INT NOT NULL DEFAULT 0");
mysqli_query($conn_rizkia, "ALTER TABLE scheduling_rizkia ADD COLUMN IF NOT EXISTS catatan_operator_rizkia TEXT NULL");

function refresh_status_job_rizkia($conn_rizkia, $job_id_rizkia){
    $job_id_rizkia = (int)$job_id_rizkia;
    if($job_id_rizkia <= 0){
        return;
    }

    $total_stmt = mysqli_prepare($conn_rizkia, "SELECT COUNT(*) AS total, SUM(CASE WHEN status_rizkia='Selesai' THEN 1 ELSE 0 END) AS selesai, SUM(CASE WHEN status_rizkia='Berjalan' THEN 1 ELSE 0 END) AS berjalan, SUM(CASE WHEN status_rizkia='Tertunda' THEN 1 ELSE 0 END) AS tertunda FROM scheduling_rizkia WHERE job_id_rizkia=?");
    mysqli_stmt_bind_param($total_stmt, 'i', $job_id_rizkia);
    mysqli_stmt_execute($total_stmt);
    $agg = mysqli_fetch_assoc(mysqli_stmt_get_result($total_stmt));

    if(!$agg || (int)$agg['total'] === 0){
        mysqli_query($conn_rizkia, "UPDATE jobs_rizkia SET status_rizkia='Menunggu' WHERE id_rizkia='{$job_id_rizkia}'");
        return;
    }

    $status = 'Dijadwalkan';
    if((int)$agg['selesai'] === (int)$agg['total']){
        $status = 'Selesai';
    } elseif((int)$agg['berjalan'] > 0){
        $status = 'Produksi';
    } elseif((int)$agg['tertunda'] > 0){
        $status = 'Tertunda';
    }

    $stmt_update = mysqli_prepare($conn_rizkia, 'UPDATE jobs_rizkia SET status_rizkia=? WHERE id_rizkia=?');
    mysqli_stmt_bind_param($stmt_update, 'si', $status, $job_id_rizkia);
    mysqli_stmt_execute($stmt_update);
}

$flash_rizkia = '';

if(isset($_POST['aksi_rizkia']) && csrf_validate_rizkia($_POST['csrf_token_rizkia'] ?? '')){
    $jadwal_id = (int)($_POST['jadwal_id_rizkia'] ?? 0);
    $aksi = $_POST['aksi_rizkia'] ?? '';
    $kendala = trim($_POST['kendala_rizkia'] ?? '');
    $qty_selesai = max(0, (int)($_POST['qty_selesai_rizkia'] ?? 0));
    $qty_reject = max(0, (int)($_POST['qty_reject_rizkia'] ?? 0));

    $stmt_cek = mysqli_prepare($conn_rizkia, 'SELECT id_rizkia, job_id_rizkia, status_rizkia FROM scheduling_rizkia WHERE id_rizkia=? AND operator_id_rizkia=? LIMIT 1');
    mysqli_stmt_bind_param($stmt_cek, 'ii', $jadwal_id, $id_operator_rizkia);
    mysqli_stmt_execute($stmt_cek);
    $jadwal = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_cek));

    if($jadwal){
        $now = date('Y-m-d H:i:s');

        if($aksi === 'mulai'){
            $stmt = mysqli_prepare($conn_rizkia, "UPDATE scheduling_rizkia SET status_rizkia='Berjalan', actual_mulai_rizkia=COALESCE(actual_mulai_rizkia, ?), kendala_rizkia=NULL WHERE id_rizkia=? AND status_rizkia IN ('Dijadwalkan','Tertunda')");
            mysqli_stmt_bind_param($stmt, 'si', $now, $jadwal_id);
            mysqli_stmt_execute($stmt);
            $flash_rizkia = 'Proses produksi berhasil dimulai.';
        }

        if($aksi === 'tunda'){
            $stmt = mysqli_prepare($conn_rizkia, "UPDATE scheduling_rizkia SET status_rizkia='Tertunda', kendala_rizkia=? WHERE id_rizkia=? AND status_rizkia='Berjalan'");
            mysqli_stmt_bind_param($stmt, 'si', $kendala, $jadwal_id);
            mysqli_stmt_execute($stmt);
            $flash_rizkia = 'Pekerjaan berhasil ditandai tertunda.';
        }

        if($aksi === 'selesai'){
            $catatan = trim(($kendala !== '' ? ('Kendala: '.$kendala.' | ') : '') . 'Input operator');
            $stmt = mysqli_prepare($conn_rizkia, "UPDATE scheduling_rizkia SET status_rizkia='Selesai', actual_selesai_rizkia=?, waktu_selesai_rizkia=?, qty_selesai_rizkia=?, qty_reject_rizkia=?, catatan_operator_rizkia=?, notif_rizkia=0 WHERE id_rizkia=? AND status_rizkia IN ('Berjalan','Tertunda','Dijadwalkan')");
            mysqli_stmt_bind_param($stmt, 'ssiisi', $now, $now, $qty_selesai, $qty_reject, $catatan, $jadwal_id);
            mysqli_stmt_execute($stmt);
            $flash_rizkia = 'Produksi selesai dan hasil telah tersimpan.';
        }

        refresh_status_job_rizkia($conn_rizkia, (int)$jadwal['job_id_rizkia']);
    }
}

$data_rizkia = mysqli_query($conn_rizkia, "SELECT s.*, j.nama_job_rizkia, j.jumlah_rizkia, m.nama_mesin_rizkia
FROM scheduling_rizkia s
JOIN jobs_rizkia j ON s.job_id_rizkia=j.id_rizkia
JOIN mesin_rizkia m ON s.mesin_id_rizkia=m.id_rizkia
WHERE s.operator_id_rizkia='{$id_operator_rizkia}'
ORDER BY FIELD(s.status_rizkia,'Berjalan','Tertunda','Dijadwalkan','Selesai'), s.waktu_mulai_rizkia ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Eksekusi Produksi Operator</title>
    <style>
        body{font-family:Arial,sans-serif;background:#eef2f7;margin:0;color:#2c3e50}
        .wrap{max-width:1100px;margin:24px auto;padding:0 16px}
        .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
        .btn{border:0;padding:9px 12px;border-radius:8px;color:#fff;cursor:pointer;font-weight:700}
        .mulai{background:#2980b9}.tunda{background:#f39c12}.selesai{background:#27ae60}
        .card{background:#fff;border-radius:14px;padding:16px;margin-bottom:14px;box-shadow:0 8px 16px rgba(0,0,0,.06)}
        .status{padding:5px 10px;border-radius:999px;font-size:12px;font-weight:700}
        .Dijadwalkan{background:#d6eaf8;color:#1b4f72}.Berjalan{background:#d5f5e3;color:#145a32}.Tertunda{background:#fdebd0;color:#7e5109}.Selesai{background:#e8f8f5;color:#0e6655}
        .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:12px}
        input,textarea{width:100%;padding:8px;border:1px solid #ccd3da;border-radius:8px;margin-top:8px;margin-bottom:8px}
        .flash{background:#eafaf1;color:#1e8449;padding:10px 12px;border-radius:8px;margin-bottom:14px}
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <h2>Work Center Operator</h2>
        <div>
            <a href="dashboard_rizkia.php">Dashboard</a> |
            <a href="../auth_rizkia/logout_rizkia.php">Logout</a>
        </div>
    </div>

    <?php if($flash_rizkia){ ?><div class="flash"><?= htmlspecialchars($flash_rizkia) ?></div><?php } ?>

    <div id="riwayat"></div>
    <?php while($d=mysqli_fetch_assoc($data_rizkia)){ ?>
    <div class="card">
        <div style="display:flex;justify-content:space-between;gap:8px;align-items:center;flex-wrap:wrap;">
            <h3 style="margin:0"><?= htmlspecialchars($d['nama_job_rizkia']) ?> - <?= htmlspecialchars($d['nama_mesin_rizkia']) ?></h3>
            <span class="status <?= htmlspecialchars($d['status_rizkia']) ?>"><?= htmlspecialchars($d['status_rizkia']) ?></span>
        </div>
        <div class="grid">
            <div><strong>Rencana Mulai</strong><br><?= htmlspecialchars($d['waktu_mulai_rizkia']) ?></div>
            <div><strong>Target Qty</strong><br><?= (int)$d['jumlah_rizkia'] ?></div>
            <div><strong>Actual Mulai</strong><br><?= htmlspecialchars($d['actual_mulai_rizkia'] ?: '-') ?></div>
            <div><strong>Actual Selesai</strong><br><?= htmlspecialchars($d['actual_selesai_rizkia'] ?: '-') ?></div>
        </div>

        <?php if($d['status_rizkia'] !== 'Selesai'){ ?>
        <form method="POST"> 
            <?= csrf_input_rizkia(); ?>
            <input type="hidden" name="jadwal_id_rizkia" value="<?= (int)$d['id_rizkia'] ?>"> 
            <label>Qty Selesai</label>
            <input type="number" name="qty_selesai_rizkia" min="0" value="<?= (int)$d['qty_selesai_rizkia'] ?>"> 
            <label>Qty Reject</label>
            <input type="number" name="qty_reject_rizkia" min="0" value="<?= (int)$d['qty_reject_rizkia'] ?>"> 
            <label>Kendala / Catatan</label>
            <textarea name="kendala_rizkia" rows="2" placeholder="Isi jika ada kendala atau catatan proses..."><?= htmlspecialchars($d['kendala_rizkia'] ?? '') ?></textarea>
            <div style="display:flex;gap:8px;flex-wrap:wrap"> 
                <?php if(in_array($d['status_rizkia'], ['Dijadwalkan','Tertunda'], true)){ ?><button class="btn mulai" name="aksi_rizkia" value="mulai">Mulai</button><?php } ?>
                <?php if($d['status_rizkia'] === 'Berjalan'){ ?><button class="btn tunda" name="aksi_rizkia" value="tunda">Tunda</button><?php } ?>
                <button class="btn selesai" name="aksi_rizkia" value="selesai">Selesai</button>
            </div>
        </form>
        <?php } ?>
        <?php if($d['status_rizkia'] === 'Selesai'){ ?>
            <p><strong>Hasil:</strong> OK <?= (int)$d['qty_selesai_rizkia'] ?> | Reject <?= (int)$d['qty_reject_rizkia'] ?></p>
            <p><strong>Catatan:</strong> <?= htmlspecialchars($d['catatan_operator_rizkia'] ?: '-') ?></p>
        <?php } ?>
    </div>
    <?php } ?>
</div>
</body>
</html>
