<?php
declare(strict_types=1);

require_once '../../app/config/config.php';

// Fetch departments for add/edit dropdown and filter
$departments = Department::getActiveList();

Layout::start('Master DPJP / Dokter', 'master-dpjp');
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <p style="color: var(--text-secondary); font-size: 14px;">Kelola dokter DPJP (Dokter Penanggung Jawab Pelayanan) dan pemetaan departemennya.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <button class="btn btn-secondary" onclick="openImportModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Import Excel
        </button>
        <button class="btn btn-primary" onclick="openAddModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Dokter
        </button>
    </div>
</div>

<!-- Filters & Search Area -->
<div class="card" style="padding: 20px; margin-bottom: 24px;">
    <div style="display: flex; gap: 16px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 260px;">
            <label class="form-label" style="margin-bottom: 4px;">Cari Dokter / Alias</label>
            <input type="text" id="search-input" class="form-control" placeholder="Cari nama dokter atau variasi nama..." oninput="loadDoctors()">
        </div>
        <div style="width: 240px;">
            <label class="form-label" style="margin-bottom: 4px;">Filter Departemen</label>
            <select id="filter-dept" class="form-control" onchange="loadDoctors()">
                <option value="0">Semua Departemen</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?php echo $dept['id']; ?>"><?php echo Helper::sanitize($dept['department_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<!-- Table Area -->
