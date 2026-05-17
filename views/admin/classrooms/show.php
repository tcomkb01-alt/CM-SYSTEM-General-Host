<?php
/**
 * @var array $classroom
 * @var array $students
 */
$BASE = $_ENV['APP_URL'] ?? '';
$sessions = $sessions ?? [];
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
                <a href="<?= $BASE ?>/admin/classrooms" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-all">
                    จัดการชั้นเรียน
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fa-solid fa-chevron-right text-slate-300 text-[10px] mx-2"></i>
                <span class="text-sm font-bold text-slate-800 tracking-tight"><?= htmlspecialchars($classroom['subject_name']) ?></span>
            </div>
        </li>
    </ol>
</nav>

<!-- Header & Info -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-indigo-600 to-violet-600 relative p-6 flex flex-col justify-end">
            <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-white text-xs font-bold tracking-widest uppercase">
                Room Code: <?= $classroom['room_code'] ?>
            </div>
            <h1 class="text-2xl font-bold text-white"><?= htmlspecialchars($classroom['subject_name']) ?></h1>
            <p class="text-indigo-100 text-sm"><?= htmlspecialchars($classroom['subject_code'] ?? 'ไม่มีรหัสวิชา') ?></p>
            <button onclick="openSettingsModal()" class="absolute top-4 left-4 bg-white/20 hover:bg-white/30 backdrop-blur-md w-8 h-8 rounded-full text-white flex items-center justify-center transition-all">
                <i class="fa-solid fa-gear text-xs"></i>
            </button>
        </div>
        <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div class="border-r border-slate-100">
                <p class="text-[10px] uppercase font-bold text-slate-400 mb-1 tracking-wider">นักเรียน</p>
                <p class="text-xl font-bold text-slate-800"><?= count($students) ?></p>
            </div>
            <div class="md:border-r border-slate-100">
                <p class="text-[10px] uppercase font-bold text-slate-400 mb-1 tracking-wider">จำนวนคาบ</p>
                <p class="text-xl font-bold text-slate-800"><?= $classroom['total_periods'] ?></p>
            </div>
            <div class="border-r border-slate-100">
                <p class="text-[10px] uppercase font-bold text-slate-400 mb-1 tracking-wider">เกณฑ์ผ่าน</p>
                <p class="text-xl font-bold text-emerald-600"><?= $classroom['pass_criteria'] ?>%</p>
            </div>
            <div>
                <p class="text-[10px] uppercase font-bold text-slate-400 mb-1 tracking-wider">สถานะ</p>
                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">เปิดสอน</span>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col justify-center items-center text-center">
        <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-4 text-2xl">
            <i class="fa-solid fa-qrcode"></i>
        </div>
        <h3 class="font-bold text-slate-800 mb-1">คิวอาร์โค้ดเช็คชื่อ</h3>
        <p class="text-xs text-slate-500 mb-4">ให้นักเรียนสแกนเพื่อบันทึกเวลาเรียน</p>
        <div class="grid grid-cols-1 gap-2 w-full">
            <button onclick="showQRModal()" class="w-full bg-slate-800 text-white py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-all flex items-center justify-center">
                <i class="fa-solid fa-expand mr-2"></i> แสดง QR เช็คชื่อ
            </button>
            <button onclick="showPortalQRModal()" class="w-full bg-indigo-50 text-indigo-600 py-2.5 rounded-xl text-sm font-bold hover:bg-indigo-100 transition-all flex items-center justify-center border border-indigo-100">
                <i class="fa-solid fa-id-card-clip mr-2 text-lg"></i> QR รหัสห้องเรียน
            </button>
        </div>
    </div>
</div>

<!-- Tabs & Actions -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div class="flex bg-slate-100 p-1 rounded-xl">
        <button onclick="switchMainTab('students')" id="tabStudentsBtn" class="px-4 py-1.5 rounded-lg text-sm font-bold bg-white text-indigo-600 shadow-sm transition-all">รายชื่อนักเรียน</button>
        <button onclick="switchMainTab('history')" id="tabHistoryBtn" class="px-4 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-700 transition-all">ประวัติเช็คชื่อ</button>
        <button onclick="switchMainTab('assignments')" id="tabAssignmentsBtn" class="px-4 py-1.5 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-700 transition-all">คะแนนการบ้าน</button>
    </div>
    <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
        <?php 
        $activeSession = null;
        foreach($sessions as $sess) {
            if($sess['status'] === 'active') { $activeSession = $sess; break; }
        }
        if($activeSession): ?>
            <button onclick="stopAttendance(<?= $activeSession['id'] ?>)" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center justify-center shadow-md flex-1 md:flex-none whitespace-nowrap">
                <i class="fa-solid fa-stop mr-2"></i> ปิดการเช็คชื่อ
            </button>
        <?php else: ?>
            <button onclick="openAttendanceModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center justify-center shadow-md flex-1 md:flex-none whitespace-nowrap">
                <i class="fa-solid fa-play mr-2"></i> เปิดการเช็คชื่อ
            </button>
        <?php endif; ?>
        <button onclick="openAddStudentModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center justify-center shadow-md flex-1 md:flex-none whitespace-nowrap">
            <i class="fa-solid fa-user-plus mr-2"></i> เพิ่มนักเรียน
        </button>
        <button onclick="openWheelModal()" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center justify-center shadow-md flex-1 md:flex-none whitespace-nowrap">
            <i class="fa-solid fa-dharmachakra mr-2"></i> วงล้อสุ่ม
        </button>
    </div>
</div>

