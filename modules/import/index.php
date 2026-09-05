<?php
declare(strict_types=1);

require_once '../../app/config/config.php';
Layout::start('Import & Processing Engine', 'import');
?>

<div style="margin-bottom: 24px;">
    <p style="color: var(--text-secondary); font-size: 14px;">
        Unggah file Excel (.xlsx) data jasa pelayanan mentah untuk diubah menjadi file laporan terformat secara otomatis.
    </p>
</div>

<!-- Upload Container -->
<div id="upload-container" class="card" style="padding: 32px; margin-bottom: 32px;">
    <div id="drop-zone" class="upload-zone">
        <div class="upload-icon">📥</div>
        <div class="upload-text">Drag & Drop file Excel di sini</div>
        <div class="upload-hint">atau klik untuk menelusuri file dari perangkat Anda</div>
        <div class="upload-hint" style="margin-top: 8px; font-weight: 500; color: var(--text-secondary);">
            Format: .xlsx (Maks. <?php echo MAX_UPLOAD_SIZE; ?> MB)
        </div>
        <input type="file" id="file-input" style="display: none;" accept=".xlsx">
    </div>
</div>

<!-- Progress Container (Initially Hidden) -->
<div id="progress-container" class="card" style="padding: 24px; margin-bottom: 32px; display: none;">
    <h3 style="font-weight: 600; font-size: 15px; margin-bottom: 20px;" id="progress-status-title">Memproses berkas...</h3>
    
    <!-- Progress bar -->
    <div class="progress-steps">
        <div class="progress-bar-fill" id="progress-bar-fill"></div>
        <div class="progress-step" id="step-node-1">
            <div class="step-node">1</div>
            <span class="step-label">Upload</span>
        </div>
        <div class="progress-step" id="step-node-2">
            <div class="step-node">2</div>
            <span class="step-label">Validasi</span>
        </div>
        <div class="progress-step" id="step-node-3">
            <div class="step-node">3</div>
            <span class="step-label">Reading</span>
        </div>
        <div class="progress-step" id="step-node-4">
            <div class="step-node">4</div>
            <span class="step-label">Mapping</span>
        </div>
        <div class="progress-step" id="step-node-5">
            <div class="step-node">5</div>
            <span class="step-label">Grouping</span>
        </div>
        <div class="progress-step" id="step-node-6">
            <div class="step-node">6</div>
            <span class="step-label">Generating</span>
        </div>
        <div class="progress-step" id="step-node-7">
            <div class="step-node">7</div>
            <span class="step-label">Saving</span>
        </div>
        <div class="progress-step" id="step-node-8">
            <div class="step-node">8</div>
            <span class="step-label">Selesai</span>
        </div>
    </div>
</div>

<!-- Duplicate Warnings Area (Initially Hidden) -->
<div id="duplicate-warning-container" class="alert-banner alert-banner-warning" style="display: none; flex-direction: column; gap: 8px;">
    <div style="display: flex; gap: 12px; align-items: center;">
        <div class="alert-banner-icon">⚠️</div>
        <div class="alert-banner-content">
            <h4 style="margin: 0;" id="duplicate-title">Terdeteksi Baris Duplikat</h4>
            <p style="margin: 0; font-size: 13px;" id="duplicate-desc">Terdapat baris data yang identik pada file Excel. Data tetap diproses.</p>
        </div>
    </div>
    <div id="duplicate-details" style="display: none; width: 100%; margin-top: 12px; padding: 12px; background: rgba(255,255,255,0.7); border-radius: 6px; border: 1px solid #fcd34d;">
        <table class="table-modern" style="background: transparent; width: 100%; border: none;">
            <thead>
                <tr>
                    <th style="background: transparent; border: none; padding: 6px;">Baris</th>
                    <th style="background: transparent; border: none; padding: 6px;">Baris Asli</th>
                    <th style="background: transparent; border: none; padding: 6px;">Nama Pasien</th>
                    <th style="background: transparent; border: none; padding: 6px;">DPJP</th>
                    <th style="background: transparent; border: none; padding: 6px;">Tarif</th>
                </tr>
            </thead>
            <tbody id="duplicate-rows-body"></tbody>
        </table>
    </div>
    <button class="btn btn-secondary" style="padding: 4px 10px; font-size: 11px; margin-top: 4px; align-self: flex-start; background: #ffffff;" onclick="toggleDuplicates()">
        Lihat Detail Baris Duplikat
    </button>
</div>

<!-- Interactive Preview Workspace (Initially Hidden) -->
<div id="preview-workspace-container" class="card" style="padding: 28px; margin-bottom: 32px; display: none;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-weight: 700; font-size: 20px; color: var(--text-primary);">Pratinjau Hasil Rekap & Koreksi</h2>
            <p style="color: var(--text-secondary); font-size: 13.5px; margin-top: 4px;">
                Tinjau, saring, dan perbaiki data transaksi secara langsung sebelum mengenerate file Excel final.
            </p>
        </div>
        <div style="display: flex; gap: 12px;">
            <button class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;" onclick="resetEngine()">Batal / Upload Ulang</button>
            <button class="btn btn-primary" id="generate-final-btn" style="padding: 8px 16px; font-size: 13px;" onclick="generateFinalReport()">🔥 Rampungkan & Download Excel</button>
        </div>
    </div>

    <!-- Quick Stats Grid in Preview -->
    <div class="stats-grid" style="margin-bottom: 24px;">
        <div class="card" style="background-color: var(--background); padding: 16px; border: 1px solid var(--border-color);">
            <div class="card-title" style="font-size: 12px; color: var(--text-muted);">Total Baris Data</div>
            <div class="card-value" style="font-size: 20px; font-weight: 700;" id="p-stat-total">0</div>
        </div>
        <div class="card" style="background-color: var(--background); padding: 16px; border: 1px solid var(--border-color);">
            <div class="card-title" style="font-size: 12px; color: var(--text-muted);">Baris Sukses</div>
            <div class="card-value" style="font-size: 20px; font-weight: 700; color: var(--success);" id="p-stat-success">0</div>
        </div>
        <div class="card" style="background-color: var(--background); padding: 16px; border: 1px solid var(--border-color);">
            <div class="card-title" style="font-size: 12px; color: var(--text-muted);">Baris Bermasalah</div>
            <div class="card-value" style="font-size: 20px; font-weight: 700; color: var(--danger);" id="p-stat-failed">0</div>
        </div>
        <div class="card" style="background-color: var(--background); padding: 16px; border: 1px solid var(--border-color);">
            <div class="card-title" style="font-size: 12px; color: var(--text-muted);">Total Tarif Terhitung</div>
            <div class="card-value" style="font-size: 18px; font-weight: 700;" id="p-stat-tarif">Rp 0</div>
            <div class="card-info" style="font-size: 11px; margin-top: 2px; color: var(--text-muted);" id="p-stat-jaspel">Jaspel: Rp 0</div>
        </div>
    </div>

    <!-- Workspace Tabs Navigation -->
    <div style="display: flex; gap: 16px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color);">
        <button id="p-tab-btn-rekap" class="btn" style="background: none; border: none; border-bottom: 3px solid var(--primary); border-radius: 0; font-weight: 600; color: var(--primary); padding: 10px 16px; cursor: pointer;" onclick="switchPreviewTab('rekap')">
            📋 1. Lembar Rekapitulasi
        </button>
        <button id="p-tab-btn-error" class="btn" style="background: none; border: none; border-bottom: 3px solid transparent; border-radius: 0; font-weight: 500; color: var(--text-secondary); padding: 10px 16px; cursor: pointer;" onclick="switchPreviewTab('error')">
            ⚠️ 2. Baris Bermasalah (<span id="p-tab-error-count">0</span>)
        </button>
    </div>

    <!-- Tab Content 1: Rekapitulasi (Dept → Doctor grouped) -->
    <div id="p-tab-content-rekap">
        <p style="font-size: 12.5px; color: var(--text-muted); margin-bottom: 14px;">Klik nama DPJP untuk melihat detail pasien dalam satu jendela pop-up.</p>
        <div id="p-rekap-grouped-container">
            <!-- JS Filled: one block per department -->
        </div>
    </div>

    <!-- Tab Content 2: Baris Bermasalah (Error & Koreksi) -->
    <div id="p-tab-content-error" style="display: none;">
        <div id="p-no-errors-message" class="card" style="padding: 30px; text-align: center; background-color: var(--success-light); border-color: var(--success-border); display: none;">
            <span style="font-size: 32px;">🎉</span>
            <h4 style="font-weight: 700; color: var(--success); font-size: 16px; margin-top: 10px; margin-bottom: 4px;">Hebat! Semua Baris Data Valid</h4>
            <p style="font-size: 13.5px; color: var(--text-secondary); margin: 0;">Tidak ditemukan lagi baris bermasalah. Anda bisa mengklik tombol 'Rampungkan & Download Excel' di atas.</p>
        </div>

        <div id="p-errors-table-container">
            <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;">
                Perbaiki data baris bermasalah di bawah ini secara langsung lalu klik tombol <strong>✓ Simpan</strong> di sampingnya untuk melakukan kalkulasi ulang secara realtime.
            </p>
            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th style="width: 80px; text-align: center;">Baris</th>
                            <th style="width: 180px;">Nama Pasien</th>
                            <th style="width: 260px;">DPJP di Excel & Koreksi Pilihan</th>
                            <th style="width: 220px;">Tindakan</th>
                            <th style="width: 130px; text-align: right;">Tarif (Rp)</th>
                            <th>Detail Masalah / Error</th>
                            <th style="width: 100px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="p-error-rows-body">
                        <!-- JS Filled -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Success Preview Panel (Initially Hidden) -->
