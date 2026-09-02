<?php
declare(strict_types=1);

require_once '../../app/config/config.php';
Layout::start('Riwayat Import & Audit', 'history');
?>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="card">
        <div class="card-title">Total Import</div>
        <div class="card-value" id="stats-total-import">0</div>
        <div class="card-info">
            <span>Jumlah pemrosesan</span>
        </div>
    </div>
    
    <div class="card">
        <div class="card-title">Total Berkas Unik</div>
        <div class="card-value" id="stats-total-files">0</div>
        <div class="card-info">
            <span>File Excel berbeda</span>
        </div>
    </div>
    
    <div class="card">
        <div class="card-title">Total Data Terproses</div>
        <div class="card-value" id="stats-total-data">0</div>
        <div class="card-info">
            <span>Baris data dianalisis</span>
        </div>
    </div>
    
    <div class="card">
        <div class="card-title">Total Jaspel Tergabung</div>
        <div class="card-value" style="font-size: 21px; line-height: 1.5; padding-top: 4px; color: var(--success);" id="stats-total-jaspel">Rp 0</div>
        <div class="card-info success">
            <span style="font-weight: 600;">Akumulasi Jaspel keseluruhan</span>
        </div>
    </div>
</div>

<!-- Filters & Search Area -->
<div class="card" style="padding: 20px; margin-bottom: 24px;">
    <div style="display: flex; gap: 16px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 260px;">
            <label class="form-label" style="margin-bottom: 4px;">Cari Riwayat</label>
            <input type="text" id="search-input" class="form-control" placeholder="Cari nama file..." oninput="loadHistory()">
        </div>
        <div style="width: 240px;">
            <label class="form-label" style="margin-bottom: 4px;">Filter Tanggal</label>
            <select id="filter-date" class="form-control" onchange="loadHistory()">
                <option value="all">Semua Waktu</option>
                <option value="today">Hari Ini</option>
                <option value="week">7 Hari Terakhir</option>
                <option value="month">30 Hari Terakhir</option>
                <option value="year">1 Tahun Terakhir</option>
            </select>
        </div>
    </div>
</div>

<!-- History List Card -->
<div class="card" style="padding: 20px;">
    <div class="table-responsive" style="margin-bottom: 0;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 150px;">Tanggal Proses</th>
                    <th>Nama File Asal</th>
                    <th style="width: 140px; text-align: center;">Total Baris</th>
                    <th style="width: 150px; text-align: right;">Total Jaspel</th>
                    <th style="width: 110px; text-align: center;">Ukuran</th>
                    <th style="width: 240px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="history-list-body">
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">
                        Memuat data riwayat...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Detail Modal -->
