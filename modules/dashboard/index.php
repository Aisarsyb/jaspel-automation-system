<?php
declare(strict_types=1);

require_once '../../app/config/config.php';

// Auth Check
Session::requireLogin();

// Fetch summary metrics
$db = Database::getConnection();

// 1. Total Dokter
$stmtDocs = $db->query("SELECT COUNT(*) FROM dpjp");
$totalDoctors = (int)$stmtDocs->fetchColumn();

// 2. Total Departemen
$stmtDepts = $db->query("SELECT COUNT(*) FROM departments");
$totalDepts = (int)$stmtDepts->fetchColumn();

// 3. Total Import Hari Ini
$stmtImportsToday = $db->query("SELECT COUNT(*) FROM import_history WHERE DATE(created_at) = CURRENT_DATE");
$totalImportsToday = (int)$stmtImportsToday->fetchColumn();

// 4. Total Jaspel Terproses (All Time)
$stmtTotalJaspel = $db->query("SELECT SUM(total_jaspel) FROM import_history");
$totalJaspelVal = (float)$stmtTotalJaspel->fetchColumn();

// 5. Fetch Recent Import History
$stmtRecent = $db->query("SELECT h.*, u.username FROM import_history h JOIN users u ON h.imported_by = u.id ORDER BY h.created_at DESC LIMIT 5");
$recentImports = $stmtRecent->fetchAll();

// 6. Find Unregistered Doctors from history errors (that are STILL not in dpjp or dpjp_aliases)
$stmtUnregistered = $db->query("
    SELECT DISTINCT e.doctor_name 
    FROM import_errors e
    WHERE e.doctor_name IS NOT NULL AND e.doctor_name != ''
      AND UPPER(e.doctor_name) NOT IN (
          SELECT UPPER(doctor_name) FROM dpjp
      )
      AND UPPER(e.doctor_name) NOT IN (
          SELECT UPPER(alias_name) FROM dpjp_aliases
      )
");
$unregisteredDoctors = $stmtUnregistered->fetchAll(PDO::FETCH_COLUMN);
$totalUnregistered = count($unregisteredDoctors);

Layout::start('Dashboard', 'dashboard');
?>

<!-- Warning Banner for Unregistered Doctors -->
<?php if ($totalUnregistered > 0): ?>
    <div class="alert-banner alert-banner-warning">
        <div class="alert-banner-icon">⚠️</div>
        <div class="alert-banner-content">
            <h4><?php echo $totalUnregistered; ?> DPJP / Dokter Belum Terdaftar</h4>
            <p>
                Ditemukan dokter dari file impor sebelumnya yang belum terpetakan ke Departemen: 
                <strong><?php echo implode(', ', array_map('Helper::sanitize', array_slice($unregisteredDoctors, 0, 5))); ?><?php if ($totalUnregistered > 5) echo '...'; ?></strong>.
                Sistem tidak dapat mengelompokkan data mereka secara otomatis. 
                <a href="../master-dpjp/index.php" style="text-decoration: underline; font-weight: 600;">Daftarkan di Master DPJP sekarang</a>.
            </p>
        </div>
    </div>
<?php endif; ?>


<!-- Stats Grid -->
<div class="stats-grid">
    <div class="card">
        <div class="card-title">Total Dokter (DPJP)</div>
        <div class="card-value"><?php echo $totalDoctors; ?></div>
        <div class="card-info">
            <span style="font-weight: 500;">Terdaftar di database</span>
        </div>
    </div>
    
    <div class="card">
        <div class="card-title">Total Departemen</div>
        <div class="card-value"><?php echo $totalDepts; ?></div>
        <div class="card-info">
            <span style="font-weight: 500;">Unit Spesialisasi</span>
        </div>
    </div>
    
    <div class="card">
        <div class="card-title">Impor Hari Ini</div>
        <div class="card-value"><?php echo $totalImportsToday; ?></div>
        <div class="card-info">
            <span style="font-weight: 500;">File Excel terproses</span>
        </div>
    </div>
    
    <div class="card">
        <div class="card-title">Total Jaspel Terproses</div>
        <div class="card-value" style="font-size: 22px; line-height: 1.4; padding-top: 5px;">
            <?php echo Helper::formatRupiah($totalJaspelVal); ?>
        </div>
        <div class="card-info success">
            <span style="font-weight: 600;">Total akumulasi</span>
        </div>
    </div>
</div>

<!-- Main Section split -->
<div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 32px;">
    <!-- Left Column: Quick Actions -->
    <div>
        <h3 style="font-weight: 600; font-size: 16px; margin-bottom: 16px; color: var(--text-secondary);">Aksi Cepat</h3>
        <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
            <a href="../import/index.php" class="card" style="padding: 20px; display: flex; align-items: center; gap: 16px;">
                <div style="width: 48px; height: 48px; background-color: var(--primary-light); color: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    📥
                </div>
                <div>
                    <h4 style="font-weight: 600; font-size: 15px;">Import Excel Baru</h4>
                    <p style="font-size: 12.5px; color: var(--text-muted);">Proses file Excel mentah jasa pelayanan</p>
                </div>
            </a>
            
            <a href="../master-dpjp/index.php" class="card" style="padding: 20px; display: flex; align-items: center; gap: 16px;">
                <div style="width: 48px; height: 48px; background-color: var(--success-light); color: var(--success); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    👨‍⚕️
                </div>
                <div>
                    <h4 style="font-weight: 600; font-size: 15px;">Kelola Dokter (DPJP)</h4>
                    <p style="font-size: 12.5px; color: var(--text-muted);">Tambah dokter baru atau edit pemetaan</p>
                </div>
            </a>
            
            <a href="../departments/index.php" class="card" style="padding: 20px; display: flex; align-items: center; gap: 16px;">
                <div style="width: 48px; height: 48px; background-color: var(--warning-light); color: var(--warning); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    🏢
                </div>
                <div>
                    <h4 style="font-weight: 600; font-size: 15px;">Kelola Departemen</h4>
                    <p style="font-size: 12.5px; color: var(--text-muted);">Atur departemen dan status aktifnya</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Right Column: Recent Activity -->
    <div>
        <h3 style="font-weight: 600; font-size: 16px; margin-bottom: 16px; color: var(--text-secondary);">Riwayat Import Terakhir</h3>
        <div class="card" style="padding: 0; overflow: hidden;">
            <?php if (count($recentImports) === 0): ?>
                <div style="padding: 40px; text-align: center; color: var(--text-muted);">
                    <div style="font-size: 32px; margin-bottom: 8px;">📂</div>
                    <p style="font-size: 14px;">Belum ada riwayat import file.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive" style="border: none; margin-bottom: 0; border-radius: 0;">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama File</th>
                                <th>Total Jaspel</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentImports as $import): ?>
                                <tr>
                                    <td style="font-size: 13px;">
                                        <?php echo date('d M Y H:i', strtotime($import['created_at'])); ?>
                                    </td>
                                    <td style="font-weight: 500; font-size: 13.5px; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?php echo Helper::sanitize($import['file_name']); ?>
                                    </td>
                                    <td style="font-size: 13px; font-weight: 600;">
                                        <?php echo Helper::formatRupiah((float)$import['total_jaspel']); ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">Selesai</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="padding: 14px; text-align: center; border-top: 1px solid var(--border-color); background-color: #f9fafb;">
                    <a href="../history/index.php" style="font-size: 13px; font-weight: 600; color: var(--primary);">Lihat Semua Riwayat ➔</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
Layout::end();
?>
