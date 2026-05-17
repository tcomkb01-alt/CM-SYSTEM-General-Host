<?php
/**
 * Classroom Report View
 * @var array $classroom
 * @var array $students
 */
ob_start();
?>

<div class="mb-8">
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li><a href="/CM_System/admin/reports" class="text-sm font-medium text-slate-400 hover:text-indigo-600">รายงานสรุปผล</a></li>
            <li><i class="fa-solid fa-chevron-right text-slate-300 text-[10px] mx-2"></i></li>
            <li class="text-sm font-bold text-slate-800"><?= htmlspecialchars($classroom['subject_name']) ?></li>
        </ol>
    </nav>
    
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800"><?= htmlspecialchars($classroom['subject_name']) ?></h1>
            <p class="text-slate-500">รายงานภาพรวมรายวิชาและการส่งงาน</p>
        </div>
        <div class="flex gap-2">
            <button onclick="exportCSV()" class="bg-emerald-500 text-white px-5 py-3 rounded-xl font-bold shadow-lg shadow-emerald-100 hover:bg-emerald-600 transition-all flex items-center">
                <i class="fa-solid fa-file-csv mr-2"></i> Export CSV
            </button>
            <button onclick="window.print()" class="bg-slate-800 text-white px-5 py-3 rounded-xl font-bold shadow-lg shadow-slate-200 hover:bg-slate-900 transition-all flex items-center">
                <i class="fa-solid fa-print mr-2"></i> พิมพ์รายงาน
            </button>
        </div>
    </div>
</div>

<!-- Summary Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">จำนวนนักเรียน</p>
        <h4 class="text-3xl font-black text-slate-800"><?= count($students) ?> <span class="text-sm font-normal text-slate-400">คน</span></h4>
    </div>
    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">เกณฑ์การเช็คชื่อ</p>
        <h4 class="text-3xl font-black text-indigo-600"><?= $classroom['pass_criteria'] ?> <span class="text-sm font-normal text-slate-400">%</span></h4>
    </div>
    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">สถานะวิชา</p>
        <h4 class="text-3xl font-black text-emerald-500"><?= $classroom['is_active'] ? 'เปิดสอน' : 'ปิดวิชา' ?></h4>
    </div>
</div>

<!-- Data Table -->
<div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden printable-content">
    <div class="overflow-x-auto">
        <table id="reportTable" class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-800 text-white uppercase text-[10px] tracking-widest font-black">
                    <th class="px-6 py-5 text-center w-16">เลขที่</th>
                    <th class="px-6 py-5 text-center">รหัสนักเรียน</th>
                    <th class="px-6 py-5">ชื่อ-นามสกุล</th>
                    <th class="px-6 py-5 text-center">มาเรียน (ครั้ง)</th>
                    <th class="px-6 py-5 text-center">ร้อยละ</th>
                    <th class="px-6 py-5 text-center">ส่งงาน (ชิ้น)</th>
                    <th class="px-6 py-5 text-center">คะแนนสะสม</th>
                    <th class="px-6 py-5 text-center">สถานะ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($students as $row): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-5 text-center font-bold text-slate-400"><?= $row['student_number'] ?></td>
                        <td class="px-6 py-5 text-center font-bold text-slate-600"><?= $row['student_code'] ?></td>
                        <td class="px-6 py-5">
                            <p class="font-bold text-slate-700"><?= $row['prefix'] . $row['first_name'] . ' ' . $row['last_name'] ?></p>
                        </td>
                        <td class="px-6 py-5 text-center font-bold text-slate-600">
                            <?= $row['attendance_present'] ?>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-12 h-1 bg-slate-100 rounded-full overflow-hidden hidden sm:block">
                                    <div class="h-full <?= $row['attendance_percent'] >= $classroom['pass_criteria'] ? 'bg-emerald-500' : 'bg-rose-500' ?>" style="width: <?= $row['attendance_percent'] ?>%"></div>
                                </div>
                                <span class="font-black <?= $row['attendance_percent'] >= $classroom['pass_criteria'] ? 'text-emerald-600' : 'text-rose-600' ?>">
                                    <?= $row['attendance_percent'] ?>%
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center font-bold text-slate-600">
                            <?= $row['tasks_submitted'] ?> จาก <?= $row['tasks_total'] ?>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="px-4 py-1 bg-amber-50 text-amber-600 rounded-lg font-black"><?= number_format($row['tasks_score'], 2) ?></span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <?php if ($row['attendance_percent'] >= $classroom['pass_criteria']): ?>
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-full uppercase">ผ่านเกณฑ์</span>
                            <?php else: ?>
                                <span class="px-3 py-1 bg-rose-50 text-rose-600 text-[10px] font-black rounded-full uppercase">มส. / ไม่ผ่าน</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function exportCSV() {
    let csv = [];
    const rows = document.querySelectorAll("#reportTable tr");
    
    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll("td, th");
        
        for (let j = 0; j < cols.length; j++) {
            let text = cols[j].innerText.trim().replace(/"/g, '""');
            // Force text format for Excel to prevent date conversion
            row.push('="' + text + '"');
        }
        csv.push(row.join(","));
    }

    // Download
    const csvContent = "\uFEFF" + csv.join("\n"); 
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", "Report_<?= $classroom['room_code'] ?>_" + new Date().toLocaleDateString() + ".csv");
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<style>
@media print {
    /* Hide everything except the table container */
    body * { visibility: hidden; }
    .printable-content, .printable-content * { visibility: visible; }
    .printable-content { 
        position: absolute; 
        left: 0; 
        top: 0; 
        width: 100%; 
        border: none !important;
        box-shadow: none !important;
    }
    
    /* Optimize table for print */
    table { width: 100% !important; border-collapse: collapse !important; }
    th { background-color: #334155 !important; color: white !important; -webkit-print-color-adjust: exact; }
    td, th { border: 1px solid #e2e8f0 !important; padding: 12px 8px !important; font-size: 10pt !important; }
    .rounded-full, .rounded-2xl, .rounded-[2.5rem] { border-radius: 0 !important; }
    .bg-slate-800 { background-color: #1e293b !important; -webkit-print-color-adjust: exact; }
}
</style>

<?php
$content = ob_get_clean();
include ROOT . '/views/layouts/admin.php';
?>
