// ==========================================
// FUNGSI UMUM (GLOBAL)
// ==========================================

const apiUrl = new URL('../backend/api.php', window.location.href);
const validStatuses = ['sesuai', 'arahan', 'telat', 'tidak'];
let studentImportRows = [];
let knownStudentNames = new Set();
let weeklyStartDate = null;

function localDateString(date = new Date()) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

async function apiRequest(resource, params = {}, options = {}) {
    const url = new URL(apiUrl);
    url.searchParams.set('resource', resource);
    Object.entries(params).forEach(([key, value]) => url.searchParams.set(key, value));
    const response = await fetch(url, {
        ...options,
        headers: { 'Content-Type': 'application/json', ...options.headers }
    });
    const result = await response.json();
    if (!response.ok) throw new Error(result.error || `Permintaan gagal (${response.status}).`);
    return result;
}

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, character => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[character]);
}

document.addEventListener('DOMContentLoaded', () => {
    const dateElement = document.getElementById('currentDate');
    if (dateElement) dateElement.textContent = new Intl.DateTimeFormat('id-ID', { dateStyle: 'long' }).format(new Date());

    const attendanceDate = document.getElementById('attendanceDate');
    if (attendanceDate) {
        const requestedDate = new URLSearchParams(window.location.search).get('date');
        attendanceDate.value = isValidDateString(requestedDate) ? requestedDate : localDateString();
        attendanceDate.addEventListener('change', () => {
            if (!document.getElementById('weeklyInputPanel').classList.contains('hidden')) {
                setWeeklyStartFromDate(attendanceDate.value);
            }
            loadAttendance();
        });
        document.getElementById('saveAbsences').addEventListener('click', saveAbsences);
        document.getElementById('activitiesContainer').addEventListener('click', handleAttendanceAction);
        document.getElementById('weeklyDateButtons').addEventListener('click', handleWeeklyDateClick);
        document.getElementById('previousWeek').addEventListener('click', () => shiftWeeklyInput(-7));
        document.getElementById('nextWeek').addEventListener('click', () => shiftWeeklyInput(7));
        if (new URLSearchParams(window.location.search).get('weekly') === '1') generateWeeklyInput();
        else loadAttendance();
    }

    ['inputWeekly', 'generateWeekly', 'mobileInputWeekly'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', openWeeklyInput);
    });

    if (document.getElementById('scheduleRows')) {
        document.getElementById('saveSchedules').addEventListener('click', saveSchedules);
        loadSchedules();
    }

    if (document.getElementById('dashboardStart')) {
        initDateRange('dashboardStart', 'dashboardEnd');
        document.getElementById('loadDashboardSummary').addEventListener('click', loadDashboardSummary);
        loadDashboardSummary();
    }

    if (document.getElementById('reportChild')) {
        initDateRange('reportStart', 'reportEnd');
        document.getElementById('loadChildReport').addEventListener('click', loadChildReport);
        const printButton = document.getElementById('printReport');
        if (printButton) printButton.addEventListener('click', () => window.print());
        loadReportChildren();
    }

    if (document.getElementById('studentRows')) {
        document.getElementById('studentForm').addEventListener('submit', addStudent);
        document.getElementById('studentImportFile').addEventListener('change', previewStudentFile);
        document.getElementById('importStudents').addEventListener('click', importStudents);
        loadStudents();
    }
});

function initDateRange(startId, endId) {
    const now = new Date();
    const start = document.getElementById(startId);
    const end = document.getElementById(endId);
    if (start && !start.value) start.value = localDateString(new Date(now.getFullYear(), now.getMonth(), 1));
    if (end && !end.value) end.value = localDateString(now);
}

async function loadDashboardSummary() {
    const message = document.getElementById('dashboardMessage');
    const start = document.getElementById('dashboardStart').value;
    const end = document.getElementById('dashboardEnd').value;
    if (!start || !end || start > end) {
        message.textContent = 'Pilih rentang tanggal yang valid.';
        message.className = 'text-sm text-red-600';
        return;
    }
    try {
        const result = await apiRequest('summary', { start, end });
        const statuses = [
            ['sesuai', 'Sesuai', 'bg-green-500'],
            ['arahan', 'Dengan Arahan', 'bg-yellow-500'],
            ['telat', 'Terlambat', 'bg-orange-500'],
            ['tidak', 'Tidak', 'bg-red-500']
        ];
        statuses.forEach(([key]) => {
            document.getElementById(`dashboard-${key}`).textContent = `${Number(result.percentages[key]).toFixed(1)}%`;
            document.getElementById(`dashboard-count-${key}`).textContent = `${result.counts[key]} evaluasi`;
        });
        document.getElementById('dashboardTotal').textContent = `${result.total_evaluations} evaluasi`;
        document.getElementById('dashboardBars').innerHTML = statuses.map(([key, label, color]) => `
            <div class="grid grid-cols-[8rem_1fr_5rem] items-center gap-3 text-sm">
                <span class="text-slate-600">${label}</span>
                <div class="h-3 bg-slate-100 rounded overflow-hidden"><div class="${color} h-full" style="width:${Number(result.percentages[key])}%"></div></div>
                <span class="text-right text-slate-500">${Number(result.percentages[key]).toFixed(1)}%</span>
            </div>
        `).join('');
        message.textContent = `Ringkasan ${result.start} sampai ${result.end}.`;
        message.className = 'text-sm text-slate-500';
    } catch (error) {
        message.textContent = error.message;
        message.className = 'text-sm text-red-600';
    }
}

