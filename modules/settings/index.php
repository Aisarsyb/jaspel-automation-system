<?php
declare(strict_types=1);

require_once '../../app/config/config.php';
Layout::start('Settings & System Health', 'settings');
?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 32px;">
    <!-- Left Column: Settings Form & Backup/Restore -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Config Card -->
        <div class="card">
            <h3 style="font-weight: 600; font-size: 16px; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Konfigurasi Sistem</h3>
            <form id="settings-form" onsubmit="submitSettings(event)">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="set-jaspel" class="form-label">Jaspel Umum / DPJP (%)</label>
                        <input type="number" id="set-jaspel" name="JASPEL_PERCENTAGE" class="form-control" min="0" max="100" step="0.1" value="<?php echo JASPEL_PERCENTAGE; ?>" required>
                        <small style="font-size: 11px; color: var(--text-muted);">Standard Jaspel DPJP (e.g. 20%)</small>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="set-rkg-jaspel" class="form-label">Jaspel Radiologi / RKG (%)</label>
                        <input type="number" id="set-rkg-jaspel" name="RKG_JASPEL_PERCENTAGE" class="form-control" min="0" max="100" step="0.1" value="<?php echo RKG_JASPEL_PERCENTAGE; ?>" required>
                        <small style="font-size: 11px; color: var(--text-muted);">Khusus Radiologi (e.g. 15%)</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="set-appname" class="form-label">Nama Aplikasi</label>
                    <input type="text" id="set-appname" name="APP_NAME" class="form-control" value="<?php echo Helper::sanitize(APP_NAME); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="set-company" class="form-label">Nama Unit / Instansi</label>
                    <input type="text" id="set-company" name="COMPANY" class="form-control" value="<?php echo Helper::sanitize(COMPANY_NAME); ?>" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label for="set-maxsize" class="form-label">Batas Upload (MB)</label>
                        <input type="number" id="set-maxsize" name="MAX_UPLOAD_SIZE" class="form-control" value="<?php echo MAX_UPLOAD_SIZE; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="set-ext" class="form-label">Ekstensi File</label>
                        <input type="text" id="set-ext" name="ALLOWED_EXTENSION" class="form-control" value="<?php echo Helper::sanitize(ALLOWED_EXTENSION); ?>" readonly>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 8px;">Simpan Pengaturan</button>
            </form>
        </div>

        <!-- Backup & Restore Card -->
        <div class="card">
            <h3 style="font-weight: 600; font-size: 16px; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Backup & Restore Database</h3>
            
            <div style="margin-bottom: 24px;">
                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 6px;">Pencadangan Database (Backup)</h4>
                <p style="font-size: 12.5px; color: var(--text-secondary); margin-bottom: 12px;">Unduh salinan cadangan SQL yang mencakup skema struktur tabel beserta seluruh data di dalamnya.</p>
                <a href="api.php?action=backup_db" class="btn btn-secondary">
                    📥 Download Backup (.sql)
                </a>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border-color); margin-bottom: 20px;">

            <div>
                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 6px;">Pemulihan Database (Restore)</h4>
                <p style="font-size: 12.5px; color: var(--text-secondary); margin-bottom: 12px; color: var(--danger);">Peringatan: Restorasi database akan menimpa seluruh data saat ini dengan data dari file backup SQL yang diunggah.</p>
                <form id="restore-form" onsubmit="submitRestore(event)">
                    <div class="form-group">
                        <input type="file" id="restore-file" name="restore_file" class="form-control" accept=".sql" required>
                    </div>
                    <button type="submit" class="btn btn-danger">🔥 Mulai Restore Database</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Health Check & System Status -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Health Check Card -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                <h3 style="font-weight: 600; font-size: 16px; margin: 0;">Status Kesehatan Sistem</h3>
                <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 12.5px;" onclick="runHealthCheck()">Periksa Ulang</button>
            </div>

            <div class="health-grid" id="health-check-results">
                <!-- Loaded via JS -->
            </div>
        </div>
    </div>
</div>

