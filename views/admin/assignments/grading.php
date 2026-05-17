<?php
/**
 * Grading Interface
 * @var array $assignment
 * @var array $classroom
 * @var array $submissions
 */
ob_start();
?>

<div class="mb-8">
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li><a href="/CM_System/admin/assignments" class="text-sm font-medium text-slate-400 hover:text-indigo-600">งานมอบหมาย</a></li>
            <li><i class="fa-solid fa-chevron-right text-slate-300 text-[10px] mx-2"></i></li>
            <li><a href="/CM_System/admin/assignments/classroom/<?= $classroom['id'] ?>" class="text-sm font-medium text-slate-400 hover:text-indigo-600"><?= htmlspecialchars($classroom['subject_name']) ?></a></li>
            <li><i class="fa-solid fa-chevron-right text-slate-300 text-[10px] mx-2"></i></li>
            <li class="text-sm font-bold text-slate-800">ให้คะแนน</li>
        </ol>
    </nav>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800"><?= htmlspecialchars($assignment['title']) ?></h1>
            <p class="text-slate-500">คะแนนเต็ม: <span class="font-bold text-indigo-600"><?= $assignment['max_score'] ?></span> | กำหนดส่ง: <?= date('d/m/Y', strtotime($assignment['due_date'])) ?></p>
        </div>
        <div class="bg-indigo-50 px-6 py-3 rounded-2xl border border-indigo-100 flex items-center">
            <i class="fa-solid fa-circle-info text-indigo-500 mr-3"></i>
            <span class="text-xs font-bold text-indigo-700 uppercase tracking-widest">ระบบบันทึกคะแนนอัตโนมัติ (Auto-save)</span>
        </div>
    </div>
</div>

<!-- Table Header for Desktop -->
<div class="hidden md:grid grid-cols-12 gap-4 px-8 py-4 bg-slate-800 text-white rounded-t-[2rem] font-bold text-xs uppercase tracking-widest">
    <div class="col-span-1 text-center">เลขที่</div>
    <div class="col-span-2">รหัสนักเรียน</div>
    <div class="col-span-4">ชื่อ-นามสกุล</div>
    <div class="col-span-2 text-center">สถานะ</div>
    <div class="col-span-3 text-center">คะแนน (Max: <?= $assignment['max_score'] ?>)</div>
</div>

<div class="space-y-3 md:space-y-0">
    <?php foreach ($submissions as $row): ?>
        <div class="bg-white md:grid md:grid-cols-12 md:gap-4 md:px-8 py-4 md:py-6 md:items-center border-b border-slate-100 last:rounded-b-[2rem] last:border-0 hover:bg-slate-50 transition-colors shadow-sm md:shadow-none rounded-2xl md:rounded-none p-4 md:p-0">
            
            <!-- Mobile Header -->
            <div class="flex md:hidden justify-between items-center mb-4 pb-4 border-b border-slate-50">
                <span class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center font-black text-slate-500">#<?= $row['student_number'] ?></span>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest"><?= $row['student_code'] ?></span>
            </div>

            <div class="hidden md:block col-span-1 text-center font-black text-slate-400">#<?= $row['student_number'] ?></div>
            <div class="hidden md:block col-span-2 text-sm font-bold text-slate-500"><?= $row['student_code'] ?></div>
            
            <div class="col-span-12 md:col-span-4">
                <p class="font-bold text-slate-800"><?= $row['prefix'] . $row['first_name'] . ' ' . $row['last_name'] ?></p>
            </div>

            <div class="col-span-6 md:col-span-2 flex justify-center mt-4 md:mt-0">
                <select onchange="updateStatus(this, <?= $row['student_id'] ?>)" 
                        class="w-full md:w-auto px-3 py-2 text-[10px] font-bold uppercase tracking-widest rounded-lg border-2 <?= $row['status'] == 'graded' ? 'border-emerald-100 bg-emerald-50 text-emerald-600' : ($row['status'] == 'submitted' ? 'border-blue-100 bg-blue-50 text-blue-600' : 'border-slate-100 bg-slate-50 text-slate-400') ?> focus:outline-none">
                    <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>ยังไม่ส่ง (Pending)</option>
                    <option value="submitted" <?= $row['status'] == 'submitted' ? 'selected' : '' ?>>ส่งแล้ว (Submitted)</option>
                    <option value="graded" <?= $row['status'] == 'graded' ? 'selected' : '' ?>>ตรวจแล้ว (Graded)</option>
                </select>
            </div>

            <div class="col-span-6 md:col-span-3 flex justify-center mt-4 md:mt-0 relative">
                <input type="number" 
                       value="<?= $row['score'] ?>" 
                       max="<?= $assignment['max_score'] ?>"
                       onchange="saveGrade(<?= $row['student_id'] ?>, this.value)"
                       placeholder="0.00"
                       class="w-full md:w-24 px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none text-center font-black text-slate-700 transition-all">
                <div class="absolute right-3 top-1/2 -translate-y-1/2 md:hidden text-[10px] font-bold text-slate-300">คะแนน</div>
            </div>

        </div>
    <?php endforeach; ?>
</div>

<script>
async function saveGrade(studentId, score) {
    const data = {
        assignment_id: <?= $assignment['id'] ?>,
        student_id: studentId,
        score: score,
        status: 'graded'
    };
    
    try {
        const response = await fetch('/CM_System/admin/assignments/save-grade', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        // Visual feedback (optional)
        if(result.success) {
            console.log('Saved student', studentId);
        }
    } catch (error) {
        console.error(error);
    }
}

async function updateStatus(el, studentId) {
    const status = el.value;
    const scoreEl = el.closest('.bg-white').querySelector('input[type="number"]');
    const score = scoreEl.value;

    const data = {
        assignment_id: <?= $assignment['id'] ?>,
        student_id: studentId,
        score: score,
        status: status
    };

    // Update style
    el.className = `w-full md:w-auto px-3 py-2 text-[10px] font-bold uppercase tracking-widest rounded-lg border-2 focus:outline-none `;
    if(status === 'graded') el.classList.add('border-emerald-100', 'bg-emerald-50', 'text-emerald-600');
    else if(status === 'submitted') el.classList.add('border-blue-100', 'bg-blue-50', 'text-blue-600');
    else el.classList.add('border-slate-100', 'bg-slate-50', 'text-slate-400');

    try {
        await fetch('/CM_System/admin/assignments/save-grade', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
    } catch (error) { console.error(error); }
}
</script>

<?php
$content = ob_get_clean();
include ROOT . '/views/layouts/admin.php';
?>