async function loadReportChildren() {
    const select = document.getElementById('reportChild');
    const message = document.getElementById('reportMessage');
    try {
        const result = await apiRequest('children');
        select.innerHTML = result.data.map(child => `<option value="${child.id}">${escapeHtml(child.name)}</option>`).join('');
        if (result.data.length) await loadChildReport();
        else message.textContent = 'Belum ada data anak.';
    } catch (error) {
        select.innerHTML = '<option value="">Data anak gagal dimuat</option>';
        message.textContent = error.message;
        message.className = 'mt-4 text-sm text-red-600';
    }
}

async function loadChildReport() {
    const select = document.getElementById('reportChild');
    const message = document.getElementById('reportMessage');
    const childId = select.value;
    const start = document.getElementById('reportStart').value;
    const end = document.getElementById('reportEnd').value;
    if (!childId || !start || !end || start > end) {
        message.textContent = 'Pilih anak dan rentang tanggal yang valid.';
        message.className = 'mt-4 text-sm text-red-600';
        return;
    }
    message.textContent = 'Memuat raport...';
    message.className = 'mt-4 text-sm text-slate-500';
    try {
        const result = await apiRequest('report', { child_id: childId, start, end });
        renderChildReport(result);
        message.textContent = `Data ${result.child.name} untuk ${result.start} sampai ${result.end} dimuat.`;
        message.className = 'mt-4 text-sm text-green-700';
    } catch (error) {
        message.textContent = error.message;
        message.className = 'mt-4 text-sm text-red-600';
    }
}

function renderChildReport(result) {
    const child = result.child;
    const nameTarget = document.getElementById('reportChildName');
    if (nameTarget) nameTarget.textContent = child.name;
    const idTarget = document.getElementById('reportChildId');
    if (idTarget) idTarget.textContent = child.id;
    const cohortTarget = document.getElementById('reportChildCohort');
    if (cohortTarget) cohortTarget.textContent = child.cohort || '-';
    const genderTarget = document.getElementById('reportChildGender');
    const genderLabel = child.gender === 'L' ? 'Laki-laki' : child.gender === 'P' ? 'Perempuan' : null;
    if (genderTarget) genderTarget.textContent = genderLabel || '-';
    const religionTarget = document.getElementById('reportChildReligion');
    if (religionTarget) religionTarget.textContent = child.religion || '-';
    const tribeTarget = document.getElementById('reportChildTribe');
    if (tribeTarget) tribeTarget.textContent = child.tribe || '-';
    const initials = document.getElementById('reportInitials');
    if (initials) initials.textContent = child.name.split(/\s+/).slice(0, 2).map(part => part.charAt(0)).join('').toUpperCase();
    const detail = document.getElementById('reportChildDetail');
    if (detail) detail.textContent = [genderLabel, child.religion].filter(Boolean).join(' · ') || 'Data anak';
    const period = document.getElementById('reportPeriod');
    if (period) period.textContent = `${result.start} - ${result.end}`;

    const totals = { sesuai: 0, arahan: 0, telat: 0, tidak: 0 };
    result.data.forEach(activity => Object.keys(totals).forEach(status => { totals[status] += Number(activity[status]); }));
    const total = Object.values(totals).reduce((sum, value) => sum + value, 0);
    Object.keys(totals).forEach(status => {
        const count = document.getElementById(`count-${status}`);
        const percent = document.getElementById(`percent-${status}`);
        if (count) count.textContent = totals[status];
        if (percent) percent.textContent = `${total ? (totals[status] / total * 100).toFixed(1) : '0.0'}%`;
    });
    const score = document.getElementById('reportScore');
    if (score) score.textContent = `${total ? (totals.sesuai / total * 100).toFixed(1) : '0.0'}%`;

    const rows = document.getElementById('reportRows');
    const summaryLayout = rows.closest('table').querySelectorAll('thead th').length === 8;
    rows.innerHTML = result.data.map((activity, index) => {
        const activityTotal = Number(activity.total);
        const activityScore = activityTotal ? (Number(activity.sesuai) / activityTotal * 100).toFixed(1) : '0.0';
        const cells = `
            ${summaryLayout ? `<td class="px-4 py-3 text-slate-500">${index + 1}</td>` : ''}
            <td class="px-4 py-3">${escapeHtml(activity.activity)}</td>
            <td class="px-2 py-3 text-center">${activity.sesuai}</td>
            <td class="px-2 py-3 text-center">${activity.arahan}</td>
            <td class="px-2 py-3 text-center">${activity.telat}</td>
            <td class="px-2 py-3 text-center">${activity.tidak}</td>
            <td class="px-2 py-3 text-center">${activity.libur}</td>
            <td class="px-2 py-3 text-center">${activityScore}%</td>
        `;
        return `<tr class="border-b border-slate-100">${cells}</tr>`;
    }).join('');
}