<!-- Active Session Quick Link -->
<?php if($activeSession): ?>
<div class="mb-6 bg-emerald-50 border-2 border-emerald-100 rounded-2xl p-4 animate__animated animate__fadeInDown">
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center w-full md:w-auto">
            <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white mr-4 shadow-sm shrink-0">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-emerald-800">กำลังเช็คชื่อ: คาบที่ <?= $activeSession['period_number'] ?></p>
                <p class="text-[10px] text-emerald-600 uppercase font-bold tracking-widest">เริ่มเมื่อ <?= date('H:i', strtotime($activeSession['started_at'])) ?> น.</p>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2 w-full md:flex md:w-auto">
            <button onclick="showQRModalWithId(<?= $activeSession['id'] ?>)" class="bg-white text-emerald-600 px-3 py-2 rounded-lg text-xs font-bold border border-emerald-200 hover:bg-emerald-100 transition-all flex items-center justify-center">
                <i class="fa-solid fa-qrcode mr-1 text-base"></i> แสดง QR
            </button>
            <a href="<?= $BASE ?>/admin/attendance/session/<?= $activeSession['id'] ?>" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-emerald-700 shadow-sm transition-all flex items-center justify-center">
                <i class="fa-solid fa-barcode mr-1 text-base"></i> หน้าสแกน
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Main Content Tabs -->
<div id="studentsTab" class="animate__animated animate__fadeIn">
    <!-- Student List Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="hidden md:table-header-group">
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider w-20">เลขที่</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">รหัส / ชื่อ-นามสกุล</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">เข้าเรียน (%)</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center w-24">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">ยังไม่มีนักเรียนในห้องเรียนนี้</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $s): ?>
                        <tr class="hover:bg-slate-50/50 transition-all flex flex-col md:table-row p-4 md:p-0 border-b md:border-b-0">
                            <!-- Name / Number Header -->
                            <td class="px-0 md:px-6 py-0 md:py-4 flex items-center md:table-cell mb-3 md:mb-0">
                                <span class="md:hidden bg-slate-100 text-slate-600 w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold mr-3 shrink-0">
                                    <?= $s['student_number'] ?>
                                </span>
                                <div class="flex items-center min-w-0 flex-1">
                                    <div class="hidden sm:flex w-8 h-8 rounded-full bg-slate-100 items-center justify-center text-slate-500 mr-3 text-xs font-bold uppercase shrink-0">
                                        <?= mb_substr($s['first_name'], 0, 1) ?>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold text-slate-800 whitespace-nowrap overflow-hidden text-ellipsis">
                                            <?= $s['prefix'] . $s['first_name'] . ' ' . $s['last_name'] ?>
                                        </p>
                                        <p class="text-[10px] text-slate-500"><?= $s['student_code'] ?></p>
                                    </div>
                                </div>
                            </td>

                            <!-- Attendance % -->
                            <td class="px-0 md:px-6 py-0 md:py-4 flex items-center justify-between md:table-cell mb-2 md:mb-0">
                                <span class="md:hidden text-[10px] font-bold text-slate-400 uppercase tracking-wider">เข้าเรียน (%)</span>
                                <div class="flex items-center justify-center">
                                    <span class="text-xs font-bold text-slate-700 mr-2"><?= $s['attendance_percent'] ?>%</span>
                                    <div class="w-16 bg-slate-100 h-1 rounded-full overflow-hidden">
                                        <div class="bg-emerald-500 h-full transition-all duration-500" style="width: <?= $s['attendance_percent'] ?>%"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Manage -->
                            <td class="px-0 md:px-6 py-0 md:py-4 flex items-center justify-end md:table-cell">
                                <button onclick="removeStudent(<?= $s['id'] ?>)" class="bg-red-50 text-red-500 md:bg-transparent md:text-slate-300 w-8 h-8 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                    <i class="fa-solid fa-user-minus"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="assignmentsTab" class="hidden animate__animated animate__fadeIn">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <h4 class="text-sm font-bold text-slate-700">ภาพรวมคะแนนการบ้าน</h4>
            <span class="text-[10px] text-slate-400 uppercase font-bold tracking-widest">ข้อมูลอัปเดตล่าสุด</span>
        </div>
        <table class="w-full text-left border-collapse">
            <thead class="hidden md:table-header-group">
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider w-20">เลขที่</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">ชื่อ-นามสกุล</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">ส่งงานแล้ว</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">คะแนนรวม</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center w-24">รายละเอียด</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($students)): ?>
                    <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">ยังไม่มีข้อมูลนักเรียน</td></tr>
                <?php else: ?>
                    <?php foreach ($students as $s): ?>
                        <tr class="hover:bg-slate-50/50 transition-all flex flex-col md:table-row p-4 md:p-0 border-b md:border-b-0">
                            <td class="px-0 md:px-6 py-0 md:py-4 md:table-cell mb-2 md:mb-0">
                                <span class="md:hidden text-[10px] font-bold text-slate-400 uppercase mr-2">เลขที่:</span>
                                <span class="text-sm font-bold text-slate-600"><?= $s['student_number'] ?></span>
                            </td>
                            <td class="px-0 md:px-6 py-0 md:py-4 flex items-center md:table-cell mb-3 md:mb-0">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500 mr-3 text-xs font-bold shrink-0">
                                        <?= mb_substr($s['first_name'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800"><?= $s['prefix'] . $s['first_name'] . ' ' . $s['last_name'] ?></p>
                                        <p class="text-[10px] text-slate-400"><?= $s['student_code'] ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-0 md:px-6 py-0 md:py-4 flex items-center justify-between md:table-cell mb-2 md:mb-0">
                                <span class="md:hidden text-[10px] font-bold text-slate-400 uppercase tracking-wider">ส่งงาน:</span>
                                <div class="text-center">
                                    <span class="text-sm font-bold text-slate-700"><?= $s['assignment_submitted'] ?> / <?= $s['assignment_total'] ?></span>
                                    <div class="w-20 bg-slate-100 h-1 rounded-full overflow-hidden mx-auto mt-1">
                                        <div class="bg-indigo-500 h-full" style="width: <?= $s['assignment_total'] > 0 ? ($s['assignment_submitted'] / $s['assignment_total'] * 100) : 0 ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-0 md:px-6 py-0 md:py-4 flex items-center justify-between md:table-cell mb-2 md:mb-0">
                                <span class="md:hidden text-[10px] font-bold text-slate-400 uppercase tracking-wider">คะแนน:</span>
                                <div class="text-center">
                                    <span class="text-sm font-bold text-emerald-600"><?= number_format($s['assignment_score'], 1) ?></span>
                                    <span class="text-[10px] text-slate-400">/ <?= number_format($s['assignment_max_score'], 1) ?></span>
                                </div>
                            </td>
                            <td class="px-0 md:px-6 py-0 md:py-4 flex items-center justify-end md:table-cell">
                                <button onclick="viewStudentAssignments(<?= $s['id'] ?>, '<?= $s['first_name'] ?>')" class="w-full md:w-8 md:h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all py-2 md:py-0">
                                    <i class="fa-solid fa-list-check md:mr-0 mr-2"></i>
                                    <span class="md:hidden text-sm font-bold">ดูรายละเอียดงาน</span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="historyTab" class="hidden animate__animated animate__fadeIn">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="hidden md:table-header-group">
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">วันที่</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">คาบที่</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">สถานะ</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($sessions)): ?>
                    <tr><td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">ยังไม่มีประวัติการเช็คชื่อ</td></tr>
                <?php else: ?>
                    <?php foreach ($sessions as $sess): ?>
                        <tr class="hover:bg-slate-50/50 transition-all flex flex-col md:table-row p-4 md:p-0 border-b md:border-b-0">
                            <!-- Date / Time -->
                            <td class="px-0 md:px-6 py-0 md:py-4 flex items-center justify-between md:table-cell mb-2 md:mb-0">
                                <div>
                                    <p class="text-sm font-bold text-slate-800"><?= date('d/m/Y', strtotime($sess['session_date'])) ?></p>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-widest"><?= date('H:i', strtotime($sess['started_at'])) ?> น.</p>
                                </div>
                                <!-- Status (Mobile) -->
                                <div class="md:hidden">
                                    <?php if($sess['status'] === 'active'): ?>
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">กำลังเช็คชื่อ</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">ปิดรอบแล้ว</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Period -->
                            <td class="px-0 md:px-6 py-0 md:py-4 flex items-center md:table-cell mb-2 md:mb-0">
                                <span class="md:hidden text-[10px] font-bold text-slate-400 uppercase mr-2 tracking-wider">ข้อมูล:</span>
                                <span class="text-sm font-bold text-slate-600">คาบที่ <?= $sess['period_number'] ?></span>
                            </td>

                            <!-- Status (Desktop) -->
                            <td class="hidden md:table-cell px-6 py-4 text-center">
                                <?php if($sess['status'] === 'active'): ?>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">กำลังเช็คชื่อ</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">ปิดรอบแล้ว</span>
                                <?php endif; ?>
                            </td>

                            <!-- Manage -->
                            <td class="px-0 md:px-6 py-0 md:py-4 flex items-center justify-end md:table-cell mt-2 md:mt-0">
                                <a href="<?= $BASE ?>/admin/attendance/session/<?= $sess['id'] ?>" class="w-full md:w-auto bg-indigo-50 text-indigo-600 md:bg-transparent px-4 py-2 md:px-0 md:py-0 rounded-xl text-center text-sm font-bold hover:text-indigo-800 transition-all flex items-center justify-center">
                                    <i class="fa-solid fa-arrow-up-right-from-square mr-2 md:mr-1"></i> จัดการ
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Start Attendance Modal -->
<div id="attendanceModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeAttendanceModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm relative z-10 animate__animated animate__zoomIn" style="animation-duration: 0.3s;">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">ระบุคาบเรียน</h3>
                <button onclick="closeAttendanceModal()" class="text-slate-400 hover:text-slate-600 text-xl"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="p-6">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">คาบเรียนที่ (เช่น 1)</label>
                    <input type="number" id="periodNumber" value="1" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>
                <button onclick="startAttendance()" class="w-full bg-emerald-600 text-white py-3 rounded-xl font-bold hover:bg-emerald-700 shadow-lg transition-all flex items-center justify-center">
                    <i class="fa-solid fa-play mr-2"></i> เปิดระบบเช็คชื่อ
                </button>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div id="qrModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-70" onclick="closeQRModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm relative z-10 text-center p-8">
            <button onclick="closeQRModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 text-xl"><i class="fa-solid fa-xmark"></i></button>
            <div id="qrContent">
                <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">QR Code เช็คชื่อ</h3>
                <p class="text-xs text-slate-500 mb-4">ให้นักเรียนสแกน QR Code นี้</p>
                <div id="qrImageBox" class="flex justify-center mb-4">
                    <p class="text-slate-400 text-sm py-8">กรุณาเปิดระบบเช็คชื่อก่อน</p>
                </div>
                <p id="qrSubject" class="text-sm font-bold text-slate-700"></p>
                <p id="qrPeriod" class="text-xs text-slate-400"></p>
            </div>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div id="addStudentModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeAddStudentModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative z-10 animate__animated animate__fadeInUp" style="animation-duration: 0.3s;">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">เพิ่มนักเรียนเข้าห้องเรียน</h3>
                <button onclick="closeAddStudentModal()" class="text-slate-400 hover:text-slate-600 text-xl"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">เลือกชั้นเรียน</label>
                        <select id="classLevelFilter" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">ทั้งหมด</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">ค้นหารายชื่อ</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" id="studentSearchQuery" placeholder="รหัส หรือ ชื่อ..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>
                    </div>
                </div>
                <!-- Select All -->
                <div id="selectAllBar" class="hidden flex items-center justify-between bg-indigo-50 px-4 py-2 rounded-xl mb-2">
                    <label class="flex items-center cursor-pointer text-sm font-medium text-indigo-700">
                        <input type="checkbox" id="selectAllCheckbox" class="mr-2 w-4 h-4 accent-indigo-600 rounded" onchange="toggleSelectAll()"> เลือกทั้งหมด
                    </label>
                    <span id="selectedCount" class="text-xs font-bold text-indigo-500">เลือกแล้ว 0 คน</span>
                </div>
                <div id="searchResults" class="max-h-64 overflow-y-auto border border-slate-100 rounded-xl hidden"></div>
                <div id="noResults" class="text-center py-8 text-slate-400 text-sm hidden">ไม่พบข้อมูลนักเรียน</div>
                <div id="searchPlaceholder" class="text-center py-8 text-slate-400 text-sm">กรุณาเลือกชั้นเรียนเพื่อแสดงรายชื่อ</div>
            </div>
            <!-- Batch Add Button -->
            <div id="batchAddBar" class="hidden px-6 py-4 border-t border-slate-200 bg-slate-50 rounded-b-2xl">
                <button onclick="addSelectedStudents()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-bold transition-all flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-user-plus mr-2"></i> <span id="addBtnText">เพิ่มนักเรียนที่เลือก</span>
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Classroom Settings Modal -->
<div id="settingsModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeSettingsModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm relative z-10 animate__animated animate__zoomIn" style="animation-duration: 0.3s;">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">ตั้งค่าชั้นเรียน</h3>
                <button onclick="closeSettingsModal()" class="text-slate-400 hover:text-slate-600 text-xl"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="p-6">
                <form id="settingsForm" onsubmit="event.preventDefault(); updateSettings();">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">จำนวนคาบทั้งหมด</label>
                        <input type="number" id="settingTotalPeriods" name="total_periods" value="<?= $classroom['total_periods'] ?>" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">เกณฑ์ผ่านการเข้าเรียน (%)</label>
                        <input type="number" id="settingPassCriteria" name="pass_criteria" value="<?= $classroom['pass_criteria'] ?>" step="0.01" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 shadow-lg transition-all flex items-center justify-center">
                        <i class="fa-solid fa-save mr-2"></i> บันทึกการตั้งค่า
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Student Assignments Detail Modal -->
<div id="studentAssignmentsModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeStudentAssignmentsModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative z-10 animate__animated animate__fadeInUp" style="animation-duration: 0.3s;">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">รายละเอียดงานส่ง</h3>
                    <p class="text-xs text-slate-400" id="assignModalStudentName"></p>
                </div>
                <button onclick="closeStudentAssignmentsModal()" class="text-slate-400 hover:text-slate-600 text-xl"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="p-6">
                <div id="studentAssignmentsList" class="space-y-3 max-h-96 overflow-y-auto pr-2">
                    <!-- Dynamic content -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Random Wheel Modal -->
<div id="wheelModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900 bg-opacity-80 backdrop-blur-sm transition-opacity" onclick="closeWheelModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg relative z-10 animate__animated animate__zoomIn" style="animation-duration: 0.4s;">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xl font-black text-slate-800 flex items-center">
                    <i class="fa-solid fa-dharmachakra text-amber-500 mr-3"></i> วงล้อสุ่มรายชื่อ
                </h3>
                <button onclick="closeWheelModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:bg-red-100 hover:text-red-500 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-8 text-center relative overflow-hidden flex flex-col items-center">
                <!-- Pointer -->
                <div class="absolute top-8 left-1/2 -translate-x-1/2 z-20 w-8 h-8">
                    <div class="w-0 h-0 border-l-[16px] border-r-[16px] border-t-[24px] border-l-transparent border-r-transparent border-t-red-500 drop-shadow-md"></div>
                </div>
                
                <!-- Canvas container -->
                <div class="relative w-80 h-80 mx-auto rounded-full shadow-inner border-4 border-slate-100 bg-slate-50 overflow-hidden mb-6">
                    <canvas id="wheelCanvas" width="320" height="320" class="absolute top-0 left-0"></canvas>
                </div>

                <div id="wheelResult" class="h-14 flex items-center justify-center w-full bg-slate-50 rounded-xl border border-slate-200 mb-6 font-bold text-lg text-slate-700">
                    <span class="text-slate-400 font-normal text-sm">กดหมุนวงล้อเพื่อเริ่มสุ่ม</span>
                </div>

                <div class="flex gap-3 w-full">
                    <button onclick="resetWheel()" class="px-4 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition-colors w-1/3">
                        <i class="fa-solid fa-rotate-right mr-1"></i> รีเซ็ต
                    </button>
                    <button id="spinBtn" onclick="spinWheel()" class="px-4 py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-xl font-black hover:shadow-lg hover:scale-105 transition-all w-2/3 shadow-amber-500/30">
                        <i class="fa-solid fa-play mr-2"></i> หมุนเลย!
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const BASE_URL = '<?= $BASE ?>';
const CLASSROOM_ID = <?= $classroom['id'] ?>;

function switchMainTab(tab) {
    const studentsTab = document.getElementById('studentsTab');
    const historyTab = document.getElementById('historyTab');
    const assignmentsTab = document.getElementById('assignmentsTab');
    const studentsBtn = document.getElementById('tabStudentsBtn');
    const historyBtn = document.getElementById('tabHistoryBtn');
    const assignmentsBtn = document.getElementById('tabAssignmentsBtn');

    [studentsTab, historyTab, assignmentsTab].forEach(t => t.classList.add('hidden'));
    [studentsBtn, historyBtn, assignmentsBtn].forEach(b => {
        b.classList.remove('bg-white', 'text-indigo-600', 'shadow-sm');
        b.classList.add('text-slate-500');
    });

    if (tab === 'students') {
        studentsTab.classList.remove('hidden');
        studentsBtn.classList.add('bg-white', 'text-indigo-600', 'shadow-sm');
        studentsBtn.classList.remove('text-slate-500');
    } else if (tab === 'history') {
        historyTab.classList.remove('hidden');
        historyBtn.classList.add('bg-white', 'text-indigo-600', 'shadow-sm');
        historyBtn.classList.remove('text-slate-500');
    } else if (tab === 'assignments') {
        assignmentsTab.classList.remove('hidden');
        assignmentsBtn.classList.add('bg-white', 'text-indigo-600', 'shadow-sm');
        assignmentsBtn.classList.remove('text-slate-500');
    }
}

function showQRModalWithId(sessionId) {
    activeSessionId = sessionId;
    showQRModal();
}

async function openAddStudentModal() {
    document.getElementById('addStudentModal').classList.remove('hidden');
    await fetchClassLevels();
    await searchStudents(); // Fetch initial list
}

function openAttendanceModal() {
    document.getElementById('attendanceModal').classList.remove('hidden');
}

function closeAttendanceModal() {
    document.getElementById('attendanceModal').classList.add('hidden');
}

let activeSessionId = null;

function showQRModal() {
    document.getElementById('qrModal').classList.remove('hidden');
    if (activeSessionId) {
        const checkinUrl = `${BASE_URL}/attendance/checkin/${activeSessionId}`;
        const qrApiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(checkinUrl)}`;
        document.getElementById('qrImageBox').innerHTML = `
            <div class="flex flex-col items-center">
                <img src="${qrApiUrl}" alt="Attendance QR" class="rounded-xl border-4 border-slate-50 shadow-sm mb-4">
                <a href="${qrApiUrl}" download="Attendance_QR.png" target="_blank" class="text-[10px] font-bold text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-download mr-1"></i> บันทึกรูปภาพ QR
                </a>
            </div>
        `;
        document.getElementById('qrSubject').textContent = '<?= htmlspecialchars($classroom['subject_name']) ?>';
        document.getElementById('qrPeriod').textContent = 'สแกนเพื่อเช็คชื่อเข้าเรียน';
    } else {
        document.getElementById('qrImageBox').innerHTML = `
            <div class="py-8 px-4">
                <p class="text-slate-400 text-sm mb-4">กรุณาเปิดระบบเช็คชื่อก่อน<br>เพื่อใช้งาน QR เช็คชื่อ</p>
                <button onclick="closeQRModal(); openAttendanceModal();" class="text-xs font-bold text-indigo-600 underline">
                    ไปที่หน้าเปิดระบบ
                </button>
            </div>
        `;
    }
}

function showPortalQRModal() {
    document.getElementById('qrModal').classList.remove('hidden');
    const portalUrl = `${BASE_URL}/portal/<?= $classroom['room_code'] ?>`;
    const qrApiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(portalUrl)}`;
    
    document.getElementById('qrImageBox').innerHTML = `
        <div class="flex flex-col items-center">
            <img src="${qrApiUrl}" alt="Portal QR" class="rounded-xl border-4 border-indigo-50 shadow-sm mb-4">
            <a href="${qrApiUrl}" download="Portal_QR_<?= $classroom['room_code'] ?>.png" target="_blank" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-xs font-bold shadow-md hover:bg-indigo-700 transition-all">
                <i class="fa-solid fa-download mr-1"></i> บันทึกรูปภาพ QR Code
            </a>
        </div>
    `;
    document.getElementById('qrSubject').textContent = 'Student Portal (ห้องเรียน)';
    document.getElementById('qrPeriod').textContent = 'รหัสห้องเรียน: <?= $classroom['room_code'] ?>';
}

function closeQRModal() {
    document.getElementById('qrModal').classList.add('hidden');
}

async function startAttendance() {
    const period = document.getElementById('periodNumber').value;
    const token = document.querySelector('input[name="csrf_token"]').value;
    
    try {
        const response = await fetch(`${BASE_URL}/admin/attendance/start/${CLASSROOM_ID}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ period_number: period })
        });
        const result = await response.json();
        if (result.success) {
            activeSessionId = result.session_id;
            closeAttendanceModal();
            Swal.fire({ icon: 'success', title: 'เปิดระบบเช็คชื่อแล้ว', timer: 1500, showConfirmButton: false });
            setTimeout(() => window.location.reload(), 1500);
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ' });
    }
}

async function stopAttendance(sessionId) {
    const token = document.querySelector('input[name="csrf_token"]').value;
    const result = await Swal.fire({
        title: 'ยืนยันการปิดเช็คชื่อ?',
        text: 'นักเรียนที่ยังไม่ได้เช็คชื่อจะถูกเช็คขาดทันที',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'ใช่, ปิดการเช็คชื่อ',
        cancelButtonText: 'ยกเลิก'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`${BASE_URL}/admin/attendance/stop/${sessionId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token }
            });
            const res = await response.json();
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, timer: 1500, showConfirmButton: false });
                setTimeout(() => window.location.reload(), 1500);
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้' });
        }
    }
}

