<?php
/**
 * @var array  $classrooms
 * @var string $search
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
                <a href="<?= $BASE ?>/admin/apps" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-all">
                    ศูนย์รวมแอป
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fa-solid fa-chevron-right text-slate-300 text-[10px] mx-2"></i>
                <span class="text-sm font-bold text-slate-800 tracking-tight">จัดการชั้นเรียน</span>
            </div>
        </li>
    </ol>
</nav>

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">จัดการชั้นเรียน</h1>
        <p class="text-slate-500 text-sm">สร้างและจัดการห้องเรียน รายวิชา และรหัสเข้าห้องเรียน</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center shadow-sm">
            <i class="fa-solid fa-plus mr-2"></i> สร้างห้องเรียนใหม่
        </button>
    </div>
</div>

<!-- Search Area -->
<div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6">
    <form action="" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="ค้นหาชื่อวิชา, รหัสวิชา หรือรหัสห้อง..." class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <button type="submit" class="bg-slate-800 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-slate-900 transition-all">
            ค้นหา
        </button>
    </form>
</div>

<!-- Classrooms List -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($classrooms)): ?>
        <div class="col-span-full bg-white py-12 rounded-xl border-2 border-dashed border-slate-200 text-center">
            <i class="fa-solid fa-chalkboard text-4xl text-slate-300 mb-3 block"></i>
            <p class="text-slate-500 italic">ยังไม่มีข้อมูลชั้นเรียนในระบบ</p>
            <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="mt-4 text-indigo-600 font-medium hover:underline">สร้างห้องเรียนแรกของคุณที่นี่</button>
        </div>
    <?php else: ?>
        <?php foreach ($classrooms as $room): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition-all group">
                <div class="h-32 bg-slate-100 relative overflow-hidden">
                    <?php if ($room['cover_image']): ?>
                        <img src="<?= htmlspecialchars($room['cover_image']) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <i class="fa-solid fa-book-bookmark text-white text-4xl opacity-20"></i>
                        </div>
                    <?php endif; ?>
                    <div class="absolute top-3 right-3">
                        <span class="bg-white/90 backdrop-blur px-2 py-1 rounded-md text-[10px] font-bold text-indigo-600 shadow-sm uppercase tracking-wider">
                            ID: <?= $room['room_code'] ?>
                        </span>
                    </div>
                </div>
                <div class="p-5">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">
                            <?= htmlspecialchars($room['subject_name']) ?>
                        </h3>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-tight">
                            รหัสวิชา: <?= htmlspecialchars($room['subject_code'] ?? '-') ?>
                        </p>
                    </div>
                    
                    <div class="flex items-center justify-between text-xs text-slate-600 mb-5 pb-4 border-b border-slate-100">
                        <div class="flex items-center">
                            <i class="fa-solid fa-user-tie mr-2 text-slate-400"></i>
                            <?= htmlspecialchars($room['admin_name'] ?? 'Admin') ?>
                        </div>
                        <div class="flex items-center">
                            <i class="fa-solid fa-clock mr-2 text-slate-400"></i>
                            <?= $room['total_periods'] ?> คาบ
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="<?= $BASE ?>/admin/classrooms/show/<?= $room['id'] ?>" class="flex-1 bg-indigo-50 text-indigo-600 text-center py-2 rounded-lg text-sm font-bold hover:bg-indigo-600 hover:text-white transition-all">
                            เข้าห้องเรียน
                        </a>
                        <button onclick="openEditModal(<?= $room['id'] ?>)" class="px-3 bg-slate-50 text-slate-500 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-all border border-slate-200" title="แก้ไข">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button onclick="deleteClassroom(<?= $room['id'] ?>)" class="px-3 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all border border-red-100" title="ลบ">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- ===================== -->
<!-- Create Classroom Modal -->
<!-- ===================== -->
<div id="createModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="document.getElementById('createModal').classList.add('hidden')"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative z-10 animate__animated animate__zoomIn" style="animation-duration: 0.3s;">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">สร้างห้องเรียนใหม่</h3>
                <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="createForm" class="p-6">
                <?= csrfField() ?>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">ชื่อวิชา / ชื่อห้องเรียน *</label>
                        <input type="text" name="subject_name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="เช่น วิทยาการคำนวณ">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">รหัสวิชา (ถ้ามี)</label>
                        <input type="text" name="subject_code" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="เช่น ว30101">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">จำนวนคาบเรียนทั้งหมด</label>
                        <input type="number" name="total_periods" value="40" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">ยกเลิก</button>
                    <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-md transition-all">สร้างห้องเรียน</button>
                </div>
            </form>
        </div>
    </div>
<!-- Edit Classroom Modal -->
<div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="document.getElementById('editModal').classList.add('hidden')"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative z-10 animate__animated animate__zoomIn" style="animation-duration: 0.3s;">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">แก้ไขข้อมูลห้องเรียน</h3>
                <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="editForm" class="p-6">
                <?= csrfField() ?>
                <input type="hidden" name="classroom_id" id="edit_classroom_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">ชื่อวิชา / ชื่อห้องเรียน *</label>
                        <input type="text" name="subject_name" id="edit_subject_name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">รหัสวิชา (ถ้ามี)</label>
                        <input type="text" name="subject_code" id="edit_subject_code" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">จำนวนคาบเรียนทั้งหมด</label>
                        <input type="number" name="total_periods" id="edit_total_periods" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="flex-1 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">ยกเลิก</button>
                    <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-md transition-all">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const BASE_URL = '<?= $BASE ?>';

// --- Classroom CRUD Logic ---
document.getElementById('createForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch(BASE_URL + '/admin/classrooms/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();

        if (result.success) {
            Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: 'รหัสห้องเรียน: ' + result.room_code }).then(() => window.location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message });
        }
    } catch (error) { console.error(error); }
});

async function openEditModal(id) {
    const modal = document.getElementById('editModal');
    modal.classList.remove('hidden');
    try {
        const response = await fetch(BASE_URL + '/admin/classrooms/edit/' + id);
        const result = await response.json();
        if (result.success) {
            const r = result.data;
            document.getElementById('edit_classroom_id').value = r.id;
            document.getElementById('edit_subject_name').value = r.subject_name;
            document.getElementById('edit_subject_code').value = r.subject_code || '';
            document.getElementById('edit_total_periods').value = r.total_periods;
        }
    } catch (error) { console.error(error); }
}

document.getElementById('editForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('edit_classroom_id').value;
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch(BASE_URL + '/admin/classrooms/update/' + id, {
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

function deleteClassroom(id) {
    const token = document.querySelector('input[name="csrf_token"]').value;
    Swal.fire({
        title: 'ยืนยันการลบ?',
        text: "นักเรียนและข้อมูลการเช็คชื่อทั้งหมดจะถูกลบไปด้วย!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'ลบเลย',
        cancelButtonText: 'ยกเลิก'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(BASE_URL + '/admin/classrooms/delete/' + id, { 
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token }
                });
                const res = await response.json();
                if (res.success) {
                    Swal.fire('ลบแล้ว!', res.message, 'success').then(() => window.location.reload());
                }
            } catch (error) { console.error(error); }
        }
    });
}
</script>

<?php
$content = ob_get_clean();
include ROOT . '/views/layouts/admin.php';
?>