async function loadAttendance() {
    const date = document.getElementById('attendanceDate').value;
    const container = document.getElementById('activitiesContainer');
    const message = document.getElementById('attendanceMessage');
    if (!date) return;
    message.textContent = 'Memuat data...';
    message.className = 'mb-4 text-sm text-slate-600';
    try {
        const weeklyMode = !document.getElementById('weeklyInputPanel').classList.contains('hidden');
        const [childrenResult, activitiesResult, schedulesResult] = await Promise.all([
            apiRequest('children'),
            apiRequest('activities'),
            apiRequest('schedules')
        ]);
        const children = childrenResult.data;
        const activities = activitiesResult.data;
        const attendanceResults = await Promise.all(activities.map(activity =>
            apiRequest('attendance', { date, activity_id: activity.id })
        ));
        const activeChildren = children.filter(child => Boolean(Number(child.is_active)));
        renderAbsenceChildren(activeChildren, attendanceResults[0]?.data || []);
        const schedules = new Map((schedulesResult?.data || []).map(schedule => [String(schedule.activity_id), schedule]));
        const visibleActivities = activities.filter((activity, index) => {
            if (!isActivityScheduled(activity, date, schedules.get(String(activity.id)))) return false;
            return !isPrayerActivity(activity) || attendanceResults[index].data.some(isPrayerEligible);
        });
        container.innerHTML = visibleActivities.length
            ? visibleActivities.map(activity => {
                const index = activities.findIndex(item => item.id === activity.id);
                return renderActivity(activity, index, attendanceResults[index].data);
            }).join('')
            : '<p class="border border-slate-200 rounded-lg bg-white p-5 text-sm text-slate-500">Tidak ada kegiatan terjadwal untuk tanggal ini.</p>';
        message.textContent = `${visibleActivities.length} kegiatan${weeklyMode ? ' terjadwal' : ''} dan ${activeChildren.length} anak aktif dimuat.`;
    } catch (error) {
        container.textContent = 'Data tidak dapat dimuat.';
        message.textContent = error.message;
        message.className = 'mb-4 text-sm text-red-600';
    }
}

function isActivityScheduled(activity, date, schedule) {
    const ruleType = schedule?.rule_type || 'setiap_hari';
    const isoDay = (new Date(`${date}T00:00:00`).getDay() + 6) % 7 + 1;
    if (ruleType === 'hari_kerja') return isoDay <= 5;
    if (ruleType === 'hari_tertentu') return (schedule.config?.days || []).map(Number).includes(isoDay);
    if (ruleType === 'multi_rentang') {
        return (schedule.config?.ranges || []).some(range => range.start <= date && date <= range.end);
    }
    return true;
}

function isPrayerActivity(activity) {
    return /\bsh(?:a|o)lat\b/i.test(activity.name);
}

function isPrayerEligible(child) {
    const religion = String(child.religion || '').trim().toLocaleLowerCase('id-ID');
    return !religion || religion === 'islam';
}

function renderAbsenceChildren(children, attendance) {
    const absentIds = new Set(attendance.filter(child => child.is_absent).map(child => String(child.child_id)));
    document.getElementById('absenceChildren').innerHTML = children.map(child => `
        <label class="flex items-center gap-2 bg-white px-3 py-1.5 border border-yellow-200 rounded cursor-pointer">
            <input type="checkbox" value="${child.id}" ${absentIds.has(String(child.id)) ? 'checked' : ''} class="absence-child rounded text-yellow-600">
            <span>${escapeHtml(child.name)}</span>
        </label>
    `).join('');
}

function renderActivity(activity, index, children) {
    const visibleChildren = isPrayerActivity(activity) ? children.filter(isPrayerEligible) : children;
    return `
        <section class="border border-slate-200 rounded-lg overflow-hidden bg-white">
            <button type="button" onclick="toggleAccordion(${activity.id})" class="w-full text-left bg-slate-100 hover:bg-slate-200 px-4 py-3 border-b border-slate-200 flex justify-between items-center">
                <span class="font-bold text-slate-700"><span class="bg-blue-600 text-white rounded-full w-6 h-6 inline-flex items-center justify-center text-xs mr-2">${index + 1}</span>${escapeHtml(activity.name)}</span>
                <i id="icon-${activity.id}" class="fa-solid fa-chevron-down text-slate-400 transition-transform duration-300"></i>
            </button>
            <div id="content-${activity.id}" class="hidden">
                <div class="px-4 py-2 bg-slate-50 flex flex-wrap items-center gap-2 border-b border-slate-200 text-sm">
                    <span class="font-medium text-slate-600 mr-2">Set massal:</span>
                    ${validStatuses.map(status => `<button type="button" data-action="set-all" data-activity-id="${activity.id}" data-status="${status}" class="border border-slate-200 rounded px-3 py-1.5 ${statusColor(status)}">${statusLabel(status)}</button>`).join('')}
                </div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-slate-50/50">
                    ${visibleChildren.map(child => `
                        <div class="border border-slate-200 bg-white rounded-md p-3" data-child-card data-child-id="${child.child_id}" data-activity-id="${activity.id}">
                            <p class="font-medium text-sm mb-3 text-slate-700">${escapeHtml(child.name)}${child.is_absent ? ' <span class="text-xs text-yellow-700">(Izin)</span>' : ''}</p>
                            <div class="grid grid-cols-2 gap-2 text-xs font-medium">
                                ${validStatuses.map(status => `<button type="button" data-action="set-status" data-status="${status}" class="status-choice border border-slate-200 rounded px-2 py-1.5 ${statusColor(status)} ${child.status === status ? 'is-selected ring-2 ring-offset-1 ring-slate-400' : ''} ${child.is_absent ? 'opacity-40 cursor-not-allowed' : ''}" ${child.is_absent ? 'disabled' : ''}>${statusLabel(status)}</button>`).join('')}
                            </div>
                        </div>
                    `).join('')}
                </div>
                <div class="bg-slate-50 px-4 py-3 text-right border-t border-slate-200">
                    <button type="button" data-action="save-activity" data-activity-id="${activity.id}" class="text-sm bg-teal-600 hover:bg-teal-700 text-white font-medium rounded px-6 py-1.5"><i class="fa-solid fa-check mr-1"></i>Simpan Penilaian</button>
                </div>
            </div>
        </section>
    `;
}

