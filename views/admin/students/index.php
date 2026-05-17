<?php
/**
 * @var array  $students
 * @var string $search
 */
$BASE = $_ENV['APP_URL'] ?? '';
ob_start();
?>

<!-- Breadcrumbs -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="/CM_System/admin/dashboard" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-all">
                <i class="fa-solid fa-house mr-2 text-xs"></i> แดชบอร์ด
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fa-solid fa-chevron-right text-slate-300 text-[10px] mx-2"></i>
                <a href="/CM_System/admin/apps" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-all">
                    ศูนย์รวมแอป
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fa-solid fa-chevron-right text-slate-300 text-[10px] mx-2"></i>
                <span class="text-sm font-bold text-slate-800 tracking-tight">จัดการข้อมูลนักเรียน</span>
            </div>
        </li>
    </ol>
</nav>

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">จัดการข้อมูลนักเรียน</h1>
        <p class="text-slate-500 text-sm">ดูข้อมูล แก้ไข และจัดการรายชื่อนักเรียนทั้งหมดในระบบ</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="openCreateModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center shadow-sm">
            <i class="fa-solid fa-plus mr-2"></i> เพิ่มนักเรียน
        </button>
        <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center border border-slate-200">
            <i class="fa-solid fa-file-import mr-2"></i> นำเข้า CSV
        </button>
        <a href="<?= $BASE ?>/admin/students/export" class="bg-white hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center border border-slate-200">
            <i class="fa-solid fa-file-export mr-2"></i> ส่งออก CSV
        </a>
    </div>
</div>

<!-- Search & Filters -->
<div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6">
    <form action="" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="ค้นหาด้วยรหัส, ชื่อ หรือชั้นเรียน..." class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <button type="submit" class="bg-slate-800 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-slate-900 transition-all">
            ค้นหา
        </button>
    </form>
</div>

<!-- Student Table -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
        <span class="text-sm text-slate-500">ทั้งหมด <strong class="text-slate-800"><?= count($students) ?></strong> คน</span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">รหัสนักเรียน</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ชื่อ-นามสกุล</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ชั้นเรียน/เลขที่</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">สถานะ</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">จัดการ</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-400 italic">
                            <i class="fa-solid fa-inbox text-4xl mb-3 block"></i>
                            ไม่พบข้อมูลนักเรียน
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $student): ?>
                        <tr class="hover:bg-slate-50 transition-all">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                <?= htmlspecialchars($student['student_code']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs mr-3">
                                        <?= mb_substr($student['first_name'], 0, 1) ?>
                                    </div>
                                    <div class="text-sm font-medium text-slate-900">
                                        <?= htmlspecialchars($student['prefix'] . $student['first_name'] . ' ' . $student['last_name']) ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?= htmlspecialchars($student['class_level']) ?> / <?= htmlspecialchars($student['student_number']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">ปกติ</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="openEditModal(<?= $student['id'] ?>)" class="text-indigo-600 hover:text-indigo-900 mr-3" title="แก้ไข"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button onclick="deleteStudent(<?= $student['id'] ?>)" class="text-red-600 hover:text-red-900" title="ลบ"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Import Modal Code (Keep existing) -->
<div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="document.getElementById('importModal').classList.add('hidden')"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative z-10 animate__animated animate__fadeInUp" style="animation-duration: 0.3s;">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-file-import text-indigo-600 mr-2"></i> นำเข้าข้อมูลนักเรียน (CSV)</h3>
                <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="p-6">
                <form id="importForm" enctype="multipart/form-data">
                    <?= csrfField() ?>
                    <div id="dropZone" class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center cursor-pointer hover:border-indigo-500 hover:bg-indigo-50 transition-all">
                        <i class="fa-solid fa-cloud-arrow-up text-4xl text-slate-400 mb-3 block" id="uploadIcon"></i>
                        <p class="text-sm text-slate-600 mb-2" id="dropText">ลากไฟล์มาวางที่นี่ หรือ คลิกเพื่อเลือกไฟล์</p>
                        <p class="text-xs text-slate-400" id="fileName">ยังไม่ได้เลือกไฟล์</p>
                        <input type="file" name="csv_file" id="csvFileInput" accept=".csv" class="hidden">
                    </div>
                    <div id="progressBar" class="hidden mt-4">
                        <div class="w-full bg-slate-200 rounded-full h-2"><div id="progressFill" class="bg-indigo-600 h-2 rounded-full transition-all duration-500" style="width: 0%"></div></div>
                        <p class="text-xs text-slate-500 mt-1 text-center" id="progressText">กำลังนำเข้า...</p>
                    </div>
                    <div id="importResult" class="hidden mt-4 p-4 rounded-lg text-sm"></div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between bg-slate-50 rounded-b-2xl">
                <a href="<?= $BASE ?>/admin/students/template" class="text-sm text-emerald-600 hover:text-emerald-800 font-medium"><i class="fa-solid fa-download mr-1"></i> เทมเพลต</a>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">ยกเลิก</button>
                    <button type="button" id="btnImport" onclick="startImport()" disabled class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">เริ่มนำเข้า</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Modal (Add/Edit) -->
<div id="studentModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeStudentModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative z-10 animate__animated animate__fadeInUp" style="animation-duration: 0.3s;">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800" id="modalTitle">เพิ่มข้อมูลนักเรียน</h3>
                <button onclick="closeStudentModal()" class="text-slate-400 hover:text-slate-600 text-xl"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="studentForm" class="p-6">
                <?= csrfField() ?>
                <input type="hidden" name="student_id" id="student_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">รหัสนักเรียน *</label>
                        <input type="text" name="student_code" id="student_code" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">คำนำหน้า</label>
                        <select name="prefix" id="prefix" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                            <option value="เด็กชาย">เด็กชาย</option>
                            <option value="เด็กหญิง">เด็กหญิง</option>
                            <option value="นาย">นาย</option>
                            <option value="นางสาว">นางสาว</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">เลขบัตรประชาชน</label>
                        <input type="text" name="national_id" id="national_id" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">ชื่อ *</label>
                        <input type="text" name="first_name" id="first_name" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">นามสกุล *</label>
                        <input type="text" name="last_name" id="last_name" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">ชั้นเรียน *</label>
                        <input type="text" name="class_level" id="class_level" required placeholder="เช่น ม.1/1" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">เลขที่</label>
                        <input type="number" name="student_number" id="student_number" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="closeStudentModal()" class="flex-1 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">ยกเลิก</button>
                    <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-md">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const BASE_URL = '<?= $BASE ?>';
const csvInput = document.getElementById('csvFileInput');
const btnImport = document.getElementById('btnImport');
document.getElementById('dropZone').addEventListener('click', () => csvInput.click());
csvInput.addEventListener('change', (e) => { if (e.target.files.length > 0) handleFileSelect(e.target.files[0]); });
function handleFileSelect(file) {
    document.getElementById('uploadIcon').className = 'fa-solid fa-file-csv text-4xl text-emerald-500 mb-3 block';
    document.getElementById('dropText').textContent = 'เลือกไฟล์แล้ว:';
    document.getElementById('fileName').textContent = file.name;
    btnImport.disabled = false;
}
async function startImport() {
    const formData = new FormData(document.getElementById('importForm'));
    formData.append('csv_file', csvInput.files[0]);
    document.getElementById('progressBar').classList.remove('hidden');
    try {
        const response = await fetch(BASE_URL + '/admin/students/import', { method: 'POST', body: formData });
        const result = await response.json();
        if (result.success) {
            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: result.message }).then(() => window.location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message });
        }
    } catch (error) { 
        console.error(error); 
    }
}