<div id="detail-modal" class="modal-overlay">
    <div class="modal-box" style="max-width: 680px;">
        <div class="modal-header">
            <h3 class="modal-title">Detail Riwayat Pemrosesan</h3>
            <button class="modal-close" onclick="Modal.close('detail-modal')">&times;</button>
        </div>
        <div class="modal-body" style="padding-top: 16px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; font-size: 14px;">
                <div>
                    <span style="color: var(--text-secondary);">Nama File:</span><br>
                    <strong id="det-filename" style="word-break: break-all;"></strong>
                </div>
                <div>
                    <span style="color: var(--text-secondary);">Tanggal Pemrosesan:</span><br>
                    <strong id="det-date"></strong>
                </div>
                <div>
                    <span style="color: var(--text-secondary);">Durasi Proses:</span><br>
                    <strong id="det-duration"></strong>
                </div>
                <div>
                    <span style="color: var(--text-secondary);">Diproses Oleh:</span><br>
                    <strong id="det-user"></strong>
                </div>
            </div>

            <!-- Stats Block -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; text-align: center;">
                <div style="background-color: var(--background); padding: 10px; border-radius: 8px; border: 1px solid var(--border-color);">
                    <div style="font-size: 11px; color: var(--text-secondary);">Total Baris</div>
                    <strong style="font-size: 16px;" id="det-total-rows">0</strong>
                </div>
                <div style="background-color: var(--success-light); padding: 10px; border-radius: 8px; border: 1px solid #bbf7d0;">
                    <div style="font-size: 11px; color: var(--success);">Sukses</div>
                    <strong style="font-size: 16px; color: var(--success);" id="det-success-rows">0</strong>
                </div>
                <div style="background-color: var(--danger-light); padding: 10px; border-radius: 8px; border: 1px solid #fecaca;">
                    <div style="font-size: 11px; color: var(--danger);">Gagal</div>
                    <strong style="font-size: 16px; color: var(--danger);" id="det-failed-rows">0</strong>
                </div>
                <div style="background-color: var(--primary-light); padding: 10px; border-radius: 8px; border: 1px solid #bfdbfe;">
                    <div style="font-size: 11px; color: var(--primary);">Total Jaspel</div>
                    <strong style="font-size: 14px; color: var(--primary);" id="det-jaspel">Rp 0</strong>
                </div>
            </div>

            <!-- Download Button triggers -->
            <div style="display: flex; gap: 12px; justify-content: center; margin-bottom: 24px;">
                <a href="#" id="det-dl-excel" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px;">
                    📥 Download Excel
                </a>
                <a href="#" id="det-dl-zip" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">
                    📦 Download ZIP
                </a>
            </div>

            <!-- Errors table -->
            <div id="det-errors-box" style="display: none;">
                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--danger);">Daftar Baris Bermasalah (Error):</h4>
                <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th style="width: 80px; text-align: center;">Baris</th>
                                <th style="width: 160px;">Dokter di Excel</th>
                                <th>Deskripsi Error</th>
                            </tr>
                        </thead>
                        <tbody id="det-errors-list">
                            <!-- JS Filled -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title" style="color: var(--danger);">Hapus Riwayat Import</h3>
            <button class="modal-close" onclick="Modal.close('delete-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus riwayat import file <strong id="delete-file-name"></strong>?</p>
            <p style="font-size: 13.5px; color: var(--danger); margin-top: 8px; font-weight: 500;">Peringatan: Seluruh data riwayat di database beserta berkas fisik Excel dan ZIP hasil proses akan dihapus secara permanen dari server!</p>
        </div>
        <div class="modal-footer">
            <input type="hidden" id="delete-id">
            <button type="button" class="btn btn-secondary" onclick="Modal.close('delete-modal')">Batal</button>
            <button type="button" class="btn btn-danger" onclick="confirmDelete()">Hapus Permanen</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        loadHistory();
    });

    async function loadHistory() {
        const search = document.getElementById('search-input').value;
        const filterDate = document.getElementById('filter-date').value;

        try {
            const res = await fetchAPI(`api.php?action=list&search=${encodeURIComponent(search)}&filter_date=${filterDate}`);
            
            if (res.success) {
                // Update stats cards
                document.getElementById('stats-total-import').innerText = res.stats.total_import;
                document.getElementById('stats-total-files').innerText = res.stats.total_files;
                document.getElementById('stats-total-data').innerText = res.stats.total_data.toLocaleString('id-ID');
                document.getElementById('stats-total-jaspel').innerText = res.stats.formatted_jaspel;

                const tbody = document.getElementById('history-list-body');
                if (res.data.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                Tidak ada data riwayat pemrosesan yang cocok.
                            </td>
                        </tr>
                    `;
                    return;
                }

                tbody.innerHTML = res.data.map(h => {
                    const errorBadge = h.failed_rows > 0 
                        ? `<span class="badge badge-danger" style="margin-left: 6px;">${h.failed_rows} error</span>` 
                        : '';
                    
                    return `
                        <tr>
                            <td style="font-size: 13px; color: var(--text-secondary);">${formatDate(h.created_at)}</td>
                            <td style="font-weight: 600; max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${escapeHtml(h.file_name)}">
                                ${escapeHtml(h.file_name)}
                            </td>
                            <td style="text-align: center; font-weight: 500;">
                                ${h.success_rows} sukses${errorBadge}
                            </td>
                            <td style="text-align: right; font-weight: 700; color: var(--text-primary);">
                                ${formatRupiah(h.total_jaspel)}
                            </td>
                            <td style="text-align: center; font-size: 13px; color: var(--text-secondary);">
                                ${h.file_size_mb} MB
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 12.5px; border-color: var(--primary); color: var(--primary);" onclick="openDetailModal(${h.id})">Detail</button>
                                    <a href="../import/export.php?file=${encodeURIComponent(h.output_file)}&format=excel" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12.5px;">Unduh</a>
                                    <button class="btn btn-danger" style="padding: 6px 12px; font-size: 12.5px;" onclick="openDeleteModal(${h.id}, '${escapeJsString(h.file_name)}')">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function openDetailModal(id) {
        try {
            const res = await fetchAPI(`api.php?action=detail&id=${id}`);
            if (res.success) {
                const h = res.history;
                
                document.getElementById('det-filename').innerText = h.file_name;
                document.getElementById('det-date').innerText = formatDate(h.created_at);
                document.getElementById('det-duration').innerText = `${h.duration_seconds} detik`;
                document.getElementById('det-user').innerText = h.username;
                
                document.getElementById('det-total-rows').innerText = h.total_rows;
                document.getElementById('det-success-rows').innerText = h.success_rows;
                document.getElementById('det-failed-rows').innerText = h.failed_rows;
                document.getElementById('det-jaspel').innerText = formatRupiah(h.total_jaspel);

                // Setup downloads links
                document.getElementById('det-dl-excel').href = `../import/export.php?file=${encodeURIComponent(h.output_file)}&format=excel`;
                document.getElementById('det-dl-zip').href = `../import/export.php?file=${encodeURIComponent(h.output_file)}&format=zip`;

                // Render error list
                const errorBox = document.getElementById('det-errors-box');
                if (res.errors.length > 0) {
                    errorBox.style.display = 'block';
                    const listBody = document.getElementById('det-errors-list');
                    listBody.innerHTML = res.errors.map(e => `
                        <tr>
                            <td style="text-align: center; font-weight: 500; font-size: 12.5px;">Baris ${e.row_number}</td>
                            <td style="font-weight: 600; font-family: monospace; font-size: 12.5px;">${escapeHtml(e.doctor_name || '-')}</td>
                            <td style="color: var(--danger); font-size: 12.5px; font-weight: 500;">${escapeHtml(e.error_message)}</td>
                        </tr>
                    `).join('');
                } else {
                    errorBox.style.display = 'none';
                }

                Modal.open('detail-modal');
            }
        } catch (err) {
            console.error(err);
        }
    }

    function openDeleteModal(id, name) {
        document.getElementById('delete-id').value = id;
        document.getElementById('delete-file-name').innerText = name;
        Modal.open('delete-modal');
    }

    async function confirmDelete() {
        const id = document.getElementById('delete-id').value;

        try {
            const res = await fetchAPI('api.php?action=delete', {
                method: 'POST',
                body: { id: id }
            });

            if (res.success) {
                Toast.success(res.message);
                Modal.close('delete-modal');
                loadHistory();
            } else {
                Toast.error(res.message);
                Modal.close('delete-modal');
            }
        } catch (err) {
            console.error(err);
        }
    }

    // Helpers
    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function escapeJsString(str) {
        return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    function formatDate(dateStr) {
        const d = new Date(dateStr);
        const pad = (n) => n.toString().padStart(2, '0');
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        return `${pad(d.getDate())} ${months[d.getMonth()]} ${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
    }

    function formatRupiah(amount) {
        return 'Rp ' + Number(amount).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }
</script>

<?php
Layout::end();
?>