function statusLabel(status) {
    return { sesuai: 'Sesuai', arahan: 'Arahan', telat: 'Telat', tidak: 'Tidak' }[status];
}

function statusColor(status) {
    return {
        sesuai: 'text-green-700 bg-green-50 hover:bg-green-100',
        arahan: 'text-yellow-700 bg-yellow-50 hover:bg-yellow-100',
        telat: 'text-orange-700 bg-orange-50 hover:bg-orange-100',
        tidak: 'text-red-700 bg-red-50 hover:bg-red-100'
    }[status];
}

function handleAttendanceAction(event) {
    const button = event.target.closest('button[data-action]');
    if (!button || button.disabled) return;
    const { action, activityId, status } = button.dataset;
    if (action === 'set-status') {
        const card = button.closest('[data-child-card]');
        card.querySelectorAll('.status-choice').forEach(choice => choice.classList.remove('is-selected', 'ring-2', 'ring-offset-1', 'ring-slate-400'));
        button.classList.add('is-selected', 'ring-2', 'ring-offset-1', 'ring-slate-400');
    } else if (action === 'set-all') {
        document.querySelectorAll(`[data-activity-id="${activityId}"][data-child-card]`).forEach(card => {
            if (card.querySelector('button:disabled')) return;
            card.querySelectorAll('.status-choice').forEach(choice => {
                const selected = choice.dataset.status === status;
                choice.classList.toggle('is-selected', selected);
                choice.classList.toggle('ring-2', selected);
                choice.classList.toggle('ring-offset-1', selected);
                choice.classList.toggle('ring-slate-400', selected);
            });
        });
    } else if (action === 'save-activity') {
        saveActivity(activityId, button);
    }
}

async function saveActivity(activityId, button) {
    const message = document.getElementById('attendanceMessage');
    const evaluations = Array.from(document.querySelectorAll(`[data-activity-id="${activityId}"][data-child-card]`))
        .filter(card => !card.querySelector('button:disabled'))
        .map(card => ({
            child_id: Number(card.dataset.childId),
            status: card.querySelector('.status-choice.is-selected')?.dataset.status
        }))
        .filter(evaluation => evaluation.status);
    button.disabled = true;
    try {
        const result = await apiRequest('attendance', {}, {
            method: 'POST',
            body: JSON.stringify({ date: document.getElementById('attendanceDate').value, activity_id: Number(activityId), evaluations })
        });
        message.textContent = `${result.message} ${result.saved} tersimpan${result.skipped_absent ? `, ${result.skipped_absent} anak izin dilewati` : ''}.`;
        message.className = 'mb-4 text-sm text-green-700';
    } catch (error) {
        message.textContent = error.message;
        message.className = 'mb-4 text-sm text-red-600';
    } finally {
        button.disabled = false;
    }
}

async function saveAbsences() {
    const button = document.getElementById('saveAbsences');
    const message = document.getElementById('attendanceMessage');
    const childIds = Array.from(document.querySelectorAll('.absence-child:checked'), input => Number(input.value));
    button.disabled = true;
    try {
        const result = await apiRequest('absences', {}, {
            method: 'POST',
            body: JSON.stringify({
                date: document.getElementById('attendanceDate').value,
                child_ids: childIds,
                reason: document.getElementById('absenceReason').value.trim() || null
            })
        });
        message.textContent = `${result.message} (${result.count} anak).`;
        message.className = 'mb-4 text-sm text-green-700';
        await loadAttendance();
    } catch (error) {
        message.textContent = error.message;
        message.className = 'mb-4 text-sm text-red-600';
    } finally {
        button.disabled = false;
    }
}


// ==========================================
// FUNGSI UNTUK HALAMAN ABSENSI
// ==========================================

// Fungsi untuk Buka/Tutup (Accordion) baris kegiatan di halaman Absensi
function toggleAccordion(index) {
    const content = document.getElementById(`content-${index}`);
    const icon = document.getElementById(`icon-${index}`);
    if (!content || !icon) return;

    if (content.classList.contains('hidden')) {
        // Tampilkan konten
        content.classList.remove('hidden');
        // Putar panah ke atas (180 derajat)
        icon.classList.add('rotate-180');
    } else {
        // Sembunyikan konten
        content.classList.add('hidden');
        // Kembalikan panah menghadap ke bawah
        icon.classList.remove('rotate-180');
    }
}


// ==========================================
// FUNGSI UNTUK HALAMAN PENGATURAN (JADWAL)
// ==========================================

async function loadSchedules() {
    const rows = document.getElementById('scheduleRows');
    try {
        const result = await apiRequest('schedules');
        rows.innerHTML = result.data.map((activity, index) => renderSchedule(activity, index)).join('');
        document.querySelectorAll('.schedule-rule').forEach(select => {
            select.addEventListener('change', () => toggleAturanJadwal(select));
            toggleAturanJadwal(select);
        });
    } catch (error) {
        rows.innerHTML = `<tr><td colspan="3" class="px-4 py-6 text-center text-red-600">${escapeHtml(error.message)}</td></tr>`;
    }
}

