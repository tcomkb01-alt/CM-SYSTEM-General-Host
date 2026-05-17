<?php
/**
 * Admin Dashboard View
 * ตัวแปรที่ได้รับจาก DashboardController:
 * @var string $title
 * @var array  $stats
 */

// จับ content ด้วย Output Buffering ก่อนส่งเข้า Layout
ob_start();
?>

<!-- Content Header -->
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-800">ยินดีต้อนรับกลับมา, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?>!</h1>
    <p class="text-slate-500">นี่คือภาพรวมของชั้นเรียนและการจัดการในวันนี้</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Total Students -->
    <div class="bg-white overflow-hidden shadow rounded-xl border border-slate-100 p-5 transition-all hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-500 rounded-lg p-3">
                <i class="fa-solid fa-users text-white text-xl"></i>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-slate-500 truncate">นักเรียนทั้งหมด</dt>
                    <dd class="text-2xl font-bold text-slate-900"><?= $stats['total_students'] ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Total Classes -->
    <div class="bg-white overflow-hidden shadow rounded-xl border border-slate-100 p-5 transition-all hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-500 rounded-lg p-3">
                <i class="fa-solid fa-chalkboard text-white text-xl"></i>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-slate-500 truncate">ชั้นเรียนที่ดูแล</dt>
                    <dd class="text-2xl font-bold text-slate-900"><?= $stats['total_classes'] ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Attendance Rate -->
    <div class="bg-white overflow-hidden shadow rounded-xl border border-slate-100 p-5 transition-all hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-emerald-500 rounded-lg p-3">
                <i class="fa-solid fa-calendar-check text-white text-xl"></i>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-slate-500 truncate">อัตราการเข้าเรียน</dt>
                    <dd class="text-2xl font-bold text-slate-900"><?= $stats['attendance_today'] ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Assignments -->
    <div class="bg-white overflow-hidden shadow rounded-xl border border-slate-100 p-5 transition-all hover:shadow-md">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-amber-500 rounded-lg p-3">
                <i class="fa-solid fa-book-open text-white text-xl"></i>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-slate-500 truncate">งานที่รอนำเข้าคะแนน</dt>
                    <dd class="text-2xl font-bold text-slate-900"><?= $stats['pending_assignments'] ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & Classes -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 min-h-[300px]">
        <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">ชั้นเรียนล่าสุด</h3>
        <div class="space-y-4">
            <p class="text-slate-400 text-sm italic">ยังไม่มีข้อมูลชั้นเรียนที่เปิดสอน...</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 min-h-[300px]">
        <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">กิจกรรมล่าสุด</h3>
        <div class="space-y-4">
            <p class="text-slate-400 text-sm italic">ยังไม่มีบันทึกกิจกรรมล่าสุด...</p>
        </div>
    </div>
</div>

<?php
// จับ content ที่ render ข้างบนทั้งหมดไว้ในตัวแปร $content
$content = ob_get_clean();

// ส่ง $content ไปให้ Layout แสดงผล
include ROOT . '/views/layouts/admin.php';
?>
