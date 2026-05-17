<?php
/**
 * Reports Index - Select Classroom
 * @var array $classrooms
 */
ob_start();
?>

<!-- Breadcrumbs -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="<?= $_ENV['APP_URL'] ?? '' ?>/admin/dashboard" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-all">
                <i class="fa-solid fa-house mr-2 text-xs"></i> แดชบอร์ด
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fa-solid fa-chevron-right text-slate-300 text-[10px] mx-2"></i>
                <a href="<?= $_ENV['APP_URL'] ?? '' ?>/admin/apps" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-all">
                    ศูนย์รวมแอป
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fa-solid fa-chevron-right text-slate-300 text-[10px] mx-2"></i>
                <span class="text-sm font-bold text-slate-800 tracking-tight">รายงานสรุปผล</span>
            </div>
        </li>
    </ol>
</nav>

<div class="mb-10">
    <h1 class="text-3xl font-black text-slate-800 mb-2">รายงานสรุปผล</h1>
    <p class="text-slate-500">เลือกชั้นเรียนที่ต้องการดูรายงานสรุปการเข้าเรียนและคะแนนงานมอบหมาย</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($classrooms)): ?>
        <div class="col-span-full bg-white rounded-[2.5rem] p-16 text-center border-2 border-dashed border-slate-100">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                <i class="fa-solid fa-folder-open text-5xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-400">ยังไม่มีชั้นเรียน</h3>
            <p class="text-slate-400 text-sm mt-2">สร้างชั้นเรียนก่อนเพื่อดูรายงาน</p>
        </div>
    <?php else: ?>
        <?php foreach ($classrooms as $row): ?>
            <a href="/CM_System/admin/reports/classroom/<?= $row['id'] ?>" class="group bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 transition-all hover:shadow-xl hover:-translate-y-1">
                <div class="flex items-start justify-between mb-6">
                    <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-chart-bar text-2xl"></i>
                    </div>
                    <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[10px] font-black rounded-lg uppercase tracking-widest"><?= $row['room_code'] ?></span>
                </div>
                <h3 class="text-lg font-black text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors"><?= htmlspecialchars($row['subject_name']) ?></h3>
                <p class="text-xs text-slate-400 mb-6"><?= htmlspecialchars($row['subject_code'] ?: 'ไม่มีรหัสวิชา') ?></p>
                
                <div class="flex items-center text-xs font-bold text-indigo-600">
                    ดูรายงานฉบับเต็ม <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include ROOT . '/views/layouts/admin.php';
?>