function renderSchedule(activity, index) {
    const selectedDays = new Set(activity.config?.days || []);
    const ranges = activity.config?.ranges?.length ? activity.config.ranges : [{ start: '', end: '' }];
    const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    return `
        <tr class="border-b border-slate-100 hover:bg-slate-50/50">
            <td class="px-4 py-3 text-center text-slate-500 align-top">${index + 1}</td>
            <td class="px-4 py-3 font-medium text-slate-700 align-top">${escapeHtml(activity.name)}</td>
            <td class="px-4 py-3 align-top">
                <select class="schedule-rule w-full border border-slate-300 rounded px-2 py-1.5 text-sm bg-white" data-activity-id="${activity.activity_id}" data-index="${index}">
                    ${[['setiap_hari', 'Setiap Hari'], ['hari_kerja', 'Hari Kerja (Senin-Jumat)'], ['multi_rentang', 'Beberapa Rentang (Multi)'], ['hari_tertentu', 'Hari Tertentu']].map(([value, label]) => `<option value="${value}" ${activity.rule_type === value ? 'selected' : ''}>${label}</option>`).join('')}
                </select>
                <div id="opsi-hari-${index}" class="hidden mt-3 p-3 bg-white border border-slate-200 rounded">
                    <div class="flex flex-wrap gap-4 text-sm text-slate-600 justify-center">
                        ${days.map((day, dayIndex) => `<label class="flex items-center gap-1.5 cursor-pointer" title="${day}"><input type="checkbox" value="${dayIndex + 1}" ${selectedDays.has(dayIndex + 1) ? 'checked' : ''} class="schedule-day">${day.slice(0, 1)}</label>`).join('')}
                    </div>
                </div>
                <div id="opsi-rentang-${index}" class="hidden mt-3 p-3 bg-white border border-slate-200 rounded">
                    <div id="container-rentang-${index}" class="space-y-2">
                        ${ranges.map(range => renderScheduleRange(range)).join('')}
                    </div>
                    <button type="button" onclick="tambahRentang(${index})" class="mt-3 text-xs bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1.5 rounded w-full">+ Tambah Rentang</button>
                </div>
            </td>
        </tr>
    `;
}

function renderScheduleRange(range = { start: '', end: '' }) {
    return `
        <div class="schedule-range flex items-center gap-2 text-xs">
            <input type="date" data-range-field="start" value="${escapeHtml(range.start || '')}" class="border border-slate-300 text-slate-600 rounded px-2 py-1.5 w-full">
            <span class="text-slate-400"><i class="fa-solid fa-minus"></i></span>
            <input type="date" data-range-field="end" value="${escapeHtml(range.end || '')}" class="border border-slate-300 text-slate-600 rounded px-2 py-1.5 w-full">
            <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-700 px-1" title="Hapus rentang"><i class="fa-solid fa-xmark"></i></button>
        </div>
    `;
}

function toggleAturanJadwal(selectElement) {
    const index = selectElement.dataset.index;
    const value = selectElement.value;
    const opsiHari = document.getElementById(`opsi-hari-${index}`);
    const opsiRentang = document.getElementById(`opsi-rentang-${index}`);
    if (!opsiHari || !opsiRentang) return;

    // Sembunyikan keduanya terlebih dahulu (Reset)
    opsiHari.classList.add('hidden');
    opsiRentang.classList.add('hidden');

    // Tampilkan panel sesuai pilihan yang dipilih
    if (value === 'hari_tertentu') {
        opsiHari.classList.remove('hidden');
    } else if (value === 'multi_rentang') {
        opsiRentang.classList.remove('hidden');
    }
}

function tambahRentang(index) {
    const container = document.getElementById(`container-rentang-${index}`);
    if (container) container.insertAdjacentHTML('beforeend', renderScheduleRange());
}

async function saveSchedules() {
    const message = document.getElementById('scheduleMessage');
    const schedules = [];
    for (const select of document.querySelectorAll('.schedule-rule')) {
        const row = select.closest('tr');
        const schedule = { activity_id: Number(select.dataset.activityId), rule_type: select.value, config: null };
        if (schedule.rule_type === 'hari_tertentu') {
            schedule.config = { days: Array.from(row.querySelectorAll('.schedule-day:checked'), input => Number(input.value)) };
        } else if (schedule.rule_type === 'multi_rentang') {
            const ranges = Array.from(row.querySelectorAll('.schedule-range'), range => ({
                start: range.querySelector('[data-range-field="start"]').value,
                end: range.querySelector('[data-range-field="end"]').value
            })).filter(range => range.start || range.end);
            if (ranges.some(range => !range.start || !range.end || range.start > range.end)) {
                message.textContent = 'Isi tanggal mulai dan selesai yang valid untuk setiap rentang.';
                message.className = 'mt-3 text-sm text-red-600';
                return;
            }
            schedule.config = { ranges };
        }
        schedules.push(schedule);
    }
    try {
        const result = await apiRequest('schedules', {}, { method: 'PUT', body: JSON.stringify({ schedules }) });
        message.textContent = `${result.message} (${result.saved} kegiatan).`;
        message.className = 'mt-3 text-sm text-green-700';
    } catch (error) {
        message.textContent = error.message;
        message.className = 'mt-3 text-sm text-red-600';
    }
}