<div id="success-container" class="card" style="padding: 28px; margin-bottom: 32px; display: none;">
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 56px; height: 56px; background-color: var(--success-light); color: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 12px;">
            ✓
        </div>
        <h2 style="font-weight: 700; font-size: 22px;">Proses Rekapitulasi Jaspel Sukses!</h2>
        <p style="color: var(--text-secondary); font-size: 14.5px; margin-top: 4px;" id="success-filename-lbl"></p>
    </div>

    <!-- Preview Grid -->
    <div class="stats-grid" style="margin-bottom: 28px;">
        <div class="card" style="background-color: var(--background);">
            <div class="card-title" style="font-size: 12.5px;">Jumlah Data Sukses</div>
            <div class="card-value" style="font-size: 24px;" id="lbl-total-data">0</div>
            <div class="card-info" id="lbl-failed-data" style="color: var(--danger); font-weight: 500;">0 baris diabaikan</div>
        </div>
        <div class="card" style="background-color: var(--background);">
            <div class="card-title" style="font-size: 12.5px;">Departemen Terbentuk</div>
            <div class="card-value" style="font-size: 24px;" id="lbl-total-depts">0</div>
            <div class="card-info" style="font-weight: 500;">Menjadi sheet terpisah</div>
        </div>
        <div class="card" style="background-color: var(--background);">
            <div class="card-title" style="font-size: 12.5px;">Dokter DPJP Terlibat</div>
            <div class="card-value" style="font-size: 24px;" id="lbl-total-docs">0</div>
            <div class="card-info" style="font-weight: 500;">Terpetakan otomatis</div>
        </div>
        <div class="card" style="background-color: var(--background);">
            <div class="card-title" style="font-size: 12.5px;">Total Jaspel Terhitung</div>
            <div class="card-value" style="font-size: 20px; font-weight: 700; line-height: 1.5; color: var(--success);" id="lbl-total-jaspel">Rp 0</div>
            <div class="card-info" style="font-weight: 500;" id="lbl-total-tarif">Tarif: Rp 0</div>
        </div>
    </div>

    <div style="display: flex; gap: 32px; border-top: 1px solid var(--border-color); padding-top: 24px;">
        <!-- Left: List of Sheets created -->
        <div style="flex: 1;">
            <h4 style="font-size: 14.5px; font-weight: 600; margin-bottom: 12px; color: var(--text-secondary);">Sheet yang Terbentuk di Excel:</h4>
            <div id="sheets-badges-list" style="display: flex; flex-wrap: wrap; gap: 8px;">
                <!-- JS Filled -->
            </div>
            <div style="margin-top: 16px; font-size: 12px; color: var(--text-muted);">
                Waktu pemrosesan: <span id="lbl-duration">0</span> detik | Ukuran file: <span id="lbl-size">0</span> MB
            </div>
        </div>

        <!-- Right: Action Download Buttons -->
        <div style="display: flex; flex-direction: column; justify-content: center; gap: 12px; width: 260px;">
            <a href="#" id="download-excel-btn" class="btn btn-primary" style="padding: 12px;">
                📥 Download Laporan Excel
            </a>
            <a href="#" id="download-zip-btn" class="btn btn-secondary" style="padding: 12px;">
                📦 Download Laporan ZIP
            </a>
            <a href="#" id="download-doctor-zip-btn" class="btn btn-secondary" style="padding: 12px;">
                📦 Download ZIP per DPJP
            </a>
            <button class="btn btn-secondary" style="padding: 10px;" onclick="resetEngine()">
                Muat Ulang / Proses File Lain
            </button>
        </div>
    </div>
</div>

