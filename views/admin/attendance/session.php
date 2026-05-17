<?php
/**
 * @var array $session
 * @var array $records
 */
$BASE = $_ENV['APP_URL'] ?? '';
ob_start();
?>

<!-- Breadcrumbs -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="<?= $BASE ?>/admin/dashboard" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-all">
                <i class="fa-solid fa-house mr-2 text-xs"></i> แดชบอร์ด
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fa-solid fa-chevron-right text-slate-300 text-[10px] mx-2"></i>
                <a href="<?= $BASE ?>/admin/classrooms/show/<?= $session['classroom_id'] ?>" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-all">
                    <?= htmlspecialchars($session['subject_name']) ?>
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fa-solid fa-chevron-right text-slate-300 text-[10px] mx-2"></i>
                <span class="text-sm font-bold text-slate-800 tracking-tight">บันทึกการเข้าเรียน</span>
            </div>
        </li>
    </ol>
</nav>

<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">บันทึกการเข้าเรียน</h1>
        <p class="text-slate-500 text-sm">วันที่ <?= date('d/m/Y', strtotime($session['session_date'])) ?> | คาบที่ <?= $session['period_number'] ?></p>
    </div>
    <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
        <button id="scanModeBtn" onclick="toggleScanMode()" class="bg-indigo-50 text-indigo-600 px-4 py-2.5 rounded-xl text-sm font-bold border border-indigo-100 hover:bg-indigo-100 transition-all flex items-center justify-center">
            <i class="fa-solid fa-barcode mr-2"></i> โหมดแสกน: <span id="scanModeStatus" class="ml-1 text-slate-400">ปิดอยู่</span>
        </button>
        <div class="grid grid-cols-2 gap-2 sm:flex">
            <button onclick="deleteSession(<?= $session['id'] ?>)" class="bg-red-50 border border-red-100 text-red-500 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-red-500 hover:text-white transition-all flex items-center justify-center" title="ลบบันทึกนี้">
                <i class="fa-solid fa-trash mr-2"></i> ลบ
            </button>
            <button onclick="window.location.reload()" class="bg-white border border-slate-200 text-slate-600 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-50 transition-all flex items-center justify-center">
                <i class="fa-solid fa-rotate mr-2"></i> รีเฟรช
            </button>
            <a href="<?= $BASE ?>/admin/classrooms/show/<?= $session['classroom_id'] ?>" class="bg-slate-800 text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-all shadow-md flex items-center justify-center">
                เสร็จสิ้น
            </a>
        </div>
    </div>
</div>

<!-- Scanner Input (Hidden) -->
<input type="text" id="barcodeScanner" class="opacity-0 absolute -z-10" autocomplete="off">

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-8">
    <div class="bg-white p-3 md:p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center">
        <div class="w-8 h-8 md:w-10 md:h-10 bg-indigo-50 rounded-lg md:rounded-xl flex items-center justify-center text-indigo-600 mr-3 text-sm md:text-base"><i class="fa-solid fa-users"></i></div>
        <div><p class="text-[9px] md:text-[10px] uppercase font-bold text-slate-400">ทั้งหมด</p><p class="text-base md:text-lg font-bold text-slate-800"><?= count($records) ?></p></div>
    </div>
    <div class="bg-white p-3 md:p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center">
        <div class="w-8 h-8 md:w-10 md:h-10 bg-emerald-50 rounded-lg md:rounded-xl flex items-center justify-center text-emerald-600 mr-3 text-sm md:text-base"><i class="fa-solid fa-check"></i></div>
        <div><p class="text-[9px] md:text-[10px] uppercase font-bold text-slate-400">มาเรียน</p><p class="text-base md:text-lg font-bold text-emerald-600" id="stat-present">0</p></div>
    </div>
    <div class="bg-white p-3 md:p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center">
        <div class="w-8 h-8 md:w-10 md:h-10 bg-amber-50 rounded-lg md:rounded-xl flex items-center justify-center text-amber-600 mr-3 text-sm md:text-base"><i class="fa-solid fa-clock"></i></div>
        <div><p class="text-[9px] md:text-[10px] uppercase font-bold text-slate-400">สาย</p><p class="text-base md:text-lg font-bold text-amber-600" id="stat-late">0</p></div>
    </div>
    <div class="bg-white p-3 md:p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center">
        <div class="w-8 h-8 md:w-10 md:h-10 bg-red-50 rounded-lg md:rounded-xl flex items-center justify-center text-red-600 mr-3 text-sm md:text-base"><i class="fa-solid fa-xmark"></i></div>
        <div><p class="text-[9px] md:text-[10px] uppercase font-bold text-slate-400">ขาด</p><p class="text-base md:text-lg font-bold text-red-600" id="stat-absent">0</p></div>
    </div>
</div>

