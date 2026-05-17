<?php
/**
 * Select Students for Cards
 * @var array $classroom
 * @var array $students
 */
ob_start();
?>

<div class="mb-8">
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li><a href="/CM_System/admin/cards" class="text-sm font-medium text-slate-400 hover:text-rose-600">บัตรนักเรียน</a></li>
            <li><i class="fa-solid fa-chevron-right text-slate-300 text-[10px] mx-2"></i></li>
            <li class="text-sm font-bold text-slate-800"><?= htmlspecialchars($classroom['subject_name']) ?></li>
        </ol>
    </nav>
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-black text-slate-800">เลือกนักเรียน</h1>
            <p class="text-slate-500">เลือกรายชื่อนักเรียนที่ต้องการสร้างบัตรประจำตัว</p>
        </div>
        <button type="submit" form="cardForm" class="bg-slate-900 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-slate-200 hover:bg-black transition-all flex items-center">
            <i class="fa-solid fa-wand-magic-sparkles mr-2"></i> สร้างบัตรนักเรียน
        </button>
    </div>
</div>

<form id="cardForm" action="/CM_System/admin/cards/generate" method="POST">
    <?= csrfField() ?>
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-400 uppercase text-[10px] tracking-widest font-black">
                    <th class="px-8 py-5 w-16">
                        <input type="checkbox" id="selectAll" class="w-5 h-5 rounded border-slate-200 text-rose-600 focus:ring-rose-500">
                    </th>
                    <th class="px-6 py-5 w-16 text-center">เลขที่</th>
                    <th class="px-6 py-5">ชื่อ-นามสกุล</th>
                    <th class="px-6 py-5 text-center">รหัสนักเรียน</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($students as $row): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-8 py-5">
                            <input type="checkbox" name="student_ids[]" value="<?= $row['id'] ?>" class="student-checkbox w-5 h-5 rounded border-slate-200 text-rose-600 focus:ring-rose-500">
                        </td>
                        <td class="px-6 py-5 text-center font-bold text-slate-400">#<?= $row['student_number'] ?></td>
                        <td class="px-6 py-5">
                            <p class="font-bold text-slate-700"><?= $row['prefix'] . $row['first_name'] . ' ' . $row['last_name'] ?></p>
                        </td>
                        <td class="px-6 py-5 text-center font-bold text-slate-500"><?= $row['student_code'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</form>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.student-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>

<?php
$content = ob_get_clean();
include ROOT . '/views/layouts/admin.php';
?>
