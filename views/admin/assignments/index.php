<?php
/**
 * Assignment Hub - Class Selection
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
                <span class="text-sm font-bold text-slate-800 tracking-tight">จัดการงานมอบหมาย</span>
            </div>
        </li>
    </ol>
</nav>

<div class="mb-8">
    <h1 class="text-3xl font-black text-slate-800">จัดการงานมอบหมาย</h1>
    <p class="text-slate-500">เลือกชั้นเรียนที่ต้องการจัดการภารกิจและคะแนน</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($classrooms)): ?>
        <div class="col-span-full bg-white rounded-3xl p-12 text-center border-2 border-dashed border-slate-200">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                <i class="fa-solid fa-chalkboard text-4xl"></i>
            </div>
            <p class="text-slate-500 font-medium">ยังไม่มีชั้นเรียนในระบบ</p>
            <a href="/CM_System/admin/classrooms" class="mt-4 inline-flex items-center text-indigo-600 font-bold hover:underline">
                ไปที่หน้าจัดการชั้นเรียน <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($classrooms as $room): ?>
            <a href="/CM_System/admin/assignments/classroom/<?= $room['id'] ?>" 
               class="group bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 transition-all hover:shadow-xl hover:-translate-y-2 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-full -mr-16 -mt-16 transition-all group-hover:bg-indigo-50"></div>
                
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-6 text-2xl group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <i class="fa-solid fa-book"></i>
                    </div>
                    
                    <h3 class="text-xl font-bold text-slate-800 mb-2 group-hover:text-indigo-600 transition-colors"><?= htmlspecialchars($room['subject_name']) ?></h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6"><?= htmlspecialchars($room['subject_code']) ?></p>
                    
                    <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                        <div class="flex items-center text-slate-500">
                            <i class="fa-solid fa-tasks mr-2"></i>
                            <span class="text-sm font-bold"><?= $room['assignment_count'] ?> งาน</span>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include ROOT . '/views/layouts/admin.php';
?>