async function loadStudents() {
    const rows = document.getElementById('studentRows');
    try {
        const result = await apiRequest('children');
        knownStudentNames = new Set(result.data.map(student => normalizeStudentName(student.name).toLocaleLowerCase('id-ID')));
        document.getElementById('studentCount').textContent = `${result.data.length} siswa`;
        rows.innerHTML = result.data.map(student => `
            <tr class="border-b border-slate-100">
                <td class="px-4 py-3 text-slate-500">${student.id}</td>
                <td class="px-4 py-3 font-medium text-slate-700">${escapeHtml(student.name)}</td>
                <td class="px-4 py-3">${student.gender === 'L' ? 'Laki-laki' : student.gender === 'P' ? 'Perempuan' : '-'}</td>
                <td class="px-4 py-3">${escapeHtml(student.religion || '-')}</td>
                <td class="px-4 py-3">${escapeHtml(student.tribe || '-')}</td>
                <td class="px-4 py-3">${escapeHtml(student.cohort || '-')}</td>
                <td class="px-4 py-3">${Number(student.is_active) ? 'Aktif' : 'Nonaktif'}</td>
            </tr>
        `).join('') || '<tr><td colspan="7" class="px-4 py-5 text-center text-slate-500">Belum ada siswa.</td></tr>';
    } catch (error) {
        rows.innerHTML = `<tr><td colspan="7" class="px-4 py-5 text-center text-red-600">${escapeHtml(error.message)}</td></tr>`;
    }
}

async function addStudent(event) {
    event.preventDefault();
    const message = document.getElementById('studentMessage');
    const name = normalizeStudentName(document.getElementById('studentName').value);
    const gender = document.getElementById('studentGender').value || null;
    const religion = document.getElementById('studentReligion').value.trim() || null;
    const tribe = document.getElementById('studentTribe').value.trim() || null;
    const cohort = document.getElementById('studentCohort').value.trim() || null;
    if (!name || name.length > 120 || (religion && religion.length > 80) || (tribe && tribe.length > 80) || (cohort && cohort.length > 80)) {
        message.textContent = 'Nama wajib diisi (maksimal 120 karakter); agama, suku, dan angkatan maksimal 80 karakter.';
        message.className = 'mb-4 text-sm text-red-600';
        return;
    }
    if (knownStudentNames.has(name.toLocaleLowerCase('id-ID'))) {
        message.textContent = 'Nama siswa tersebut sudah terdaftar.';
        message.className = 'mb-4 text-sm text-red-600';
        return;
    }
    try {
        const result = await apiRequest('children', {}, {
            method: 'POST',
            body: JSON.stringify({ name, gender, religion, tribe, cohort })
        });
        document.getElementById('studentForm').reset();
        await loadStudents();
        message.textContent = `${result.data.name} berhasil ditambahkan dengan ID ${result.data.id}.`;
        message.className = 'mb-4 text-sm text-green-700';
    } catch (error) {
        message.textContent = error.message;
        message.className = 'mb-4 text-sm text-red-600';
    }
}

async function previewStudentFile(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('studentPreview');
    const importButton = document.getElementById('importStudents');
    const message = document.getElementById('studentMessage');
    studentImportRows = [];
    preview.classList.add('hidden');
    importButton.disabled = true;
    if (!file) return;

    try {
        const rows = await readStudentFile(file);
        studentImportRows = validateStudentRows(rows);
        renderStudentPreview();
        preview.classList.remove('hidden');
        const validCount = studentImportRows.filter(row => row.valid).length;
        importButton.disabled = validCount === 0;
        message.textContent = `${studentImportRows.length} baris ditemukan; ${validCount} siap diimpor.`;
        message.className = 'mb-4 text-sm text-slate-600';
    } catch (error) {
        message.textContent = error.message;
        message.className = 'mb-4 text-sm text-red-600';
    }
}

async function readStudentFile(file) {
    const extension = file.name.split('.').pop().toLowerCase();
    const buffer = await file.arrayBuffer();
    if (extension === 'docx') {
        if (!window.mammoth) throw new Error('Pembaca Word belum termuat. Periksa koneksi internet lalu muat ulang halaman.');
        const converted = await window.mammoth.convertToHtml({ arrayBuffer: buffer });
        const documentContent = new DOMParser().parseFromString(converted.value, 'text/html');
        const tableRows = Array.from(documentContent.querySelectorAll('table tr'), row =>
            Array.from(row.querySelectorAll('th, td'), cell => cell.textContent.trim())
        );
        if (tableRows.length) return tableRows;
        const text = (await window.mammoth.extractRawText({ arrayBuffer: buffer })).value;
        return text.split(/\r?\n/).map(line => line.trim()).filter(Boolean).map(line => line.split(/\t|\s\|\s/));
    }

    if (!['xlsx', 'xls', 'csv'].includes(extension)) {
        throw new Error('Format berkas tidak didukung. Pilih XLSX, XLS, CSV, atau DOCX.');
    }
    if (!window.XLSX) throw new Error('Pembaca Excel belum termuat. Periksa koneksi internet lalu muat ulang halaman.');
    const workbook = window.XLSX.read(buffer, { type: 'array' });
    const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
    if (!firstSheet) throw new Error('Berkas tidak memiliki lembar data.');
    return window.XLSX.utils.sheet_to_json(firstSheet, { header: 1, defval: '', raw: false });
}