function closeAddStudentModal() {
    document.getElementById('addStudentModal').classList.add('hidden');
    document.getElementById('studentSearchQuery').value = '';
    document.getElementById('searchResults').classList.add('hidden');
    document.getElementById('searchPlaceholder').classList.remove('hidden');
    document.getElementById('selectAllBar').classList.add('hidden');
    document.getElementById('batchAddBar').classList.add('hidden');
    document.getElementById('selectAllCheckbox').checked = false;
    selectedStudentIds.clear();
    updateSelectedCount();
}

async function fetchClassLevels() {
    try {
        const response = await fetch(`${BASE_URL}/admin/classrooms/class-levels`);
        const result = await response.json();
        if (result.success) {
            const select = document.getElementById('classLevelFilter');
            const currentVal = select.value;
            select.innerHTML = '<option value="">ทั้งหมด</option>';
            result.data.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.class_level;
                opt.textContent = item.class_level;
                select.appendChild(opt);
            });
            select.value = currentVal;
        }
    } catch (error) { console.error(error); }
}

let searchTimeout;
let selectedStudentIds = new Set();

function updateSelectedCount() {
    const count = selectedStudentIds.size;
    document.getElementById('selectedCount').textContent = `เลือกแล้ว ${count} คน`;
    document.getElementById('addBtnText').textContent = `เพิ่มนักเรียนที่เลือก (${count} คน)`;
    document.getElementById('batchAddBar').classList.toggle('hidden', count === 0);
}