// --- Student CRUD Logic ---
const studentModal = document.getElementById('studentModal');
const studentForm = document.getElementById('studentForm');

function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'เพิ่มข้อมูลนักเรียน';
    studentForm.reset();
    document.getElementById('student_id').value = '';
    studentModal.classList.remove('hidden');
}

function closeStudentModal() {
    studentModal.classList.add('hidden');
}

async function openEditModal(id) {
    document.getElementById('modalTitle').textContent = 'แก้ไขข้อมูลนักเรียน';
    studentModal.classList.remove('hidden');
    try {
        const response = await fetch(BASE_URL + '/admin/students/edit/' + id);
        const result = await response.json();
        if (result.success) {
            const s = result.data;
            document.getElementById('student_id').value = s.id;
            document.getElementById('student_code').value = s.student_code;
            document.getElementById('national_id').value = s.national_id || '';
            document.getElementById('prefix').value = s.prefix;
            document.getElementById('first_name').value = s.first_name;
            document.getElementById('last_name').value = s.last_name;
            document.getElementById('class_level').value = s.class_level;
            document.getElementById('student_number').value = s.student_number;
        }
    } catch (error) { console.error(error); }
}

studentForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('student_id').value;
    const url = id ? BASE_URL + '/admin/students/update/' + id : BASE_URL + '/admin/students/store';
    const formData = new FormData(studentForm);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.success) {
            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: result.message }).then(() => window.location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message });
        }
    } catch (error) { console.error(error); }
});

function deleteStudent(id) {
    const token = document.querySelector('input[name="csrf_token"]').value;
    Swal.fire({
        title: 'ยืนยันการลบ?',
        text: "คุณจะไม่สามารถกู้คืนข้อมูลนี้ได้!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(BASE_URL + '/admin/students/delete/' + id, { 
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });
                const res = await response.json();
                if (res.success) {
                    Swal.fire('ลบแล้ว!', res.message, 'success').then(() => window.location.reload());
                } else {
                    Swal.fire('ผิดพลาด!', res.message, 'error');
                }
            } catch (error) { 
                console.error(error);
                Swal.fire('ผิดพลาด!', 'ไม่สามารถลบข้อมูลได้', 'error');
            }
        }
    });
}
</script>

<?php
$content = ob_get_clean();
include ROOT . '/views/layouts/admin.php';
?>