<!-- Student List -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="hidden md:table-header-group">
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-4 md:px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider w-16 md:w-20">เลขที่</th>
                    <th class="px-4 md:px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">ชื่อ-นามสกุล</th>
                    <th class="px-4 md:px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">สถานะ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($records as $r): ?>
                    <tr class="hover:bg-slate-50/30 transition-all flex flex-col md:table-row p-4 md:p-0 border-b md:border-b-0">
                        <!-- Number (Desktop) / Header (Mobile) -->
                        <td class="hidden md:table-cell px-6 py-4 text-center font-bold text-slate-700">
                            <?= $r['student_number'] ?>
                        </td>

                        <!-- Name Info -->
                        <td class="px-0 md:px-6 py-0 md:py-4 flex items-center md:table-cell mb-3 md:mb-0">
                            <div class="flex items-center w-full">
                                <span class="md:hidden bg-slate-100 text-slate-600 w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold mr-3 shrink-0">
                                    <?= $r['student_number'] ?>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-slate-800 whitespace-nowrap overflow-hidden text-ellipsis">
                                        <?= $r['prefix'] . $r['first_name'] . ' ' . $r['last_name'] ?>
                                    </p>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-tight"><?= $r['student_code'] ?></p>
                                </div>
                            </div>
                        </td>

                        <!-- Status Buttons -->
                        <td class="px-0 md:px-6 py-0 md:py-4">
                            <div class="grid grid-cols-4 md:flex items-center justify-center gap-1.5 md:gap-2">
                                <button onclick="updateAttendance(<?= $r['student_id'] ?>, 'present', this)" 
                                        class="status-btn py-2 md:px-4 md:py-1.5 rounded-lg text-[10px] md:text-xs font-bold transition-all border <?= $r['status'] == 'present' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md' : 'bg-white text-slate-400 border-slate-200 hover:border-emerald-500 hover:text-emerald-500' ?>">
                                    มา
                                </button>
                                <button onclick="updateAttendance(<?= $r['student_id'] ?>, 'late', this)" 
                                        class="status-btn py-2 md:px-4 md:py-1.5 rounded-lg text-[10px] md:text-xs font-bold transition-all border <?= $r['status'] == 'late' ? 'bg-amber-500 text-white border-amber-500 shadow-md' : 'bg-white text-slate-400 border-slate-200 hover:border-amber-500 hover:text-amber-500' ?>">
                                    สาย
                                </button>
                                <button onclick="updateAttendance(<?= $r['student_id'] ?>, 'absent', this)" 
                                        class="status-btn py-2 md:px-4 md:py-1.5 rounded-lg text-[10px] md:text-xs font-bold transition-all border <?= $r['status'] == 'absent' ? 'bg-red-500 text-white border-red-500 shadow-md' : 'bg-white text-slate-400 border-slate-200 hover:border-red-500 hover:text-red-500' ?>">
                                    ขาด
                                </button>
                                <button onclick="updateAttendance(<?= $r['student_id'] ?>, 'leave', this)" 
                                        class="status-btn py-2 md:px-4 md:py-1.5 rounded-lg text-[10px] md:text-xs font-bold transition-all border <?= $r['status'] == 'leave' ? 'bg-sky-500 text-white border-sky-500 shadow-md' : 'bg-white text-slate-400 border-slate-200 hover:border-sky-500 hover:text-sky-500' ?>">
                                    ลา
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<input type="hidden" id="csrf_token" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const SESSION_ID = <?= $session['id'] ?>;
const BASE_URL = '<?= $BASE ?>';

function updateStats() {
    let present = 0, late = 0, absent = 0;
    document.querySelectorAll('.status-btn').forEach(btn => {
        if (btn.classList.contains('bg-emerald-600')) present++;
        if (btn.classList.contains('bg-amber-500')) late++;
        if (btn.classList.contains('bg-red-500')) absent++;
    });
    document.getElementById('stat-present').textContent = present;
    document.getElementById('stat-late').textContent = late;
    document.getElementById('stat-absent').textContent = absent;
}

updateStats();

// Barcode Scanning Logic
let isScanMode = false;
const scannerInput = document.getElementById('barcodeScanner');
const scanBtn = document.getElementById('scanModeBtn');
const scanStatus = document.getElementById('scanModeStatus');

function toggleScanMode() {
    isScanMode = !isScanMode;
    if (isScanMode) {
        scanBtn.classList.replace('bg-indigo-50', 'bg-indigo-600');
        scanBtn.classList.replace('text-indigo-600', 'text-white');
        scanStatus.textContent = 'เปิดอยู่';
        scanStatus.classList.replace('text-slate-400', 'text-indigo-200');
        scannerInput.focus();
        
        Swal.fire({
            title: 'โหมดแสกนเปิดอยู่',
            text: 'สามารถใช้เครื่องยิงบาร์โค้ดได้ทันที',
            icon: 'info',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
        });
    } else {
        scanBtn.classList.replace('bg-indigo-600', 'bg-indigo-50');
        scanBtn.classList.replace('text-white', 'text-indigo-600');
        scanStatus.textContent = 'ปิดอยู่';
        scanStatus.classList.replace('text-indigo-200', 'text-slate-400');
    }
}

// Keep focus on scanner input if mode is active
document.addEventListener('click', () => {
    if (isScanMode) scannerInput.focus();
});

