<?php
/**
 * Classroom Assignments List
 * @var array $classroom
 * @var array $assignments
 */
ob_start();
?>

<div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
    <div>
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li><a href="/CM_System/admin/assignments" class="text-sm font-medium text-slate-400 hover:text-indigo-600">งานมอบหมาย</a></li>
                <li><i class="fa-solid fa-chevron-right text-slate-300 text-[10px] mx-2"></i></li>
                <li class="text-sm font-bold text-slate-800"><?= htmlspecialchars($classroom['subject_name']) ?></li>
            </ol>
        </nav>
        <h1 class="text-3xl font-black text-slate-800">รายการงานมอบหมาย</h1>
    </div>
    <button onclick="openModal('addAssignmentModal')" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all flex items-center justify-center">
        <i class="fa-solid fa-plus mr-2"></i> มอบหมายงานใหม่
    </button>
</div>

<div class="grid grid-cols-1 gap-4">
    <?php if (empty($assignments)): ?>
        <div class="bg-white rounded-[2.5rem] p-16 text-center border-2 border-dashed border-slate-100">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                <i class="fa-solid fa-clipboard-list text-5xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-400">ยังไม่มีงานที่มอบหมาย</h3>
            <p class="text-slate-400 text-sm mt-2">กดปุ่ม "มอบหมายงานใหม่" เพื่อเริ่มสร้างภารกิจแรก</p>
        </div>
    <?php else: ?>
        <?php foreach ($assignments as $task): ?>
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 transition-all hover:shadow-md">
                <div class="flex items-start gap-6">
                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex flex-col items-center justify-center shrink-0 border border-slate-100">
                        <span class="text-[10px] font-black text-slate-400 uppercase"><?= date('M', strtotime($task['due_date'])) ?></span>
                        <span class="text-xl font-black text-slate-800 leading-tight"><?= date('d', strtotime($task['due_date'])) ?></span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 mb-1"><?= htmlspecialchars($task['title']) ?></h3>
                        <p class="text-sm text-slate-400 line-clamp-1 mb-4"><?= htmlspecialchars($task['description'] ?: 'ไม่มีคำอธิบาย') ?></p>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-bold rounded-full uppercase">คะแนนเต็ม: <?= $task['max_score'] ?></span>
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded-full uppercase">
                                ส่งแล้ว: <?= $task['submitted_count'] ?> / <?= $task['total_students'] ?> คน
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="/CM_System/admin/assignments/grading/<?= $task['id'] ?>" class="flex-1 md:flex-none bg-slate-800 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-slate-900 transition-all text-center">
                        <i class="fa-solid fa-star mr-2"></i> ให้คะแนน
                    </a>
                    <button onclick="deleteAssignment(<?= $task['id'] ?>)" class="w-12 h-12 flex items-center justify-center text-red-400 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal: Add Assignment -->
<div id="addAssignmentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('addAssignmentModal')"></div>
        <div class="relative bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl p-8 md:p-10">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-black text-slate-800">มอบหมายงานใหม่</h2>
                <button onclick="closeModal('addAssignmentModal')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-2xl"></i></button>
            </div>
            
            <form id="addAssignmentForm" class="space-y-6">
                <input type="hidden" name="classroom_id" value="<?= $classroom['id'] ?>">
                
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">หัวข้อคำสั่งงาน</label>
                    <input type="text" name="title" required placeholder="เช่น ใบงานที่ 1..." 
                           class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-100 focus:outline-none font-bold text-slate-700">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">คำอธิบายเพิ่มเติม</label>
                    <textarea name="description" rows="3" placeholder="ระบุรายละเอียดงาน..." 
                              class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-100 focus:outline-none text-slate-600"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">คะแนนเต็ม</label>
                        <input type="number" name="max_score" value="10" required 
                               class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-100 focus:outline-none font-bold text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">กำหนดส่ง</label>
                        <input type="date" name="due_date" value="<?= date('Y-m-d') ?>" required 
                               class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-100 focus:outline-none font-bold text-slate-700">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center justify-center">
                        <i class="fa-solid fa-paper-plane mr-2"></i> ยืนยันการมอบหมาย
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.body.style.overflow = 'auto'; }

document.getElementById('addAssignmentForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.from_rows = {};
    formData.forEach((value, key) => data[key] = value);

    try {
        const response = await fetch('/CM_System/admin/assignments/store', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.success) {
            location.reload();
        } else {
            alert(result.message);
        }
    } catch (error) {
        alert('เกิดข้อผิดพลาดในการบันทึก');
    }
});

async function deleteAssignment(id) {
    if (!confirm('ยืนยันการลบงานมอบหมายนี้?')) return;
    try {
        const response = await fetch(`/CM_System/admin/assignments/delete/${id}`, { 
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        const result = await response.json();
        if (result.success) location.reload();
    } catch (error) {
        alert('เกิดข้อผิดพลาด');
    }
}
</script>

<?php
$content = ob_get_clean();
include ROOT . '/views/layouts/admin.php';
?>
