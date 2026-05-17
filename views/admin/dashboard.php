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

<!-- License Info -->
<?php 
$is_active = isset($license_data) && !empty($license_data['license_key']) && (!isset($license_data['status']) || in_array(strtolower($license_data['status']), ['active', 'valid']));
$bg_class = $is_active ? 'bg-gradient-to-r from-emerald-600 to-emerald-800 border-emerald-500' : 'bg-gradient-to-r from-slate-800 to-slate-900 border-slate-700';
$icon_bg = $is_active ? 'bg-emerald-700/50 border-emerald-500/50 text-white' : 'bg-slate-700/50 border-slate-600/50 text-emerald-400';
?>
<div class="mb-8 <?= $bg_class ?> rounded-[2rem] p-6 shadow-lg text-white flex flex-col md:flex-row items-center justify-between border relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl -mr-20 -mt-20"></div>
    <div class="flex items-center mb-4 md:mb-0 relative z-10">
        <div class="w-14 h-14 rounded-2xl <?= $icon_bg ?> flex items-center justify-center mr-5 text-2xl shadow-inner border">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div>
            <h3 class="font-bold text-lg mb-1 flex items-center">
                สถานะใบอนุญาต (License)
                <?php if ($is_active): ?>
                    <span class="ml-3 px-2.5 py-0.5 rounded-full bg-white/20 text-white border border-white/30 text-[10px] font-black uppercase tracking-widest flex items-center shadow-sm">
                        <i class="fa-solid fa-check mr-1"></i> Active
                    </span>
                <?php else: ?>
                    <span class="ml-3 px-2.5 py-0.5 rounded-full bg-rose-500/80 text-white border border-rose-400/50 text-[10px] font-black uppercase tracking-widest flex items-center shadow-sm">
                        <i class="fa-solid fa-xmark mr-1"></i> Inactive
                    </span>
                <?php endif; ?>
            </h3>
            <?php if (!empty($license_data['license_key'])): ?>
                <p class="text-xs <?= $is_active ? 'text-emerald-100' : 'text-slate-400' ?> font-medium">คีย์: <span class="text-white"><?= substr($license_data['license_key'], 0, 10) ?>...</span> <span class="mx-2 <?= $is_active ? 'text-emerald-300' : 'text-slate-500' ?>">|</span> ผูกกับโดเมน: <span class="text-white"><?= htmlspecialchars($license_data['domain'] ?? '-') ?></span></p>
            <?php else: ?>
                <p class="text-xs text-slate-400 font-medium">ระบบยังไม่ได้รับการ Activate ด้วย License Key ที่ถูกต้อง</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="relative z-10">
        <?php if (!$is_active): ?>
            <a href="<?= $_ENV['APP_URL'] ?? '' ?>/license/activate" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-bold text-sm shadow-md shadow-rose-500/20 transition-all flex items-center hover:scale-105">
                <i class="fa-solid fa-key mr-2"></i> ไปหน้า Activate
            </a>
        <?php endif; ?>
    </div>
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

<!-- Quick Links -->
<div class="mb-8">
    <h3 class="text-lg font-bold text-slate-800 mb-4">เมนูด่วน</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <a href="<?= $_ENV['APP_URL'] ?? '' ?>/admin/students" class="flex flex-col items-center justify-center bg-white p-4 rounded-xl shadow-sm border border-slate-100 hover:shadow-md hover:border-blue-300 hover:bg-blue-50 transition-all group">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-user-graduate text-xl"></i>
            </div>
            <span class="text-sm font-medium text-slate-700 group-hover:text-blue-700">นักเรียน</span>
        </a>
        <a href="<?= $_ENV['APP_URL'] ?? '' ?>/admin/classrooms" class="flex flex-col items-center justify-center bg-white p-4 rounded-xl shadow-sm border border-slate-100 hover:shadow-md hover:border-indigo-300 hover:bg-indigo-50 transition-all group">
            <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-chalkboard text-xl"></i>
            </div>
            <span class="text-sm font-medium text-slate-700 group-hover:text-indigo-700">ชั้นเรียน</span>
        </a>
        <a href="<?= $_ENV['APP_URL'] ?? '' ?>/admin/classrooms" class="flex flex-col items-center justify-center bg-white p-4 rounded-xl shadow-sm border border-slate-100 hover:shadow-md hover:border-emerald-300 hover:bg-emerald-50 transition-all group">
            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-calendar-check text-xl"></i>
            </div>
            <span class="text-sm font-medium text-slate-700 group-hover:text-emerald-700">เช็คชื่อ</span>
        </a>
        <a href="<?= $_ENV['APP_URL'] ?? '' ?>/admin/assignments" class="flex flex-col items-center justify-center bg-white p-4 rounded-xl shadow-sm border border-slate-100 hover:shadow-md hover:border-amber-300 hover:bg-amber-50 transition-all group">
            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-book-open text-xl"></i>
            </div>
            <span class="text-sm font-medium text-slate-700 group-hover:text-amber-700">งาน/คะแนน</span>
        </a>
        <a href="<?= $_ENV['APP_URL'] ?? '' ?>/admin/cards" class="flex flex-col items-center justify-center bg-white p-4 rounded-xl shadow-sm border border-slate-100 hover:shadow-md hover:border-purple-300 hover:bg-purple-50 transition-all group">
            <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-id-card text-xl"></i>
            </div>
            <span class="text-sm font-medium text-slate-700 group-hover:text-purple-700">บัตรนักเรียน</span>
        </a>
        <a href="<?= $_ENV['APP_URL'] ?? '' ?>/admin/reports" class="flex flex-col items-center justify-center bg-white p-4 rounded-xl shadow-sm border border-slate-100 hover:shadow-md hover:border-rose-300 hover:bg-rose-50 transition-all group">
            <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-chart-pie text-xl"></i>
            </div>
            <span class="text-sm font-medium text-slate-700 group-hover:text-rose-700">รายงาน</span>
        </a>
    </div>