function translateThaiToEn(text) {
    const map = {
        'ๅ': '1', '/': '2', '-': '3', 'ภ': '4', 'ถ': '5', 'ุ': '6', 'ึ': '7', 'ค': '8', 'ต': '9', 'จ': '0',
        'ๆ': '1', '๑': '2', '๒': '3', '๓': '4', '๔': '5', 'ู': '6', '฿': '7', '๕': '8', '๖': '9', '๗': '0'
    };
    return text.split('').map(c => map[c] || c).join('');
}

scannerInput.addEventListener('keypress', async (e) => {
    if (e.key === 'Enter') {
        let studentCode = scannerInput.value.trim();
        scannerInput.value = ''; // Clear immediately
        
        // แปลงภาษาไทยเป็นเลขอังกฤษ (เผื่อลืมเปลี่ยนภาษาเครื่องยิง)
        studentCode = translateThaiToEn(studentCode);
        
        if (studentCode) {
            await handleScan(studentCode);
        }
    }
});

async function handleScan(code) {
    const token = document.getElementById('csrf_token').value;
    
    try {
        const response = await fetch(`${BASE_URL}/admin/attendance/scan/${SESSION_ID}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ student_code: code })
        });
        
        const result = await response.json();
        if (result.success) {
            // Find student row and update UI
            const rows = document.querySelectorAll('tbody tr');
            let found = false;
            rows.forEach(row => {
                const codeText = row.querySelector('.text-\\[10px\\]').textContent.trim();
                if (codeText === code) {
                    const presentBtn = row.querySelector('.status-btn:nth-child(1)');
                    updateAttendanceUI(row, presentBtn, 'present');
                    found = true;
                    
                    // Visual feedback on the row
                    row.classList.add('bg-emerald-50');
                    setTimeout(() => row.classList.remove('bg-emerald-50'), 2000);
                }
            });
            
            updateStats();
            
            // Audio/Visual Feedback
            Swal.fire({
                title: result.message,
                icon: 'success',
                toast: true,
                position: 'top-end',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                title: 'ไม่สำเร็จ',
                text: result.message,
                icon: 'error',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
        }
    } catch (error) { console.error(error); }
}

function updateAttendanceUI(row, btn, status) {
    row.querySelectorAll('.status-btn').forEach(b => {
        b.className = 'status-btn px-4 py-1.5 rounded-lg text-xs font-bold transition-all border bg-white text-slate-400 border-slate-200 hover:opacity-80';
        if (b.innerText.trim() === 'มา') b.classList.add('hover:border-emerald-500', 'hover:text-emerald-500');
        if (b.innerText.trim() === 'สาย') b.classList.add('hover:border-amber-500', 'hover:text-amber-500');
        if (b.innerText.trim() === 'ขาด') b.classList.add('hover:border-red-500', 'hover:text-red-500');
        if (b.innerText.trim() === 'ลา') b.classList.add('hover:border-sky-500', 'hover:text-sky-500');
    });
    
    if (status === 'present') btn.className = 'status-btn px-4 py-1.5 rounded-lg text-xs font-bold transition-all border bg-emerald-600 text-white border-emerald-600 shadow-md';
    if (status === 'late') btn.className = 'status-btn px-4 py-1.5 rounded-lg text-xs font-bold transition-all border bg-amber-500 text-white border-amber-500 shadow-md';
    if (status === 'absent') btn.className = 'status-btn px-4 py-1.5 rounded-lg text-xs font-bold transition-all border bg-red-500 text-white border-red-500 shadow-md';
    if (status === 'leave') btn.className = 'status-btn px-4 py-1.5 rounded-lg text-xs font-bold transition-all border bg-sky-500 text-white border-sky-500 shadow-md';
}

// Update the manual check to use the same UI update logic
async function updateAttendance(studentId, status, btn) {
    const token = document.getElementById('csrf_token').value;
    
    try {
        const response = await fetch(`${BASE_URL}/admin/attendance/check/${SESSION_ID}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ student_id: studentId, status: status, method: 'manual' })
        });
        
        const result = await response.json();
        if (result.success) {
            updateAttendanceUI(btn.closest('tr'), btn, status);
            updateStats();
        }
    } catch (error) { console.error(error); }
}

async function deleteSession(id) {
    const token = document.getElementById('csrf_token').value;
    const result = await Swal.fire({
        title: 'ยืนยันการลบบันทึกนี้?',
        text: "ข้อมูลการเช็คชื่อทั้งหมดในรอบนี้จะถูกลบถาวร!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'ใช่, ลบเลย',
        cancelButtonText: 'ยกเลิก'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`${BASE_URL}/admin/attendance/delete/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token }
            });
            const res = await response.json();
            if (res.success) {
                Swal.fire('สำเร็จ', res.message, 'success').then(() => {
                    window.location.href = `${BASE_URL}/admin/classrooms/show/<?= $session['classroom_id'] ?>`;
                });
            }
        } catch (e) {
            Swal.fire('ผิดพลาด', 'ไม่สามารถลบข้อมูลได้', 'error');
        }
    }
}

updateStats();
</script>

<?php
$content = ob_get_clean();
include ROOT . '/views/layouts/admin.php';
?>