function toggleSelectAll() {
    const checked = document.getElementById('selectAllCheckbox').checked;
    document.querySelectorAll('.student-checkbox').forEach(cb => {
        cb.checked = checked;
        const id = parseInt(cb.value);
        if (checked) selectedStudentIds.add(id);
        else selectedStudentIds.delete(id);
    });
    updateSelectedCount();
}

function onStudentCheck(cb) {
    const id = parseInt(cb.value);
    if (cb.checked) selectedStudentIds.add(id);
    else selectedStudentIds.delete(id);
    updateSelectedCount();
    // Update select all checkbox
    const allCbs = document.querySelectorAll('.student-checkbox');
    const allChecked = [...allCbs].every(c => c.checked);
    document.getElementById('selectAllCheckbox').checked = allChecked;
}

async function searchStudents() {
    const query = document.getElementById('studentSearchQuery').value.trim();
    const classLevel = document.getElementById('classLevelFilter').value;
    const resultsDiv = document.getElementById('searchResults');
    const placeholder = document.getElementById('searchPlaceholder');
    const noResults = document.getElementById('noResults');
    placeholder.classList.add('hidden');

    try {
        const response = await fetch(`${BASE_URL}/admin/classrooms/search-students/${CLASSROOM_ID}?query=${encodeURIComponent(query)}&class_level=${encodeURIComponent(classLevel)}`);
        const result = await response.json();

        if (result.success && result.data.length > 0) {
            resultsDiv.innerHTML = '';
            result.data.forEach(s => {
                const isChecked = selectedStudentIds.has(s.id);
                const div = document.createElement('div');
                div.className = 'flex items-center p-3 hover:bg-indigo-50 transition-colors border-b border-slate-50 last:border-0';
                div.innerHTML = `
                    <label class="flex items-center flex-1 cursor-pointer">
                        <input type="checkbox" class="student-checkbox mr-3 w-4 h-4 accent-indigo-600 rounded" value="${s.id}" ${isChecked ? 'checked' : ''} onchange="onStudentCheck(this)">
                        <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 mr-3 text-xs font-bold uppercase">${s.first_name.charAt(0)}</div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">${s.prefix}${s.first_name} ${s.last_name}</p>
                            <p class="text-[10px] text-slate-500">${s.student_code} | ${s.class_level}</p>
                        </div>
                    </label>`;
                resultsDiv.appendChild(div);
            });
            resultsDiv.classList.remove('hidden');
            noResults.classList.add('hidden');
            document.getElementById('selectAllBar').classList.remove('hidden');
        } else {
            resultsDiv.classList.add('hidden');
            noResults.classList.remove('hidden');
            document.getElementById('selectAllBar').classList.add('hidden');
        }
    } catch (error) { console.error(error); }
}