function normalizeStudentHeader(value) {
    return String(value || '').toLocaleLowerCase('id-ID').replace(/[^a-z0-9]/g, '');
}

function normalizeStudentName(value) {
    return String(value || '').trim()
        .replace(/^(?:\d+[.)]\s*|[-*]\s*)/, '')
        .replace(/^nama\s*:\s*/i, '')
        .trim();
}

function normalizeStudentGender(value) {
    const gender = String(value || '').trim().toLocaleLowerCase('id-ID').replace(/[\s_-]/g, '');
    if (!gender) return null;
    if (['l', 'lakilaki', 'male', 'putra'].includes(gender)) return 'L';
    if (['p', 'perempuan', 'female', 'putri'].includes(gender)) return 'P';
    return String(value).trim();
}

function normalizeStudentReligion(value) {
    const religion = String(value || '').trim();
    if (!religion) return null;
    const normalized = religion.toLocaleLowerCase('id-ID').replace(/[\s_-]/g, '');
    const religions = {
        islam: 'Islam',
        kristen: 'Kristen',
        hindu: 'Hindu',
        budha: 'Budha',
        buddha: 'Budha',
        konghuchu: 'Konghuchu',
        konghucu: 'Konghuchu',
        khonghucu: 'Konghuchu',
        lainnya: 'Lainnya'
    };
    return religions[normalized] || religion;
}

function validateStudentRows(rows) {
    const sourceRows = rows.filter(row => Array.isArray(row) && row.some(cell => String(cell || '').trim()));
    if (!sourceRows.length) throw new Error('Tidak ada baris siswa yang ditemukan di berkas.');

    const headers = sourceRows[0].map(normalizeStudentHeader);
    const nameColumn = headers.findIndex(value => ['nama', 'namaanak', 'namasiswa', 'name', 'studentname'].includes(value));
    const genderColumn = headers.findIndex(value => ['gender', 'jeniskelamin', 'kelamin'].includes(value));
    const religionColumn = headers.findIndex(value => ['agama', 'religion', 'kepercayaan'].includes(value));
    const tribeColumn = headers.findIndex(value => ['suku', 'tribe', 'etnis', 'ethnicity'].includes(value));
    const cohortColumn = headers.findIndex(value => ['cohort', 'angkatan', 'gelombang'].includes(value));
    const hasHeaders = nameColumn >= 0 || genderColumn >= 0 || religionColumn >= 0 || tribeColumn >= 0 || cohortColumn >= 0;
    if (hasHeaders && nameColumn < 0) throw new Error('Kolom nama wajib ada pada baris judul berkas.');

    const columns = hasHeaders
        ? { name: nameColumn, gender: genderColumn, religion: religionColumn, tribe: tribeColumn, cohort: cohortColumn }
        : { name: 0, gender: 1, religion: 3, cohort: 2, tribe: 4 };
    const dataRows = hasHeaders ? sourceRows.slice(1) : sourceRows;
    const seenNames = new Set(knownStudentNames);
    return dataRows.map(cells => {
        const name = normalizeStudentName(cells[columns.name]);
        const gender = normalizeStudentGender(columns.gender < 0 ? '' : cells[columns.gender]);
        const religion = normalizeStudentReligion(columns.religion < 0 ? '' : cells[columns.religion]);
        const tribe = String(columns.tribe < 0 ? '' : cells[columns.tribe] || '').trim();
        const cohort = String(columns.cohort < 0 ? '' : cells[columns.cohort] || '').trim();
        const issues = [];
        if (!name) issues.push('Nama wajib diisi');
        else if (name.length > 120) issues.push('Nama melebihi 120 karakter');
        if (gender !== null && !['L', 'P'].includes(gender)) issues.push('Gender harus L atau P');
        if (religion && !['Islam', 'Kristen', 'Hindu', 'Budha', 'Konghuchu', 'Lainnya'].includes(religion)) {
            issues.push('Agama tidak tersedia di pilihan; gunakan Lainnya');
        }
        if (tribe.length > 80) issues.push('Suku melebihi 80 karakter');
        if (cohort.length > 80) issues.push('Angkatan melebihi 80 karakter');
        const nameKey = name.toLocaleLowerCase('id-ID');
        if (name && seenNames.has(nameKey)) issues.push('Nama sudah terdaftar/duplikat');
        if (name) seenNames.add(nameKey);
        return { name, gender, religion: religion || null, tribe: tribe || null, cohort: cohort || null, issues, valid: issues.length === 0, imported: false };
    }).filter(row => row.name || row.issues.length);
}

function renderStudentPreview() {
    const rows = document.getElementById('studentPreviewRows');
    rows.innerHTML = studentImportRows.map((student, index) => `
        <tr class="border-t border-slate-100">
            <td class="px-3 py-2"><input class="student-import-select" type="checkbox" data-index="${index}" ${student.valid ? 'checked' : 'disabled'}></td>
            <td class="px-3 py-2">${escapeHtml(student.name || '-')}</td>
            <td class="px-3 py-2">${student.gender || '-'}</td>
            <td class="px-3 py-2">${escapeHtml(student.religion || '-')}</td>
            <td class="px-3 py-2">${escapeHtml(student.tribe || '-')}</td>
            <td class="px-3 py-2">${escapeHtml(student.cohort || '-')}</td>
            <td class="px-3 py-2 ${student.valid ? 'text-green-700' : 'text-red-600'}">${student.imported ? 'Tersimpan' : escapeHtml(student.issues.join(', ') || 'Siap diimpor')}</td>
        </tr>
    `).join('');
}

