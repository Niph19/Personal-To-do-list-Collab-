/**
 * Dashboard - Task management dengan Bearer token
 * FE: Token otomatis dikirim di setiap request via API.js
 */

// Guard: require auth, redirect ke login jika no token
if (!AuthGuard.requireAuth()) {
    throw new Error('Unauthorized');
}

const $ = s => document.querySelector(s);
let tasks = [];
let filter = 'all';
let editId = null;

// Utils
const clean = s => String(s).replace(/[&<>'"]/g, c => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
}[c]));

const formatDate = d => {
    if (!d) return '—';
    const iso = d.includes('T') ? d : d + 'T00:00:00Z';
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    }).format(new Date(iso));
};

const isLate = t => {
    if (t.status !== 'todo' || !t.deadline) return false;
    const deadlineDate = t.deadline.split('T')[0];
    const today = new Date().toISOString().slice(0, 10);
    return deadlineDate < today;
};

const notice = s => {
    $('#toast').textContent = s;
    $('#toast').classList.add('show');
    setTimeout(() => $('#toast').classList.remove('show'), 2200);
};

// Render tasks list
function render() {
    const list = tasks.filter(t => filter === 'all' || t.status === filter);
    const todoCount = tasks.filter(t => t.status === 'todo').length;
    
    // Update metrics
    $('#total').textContent = tasks.length;
    $('#todo-count').textContent = todoCount;
    $('#done-count').textContent = tasks.length - todoCount;
    
    // Update summary text
    $('#finding').textContent = todoCount 
        ? `${todoCount} tugas belum selesai. Pilih satu yang ingin kamu tuntaskan hari ini.`
        : 'Semua tugas sudah selesai. Tambahkan langkah berikutnya saat siap.';
    
    // Update timestamp
    $('#updated').textContent = 'Diperbarui ' + new Intl.DateTimeFormat('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date());
    
    // Render task list
    $('#tasks').innerHTML = list.length
        ? list.map(t => `
            <article class="task ${t.status === 'done' ? 'done' : ''}">
                <input class="check" type="checkbox" data-toggle="${t.id}" 
                    ${t.status === 'done' ? 'checked' : ''} 
                    aria-label="Ubah status ${clean(t.title)}">
                <div class="title">${clean(t.title)}</div>
                <time class="${isLate(t) ? 'late' : ''}">
                    ${isLate(t) ? 'Lewat ' : ''}${formatDate(t.deadline)}
                </time>
                <button class="action-btn" data-edit="${t.id}" 
                    aria-label="Ubah ${clean(t.title)}">Ubah</button>
                <button class="action-btn delete-btn" data-delete="${t.id}" 
                    aria-label="Hapus ${clean(t.title)}">Hapus</button>
            </article>
        `).join('')
        : '<div class="empty">Tidak ada tugas di daftar ini.<br><button class="text" id="empty-add">Tambah tugas</button></div>';
    
    // Update deadlines sidebar
    const upcoming = tasks
        .filter(t => t.status === 'todo')
        .sort((a, b) => (a.deadline || '').localeCompare(b.deadline || ''))
        .slice(0, 3);
    
    $('#deadlines').innerHTML = upcoming.length
        ? upcoming.map(t => `
            <li>
                <span>${clean(t.title)}</span>
                <time>${formatDate(t.deadline)}</time>
            </li>
        `).join('')
        : '<li>Tak ada tenggat aktif.</li>';
    
    // Update filter buttons
    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.filter === filter);
    });
}

// Load tasks from API
async function loadTasks() {
    try {
        // API.get() otomatis kirim Bearer token via AuthGuard
        tasks = await API.get('/tugas');
        render();
    } catch (error) {
        $('#tasks').innerHTML = `
            <div class="error">${clean(error.message)}
            <br><button class="text" id="retry">Coba lagi</button></div>
        `;
    }
}

// Toggle task status (todo ↔ done)
async function toggleTask(id) {
    const task = tasks.find(t => t.id === id);
    if (!task) return;
    
    const newStatus = task.status === 'todo' ? 'done' : 'todo';
    
    try {
        // Update via API dengan Bearer token otomatis
        const updated = await API.put(`/tugas/${id}`, {
            title: task.title,
            deadline: task.deadline,
            status: newStatus
        });
        
        Object.assign(task, updated);
        render();
        notice(newStatus === 'done' ? 'Tugas ditandai selesai.' : 'Tugas dibuka kembali.');
    } catch (error) {
        // Rollback checkbox jika gagal
        const checkbox = document.querySelector(`[data-toggle="${id}"]`);
        if (checkbox) checkbox.checked = !checkbox.checked;
        notice('Gagal mengubah status.');
    }
}

