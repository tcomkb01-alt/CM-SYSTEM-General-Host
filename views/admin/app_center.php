<?php
/**
 * Application Center View
 */
ob_start();
?>

<!-- Breadcrumbs / Navigation -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="/CM_System/admin/dashboard" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-indigo-600 transition-all">
                <i class="fa-solid fa-house mr-2 text-xs"></i>
                แผงควบคุม
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fa-solid fa-chevron-right text-slate-300 text-[10px] mx-2"></i>
                <span class="text-sm font-bold text-slate-800">ศูนย์รวมแอปพลิเคชัน</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Header Area -->
<div class="mb-10 text-center md:text-left">
    <h1 class="text-3xl md:text-4xl font-bold text-slate-800 mb-2">แอปพลิเคชัน</h1>
    <p class="text-slate-500 text-sm md:text-base">เลือกเข้าใช้งานโมดูลต่าง ๆ ของระบบ</p>
</div>

<!-- App Grid -->
<div class="grid grid-cols-5 gap-3 md:gap-10">
    
    <!-- Module 1: จัดการข้อมูลนักเรียน -->
    <a href="/CM_System/admin/students" class="group flex flex-col items-center">
        <div class="w-14 h-14 sm:w-20 sm:h-20 md:w-28 md:h-28 bg-white rounded-xl sm:rounded-2xl md:rounded-3xl shadow-sm border-t-4 border-indigo-500 flex items-center justify-center mb-2 md:mb-4 transition-all group-hover:shadow-xl group-hover:-translate-y-2 relative overflow-hidden">
            <div class="absolute inset-0 bg-indigo-50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <i class="fa-solid fa-users text-xl sm:text-3xl md:text-5xl text-indigo-500 relative z-10"></i>
        </div>
        <span class="text-[9px] sm:text-xs md:text-lg text-slate-700 font-bold text-center leading-tight group-hover:text-indigo-600">ข้อมูลนักเรียน</span>
    </a>

    <!-- Module 2: จัดการชั้นเรียน -->
    <a href="/CM_System/admin/classrooms" class="group flex flex-col items-center">
        <div class="w-14 h-14 sm:w-20 sm:h-20 md:w-28 md:h-28 bg-white rounded-xl sm:rounded-2xl md:rounded-3xl shadow-sm border-t-4 border-purple-500 flex items-center justify-center mb-2 md:mb-4 transition-all group-hover:shadow-xl group-hover:-translate-y-2 relative overflow-hidden">
            <div class="absolute inset-0 bg-purple-50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <i class="fa-solid fa-chalkboard-user text-xl sm:text-3xl md:text-5xl text-purple-500 relative z-10"></i>
        </div>
        <span class="text-[9px] sm:text-xs md:text-lg text-slate-700 font-bold text-center leading-tight group-hover:text-purple-600">จัดการชั้นเรียน</span>
    </a>

    <!-- Module 3: งานมอบหมาย -->
    <a href="/CM_System/admin/assignments" class="group flex flex-col items-center">
        <div class="w-14 h-14 sm:w-20 sm:h-20 md:w-28 md:h-28 bg-white rounded-xl sm:rounded-2xl md:rounded-3xl shadow-sm border-t-4 border-amber-500 flex items-center justify-center mb-2 md:mb-4 transition-all group-hover:shadow-xl group-hover:-translate-y-2 relative overflow-hidden">
            <div class="absolute inset-0 bg-amber-50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <i class="fa-solid fa-book-open text-xl sm:text-3xl md:text-5xl text-amber-500 relative z-10"></i>
        </div>
        <span class="text-[9px] sm:text-xs md:text-lg text-slate-700 font-bold text-center leading-tight group-hover:text-amber-600">งานมอบหมาย</span>
    </a>

    <!-- Module 4: รายงาน -->
    <a href="/CM_System/admin/reports" class="group flex flex-col items-center">
        <div class="w-14 h-14 sm:w-20 sm:h-20 md:w-28 md:h-28 bg-white rounded-xl sm:rounded-2xl md:rounded-3xl shadow-sm border-t-4 border-emerald-500 flex items-center justify-center mb-2 md:mb-4 transition-all group-hover:shadow-xl group-hover:-translate-y-2 relative overflow-hidden">
            <div class="absolute inset-0 bg-emerald-50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <i class="fa-solid fa-chart-pie text-xl sm:text-3xl md:text-5xl text-emerald-500 relative z-10"></i>
        </div>
        <span class="text-[9px] sm:text-xs md:text-lg text-slate-700 font-bold text-center leading-tight group-hover:text-emerald-600">รายงานสรุป</span>
    </a>

    <!-- Module 5: บัตรนักเรียน -->
    <a href="/CM_System/admin/cards" class="group flex flex-col items-center">
        <div class="w-14 h-14 sm:w-20 sm:h-20 md:w-28 md:h-28 bg-white rounded-xl sm:rounded-2xl md:rounded-3xl shadow-sm border-t-4 border-rose-500 flex items-center justify-center mb-2 md:mb-4 transition-all group-hover:shadow-xl group-hover:-translate-y-2 relative overflow-hidden">
            <div class="absolute inset-0 bg-rose-50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <i class="fa-solid fa-address-card text-xl sm:text-3xl md:text-5xl text-rose-500 relative z-10"></i>
        </div>
        <span class="text-[9px] sm:text-xs md:text-lg text-slate-700 font-bold text-center leading-tight group-hover:text-rose-600">บัตรนักเรียน</span>
    </a>

    <!-- Module 6: ข้อมูลส่วนตัว -->
    <a href="/CM_System/admin/profile" class="group flex flex-col items-center">
        <div class="w-14 h-14 sm:w-20 sm:h-20 md:w-28 md:h-28 bg-white rounded-xl sm:rounded-2xl md:rounded-3xl shadow-sm border-t-4 border-sky-500 flex items-center justify-center mb-2 md:mb-4 transition-all group-hover:shadow-xl group-hover:-translate-y-2 relative overflow-hidden">
            <div class="absolute inset-0 bg-sky-50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <i class="fa-solid fa-id-card text-xl sm:text-3xl md:text-5xl text-sky-500 relative z-10"></i>
        </div>
        <span class="text-[9px] sm:text-xs md:text-lg text-slate-700 font-bold text-center leading-tight group-hover:text-sky-600">ข้อมูลส่วนตัว</span>
    </a>

</div>

</div>

<?php
$content = ob_get_clean();
include ROOT . '/views/layouts/admin.php';
?>