<!-- Logs Tabs section -->
<div class="card" style="padding: 24px;">
    <h3 style="font-weight: 600; font-size: 16px; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Log Catatan Sistem</h3>
    
    <!-- Tab Headers -->
    <div style="display: flex; gap: 16px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color);">
        <button id="tab-btn-audit" class="btn" style="background: none; border: none; border-bottom: 3px solid var(--primary); border-radius: 0; font-weight: 600; color: var(--primary); padding: 10px 16px; cursor: pointer;" onclick="switchLogTab('audit')">
            Audit Logs (Aktivitas Admin)
        </button>
        <button id="tab-btn-system" class="btn" style="background: none; border: none; border-bottom: 3px solid transparent; border-radius: 0; font-weight: 500; color: var(--text-secondary); padding: 10px 16px; cursor: pointer;" onclick="switchLogTab('system')">
            System Logs (Error Internal)
        </button>
    </div>

    <!-- Tab Contents -->
    <div id="tab-content-audit" class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 160px;">Waktu</th>
                    <th style="width: 100px;">User</th>
                    <th style="width: 180px;">Aktivitas</th>
                    <th>Detail Aktivitas</th>
                    <th style="width: 120px;">IP Address</th>
                </tr>
            </thead>
            <tbody id="audit-log-body">
                <!-- JS Loaded -->
            </tbody>
        </table>
    </div>

    <div id="tab-content-system" class="table-responsive" style="max-height: 400px; overflow-y: auto; display: none;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 160px;">Waktu</th>
                    <th style="width: 100px; text-align: center;">Tingkat</th>
                    <th>Pesan Error</th>
                    <th>Stack Trace</th>
                </tr>
            </thead>
            <tbody id="system-log-body">
                <!-- JS Loaded -->
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        runHealthCheck();
        loadLogs();
    });

    async function runHealthCheck() {
        const container = document.getElementById('health-check-results');
        container.innerHTML = '<p style="color: var(--text-muted); font-size: 13.5px; padding: 20px; text-align: center;">Mengecek kondisi server...</p>';

        try {
            const res = await fetchAPI('api.php?action=health_check');
            if (res.success) {
                let html = '';
                
                // Render Database Check
                html += renderHealthItem(
                    'Koneksi Database',
                    'Menghubungkan aplikasi ke server MariaDB MySQL',
                    res.checks.database.status,
                    res.checks.database.message
                );

                // Render Storage Check
                html += renderHealthItem(
                    'Izin Folder Storage',
                    'Memastikan folder storage/ terdaftar dan dapat ditulisi',
                    res.checks.storage.status,
                    res.checks.storage.message
                );

                // Render PHP Extensions Check
                html += renderHealthItem(
                    'Ekstensi PHP Terpasang',
                    'Memeriksa ketersediaan ekstensi pdo, zip, mbstring, gd, xml',
                    res.checks.php_extensions.status,
                    res.checks.php_extensions.message
                );

                // Render PhpSpreadsheet Check
                html += renderHealthItem(
                    'Library PhpSpreadsheet',
                    'Memastikan PhpSpreadsheet aktif di direktori vendor/',
                    res.checks.phpspreadsheet.status,
                    res.checks.phpspreadsheet.message
                );

                container.innerHTML = html;
            }
        } catch (err) {
            container.innerHTML = '<p style="color: var(--danger); font-size: 13.5px; padding: 20px; text-align: center;">Gagal menjalankan Health Check.</p>';
        }
    }

    function renderHealthItem(title, desc, status, message) {
        const badgeColor = status ? 'var(--success)' : 'var(--danger)';
        const badgeText = status ? '🟢 OK' : '🔴 ERROR';
        
        return `
            <div class="health-item">
                <div class="health-item-info">
                    <span class="health-item-title">${title}</span>
                    <span class="health-item-desc">${desc}</span>
                    <span style="font-size: 12px; margin-top: 4px; color: ${status ? 'var(--text-secondary)' : 'var(--danger)'}; font-weight: 500;">${message}</span>
                </div>
                <div class="health-status" style="color: ${badgeColor};">
                    ${badgeText}
                </div>
            </div>
        `;
    }

    async function loadLogs() {
        try {
            const res = await fetchAPI('api.php?action=logs');
            if (res.success) {
                // Render Audit logs
                const auditBody = document.getElementById('audit-log-body');
                if (res.audit_logs.length === 0) {
                    auditBody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada log audit.</td></tr>';
                } else {
                    auditBody.innerHTML = res.audit_logs.map(log => `
                        <tr>
                            <td style="font-size: 13px; color: var(--text-secondary);">${formatDate(log.created_at)}</td>
                            <td style="font-weight: 600;">${escapeHtml(log.username)}</td>
                            <td style="font-weight: 500; color: var(--primary);">${escapeHtml(log.action)}</td>
                            <td style="font-size: 13.5px;">${escapeHtml(log.details)}</td>
                            <td style="font-family: monospace; font-size: 12.5px; color: var(--text-muted);">${escapeHtml(log.ip_address)}</td>
                        </tr>
                    `).join('');
                }

                // Render System logs
                const systemBody = document.getElementById('system-log-body');
                if (res.system_logs.length === 0) {
                    systemBody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada log error sistem.</td></tr>';
                } else {
                    systemBody.innerHTML = res.system_logs.map(log => `
                        <tr>
                            <td style="font-size: 13px; color: var(--text-secondary);">${formatDate(log.created_at)}</td>
                            <td style="text-align: center;">
                                <span class="badge badge-danger" style="text-transform: uppercase;">${escapeHtml(log.severity)}</span>
                            </td>
                            <td style="font-weight: 500; font-size: 13px; color: var(--danger); font-family: monospace; max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${escapeHtml(log.error_message)}">
                                ${escapeHtml(log.error_message)}
                            </td>
                            <td>
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="alert('Stack Trace:\\n\\n' + \`${escapeJsString(log.stack_trace || 'Tidak ada stack trace')}\`)">Lihat Trace</button>
                            </td>
                        </tr>
                    `).join('');
                }
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function submitSettings(e) {
        e.preventDefault();
        const percentage = document.getElementById('set-jaspel').value;
        const rkgPercentage = document.getElementById('set-rkg-jaspel').value;
        const appName = document.getElementById('set-appname').value;
        const company = document.getElementById('set-company').value;
        const maxSize = document.getElementById('set-maxsize').value;

        try {
            const res = await fetchAPI('api.php?action=update_settings', {
                method: 'POST',
                body: {
                    settings: {
                        JASPEL_PERCENTAGE: percentage,
                        RKG_JASPEL_PERCENTAGE: rkgPercentage,
                        APP_NAME: appName,
                        COMPANY: company,
                        MAX_UPLOAD_SIZE: maxSize
                    }
                }
            });

            if (res.success) {
                Toast.success(res.message);
                // Refresh title or header settings instantly
                setTimeout(() => window.location.reload(), 1000);
            } else {
                Toast.error(res.message);
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function submitRestore(e) {
        e.preventDefault();
        const fileInput = document.getElementById('restore-file');
        if (fileInput.files.length === 0) return;

        if (!confirm('PERINGATAN: Apakah Anda benar-benar ingin merestore database? Seluruh data transaksi, dokter, dan departemen saat ini akan digantikan sepenuhnya!')) {
            return;
        }

        const formData = new FormData();
        formData.append('restore_file', fileInput.files[0]);

        try {
            Toast.info('Sedang memproses restorasi database, harap tunggu...');
            const res = await fetch('api.php?action=restore_db', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            }).then(r => r.json());

            if (res.success) {
                Toast.success(res.message, 5000);
                fileInput.value = '';
                setTimeout(() => window.location.reload(), 1500);
            } else {
                Toast.error(res.message);
            }
        } catch (err) {
            console.error(err);
            Toast.error('Gagal memproses restorasi database.');
        }
    }

    function switchLogTab(tab) {
        const btnAudit = document.getElementById('tab-btn-audit');
        const btnSystem = document.getElementById('tab-btn-system');
        const contentAudit = document.getElementById('tab-content-audit');
        const contentSystem = document.getElementById('tab-content-system');

        if (tab === 'audit') {
            btnAudit.style.borderBottom = '3px solid var(--primary)';
            btnAudit.style.fontWeight = '600';
            btnAudit.style.color = 'var(--primary)';
            btnSystem.style.borderBottom = '3px solid transparent';
            btnSystem.style.fontWeight = '500';
            btnSystem.style.color = 'var(--text-secondary)';

            contentAudit.style.display = 'block';
            contentSystem.style.display = 'none';
        } else {
            btnSystem.style.borderBottom = '3px solid var(--primary)';
            btnSystem.style.fontWeight = '600';
            btnSystem.style.color = 'var(--primary)';
            btnAudit.style.borderBottom = '3px solid transparent';
            btnAudit.style.fontWeight = '500';
            btnAudit.style.color = 'var(--text-secondary)';

            contentSystem.style.display = 'block';
            contentAudit.style.display = 'none';
        }
    }

    // Helper utilities
    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function escapeJsString(str) {
        return str.replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"').replace(/\n/g, '\\n').replace(/\r/g, '\\r');
    }

    function formatDate(dateStr) {
        const d = new Date(dateStr);
        const pad = (n) => n.toString().padStart(2, '0');
        return `${pad(d.getDate())}-${pad(d.getMonth() + 1)}-${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
    }
</script>

<?php
Layout::end();
?>