async function importStudents() {
    const message = document.getElementById('studentMessage');
    const button = document.getElementById('importStudents');
    const selected = Array.from(document.querySelectorAll('.student-import-select:checked'), input => Number(input.dataset.index));
    if (!selected.length) {
        message.textContent = 'Pilih setidaknya satu siswa valid untuk diimpor.';
        message.className = 'mb-4 text-sm text-red-600';
        return;
    }

    button.disabled = true;
    let imported = 0;
    const failures = [];
    for (const [position, index] of selected.entries()) {
        const student = studentImportRows[index];
        message.textContent = `Mengimpor ${position + 1} dari ${selected.length}: ${student.name}`;
        try {
            const result = await apiRequest('children', {}, {
                method: 'POST',
                body: JSON.stringify({ name: student.name, gender: student.gender, religion: student.religion, tribe: student.tribe, cohort: student.cohort })
            });
            student.imported = true;
            student.valid = false;
            student.issues = [`Tersimpan dengan ID ${result.data.id}`];
            knownStudentNames.add(student.name.toLocaleLowerCase('id-ID'));
            imported++;
        } catch (error) {
            student.valid = false;
            student.issues = [error.message];
            failures.push(student.name);
        }
    }

    renderStudentPreview();
    await loadStudents();
    message.textContent = `Impor selesai: ${imported} berhasil, ${failures.length} gagal.`;
    message.className = `mb-4 text-sm ${failures.length ? 'text-orange-700' : 'text-green-700'}`;
    button.disabled = !studentImportRows.some(student => student.valid);
}

function isValidDateString(value) {
    if (!/^\d{4}-\d{2}-\d{2}$/.test(value || '')) return false;
    const date = new Date(`${value}T00:00:00`);
    return !Number.isNaN(date.getTime()) && localDateString(date) === value;
}

function openWeeklyInput() {
    if (!document.getElementById('attendanceDate')) {
        const target = new URL('absensi.php', window.location.href);
        target.searchParams.set('weekly', '1');
        target.searchParams.set('date', localDateString());
        window.location.href = target.toString();
        return;
    }
    generateWeeklyInput();
}

function generateWeeklyInput() {
    const panel = document.getElementById('weeklyInputPanel');
    const dateInput = document.getElementById('attendanceDate');
    if (!panel || !dateInput) return;
    panel.classList.remove('hidden');
    setWeeklyStartFromDate(dateInput.value || localDateString());
    loadAttendance();
    panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function startOfWeek(date) {
    const weekStart = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    const mondayOffset = (weekStart.getDay() + 6) % 7;
    weekStart.setDate(weekStart.getDate() - mondayOffset);
    return weekStart;
}

function setWeeklyStartFromDate(value) {
    const date = isValidDateString(value) ? new Date(`${value}T00:00:00`) : new Date();
    weeklyStartDate = startOfWeek(date);
    renderWeeklyDates();
}

function renderWeeklyDates() {
    if (!weeklyStartDate) return;
    const selectedDate = document.getElementById('attendanceDate').value;
    const endDate = new Date(weeklyStartDate);
    endDate.setDate(endDate.getDate() + 6);
    const formatter = new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short' });
    document.getElementById('weeklyPeriod').textContent = `${localDateString(weeklyStartDate)} - ${localDateString(endDate)}`;
    document.getElementById('weeklyDateButtons').innerHTML = Array.from({ length: 7 }, (_, index) => {
        const date = new Date(weeklyStartDate);
        date.setDate(date.getDate() + index);
        const value = localDateString(date);
        const weekday = new Intl.DateTimeFormat('id-ID', { weekday: 'short' }).format(date);
        const selected = value === selectedDate;
        return `<button type="button" data-week-date="${value}" aria-pressed="${selected}" class="border rounded px-2 py-2 text-sm ${selected ? 'bg-blue-700 border-blue-700 text-white' : 'bg-white border-blue-200 text-slate-700 hover:bg-blue-100'}"><span class="block font-medium">${weekday}</span><span>${formatter.format(date)}</span></button>`;
    }).join('');
}

function handleWeeklyDateClick(event) {
    const button = event.target.closest('[data-week-date]');
    if (!button) return;
    const dateInput = document.getElementById('attendanceDate');
    if (dateInput.value === button.dataset.weekDate) return;
    dateInput.value = button.dataset.weekDate;
    renderWeeklyDates();
    loadAttendance();
}

function shiftWeeklyInput(days) {
    if (!weeklyStartDate) return;
    const dateInput = document.getElementById('attendanceDate');
    const currentDate = isValidDateString(dateInput.value) ? new Date(`${dateInput.value}T00:00:00`) : new Date(weeklyStartDate);
    const dayOffset = Math.round((currentDate - weeklyStartDate) / 86400000);
    weeklyStartDate.setDate(weeklyStartDate.getDate() + days);
    currentDate.setTime(weeklyStartDate.getTime());
    currentDate.setDate(currentDate.getDate() + dayOffset);
    dateInput.value = localDateString(currentDate);
    renderWeeklyDates();
    loadAttendance();
}