document.getElementById('studentSearchQuery').addEventListener('input', () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(searchStudents, 300);
});
document.getElementById('classLevelFilter').addEventListener('change', searchStudents);

async function addSelectedStudents() {
    if (selectedStudentIds.size === 0) return;
    const token = document.querySelector('input[name="csrf_token"]').value;
    try {
        const response = await fetch(`${BASE_URL}/admin/classrooms/add-student/${CLASSROOM_ID}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ student_ids: [...selectedStudentIds] })
        });
        const result = await response.json();
        if (result.success) {
            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: result.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
            setTimeout(() => window.location.reload(), 1500);
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message });
        }
    } catch (error) { console.error(error); }
}


async function removeStudent(studentId) {
    const token = document.querySelector('input[name="csrf_token"]').value;
    Swal.fire({
        title: 'ถอดนักเรียนออกจากห้อง?',
        text: "ข้อมูลการเช็คชื่อของนักเรียนคนนี้ในห้องนี้จะถูกลบไปด้วย!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'ถอดออก',
        cancelButtonText: 'ยกเลิก'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`${BASE_URL}/admin/classrooms/remove-student/${CLASSROOM_ID}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({ student_id: studentId })
                });
                const res = await response.json();
                if (res.success) {
                    Swal.fire('สำเร็จ', res.message, 'success').then(() => window.location.reload());
                }
            } catch (error) { console.error(error); }
        }
    });
}