// Delete task
async function deleteTask(id) {
    if (!confirm('Hapus tugas ini?')) return;
    
    try {
        await API.delete(`/tugas/${id}`);
        tasks = tasks.filter(t => t.id !== id);
        render();
        notice('Tugas dihapus.');
    } catch (error) {
        notice('Gagal menghapus tugas.');
    }
}

// Modal
function openModal(task = null) {
    editId = task?.id || null;
    document.body.append($('#modal-tpl').content.cloneNode(true));
    
    $('#modal-title').textContent = task ? 'Ubah tugas' : 'Tambah tugas';
    $('#e-title').value = task?.title || '';
    
    const deadlineValue = task?.deadline 
        ? task.deadline.split('T')[0] 
        : new Date().toISOString().slice(0, 10);
    $('#e-date').value = deadlineValue;
    
    $('#e-title').focus();
    $('#cancel').onclick = closeModal;
    
    if (task) {
        $('#delete').hidden = false;
        $('#delete').onclick = async () => {
            closeModal();
            await deleteTask(editId);
        };
    }
    
    $('.backdrop').onclick = e => {
        if (e.target.classList.contains('backdrop')) closeModal();
    };
    
    $('#editor').onsubmit = async e => {
        e.preventDefault();
        
        const title = $('#e-title').value.trim();
        const deadline = $('#e-date').value;
        
        if (!title || !deadline) {
            $('#edit-msg').textContent = 'Judul dan tenggat wajib diisi.';
            return;
        }
        
        const payload = {
            title,
            deadline: deadline + 'T23:59:59Z',
            status: task?.status || 'todo'
        };
        
        try {
            if (editId) {
                // Update existing
                const updated = await API.put(`/tugas/${editId}`, payload);
                Object.assign(tasks.find(t => t.id === editId), updated);
                notice('Tugas diperbarui.');
            } else {
                // Create new
                const created = await API.post('/tugas', payload);
                tasks.unshift(created);
                notice('Tugas ditambahkan.');
            }
            
            closeModal();
            render();
        } catch (error) {
            $('#edit-msg').textContent = clean(error.message);
        }
    };
}

function closeModal() {
    $('.backdrop')?.remove();
}

// Event listeners
document.addEventListener('click', e => {
    // Filter buttons
    const filterBtn = e.target.closest('[data-filter]');
    if (filterBtn) {
        filter = filterBtn.dataset.filter;
        render();
    }
    
    // Edit button
    const editBtn = e.target.closest('[data-edit]');
    if (editBtn) {
        const task = tasks.find(t => t.id === editBtn.dataset.edit);
        openModal(task);
    }
    
    // Delete button
    const delBtn = e.target.closest('[data-delete]');
    if (delBtn) {
        deleteTask(delBtn.dataset.delete);
    }
    
    // Toggle checkbox
    const toggle = e.target.closest('[data-toggle]');
    if (toggle) {
        toggleTask(toggle.dataset.toggle);
    }
    
    // Empty state add button
    if (e.target.id === 'empty-add') {
        openModal();
    }
    
    // Retry button
    if (e.target.id === 'retry') {
        loadTasks();
    }
});

// Add task button
$('#add').onclick = () => openModal();

// Quick add form
$('#quick-form').onsubmit = async e => {
    e.preventDefault();
    
    const title = $('#q-title').value.trim();
    const deadline = $('#q-date').value;
    
    if (!title || !deadline) return;
    
    try {
        const created = await API.post('/tugas', {
            title,
            deadline: deadline + 'T23:59:59Z',
            status: 'todo'
        });
        
        tasks.unshift(created);
        e.target.reset();
        render();
        notice('Tugas ditambahkan.');
    } catch (error) {
        notice('Gagal menambahkan tugas.');
    }
};

// Logout
$('#logout').onclick = async () => {
    try {
        await API.post('/logout');
    } catch (e) {
        // Ignore error, clear local anyway
    }
    
    AuthGuard.clearAuth();
    window.location.href = '/login';
};

// Initialize user display
const user = AuthGuard.getUser();
if (user) {
    $('#name').textContent = user.name;
    $('#email').textContent = user.email;
    $('#initial').textContent = user.name[0].toUpperCase();
}

// Load tasks on page load
loadTasks();