<div class="card" style="padding: 20px;">
    <div class="table-responsive" style="margin-bottom: 0;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 70px; text-align: center;">No</th>
                    <th>Nama Dokter</th>
                    <th>Departemen</th>
                    <th>Variasi Nama (Alias)</th>
                    <th style="width: 240px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="doctor-list-body">
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px;">
                        Loading data dokter...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div id="add-modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Dokter Baru</h3>
            <button class="modal-close" onclick="Modal.close('add-modal')">&times;</button>
        </div>
        <form id="add-form" onsubmit="submitAddForm(event)">
            <div class="modal-body">
                <div class="form-group">
                    <label for="add-name" class="form-label">Nama Dokter (Lengkap dengan gelar jika ada)</label>
                    <input type="text" id="add-name" name="doctor_name" class="form-control" placeholder="Contoh: dr. Andi Kurniawan, Sp.B" required>
                </div>
                <div class="form-group">
                    <label for="add-dept" class="form-label">Departemen</label>
                    <select id="add-dept" name="department_id" class="form-control" required>
                        <option value="">Pilih Departemen</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>"><?php echo Helper::sanitize($dept['department_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="Modal.close('add-modal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Edit Dokter</h3>
            <button class="modal-close" onclick="Modal.close('edit-modal')">&times;</button>
        </div>
        <form id="edit-form" onsubmit="submitEditForm(event)">
            <input type="hidden" id="edit-id" name="id">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit-name" class="form-label">Nama Dokter</label>
                    <input type="text" id="edit-name" name="doctor_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit-dept" class="form-label">Departemen</label>
                    <select id="edit-dept" name="department_id" class="form-control" required>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>"><?php echo Helper::sanitize($dept['department_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="Modal.close('edit-modal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Import Excel Modal -->
<div id="import-modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">Import Dokter dari Excel</h3>
            <button class="modal-close" onclick="Modal.close('import-modal')">&times;</button>
        </div>
        <form id="import-form" onsubmit="submitImportForm(event)">
            <div class="modal-body">
                <p style="font-size: 13.5px; color: var(--text-secondary); margin-bottom: 16px;">
                    Unggah file Excel (.xlsx) yang memiliki kolom header <strong>Nama Dokter</strong> dan <strong>Departemen</strong> pada baris pertama. Departemen baru akan dibuat otomatis jika belum terdaftar.
                </p>
                <div class="form-group">
                    <label class="form-label">Pilih File Excel (.xlsx)</label>
                    <input type="file" id="import-file" name="doctors_file" class="form-control" accept=".xlsx" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="Modal.close('import-modal')">Batal</button>
                <button type="submit" class="btn btn-primary">Mulai Import</button>
            </div>
        </form>
    </div>
</div>

<!-- Aliases Modal -->
<div id="alias-modal" class="modal-overlay">
    <div class="modal-box" style="max-width: 550px;">
        <div class="modal-header">
            <h3 class="modal-title">Kelola Variasi Nama (Alias)</h3>
            <button class="modal-close" onclick="Modal.close('alias-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <p style="font-size: 13.5px; color: var(--text-secondary); margin-bottom: 16px;">
                DPJP: <strong id="alias-doc-name" style="color: var(--text-primary);"></strong>
            </p>
            
            <form id="alias-form" onsubmit="submitAddAlias(event)" style="display: flex; gap: 12px; margin-bottom: 24px;">
                <input type="hidden" id="alias-doc-id">
                <div style="flex: 1;">
                    <input type="text" id="new-alias-name" class="form-control" placeholder="Contoh: ANDI KURNIAWAN" required>
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 10px 16px;">Tambah</button>
            </form>

            <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--text-secondary);">Daftar Alias Terdaftar</h4>
            <div style="max-height: 200px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 8px; padding: 4px;">
                <table class="table-modern" style="border: none;">
                    <tbody id="alias-list-body">
                        <!-- Loaded via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title" style="color: var(--danger);">Hapus Dokter</h3>
            <button class="modal-close" onclick="Modal.close('delete-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus dokter <strong id="delete-doc-name"></strong>?</p>
            <p style="font-size: 13px; color: var(--text-secondary); margin-top: 8px;">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer">
            <input type="hidden" id="delete-id">
            <button type="button" class="btn btn-secondary" onclick="Modal.close('delete-modal')">Batal</button>
            <button type="button" class="btn btn-danger" onclick="confirmDelete()">Hapus</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        loadDoctors();
    });

    let doctorsData = [];

    async function loadDoctors() {
        const search = document.getElementById('search-input').value;
        const deptId = document.getElementById('filter-dept').value;
        
        try {
            const res = await fetchAPI(`api.php?action=list&search=${encodeURIComponent(search)}&department_id=${deptId}`);
            const tbody = document.getElementById('doctor-list-body');
            
            if (res.success) {
                doctorsData = res.data;
                if (res.data.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                Belum ada data dokter.
                            </td>
                        </tr>
                    `;
                    return;
                }

                // Fetch aliases for all doctors to render as badges in list
                let html = '';
                res.data.forEach((doc, index) => {
                    html += `
                        <tr>
                            <td style="text-align: center; font-weight: 500;">${index + 1}</td>
                            <td style="font-weight: 600;">${escapeHtml(doc.doctor_name)}</td>
                            <td>${escapeHtml(doc.department_name)}</td>
                            <td>
                                <div id="aliases-badges-${doc.id}" style="display: flex; flex-wrap: wrap; gap: 4px;">
                                    <span style="font-size: 12px; color: var(--text-muted); font-style: italic;">Loading alias...</span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 12.5px; border-color: var(--primary); color: var(--primary);" onclick="openAliasModal(${doc.id}, '${escapeJsString(doc.doctor_name)}')">Kelola Alias</button>
                                    <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 12.5px;" onclick="openEditModal(${doc.id}, '${escapeJsString(doc.doctor_name)}', ${doc.department_id})">Edit</button>
                                    <button class="btn btn-danger" style="padding: 6px 12px; font-size: 12.5px;" onclick="openDeleteModal(${doc.id}, '${escapeJsString(doc.doctor_name)}')">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;

                // Load badges for each doctor asynchronously
                res.data.forEach(doc => {
                    loadAliasesBadge(doc.id);
                });
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function loadAliasesBadge(doctorId) {
        try {
            const res = await fetchAPI(`api.php?action=get_aliases&doctor_id=${doctorId}`);
            const badgeContainer = document.getElementById(`aliases-badges-${doctorId}`);
            if (badgeContainer && res.success) {
                if (res.aliases.length === 0) {
                    badgeContainer.innerHTML = '<span style="font-size: 12px; color: var(--text-muted); font-style: italic;">Tidak ada alias khusus</span>';
                } else {
                    badgeContainer.innerHTML = res.aliases.map(a => `
                        <span class="badge" style="background-color: var(--background); border: 1px solid var(--border-color); color: var(--text-secondary); font-size: 11px;">
                            ${escapeHtml(a.alias_name)}
                        </span>
                    `).join('');
                }
            }
        } catch (err) {
            console.error(err);
        }
    }

    function openAddModal() {
        document.getElementById('add-form').reset();
        Modal.open('add-modal');
    }

    async function submitAddForm(e) {
        e.preventDefault();
        const name = document.getElementById('add-name').value;
        const deptId = document.getElementById('add-dept').value;

        try {
            const res = await fetchAPI('api.php?action=create', {
                method: 'POST',
                body: { doctor_name: name, department_id: deptId }
            });

            if (res.success) {
                Toast.success(res.message);
                Modal.close('add-modal');
                loadDoctors();
            } else {
                Toast.error(res.message);
            }
        } catch (err) {
            console.error(err);
        }
    }

    function openEditModal(id, name, deptId) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-dept').value = deptId;
        Modal.open('edit-modal');
    }

    async function submitEditForm(e) {
        e.preventDefault();
        const id = document.getElementById('edit-id').value;
        const name = document.getElementById('edit-name').value;
        const deptId = document.getElementById('edit-dept').value;

        try {
            const res = await fetchAPI('api.php?action=update', {
                method: 'POST',
                body: { id: id, doctor_name: name, department_id: deptId }
            });

            if (res.success) {
                Toast.success(res.message);
                Modal.close('edit-modal');
                loadDoctors();
            } else {
                Toast.error(res.message);
            }
        } catch (err) {
            console.error(err);
        }
    }

    function openImportModal() {
        document.getElementById('import-form').reset();
        Modal.open('import-modal');
    }

    async function submitImportForm(e) {
        e.preventDefault();
        const fileInput = document.getElementById('import-file');
        if (fileInput.files.length === 0) return;

        const formData = new FormData();
        formData.append('doctors_file', fileInput.files[0]);

        try {
            Toast.info('Mengimpor dokter dari Excel, mohon tunggu...');
            const res = await fetch(`api.php?action=import_excel`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            }).then(r => r.json());

            if (res.success) {
                Toast.success(res.message, 5000);
                Modal.close('import-modal');
                loadDoctors();
            } else {
                Toast.error(res.message);
            }
        } catch (err) {
            console.error(err);
            Toast.error('Gagal mengimpor file Excel.');
        }
    }

    // --- Alias Modal Management ---

    async function openAliasModal(id, name) {
        document.getElementById('alias-doc-id').value = id;
        document.getElementById('alias-doc-name').innerText = name;
        document.getElementById('new-alias-name').value = '';
        Modal.open('alias-modal');
        loadAliasesList(id);
    }

    async function loadAliasesList(doctorId) {
        try {
            const res = await fetchAPI(`api.php?action=get_aliases&doctor_id=${doctorId}`);
            const tbody = document.getElementById('alias-list-body');
            
            if (res.success) {
                if (res.aliases.length === 0) {
                    tbody.innerHTML = '<tr><td style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada alias terdaftar.</td></tr>';
                    return;
                }

                tbody.innerHTML = res.aliases.map(a => `
                    <tr>
                        <td style="font-weight: 500; font-family: monospace; font-size: 13px;">${escapeHtml(a.alias_name)}</td>
                        <td style="text-align: right; width: 60px;">
                            <button class="btn btn-danger" style="padding: 4px 8px; font-size: 11px;" onclick="deleteAlias(${a.id}, ${doctorId})">Hapus</button>
                        </td>
                    </tr>
                `).join('');
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function submitAddAlias(e) {
        e.preventDefault();
        const doctorId = document.getElementById('alias-doc-id').value;
        const aliasName = document.getElementById('new-alias-name').value;

        try {
            const res = await fetchAPI('api.php?action=add_alias', {
                method: 'POST',
                body: { doctor_id: doctorId, alias_name: aliasName }
            });

            if (res.success) {
                Toast.success(res.message);
                document.getElementById('new-alias-name').value = '';
                loadAliasesList(doctorId);
                loadAliasesBadge(doctorId);
            } else {
                Toast.error(res.message);
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function deleteAlias(aliasId, doctorId) {
        if (!confirm('Apakah Anda yakin ingin menghapus alias ini?')) return;
        try {
            const res = await fetchAPI('api.php?action=delete_alias', {
                method: 'POST',
                body: { alias_id: aliasId }
            });

            if (res.success) {
                Toast.success(res.message);
                loadAliasesList(doctorId);
                loadAliasesBadge(doctorId);
            } else {
                Toast.error(res.message);
            }
        } catch (err) {
            console.error(err);
        }
    }

    // --- Delete Modal Management ---

    function openDeleteModal(id, name) {
        document.getElementById('delete-id').value = id;
        document.getElementById('delete-doc-name').innerText = name;
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
                loadDoctors();
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
</script>

<?php
Layout::end();
?>