function openSettingsModal() {
    document.getElementById('settingsModal').classList.remove('hidden');
}

function closeSettingsModal() {
    document.getElementById('settingsModal').classList.add('hidden');
}

async function updateSettings() {
    const totalPeriods = document.getElementById('settingTotalPeriods').value;
    const passCriteria = document.getElementById('settingPassCriteria').value;
    const token = document.querySelector('input[name="csrf_token"]').value;

    try {
        const response = await fetch(`${BASE_URL}/admin/classrooms/update-settings/${CLASSROOM_ID}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ total_periods: totalPeriods, pass_criteria: passCriteria })
        });
        const result = await response.json();
        if (result.success) {
            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: result.message, timer: 1500, showConfirmButton: false });
            setTimeout(() => window.location.reload(), 1500);
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ' });
    }
}

async function viewStudentAssignments(studentId, firstName) {
    const modal = document.getElementById('studentAssignmentsModal');
    const list = document.getElementById('studentAssignmentsList');
    const nameLabel = document.getElementById('assignModalStudentName');
    
    nameLabel.textContent = `นักเรียน: ${firstName}`;
    list.innerHTML = '<div class="text-center py-8"><i class="fa-solid fa-circle-notch fa-spin text-indigo-500 text-2xl mb-2"></i><p class="text-xs text-slate-400">กำลังโหลดข้อมูล...</p></div>';
    modal.classList.remove('hidden');

    try {
        // We reuse the verifyStudent endpoint or similar to get student assignment data
        const response = await fetch(`${BASE_URL}/admin/classrooms/search-students/${CLASSROOM_ID}?query=${studentId}`);
        // Actually, let's just use a more direct way if verifyStudent is public
        // But since we are admin, we can fetch all assignments for this classroom and filter for this student
        // Or better yet, we should have an endpoint for this.
        // For now, let's assume we can get it from verifyStudent logic but through an admin-safe way.
        // Since I can't easily add a new route and controller method for EVERY small thing without making this huge,
        // I will use a simple approach: fetch from a new endpoint I'll add.
        
        // Wait, I'll add a quick endpoint for student assignments in ClassroomController
        const res = await fetch(`${BASE_URL}/admin/classrooms/student-assignments/${CLASSROOM_ID}/${studentId}`);
        const result = await res.json();
        
        if (result.success) {
            list.innerHTML = '';
            if (result.data.length === 0) {
                list.innerHTML = '<p class="text-center py-8 text-slate-400 text-sm">ยังไม่มีการมอบหมายงานในห้องเรียนนี้</p>';
                return;
            }
            result.data.forEach(task => {
                const isSubmitted = task.status === 'graded' || task.status === 'submitted';
                const scoreDisplay = isSubmitted ? `<span class="text-emerald-600 font-bold">${task.score ?? 0}</span>` : '<span class="text-slate-300">--</span>';
                
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100 hover:border-indigo-200 transition-all';
                div.innerHTML = `
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg ${isSubmitted ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-200 text-slate-400'} flex items-center justify-center mr-3 shrink-0">
                            <i class="fa-solid ${isSubmitted ? 'fa-check-circle' : 'fa-circle-question'}"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">${task.title}</p>
                            <p class="text-[10px] text-slate-400 uppercase tracking-widest">ครบกำหนด: ${task.due_date || 'ไม่ระบุ'}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold">${scoreDisplay} <span class="text-slate-400">/ ${task.max_score}</span></p>
                        <span class="text-[10px] font-bold ${isSubmitted ? 'text-emerald-500' : 'text-red-400'} uppercase tracking-tighter">
                            ${isSubmitted ? 'ส่งแล้ว' : 'ยังไม่ส่ง'}
                        </span>
                    </div>
                `;
                list.appendChild(div);
            });
        } else {
            list.innerHTML = `<p class="text-center py-8 text-red-400 text-sm">${result.message}</p>`;
        }
    } catch (e) {
        list.innerHTML = '<p class="text-center py-8 text-red-400 text-sm">เกิดข้อผิดพลาดในการโหลดข้อมูล</p>';
    }
}

function closeStudentAssignmentsModal() {
    document.getElementById('studentAssignmentsModal').classList.add('hidden');
}
// --- Wheel of Names Logic ---
const initialWheelStudents = <?= json_encode(array_values(array_map(function($s) {
    return ['id' => $s['id'], 'name' => $s['first_name'] . ' ' . mb_substr($s['last_name'], 0, 1) . '.'];
}, $students))) ?>;

let currentWheelStudents = [...initialWheelStudents];
const wheelCanvas = document.getElementById('wheelCanvas');
const wheelCtx = wheelCanvas?.getContext('2d');
let currentAngle = 0;
let spinTimeout = null;
let isSpinning = false;

const wheelColors = [
    '#F87171', '#FBBF24', '#34D399', '#60A5FA', '#A78BFA', '#F472B6', 
    '#FB923C', '#4ADE80', '#38BDF8', '#818CF8', '#E879F9', '#FB7185'
];

function drawWheel() {
    if (!wheelCtx) return;
    const w = wheelCanvas.width;
    const h = wheelCanvas.height;
    const cx = w / 2;
    const cy = h / 2;
    const radius = w / 2;
    const numSlices = currentWheelStudents.length;

    wheelCtx.clearRect(0, 0, w, h);

    if (numSlices === 0) {
        wheelCtx.beginPath();
        wheelCtx.arc(cx, cy, radius, 0, 2 * Math.PI);
        wheelCtx.fillStyle = '#f1f5f9';
        wheelCtx.fill();
        wheelCtx.fillStyle = '#94a3b8';
        wheelCtx.font = 'bold 16px "Prompt"';
        wheelCtx.textAlign = 'center';
        wheelCtx.textBaseline = 'middle';
        wheelCtx.fillText('หมดรายชื่อแล้ว', cx, cy);
        return;
    }

    const arc = (2 * Math.PI) / numSlices;

    for (let i = 0; i < numSlices; i++) {
        const angle = currentAngle + i * arc;
        
        wheelCtx.beginPath();
        wheelCtx.fillStyle = wheelColors[i % wheelColors.length];
        wheelCtx.moveTo(cx, cy);
        wheelCtx.arc(cx, cy, radius, angle, angle + arc);
        wheelCtx.lineTo(cx, cy);
        wheelCtx.fill();
        
        wheelCtx.strokeStyle = '#ffffff';
        wheelCtx.lineWidth = 2;
        wheelCtx.stroke();

        wheelCtx.save();
        wheelCtx.translate(cx, cy);
        wheelCtx.rotate(angle + arc / 2);
        wheelCtx.textAlign = 'right';
        wheelCtx.fillStyle = '#fff';
        wheelCtx.font = 'bold 14px "Prompt"';
        wheelCtx.shadowColor = 'rgba(0,0,0,0.2)';
        wheelCtx.shadowBlur = 4;
        wheelCtx.shadowOffsetX = 1;
        wheelCtx.shadowOffsetY = 1;
        // Draw text
        wheelCtx.fillText(currentWheelStudents[i].name, radius - 15, 5);
        wheelCtx.restore();
    }
}

function easeOut(t, b, c, d) {
    const ts = (t /= d) * t;
    const tc = ts * t;
    return b + c * (tc + -3 * ts + 3 * t);
}

function spinWheel() {
    if (isSpinning || currentWheelStudents.length === 0) return;
    isSpinning = true;
    document.getElementById('spinBtn').disabled = true;
    document.getElementById('wheelResult').innerHTML = '<span class="text-slate-400 font-normal animate-pulse text-sm">กำลังหมุน...</span>';

    const spinTimeTotal = 3000 + Math.random() * 2000;
    const spinAngleStart = Math.random() * 10 + 20; 
    let spinTime = 0;

    function rotate() {
        spinTime += 30;
        if (spinTime >= spinTimeTotal) {
            stopRotateWheel();
            return;
        }
        const spinAngle = spinAngleStart - easeOut(spinTime, 0, spinAngleStart, spinTimeTotal);
        currentAngle += (spinAngle * Math.PI / 180);
        drawWheel();
        spinTimeout = requestAnimationFrame(rotate);
    }

    rotate();
}

function stopRotateWheel() {
    clearTimeout(spinTimeout);
    isSpinning = false;
    document.getElementById('spinBtn').disabled = false;
    
    if (currentWheelStudents.length === 0) return;

    // 90 degrees offset for pointer at the top (12 o'clock)
    let degrees = (currentAngle * 180 / Math.PI) % 360;
    let pointerAngle = 270; // 270 degrees is top
    let sliceAngle = 360 / currentWheelStudents.length;
    
    // Normalize target angle
    let targetAngle = (pointerAngle - degrees + 360) % 360;
    let index = Math.floor(targetAngle / sliceAngle);

    const winner = currentWheelStudents[index];
    
    document.getElementById('wheelResult').innerHTML = `<span class="text-amber-600 animate__animated animate__tada text-xl font-black"><i class="fa-solid fa-star text-amber-400 mr-2"></i> ${winner.name}</span>`;
    
    Swal.fire({
        title: '🎉 ผู้โชคดี!',
        text: winner.name,
        icon: 'success',
        confirmButtonText: 'รับทราบ',
        confirmButtonColor: '#f59e0b'
    }).then(() => {
        // Remove from array after acknowledge
        currentWheelStudents.splice(index, 1);
        drawWheel();
        if (currentWheelStudents.length === 0) {
            document.getElementById('wheelResult').innerHTML = '<span class="text-slate-400 font-normal text-sm">สุ่มครบทุกคนแล้ว</span>';
        }
    });
}

function openWheelModal() {
    document.getElementById('wheelModal').classList.remove('hidden');
    drawWheel();
}

function closeWheelModal() {
    if (!isSpinning) {
        document.getElementById('wheelModal').classList.add('hidden');
    }
}

function resetWheel() {
    if (isSpinning) return;
    currentWheelStudents = [...initialWheelStudents];
    currentAngle = 0;
    document.getElementById('wheelResult').innerHTML = '<span class="text-slate-400 font-normal text-sm">รีเซ็ตวงล้อแล้ว กดหมุนวงล้อเพื่อเริ่มสุ่ม</span>';
    drawWheel();
}

</script>

<?php
$content = ob_get_clean();
include ROOT . '/views/layouts/admin.php';
?>