</div>

<!-- Recent Activity & Classes -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 min-h-[300px]">
        <div class="flex justify-between items-center border-b pb-2 mb-4">
            <h3 class="font-bold text-slate-800">ชั้นเรียนล่าสุด</h3>
            <a href="<?= $_ENV['APP_URL'] ?? '' ?>/admin/classrooms" class="text-sm text-blue-600 hover:underline">ดูทั้งหมด</a>
        </div>
        <div class="space-y-4">
            <?php if (!empty($recent_classes)): ?>
                <?php foreach ($recent_classes as $class): ?>
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-100 hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                                <?= mb_substr($class['subject_name'], 0, 1) ?>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800"><?= htmlspecialchars($class['subject_name']) ?></h4>
                                <p class="text-xs text-slate-500">รหัส: <?= htmlspecialchars($class['room_code']) ?></p>
                            </div>
                        </div>
                        <a href="<?= $_ENV['APP_URL'] ?? '' ?>/admin/classrooms/show/<?= $class['id'] ?>" class="text-xs bg-white border border-slate-200 px-3 py-1.5 rounded-md hover:bg-blue-50 text-blue-600 transition-colors">จัดการ</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8">
                    <div class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-slate-100 mb-3">
                        <i class="fa-solid fa-chalkboard text-slate-400"></i>
                    </div>
                    <p class="text-slate-500 text-sm">ยังไม่มีข้อมูลชั้นเรียน</p>
                    <a href="<?= $_ENV['APP_URL'] ?? '' ?>/admin/classrooms" class="inline-block mt-2 text-xs text-blue-600 hover:underline">สร้างชั้นเรียนแรกของคุณ</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 min-h-[300px]">
        <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">กิจกรรมล่าสุด</h3>
        <div class="space-y-4">
            <?php if (!empty($recent_activities)): ?>
                <div class="relative border-l border-slate-200 ml-3 space-y-6">
                    <?php foreach ($recent_activities as $activity): ?>
                        <div class="relative pl-6">
                            <div class="absolute -left-1.5 top-1 w-3 h-3 bg-blue-500 rounded-full ring-4 ring-white"></div>
                            <h4 class="text-sm font-bold text-slate-800">
                                <?= htmlspecialchars(ucfirst($activity['action'])) ?> <?= htmlspecialchars($activity['entity_type']) ?>
                            </h4>
                            <?php if (!empty($activity['description'])): ?>
                                <p class="text-xs text-slate-600 mt-1"><?= htmlspecialchars($activity['description']) ?></p>
                            <?php endif; ?>
                            <span class="text-xs text-slate-400 block mt-1">
                                <?= date('d M Y, H:i', strtotime($activity['created_at'])) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <div class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-slate-100 mb-3">
                        <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
                    </div>
                    <p class="text-slate-500 text-sm">ยังไม่มีบันทึกกิจกรรมล่าสุด</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// จับ content ที่ render ข้างบนทั้งหมดไว้ในตัวแปร $content
$content = ob_get_clean();

// ส่ง $content ไปให้ Layout แสดงผล
include ROOT . '/views/layouts/admin.php';
?>
