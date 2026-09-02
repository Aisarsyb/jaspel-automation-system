<?php
declare(strict_types=1);

require_once '../../app/config/config.php';
Layout::start('Master Departemen', 'departments');
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <p style="color: var(--text-secondary); font-size: 14px;">Kelola daftar departemen spesialisasi rumah sakit.</p>
    </div>
    <button class="btn btn-primary" onclick="openAddModal()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Departemen
    </button>
</div>

<!-- Search & Table Area -->
<div class="card" style="padding: 20px;">
    <div style="margin-bottom: 20px; max-width: 320px;">
        <input type="text" id="search-input" class="form-control" placeholder="Cari nama departemen..." oninput="loadDepartments()">
    </div>

    <div class="table-responsive" style="margin-bottom: 0;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">No</th>
                    <th>Nama Departemen</th>
                    <th style="width: 150px; text-align: center;">Jumlah DPJP</th>
                    <th style="width: 150px; text-align: center;">Status</th>
                    <th style="width: 160px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="department-list-body">
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px;">
                        Loading data departemen...
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
            <h3 class="modal-title">Tambah Departemen Baru</h3>
            <button class="modal-close" onclick="Modal.close('add-modal')">&times;</button>
        </div>
        <form id="add-form" onsubmit="submitAddForm(event)">
            <div class="modal-body">
                <div class="form-group">
                    <label for="add-name" class="form-label">Nama Departemen</label>
                    <input type="text" id="add-name" name="department_name" class="form-control" placeholder="Contoh: Bedah Mulut" required>
                </div>
                <div class="form-group">
                    <label for="add-status" class="form-label">Status</label>
                    <select id="add-status" name="status" class="form-control">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
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
            <h3 class="modal-title">Edit Departemen</h3>
            <button class="modal-close" onclick="Modal.close('edit-modal')">&times;</button>
        </div>
        <form id="edit-form" onsubmit="submitEditForm(event)">
            <input type="hidden" id="edit-id" name="id">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit-name" class="form-label">Nama Departemen</label>
                    <input type="text" id="edit-name" name="department_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit-status" class="form-label">Status</label>
                    <select id="edit-status" name="status" class="form-control">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
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

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title" style="color: var(--danger);">Hapus Departemen</h3>
            <button class="modal-close" onclick="Modal.close('delete-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus departemen <strong id="delete-dept-name"></strong>?</p>
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
    // Load lists on startup
    document.addEventListener('DOMContentLoaded', () => {
        loadDepartments();
    });

    async function loadDepartments() {
        const search = document.getElementById('search-input').value;
        try {
            const res = await fetchAPI(`api.php?action=list&search=${encodeURIComponent(search)}`);
            const tbody = document.getElementById('department-list-body');
            
            if (res.success) {
                if (res.data.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                Belum ada data departemen.
                            </td>
                        </tr>
                    `;
                    return;
                }

                let html = '';
                res.data.forEach((dept, index) => {
                    const statusBadge = dept.status === 'active' 
                        ? '<span class="badge badge-success">Aktif</span>' 
                        : '<span class="badge badge-danger">Nonaktif</span>';
                    
                    html += `
                        <tr>
                            <td style="text-align: center; font-weight: 500;">${index + 1}</td>
                            <td style="font-weight: 600;">${escapeHtml(dept.department_name)}</td>
                            <td style="text-align: center; font-weight: 600; color: var(--primary);">${dept.doctor_count}</td>
                            <td style="text-align: center;">${statusBadge}</td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 12.5px;" onclick="openEditModal(${dept.id}, '${escapeJsString(dept.department_name)}', '${dept.status}')">Edit</button>
                                    <button class="btn btn-danger" style="padding: 6px 12px; font-size: 12.5px;" onclick="openDeleteModal(${dept.id}, '${escapeJsString(dept.department_name)}')">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
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
        const status = document.getElementById('add-status').value;

        try {
            const res = await fetchAPI('api.php?action=create', {
                method: 'POST',
                body: { department_name: name, status: status }
            });

            if (res.success) {
                Toast.success(res.message);
                Modal.close('add-modal');
                loadDepartments();
            } else {
                Toast.error(res.message);
            }
        } catch (err) {
            console.error(err);
        }
    }

    function openEditModal(id, name, status) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-status').value = status;
        Modal.open('edit-modal');
    }

    async function submitEditForm(e) {
        e.preventDefault();
        const id = document.getElementById('edit-id').value;
        const name = document.getElementById('edit-name').value;
        const status = document.getElementById('edit-status').value;

        try {
            const res = await fetchAPI('api.php?action=update', {
                method: 'POST',
                body: { id: id, department_name: name, status: status }
            });

            if (res.success) {
                Toast.success(res.message);
                Modal.close('edit-modal');
                loadDepartments();
            } else {
                Toast.error(res.message);
            }
        } catch (err) {
            console.error(err);
        }
    }

    function openDeleteModal(id, name) {
        document.getElementById('delete-id').value = id;
        document.getElementById('delete-dept-name').innerText = name;
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
                loadDepartments();
            } else {
                Toast.error(res.message);
                Modal.close('delete-modal');
            }
        } catch (err) {
            console.error(err);
        }
    }

    // Helper functions
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