<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    let activeTempName = '';
    let activeFileName = '';

    // Drag and Drop Events
    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        if (e.dataTransfer.files.length > 0) {
            handleFileUpload(e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleFileUpload(fileInput.files[0]);
        }
    });

    // File Upload Handler via AJAX
    async function handleFileUpload(file) {
        // Validate format before upload
        const ext = file.name.split('.').pop().toLowerCase();
        if (ext !== 'xlsx') {
            Toast.error('Hanya file Excel .xlsx yang didukung.');
            return;
        }

        const formData = new FormData();
        formData.append('excel_file', file);

        document.getElementById('upload-container').style.display = 'none';
        document.getElementById('progress-container').style.display = 'block';
        
        // Step 1: Uploading
        updateProgressBar(1, 'Mengunggah file Excel...');

        try {
            const res = await fetch('upload.php', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            }).then(r => r.json());

            if (res.success) {
                activeTempName = res.temp_name;
                activeFileName = res.file_name;
                
                // Trigger Engine Processing
                runProcessingEngine(res.temp_name, res.file_name, 0);
            } else {
                Toast.error(res.message);
                resetEngine();
            }
        } catch (err) {
            console.error(err);
            Toast.error('Gagal mengupload file Excel.');
            resetEngine();
        }
    }

    // Processing engine runner
    async function runProcessingEngine(tempName, fileName, ignoreErrors = 0) {
        // Pre-load official doctors in background for inline dropdowns
        if (cachedDoctors.length === 0) {
            try {
                const res = await fetchAPI('../master-dpjp/api.php?action=list');
                if (res.success) {
                    cachedDoctors = res.data;
                }
            } catch(e) {
                console.error('Failed to load doctors list:', e);
            }
        }

        let step = 2;
        updateProgressBar(step, 'Memvalidasi template kolom...');
        
        const progressInterval = setInterval(() => {
            if (step < 7) {
                step++;
                let lbl = 'Memproses data...';
                if (step === 3) lbl = 'Membaca baris data Excel...';
                if (step === 4) lbl = 'Mencocokkan Dokter & Departemen...';
                if (step === 5) lbl = 'Mengelompokkan data per spesialisasi...';
                if (step === 6) lbl = 'Menghitung nilai Jaspel & membuat workbook...';
                if (step === 7) lbl = 'Menyimpan berkas laporan hasil rekap...';
                updateProgressBar(step, lbl);
            }
        }, 300);

        try {
            const response = await fetch('process.php?action=process_temp', {
                method: 'POST',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    temp_name: tempName,
                    file_name: fileName
                })
            }).then(r => r.json());

            clearInterval(progressInterval);

            if (response.success) {
                updateProgressBar(8, 'Selesai!');
                setTimeout(() => {
                    document.getElementById('progress-container').style.display = 'none';
                    
                    // Show duplicates warning if duplicates found
                    if (response.duplicates_count > 0) {
                        showDuplicatesWarning(response.duplicates);
                    } else {
                        document.getElementById('duplicate-warning-container').style.display = 'none';
                    }

                    // Display the interactive preview workspace
                    showPreviewWorkspace(response);
                }, 400);
            } else {
                Toast.error(response.message);
                resetEngine();
            }
        } catch (err) {
            clearInterval(progressInterval);
            console.error(err);
            Toast.error('Terjadi kesalahan sistem saat memproses data.');
            resetEngine();
        }
    }

    function updateProgressBar(step, label) {
        document.getElementById('progress-status-title').innerText = label;
        
        for (let i = 1; i <= 8; i++) {
            const el = document.getElementById(`step-node-${i}`);
            if (el) {
                if (i < step) {
                    el.className = 'progress-step completed';
                } else if (i === step) {
                    el.className = 'progress-step active';
                } else {
                    el.className = 'progress-step';
                }
            }
        }

        const pct = ((step - 1) / 7) * 100;
        const fillEl = document.getElementById('progress-bar-fill');
        if (fillEl) {
            fillEl.style.width = `${pct}%`;
        }
    }

    function showDuplicatesWarning(duplicates) {
        document.getElementById('duplicate-title').innerText = `Terdeteksi ${duplicates.length} Baris Duplikat`;
        const tbody = document.getElementById('duplicate-rows-body');
        
        tbody.innerHTML = duplicates.map(dup => `
            <tr>
                <td style="text-align: center; font-size: 12.5px; font-weight: 600; padding: 4px;">${dup.row}</td>
                <td style="text-align: center; font-size: 12.5px; font-weight: 500; padding: 4px;">${dup.original_row}</td>
                <td style="font-size: 12.5px; padding: 4px;">${escapeHtml(dup.patient_name)}</td>
                <td style="font-size: 12.5px; padding: 4px; font-family: monospace;">${escapeHtml(dup.doctor_name)}</td>
                <td style="font-size: 12.5px; padding: 4px; font-weight: 600;">${formatRupiah(dup.tarif)}</td>
            </tr>
        `).join('');

        document.getElementById('duplicate-warning-container').style.display = 'flex';
    }

    function toggleDuplicates() {
        const box = document.getElementById('duplicate-details');
        if (box.style.display === 'none') {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    }

    // --- Interactive Preview & Tabbed UI Logic ---
    let activePreviewData = null;

    window.switchPreviewTab = function(tab) {
        const tabs = ['rekap', 'error'];
        tabs.forEach(t => {
            const btn = document.getElementById(`p-tab-btn-${t}`);
            const content = document.getElementById(`p-tab-content-${t}`);
            if (!btn || !content) return;
            if (t === tab) {
                btn.style.borderBottom = '3px solid var(--primary)';
                btn.style.fontWeight = '600';
                btn.style.color = 'var(--primary)';
                content.style.display = 'block';
            } else {
                btn.style.borderBottom = '3px solid transparent';
                btn.style.fontWeight = '500';
                btn.style.color = 'var(--text-secondary)';
                content.style.display = 'none';
            }
        });
    };

    window.showPreviewWorkspace = async function(res) {
        activePreviewData = res;

        // Ensure doctors list is cached before rendering options
        if (cachedDoctors.length === 0) {
            try {
                const docRes = await fetchAPI('../master-dpjp/api.php?action=list');
                if (docRes.success) {
                    cachedDoctors = docRes.data;
                }
            } catch (err) {
                console.error('Failed to load doctors list for preview options:', err);
            }
        }

        // 1. Update general stats
        document.getElementById('p-stat-total').innerText = res.total_rows;
        document.getElementById('p-stat-success').innerText = res.success_rows;
        document.getElementById('p-stat-failed').innerText = res.failed_rows;
        document.getElementById('p-stat-tarif').innerText = res.formatted_tarif;
        document.getElementById('p-stat-jaspel').innerText = `Jaspel: ${res.formatted_jaspel}`;
        document.getElementById('p-tab-error-count').innerText = res.failed_rows;

        // 2. Render Tab 1: Rekap per Departemen → per Dokter (grouped)
        renderRekapGrouped(res);

        // 3. Render Tab 2: Errors and corrections table
        const noErrorsMsg = document.getElementById('p-no-errors-message');
        const errorsContainer = document.getElementById('p-errors-table-container');
        const errorsTbody = document.getElementById('p-error-rows-body');

        if (res.errors.length === 0) {
            noErrorsMsg.style.display = 'block';
            errorsContainer.style.display = 'none';
        } else {
            noErrorsMsg.style.display = 'none';
            errorsContainer.style.display = 'block';

            errorsTbody.innerHTML = res.errors.map(err => {
                return `
                    <tr>
                        <td style="text-align: center; font-weight: 600; color: var(--text-secondary);">Baris ${err.row}</td>
                        <td>
                            <input type="text" class="form-control" style="font-size: 13px; padding: 6px; font-weight: 500;" value="${escapeHtml(err.patient_name || '')}" id="row-patient-${err.row}">
                        </td>
                        <td>
                            <div style="display: flex; gap: 4px; align-items: center; max-width: 280px;">
                                <button type="button" class="btn btn-secondary" style="padding: 6px 10px; font-size: 12.5px; height: 34px; flex-grow: 1; text-align: left; display: flex; justify-content: space-between; align-items: center; background-color: var(--surface-color); color: var(--text-color); border: 1px solid var(--border-color);" onclick="window.openDoctorSelectModal(${err.row}, '${escapeJsString(err.doctor_name || '')}')" title="Pilih/Ganti Dokter">
                                    <span id="select-label-${err.row}" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 170px;">${escapeHtml(err.doctor_name || 'Pilih Dokter...')}</span>
                                    <span style="font-size: 10px; opacity: 0.6;">🔍</span>
                                </button>
                                <input type="hidden" id="row-doctor-${err.row}" value="${escapeHtml(err.doctor_name || '')}">
                                <button type="button" class="btn btn-secondary" style="padding: 6px 8px; font-size: 11px; font-weight: 600; height: 34px;" onclick="openMappingModal('${escapeJsString(err.doctor_name || '')}')" title="Daftarkan dokter baru">➕ Baru</button>
                            </div>
                        </td>
                        <td>
                            <input type="text" class="form-control" style="font-size: 13px; padding: 6px;" value="${escapeHtml(err.tindakan || '')}" id="row-tindakan-${err.row}">
                        </td>
                        <td>
                            <input type="number" class="form-control" style="font-size: 13px; padding: 6px; text-align: right; font-weight: 600;" value="${err.tarif || 0}" id="row-tarif-${err.row}">
                        </td>
                        <td style="color: var(--danger); font-size: 12.5px; font-weight: 500;">
                            ${escapeHtml(err.message)}
                        </td>
                        <td style="text-align: center;">
                            <button class="btn btn-success" style="padding: 6px 12px; font-size: 12px; font-weight: 600;" onclick="saveInlineCorrection(${err.row}, this)">✓ Simpan</button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Show workspace container
        document.getElementById('preview-workspace-container').style.display = 'block';
        
        // Default to active tab rekap if not on other tab
        const activeTab = document.querySelector('button[id^="p-tab-btn-"][style*="border-bottom: 3px solid var(--primary)"]');
        if (!activeTab) {
            switchPreviewTab('rekap');
        }
    };

    window.checkInlineMappedName = function(rowNum, selectEl) {
        // Option helper if needed
    };

    // Render the grouped rekap view: Dept header → Doctor rows table
    function renderRekapGrouped(res) {
        const container = document.getElementById('p-rekap-grouped-container');
        if (!container) return;

        const grouped = res.grouped || {};
        const RKG_DEPT = 'Radiologi Kedokteran Gigi';

        if (Object.keys(grouped).length === 0) {
            container.innerHTML = '<p style="text-align:center;color:var(--text-muted);padding:32px;">Belum ada data valid.</p>';
            return;
        }

        let html = '';
        Object.entries(grouped).forEach(([deptName, txs]) => {
            const isRkg = txs.length === 1 && txs[0].is_rkg_aggregate;

            // Aggregate per doctor within this dept (only non-TLB for totals)
            const docMap = {};
            txs.forEach(t => {
                const key = t.doctor_name || 'DPJP RKG';
                if (!docMap[key]) {
                    docMap[key] = { doctor_name: key, tarif: 0, jaspel: 0, patients: 0, hasTlb: false };
                }
                if (!t.is_tlb) {
                    docMap[key].tarif  += parseFloat(t.tarif)  || 0;
                    docMap[key].jaspel += parseFloat(t.jaspel) || 0;
                    if (!isRkg) docMap[key].patients++;
                } else {
                    docMap[key].hasTlb = true;
                }
            });

            const deptTarif  = Object.values(docMap).reduce((s, d) => s + d.tarif,  0);
            const deptJaspel = Object.values(docMap).reduce((s, d) => s + d.jaspel, 0);

            html += `
            <div class="card" style="margin-bottom: 28px; padding: 0; overflow: hidden; border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                <div style="display:flex; justify-content:space-between; align-items:center; padding: 16px 20px; background-color: #f8fafc; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <span style="font-size:11px; color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Departemen</span>
                        <h3 style="font-size:16px; font-weight:700; color:var(--text-primary); margin:2px 0 0;">${escapeHtml(deptName)}</h3>
                    </div>
                </div>
                <div class="table-responsive" style="margin: 0;">
                    <table class="table-modern" style="width:100%; border: none;">
                        <thead style="background-color: #ffffff;">
                            <tr>
                                <th style="width:50px;text-align:center; border-top: none; padding-top: 12px; padding-bottom: 12px;">No</th>
                                <th style="border-top: none; padding-top: 12px; padding-bottom: 12px;">${isRkg ? 'Nama DPJP' : 'Nama DPJP'}</th>
                                <th style="text-align:right; border-top: none; padding-top: 12px; padding-bottom: 12px;">${isRkg ? 'Nominal' : 'Tarif Total'}</th>
                                <th style="text-align:right; border-top: none; padding-top: 12px; padding-bottom: 12px;">Jaspel</th>
                            </tr>
                        </thead>
                        <tbody>`;

            Object.values(docMap).forEach((doc, idx) => {
                const clickable = !isRkg
                    ? `style="cursor:pointer; color:var(--primary); font-weight:600; text-decoration:none; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-hover)'; this.style.textDecoration='underline'" onmouseout="this.style.color='var(--primary)'; this.style.textDecoration='none'" onclick="openDoctorDetail('${escapeJsAttr(deptName)}','${escapeJsAttr(doc.doctor_name)}')" title="Klik untuk lihat detail pasien"`
                    : `style="font-weight:600;"`;
                const tlbBadge = doc.hasTlb
                    ? ` <span style="background:#ffc7ce;color:#9b1c1c;font-size:10px;font-weight:700;padding:1px 6px;border-radius:4px;margin-left:6px;">TLB</span>`
                    : '';
                html += `
                            <tr style="transition: background-color 0.15s ease;" onmouseover="this.style.backgroundColor='#f1f5f9'" onmouseout="this.style.backgroundColor='transparent'">
                                <td style="text-align:center; font-weight:500; color:var(--text-secondary);">${idx + 1}</td>
                                <td ${clickable}>${escapeHtml(doc.doctor_name)}${tlbBadge}</td>
                                <td style="text-align:right; font-weight:500; color:var(--text-secondary);">${formatRupiah(doc.tarif)}</td>
                                <td style="text-align:right; font-weight:700; color:var(--success);">${formatRupiah(doc.jaspel)}</td>
                            </tr>`;
            });

            html += `
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #f8fafc; font-weight:700; border-top:2px solid var(--border-color);">
                                <td colspan="2" style="text-align:right; padding:12px 20px; font-size:14px; color:var(--text-primary);">TOTAL</td>
                                <td style="text-align:right; padding:12px 20px; font-size:14px; color:var(--text-primary);">${formatRupiah(deptTarif)}</td>
                                <td style="text-align:right; padding:12px 20px; font-size:15px; color:var(--success);">${formatRupiah(deptJaspel)}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>`;
        });

        container.innerHTML = html;
    }

    function escapeJsAttr(str) {
        return (str || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }

    let activeDocModalDept = '';
    let activeDocModalDoc = '';

    window.openDoctorDetail = function(deptName, doctorName) {
        if (!activePreviewData || !activePreviewData.grouped[deptName]) return;
        activeDocModalDept = deptName;
        activeDocModalDoc = doctorName;
        const txs = activePreviewData.grouped[deptName].filter(t => t.doctor_name === doctorName);

        document.getElementById('doc-detail-modal-title').innerText = doctorName;
        document.getElementById('doc-detail-modal-dept').innerText = deptName;

        const tbody = document.getElementById('doc-detail-modal-tbody');
        if (txs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:20px;">Tidak ada data.</td></tr>';
        } else {
            let totalTarif = 0, totalJaspel = 0;
            let rows = txs.map((t, idx) => {
                totalTarif  += t.is_tlb ? 0 : (parseFloat(t.tarif)  || 0);
                totalJaspel += t.is_tlb ? 0 : (parseFloat(t.jaspel) || 0);
                const rowStyle = t.is_tlb ? 'background:#ffc7ce;' : '';
                const tlbTag = t.is_tlb ? ' <span style="font-size:10px;font-weight:700;color:#9b1c1c;">[TLB]</span>' : '';
                return `<tr style="${rowStyle}">
                    <td style="text-align:center;font-weight:500;">${idx + 1}</td>
                    <td style="text-align:center;">${escapeHtml(t.tanggal || '-')}</td>
                    <td>${escapeHtml(t.patient_name)}${tlbTag}</td>
                    <td>${escapeHtml(t.tindakan || '-')}</td>
                    <td style="text-align:right;font-weight:500;">${formatRupiah(t.tarif)}</td>
                    <td style="text-align:right;font-weight:700;color:${t.is_tlb ? '#9b1c1c' : 'var(--success)'};">${formatRupiah(t.jaspel)}</td>
                </tr>`;
            }).join('');
            rows += `<tr style="background:var(--surface-color);font-weight:700;border-top:2px solid var(--border-color);">
                <td colspan="4" style="text-align:right;padding:10px 12px;">TOTAL</td>
                <td style="text-align:right;padding:10px 12px;">${formatRupiah(totalTarif)}</td>
                <td style="text-align:right;padding:10px 12px;color:var(--success);">${formatRupiah(totalJaspel)}</td>
            </tr>`;
            tbody.innerHTML = rows;
        }

        Modal.open('doc-detail-modal');
    };

    window.exportDoctorExcel = async function() {
        if (!activeDocModalDept || !activeDocModalDoc || !activePreviewData) return;
        const txs = activePreviewData.grouped[activeDocModalDept].filter(t => t.doctor_name === activeDocModalDoc);
        
        showLoading('Meng-generate Excel khusus DPJP ini...');
        try {
            const res = await fetch('process.php?action=export_single_doctor', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    dept_name: activeDocModalDept,
                    doctor_name: activeDocModalDoc,
                    txs: txs
                })
            }).then(r => r.json());
            
            hideLoading();
            if (res.success) {
                window.location.href = `export.php?file=${encodeURIComponent(res.file)}&format=temp_excel`;
            } else {
                Toast.error(res.message || 'Gagal export Excel');
            }
        } catch (e) {
            hideLoading();
            console.error(e);
            Toast.error('Terjadi kesalahan jaringan saat export Excel');
        }
    };

    window.saveInlineCorrection = async function(rowNum, btn) {
        const patientName = document.getElementById(`row-patient-${rowNum}`).value.trim();
        const doctorName = document.getElementById(`row-doctor-${rowNum}`).value.trim();
        const tindakanVal = document.getElementById(`row-tindakan-${rowNum}`).value.trim();
        const tarifVal = parseFloat(document.getElementById(`row-tarif-${rowNum}`).value) || 0.0;

        if (patientName === '') {
            Toast.warning('Nama pasien tidak boleh kosong!');
            return;
        }

        // Disable input elements in row to prevent double-editing while saving
        document.getElementById(`row-patient-${rowNum}`).disabled = true;
        document.getElementById(`row-doctor-${rowNum}`).disabled = true;
        document.getElementById(`row-tindakan-${rowNum}`).disabled = true;
        document.getElementById(`row-tarif-${rowNum}`).disabled = true;
        btn.disabled = true;
        btn.innerText = '...';

        showLoading('Sedang menyimpan dan menghitung ulang...');

        try {
            const res = await fetchAPI('process.php?action=update_row', {
                method: 'POST',
                body: {
                    row: rowNum,
                    patient_name: patientName,
                    doctor_name: doctorName,
                    tindakan: tindakanVal,
                    tarif: tarifVal
                }
            });

            hideLoading();

            if (res.success) {
                Toast.success(`Baris ${rowNum} sukses diperbarui!`);
                showPreviewWorkspace(res);
            } else {
                Toast.error(res.message);
                // Re-enable row input
                document.getElementById(`row-patient-${rowNum}`).disabled = false;
                document.getElementById(`row-doctor-${rowNum}`).disabled = false;
                document.getElementById(`row-tindakan-${rowNum}`).disabled = false;
                document.getElementById(`row-tarif-${rowNum}`).disabled = false;
                btn.disabled = false;
                btn.innerText = '✓ Simpan';
            }
        } catch (err) {
            hideLoading();
            console.error(err);
            // Re-enable row input
            document.getElementById(`row-patient-${rowNum}`).disabled = false;
            document.getElementById(`row-doctor-${rowNum}`).disabled = false;
            document.getElementById(`row-tindakan-${rowNum}`).disabled = false;
            document.getElementById(`row-tarif-${rowNum}`).disabled = false;
            btn.disabled = false;
            btn.innerText = '✓ Simpan';
        }
    };

    window.generateFinalReport = async function() {
        const btn = document.getElementById('generate-final-btn');
        btn.disabled = true;
        btn.innerText = 'Sedang Menyimpan Laporan...';

        showLoading('Sedang menyimpan dan mengenerate berkas final...');

        try {
            const res = await fetchAPI('process.php?action=generate_final', {
                method: 'POST'
            });

            hideLoading();

            if (res.success) {
                Toast.success('Laporan rekapitulasi Jaspel berhasil dibuat!');
                
                // Hide preview workspace
                document.getElementById('preview-workspace-container').style.display = 'none';
                document.getElementById('duplicate-warning-container').style.display = 'none';

                // Display success final panel
                showSuccessPanel(res);
            } else {
                Toast.error(res.message);
                btn.disabled = false;
                btn.innerText = '🔥 Rampungkan & Download Excel';
            }
        } catch (err) {
            hideLoading();
            console.error(err);
            btn.disabled = false;
            btn.innerText = '🔥 Rampungkan & Download Excel';
        }
    };

    function resetEngine() {
        document.getElementById('upload-container').style.display = 'block';
        document.getElementById('progress-container').style.display = 'none';
        document.getElementById('preview-workspace-container').style.display = 'none';
        document.getElementById('duplicate-warning-container').style.display = 'none';
        document.getElementById('success-container').style.display = 'none';
        document.getElementById('file-input').value = '';
        activeTempName = '';
        activeFileName = '';
        
        // Remove preview_file from URL if present
        if (window.location.search.includes('preview_file=')) {
            window.history.pushState({}, document.title, window.location.pathname);
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function formatRupiah(amount) {
        return 'Rp ' + Number(amount).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    function escapeJsString(str) {
        return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    // --- Custom Searchable Select Dropdown Logic ---
    window.toggleCustomSelect = function(id) {
        // Close all other custom select dropdowns first
        const dropdowns = document.querySelectorAll('.custom-select-dropdown');
        dropdowns.forEach(dd => {
            if (dd.id !== `select-dropdown-${id}`) {
                dd.style.display = 'none';
            }
        });

        const dropdown = document.getElementById(`select-dropdown-${id}`);
        if (dropdown) {
            const isHidden = dropdown.style.display === 'none';
            dropdown.style.display = isHidden ? 'block' : 'none';
            if (isHidden) {
                // Focus search input
                const searchInput = dropdown.querySelector('.custom-select-search');
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                    filterCustomSelect(id, ''); // clear filter
                }
            }
        }
    };

    window.selectCustomOption = function(id, value) {
        // Find label and hidden input
        const label = document.getElementById(`select-label-${id}`);
        const input = document.getElementById(id === 'modal-doctor' ? 'map-doctor-search' : (id === 'modal-dept' ? 'map-dept-search' : `row-doctor-${id}`));
        
        if (label) label.innerText = value;
        if (input) input.value = value;

        // Close dropdown
        const dropdown = document.getElementById(`select-dropdown-${id}`);
        if (dropdown) dropdown.style.display = 'none';
    };

    window.filterCustomSelect = function(id, query) {
        const optionsContainer = document.getElementById(`select-options-${id}`);
        if (!optionsContainer) return;
        const options = optionsContainer.querySelectorAll('.custom-select-option');
        const cleanQuery = query.toLowerCase().trim();

        options.forEach(opt => {
            const text = opt.innerText.toLowerCase();
            if (text.includes(cleanQuery)) {
                opt.style.display = 'block';
            } else {
                opt.style.display = 'none';
            }
        });
    };

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-select-container')) {
            const dropdowns = document.querySelectorAll('.custom-select-dropdown');
            dropdowns.forEach(dd => dd.style.display = 'none');
        }
    });

    // --- Global Loader / Loading Overlay Functions ---
    function showLoading(text = 'Sedang memproses...') {
        document.getElementById('loading-overlay-text').innerText = text;
        document.getElementById('loading-overlay').style.display = 'flex';
    }

    function hideLoading() {
        document.getElementById('loading-overlay').style.display = 'none';
    }

    function showSuccessPanel(res) {
        const container = document.getElementById('success-container');
        container.style.display = 'block';

        // Labels
        document.getElementById('success-filename-lbl').innerText = 'File sumber: ' + (res.file_name || '-');
        document.getElementById('lbl-total-data').innerText = res.success_rows || 0;
        document.getElementById('lbl-failed-data').innerText = (res.failed_rows || 0) + ' baris diabaikan';
        document.getElementById('lbl-total-depts').innerText = res.total_depts || 0;
        document.getElementById('lbl-total-docs').innerText = res.total_docs || 0;
        document.getElementById('lbl-total-jaspel').innerText = res.formatted_jaspel || 'Rp 0';
        document.getElementById('lbl-total-tarif').innerText = 'Tarif: ' + (res.formatted_tarif || 'Rp 0');
        document.getElementById('lbl-duration').innerText = res.duration || 0;
        document.getElementById('lbl-size').innerText = res.file_size || 0;

        // Sheet badges
        const badgesContainer = document.getElementById('sheets-badges-list');
        if (res.departments && res.departments.length > 0) {
            badgesContainer.innerHTML = res.departments.map(d =>
                `<span class="badge badge-success" style="padding: 6px 12px; font-size: 12px;">${escapeHtml(d)}</span>`
            ).join('');
        } else {
            badgesContainer.innerHTML = '<span style="color:var(--text-muted); font-size:13px;">Tidak ada sheet.</span>';
        }

        // Download links
        const excelBtn = document.getElementById('download-excel-btn');
        const zipBtn = document.getElementById('download-zip-btn');

        if (res.output_file) {
            excelBtn.href = 'export.php?file=' + encodeURIComponent(res.output_file) + '&format=excel';
            excelBtn.style.pointerEvents = 'auto';
            excelBtn.style.opacity = '1';
        }
        if (res.output_zip) {
            zipBtn.href = 'export.php?file=' + encodeURIComponent(res.output_file) + '&format=zip';
            zipBtn.style.pointerEvents = 'auto';
            zipBtn.style.opacity = '1';
        }
        
        const doctorZipBtn = document.getElementById('download-doctor-zip-btn');
        if (res.output_doctor_zip && doctorZipBtn) {
            doctorZipBtn.href = 'export.php?file=' + encodeURIComponent(res.output_file) + '&format=doctor_zip';
            doctorZipBtn.style.pointerEvents = 'auto';
            doctorZipBtn.style.opacity = '1';
        }
    }

    window.addEventListener('load', async () => {
        const urlParams = new URLSearchParams(window.location.search);
        const previewFile = urlParams.get('preview_file');
        
        if (previewFile) {
            try {
                const res = await fetch(`../history/api.php?action=get_preview&file=${encodeURIComponent(previewFile)}`).then(r => r.json());
                if (res.success && res.data) {
                    // It returns the responseData of generate_final, we need the preview_data part
                    const pData = res.data.preview_data;
                    activePreviewData = pData;
                    activeTempName = pData.temp_name;
                    activeFileName = pData.file_name;
                    document.getElementById('upload-container').style.display = 'none';
                    
                    // Show final report success modal instead of interactive preview since it's already finalized!
                    showSuccessPanel(res.data);
                    
                    // Show the interactive workspace (tab 1 rekap) below it for viewing
                    document.getElementById('p-stat-total').innerText = pData.total_rows;
                    document.getElementById('p-stat-success').innerText = pData.success_rows;
                    document.getElementById('p-stat-failed').innerText = pData.failed_rows;
                    document.getElementById('p-stat-tarif').innerText = pData.formatted_tarif;
                    document.getElementById('p-stat-jaspel').innerText = `Jaspel: ${pData.formatted_jaspel}`;
                    
                    document.getElementById('generate-final-btn').style.display = 'none'; // hide generate
                    document.getElementById('p-tab-btn-error').style.display = 'none'; // hide error tab
                    
                    renderRekapGrouped(pData);
                    document.getElementById('preview-workspace-container').style.display = 'block';
                    
                    const btnRekap = document.getElementById('p-tab-btn-rekap');
                    btnRekap.style.borderBottom = '3px solid var(--primary)';
                    btnRekap.style.fontWeight = '600';
                    btnRekap.style.color = 'var(--primary)';
                    document.getElementById('p-tab-content-rekap').style.display = 'block';
                } else {
                    Toast.error(res.message);
                }
            } catch (err) {
                console.error('Error loading history preview:', err);
                Toast.error('Gagal memuat pratinjau riwayat.');
            }
            return;
        }

        try {
            const res = await fetchAPI('process.php?action=get_active_session');
            if (res.success && res.has_active_session) {
                activeTempName = res.data.temp_name;
                activeFileName = res.data.file_name;
                document.getElementById('upload-container').style.display = 'none';
                await showPreviewWorkspace(res.data);
            }
        } catch (err) {
            console.error('Error recovering active session:', err);
        }
    });

    // --- Instant Mapping Feature Functions ---
    let cachedDoctors = [];
    const activeDepartments = <?php echo json_encode(Department::getActiveList()); ?>;

    window.openMappingModal = async function(rawName) {
        document.getElementById('map-raw-name').innerText = rawName;
        document.getElementById('map-raw-name-input').value = rawName;
        
        // Reset form inputs
        document.getElementById('mapping-form').reset();
        document.getElementById('map-doctor-search').value = '';
        document.getElementById('map-dept-search').value = '';
        
        // Reset custom select labels
        document.getElementById('select-label-modal-doctor').innerText = 'Pilih Dokter Resmi...';
        document.getElementById('select-label-modal-dept').innerText = 'Pilih Departemen...';
        
        toggleMapTypeFields();

        Modal.open('mapping-modal');

        // Preload doctors list for custom dropdown
        const optionsDoc = document.getElementById('select-options-modal-doctor');
        optionsDoc.innerHTML = '';
        
        try {
            if (cachedDoctors.length === 0) {
                const res = await fetchAPI('../master-dpjp/api.php?action=list');
                if (res.success) {
                    cachedDoctors = res.data;
                }
            }

            if (cachedDoctors.length > 0) {
                let html = '';
                cachedDoctors.forEach(doc => {
                    html += `<div class="custom-select-option" data-value="${escapeHtml(doc.doctor_name)}" onclick="selectCustomOption('modal-doctor', '${escapeJsString(doc.doctor_name)}')">${escapeHtml(doc.doctor_name)} (${escapeHtml(doc.department_name)})</div>`;
                });
                optionsDoc.innerHTML = html;
            }
        } catch (err) {
            console.error(err);
        }
    };

    window.toggleMapTypeFields = function() {
        const type = document.querySelector('input[name="map_type"]:checked').value;
        const groupAlias = document.getElementById('group-map-alias');
        const groupNew = document.getElementById('group-map-new');

        if (type === 'alias') {
            groupAlias.style.display = 'block';
            groupNew.style.display = 'none';
            document.getElementById('map-doctor-search').required = true;
            document.getElementById('map-dept-search').required = false;
        } else {
            groupAlias.style.display = 'none';
            groupNew.style.display = 'block';
            document.getElementById('map-doctor-search').required = false;
            document.getElementById('map-dept-search').required = true;
        }
    };

    window.submitMapping = async function(e) {
        e.preventDefault();
        const rawName = document.getElementById('map-raw-name-input').value;
        const type = document.querySelector('input[name="map_type"]:checked').value;

        try {
            let res;
            if (type === 'alias') {
                const docName = document.getElementById('map-doctor-search').value.trim();
                const matchedDoc = cachedDoctors.find(d => d.doctor_name.toLowerCase() === docName.toLowerCase());
                if (!matchedDoc) {
                    Toast.warning('Pilih dokter resmi dari daftar pencarian!');
                    return;
                }
                
                showLoading('Menghubungkan alias dokter...');
                res = await fetchAPI('../master-dpjp/api.php?action=add_alias', {
                    method: 'POST',
                    body: { doctor_id: matchedDoc.id, alias_name: rawName }
                });
            } else {
                const deptName = document.getElementById('map-dept-search').value.trim();
                const matchedDept = activeDepartments.find(d => d.department_name.toLowerCase() === deptName.toLowerCase());
                if (!matchedDept) {
                    Toast.warning('Pilih departemen resmi dari daftar pencarian!');
                    return;
                }
                
                showLoading('Mendaftarkan dokter baru...');
                res = await fetchAPI('../master-dpjp/api.php?action=create', {
                    method: 'POST',
                    body: { doctor_name: rawName, department_id: matchedDept.id }
                });
            }

            hideLoading();

            if (res.success) {
                Toast.success(res.message);
                Modal.close('mapping-modal');
                
                // Clear cached doctors list since data updated
                cachedDoctors = [];

                // Re-run the processing engine!
                document.getElementById('preview-workspace-container').style.display = 'none';
                document.getElementById('duplicate-warning-container').style.display = 'none';
                document.getElementById('progress-container').style.display = 'block';
                runProcessingEngine(activeTempName, activeFileName, 0);
            } else {
                Toast.error(res.message);
            }
        } catch (err) {
            hideLoading();
            console.error(err);
        }
    };

    let activeDoctorSelectRow = null;

    window.openDoctorSelectModal = function(rowId, currentDoc) {
        activeDoctorSelectRow = rowId;
        const listContainer = document.getElementById('doctor-select-modal-list');
        const searchInput = document.getElementById('doctor-select-modal-search');
        
        searchInput.value = '';
        
        let html = '';
        cachedDoctors.forEach(doc => {
            const isSelected = (doc.doctor_name === currentDoc) ? 'selected' : '';
            html += `<div class="custom-select-option ${isSelected}" data-value="${escapeHtml(doc.doctor_name)}" onclick="confirmDoctorSelect('${escapeJsString(doc.doctor_name)}')">
                ${escapeHtml(doc.doctor_name)} <span style="opacity: 0.7; font-size: 0.9em;">(${escapeHtml(doc.department_name)})</span>
            </div>`;
        });
        
        listContainer.innerHTML = html;
        Modal.open('doctor-select-modal');
        setTimeout(() => searchInput.focus(), 100);
    };

    window.filterDoctorSelectModal = function(term) {
        term = term.toLowerCase();
        const listContainer = document.getElementById('doctor-select-modal-list');
        const options = listContainer.querySelectorAll('.custom-select-option');
        
        options.forEach(opt => {
            const text = opt.textContent.toLowerCase();
            if (text.includes(term)) {
                opt.style.display = 'block';
            } else {
                opt.style.display = 'none';
            }
        });
    };

    window.confirmDoctorSelect = function(docName) {
        if (!activeDoctorSelectRow) return;
        document.getElementById(`select-label-${activeDoctorSelectRow}`).textContent = docName;
        document.getElementById(`row-doctor-${activeDoctorSelectRow}`).value = docName;
        Modal.close('doctor-select-modal');
    };
</script>

<!-- Doctor Detail Modal (click DPJP in rekap view) -->
<div id="doc-detail-modal" class="modal-overlay">
    <div class="modal-box" style="max-width: 820px; width: 95vw;">
        <div class="modal-header" style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <p style="font-size:11px; color:var(--text-muted); text-transform:uppercase; font-weight:600; letter-spacing:0.5px; margin:0 0 2px;">Nama DPJP</p>
                <h3 class="modal-title" id="doc-detail-modal-title" style="margin:0; font-size:16px;"></h3>
                <p style="font-size:13px; color:var(--text-secondary); margin:4px 0 0;"><strong>Departemen:</strong> <span id="doc-detail-modal-dept"></span></p>
            </div>
            <div style="display:flex; align-items:flex-start; gap: 12px;">
                <button class="btn btn-primary" id="btn-export-doc-modal" style="padding: 6px 12px; font-size: 12.5px; display: flex; align-items: center; gap: 6px;" onclick="exportDoctorExcel()">
                    <span style="font-size: 14px;">📥</span> Export Excel
                </button>
                <button class="modal-close" onclick="Modal.close('doc-detail-modal')" style="flex-shrink:0; margin-left: 12px;">&times;</button>
            </div>
        </div>
        <div class="modal-body" style="padding: 16px; max-height: 65vh; overflow-y: auto;">
            <table class="table-modern" style="width:100%;">
                <thead>
                    <tr>
                        <th style="width:46px; text-align:center;">No</th>
                        <th style="width:100px; text-align:center;">Tanggal</th>
                        <th>Nama Pasien</th>
                        <th>Tindakan</th>
                        <th style="text-align:right; width:140px;">Tarif</th>
                        <th style="text-align:right; width:130px;">Jaspel</th>
                    </tr>
                </thead>
                <tbody id="doc-detail-modal-tbody">
                    <!-- JS Filled -->
                </tbody>
            </table>
        </div>
        <div class="modal-footer" style="justify-content:flex-end;">
            <button class="btn btn-secondary" onclick="Modal.close('doc-detail-modal')">Tutup</button>
        </div>
    </div>
</div>

<!-- Table Row Doctor Select Modal -->
<div id="doctor-select-modal" class="modal-overlay">
    <div class="modal-box" style="max-width: 500px;">
        <div class="modal-header">
            <h3 class="modal-title">Pilih DPJP Pengganti</h3>
            <button class="modal-close" onclick="Modal.close('doctor-select-modal')">&times;</button>
        </div>
        <div class="modal-body" style="padding: 16px;">
            <input type="text" id="doctor-select-modal-search" class="form-control" placeholder="Cari nama dokter atau departemen..." style="margin-bottom: 12px; width: 100%; padding: 10px;" oninput="filterDoctorSelectModal(this.value)">
            <div id="doctor-select-modal-list" style="max-height: 350px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 6px; padding: 4px;">
                <!-- JS Filled -->
            </div>
        </div>
    </div>
</div>

<!-- Instant Mapping Modal -->
<div id="mapping-modal" class="modal-overlay">
    <div class="modal-box" style="max-width: 550px;">
        <div class="modal-header">
            <h3 class="modal-title">Pemetaan Instan Dokter</h3>
            <button class="modal-close" onclick="Modal.close('mapping-modal')">&times;</button>
        </div>
        <form id="mapping-form" onsubmit="submitMapping(event)">
            <div class="modal-body">
                <p style="font-size: 13.5px; color: var(--text-secondary); margin-bottom: 16px;">
                    Nama Dokter di Excel: <strong id="map-raw-name" style="color: var(--text-primary);"></strong>
                </p>
                <input type="hidden" id="map-raw-name-input">

                <div class="form-group">
                    <label class="form-label">Jenis Pemetaan</label>
                    <div style="display: flex; gap: 20px; margin-top: 6px; flex-wrap: wrap;">
                        <label style="font-size: 13.5px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                            <input type="radio" name="map_type" value="alias" checked onchange="toggleMapTypeFields()">
                            Hubungkan ke Dokter Resmi (Buat Alias)
                        </label>
                        <label style="font-size: 13.5px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                            <input type="radio" name="map_type" value="new" onchange="toggleMapTypeFields()">
                            Daftarkan Sebagai Dokter Baru
                        </label>
                    </div>
                </div>

                <!-- Field for Alias (select doctor) -->
                <div class="form-group" id="group-map-alias">
                    <label class="form-label">Pilih Dokter Resmi</label>
                    <div class="custom-select-container" id="modal-select-doctor-container" style="max-width: 100%;">
                        <div class="custom-select-trigger" onclick="toggleCustomSelect('modal-doctor')">
                            <span id="select-label-modal-doctor">Pilih Dokter Resmi...</span>
                            <span class="custom-select-arrow">▼</span>
                        </div>
                        <input type="hidden" id="map-doctor-search" value="">
                        <div class="custom-select-dropdown" id="select-dropdown-modal-doctor" style="display: none;">
                            <input type="text" class="custom-select-search" placeholder="Cari nama dokter..." oninput="filterCustomSelect('modal-doctor', this.value)">
                            <div class="custom-select-options" id="select-options-modal-doctor">
                                <!-- JS Filled -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Field for New Doctor (select department) -->
                <div class="form-group" id="group-map-new" style="display: none;">
                    <label class="form-label">Pilih Departemen untuk Dokter Baru</label>
                    <div class="custom-select-container" id="modal-select-dept-container" style="max-width: 100%;">
                        <div class="custom-select-trigger" onclick="toggleCustomSelect('modal-dept')">
                            <span id="select-label-modal-dept">Pilih Departemen...</span>
                            <span class="custom-select-arrow">▼</span>
                        </div>
                        <input type="hidden" id="map-dept-search" value="">
                        <div class="custom-select-dropdown" id="select-dropdown-modal-dept" style="display: none;">
                            <input type="text" class="custom-select-search" placeholder="Cari nama departemen..." oninput="filterCustomSelect('modal-dept', this.value)">
                            <div class="custom-select-options" id="select-options-modal-dept">
                                <?php foreach ($departments = Department::getActiveList() as $dept): ?>
                                    <div class="custom-select-option" data-value="<?php echo Helper::sanitize($dept['department_name']); ?>" onclick="selectCustomOption('modal-dept', '<?php echo Helper::escapeJavaScript(Helper::sanitize($dept['department_name'])); ?>')">
                                        <?php echo Helper::sanitize($dept['department_name']); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="Modal.close('mapping-modal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan & Proses Ulang</button>
            </div>
        </form>
    </div>
</div>

<!-- Full screen loading overlay with glassmorphism / opacity -->
<div id="loading-overlay" class="modal-overlay" style="display: none; background: rgba(255,255,255,0.75); backdrop-filter: blur(4px); align-items: center; justify-content: center; z-index: 9999;">
    <div style="text-align: center;">
        <div class="spinner" style="width: 48px; height: 48px; border: 4px solid var(--primary-light); border-top-color: var(--primary); border-radius: 50; border-radius: 50%; animation: import-spin 1s linear infinite; margin: 0 auto 16px;"></div>
        <p style="font-weight: 600; color: var(--text-primary); font-size: 15px;" id="loading-overlay-text">Sedang memproses...</p>
    </div>
</div>

<style>
@keyframes import-spin {
    to { transform: rotate(360deg); }
}
.spinner {
    display: inline-block;
}

/* Custom Searchable Select Dropdown */
.custom-select-container {
    position: relative;
    width: 100%;
    max-width: 280px;
    box-sizing: border-box;
}
.custom-select-trigger {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-primary);
    transition: all 0.2s ease;
    user-select: none;
}
.custom-select-trigger:hover {
    border-color: var(--primary);
}
.custom-select-arrow {
    font-size: 9px;
    color: var(--text-secondary);
}
.custom-select-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    margin-top: 4px;
    z-index: 1000;
    padding: 8px;
    box-sizing: border-box;
}
.custom-select-search {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-size: 13px;
    margin-bottom: 8px;
    box-sizing: border-box;
}
.custom-select-search:focus {
    outline: none;
    border-color: var(--primary);
}
.custom-select-options {
    max-height: 180px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.custom-select-option {
    padding: 8px 10px;
    font-size: 13px;
    cursor: pointer;
    border-radius: 4px;
    color: var(--text-primary);
    transition: all 0.15s ease;
    text-align: left;
}
.custom-select-option:hover {
    background: var(--primary-light);
    color: var(--primary);
}
.custom-select-option.selected {
    background: var(--primary);
    color: #ffffff;
    font-weight: 600;
}
</style>

<?php
Layout::end();
?>
