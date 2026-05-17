<?php
/**
 * @var array $classroom
 * @var array|null $student
 */
$BASE = $_ENV['APP_URL'] ?? '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - <?= htmlspecialchars($classroom['subject_name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        body { font-family: 'Prompt', sans-serif; background-color: #f1f5f9; }
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .tab-active { color: #4f46e5; border-bottom: 3px solid #4f46e5; }
        #reader { border: none !important; border-radius: 2rem; overflow: hidden; }
        #reader video { border-radius: 2rem !important; }
    </style>
</head>
<body class="min-h-screen pb-12">

    <?php if (!$student): ?>
        <!-- ========================================== -->
        <!-- 1. LOGIN VIEW -->
        <!-- ========================================== -->
        <div class="min-h-screen flex flex-col">
            <div class="bg-indigo-600 text-white pt-20 pb-28 px-6 rounded-b-[4rem] relative overflow-hidden shadow-2xl">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
                <div class="max-w-md mx-auto text-center relative z-10">
                    <div class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-inner text-4xl">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h1 class="text-3xl font-bold mb-2 tracking-tight">Student Portal</h1>
                    <p class="text-indigo-100 text-sm opacity-80 uppercase tracking-widest font-medium"><?= htmlspecialchars($classroom['subject_name']) ?></p>
                </div>
            </div>

            <div class="max-w-md mx-auto -mt-20 px-6 w-full relative z-20">
                <!-- Login Card -->
                <div id="loginForm" class="bg-white rounded-[2.5rem] shadow-2xl p-10 border border-slate-50">
                    <div class="text-center mb-10">
                        <h2 class="text-2xl font-black text-slate-800 mb-2">ลงชื่อเข้าใช้งาน</h2>
                        <p class="text-sm text-slate-400">กรุณาระบุตัวตนเพื่อดูข้อมูลส่วนตัว</p>
                    </div>

                    <div class="mb-8">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-2">เลขประจำตัวนักเรียน</label>
                        <div class="relative group">
                            <i class="fa-solid fa-id-card absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                            <input type="text" id="studentCodeInput" maxlength="10" placeholder="รหัส 4-10 หลัก..." 
                                   class="w-full pl-14 pr-6 py-5 bg-slate-50 border border-slate-100 rounded-3xl focus:ring-4 focus:ring-indigo-100 focus:outline-none text-xl font-bold text-slate-700 transition-all placeholder:text-slate-300 placeholder:font-normal">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <button onclick="loginStudent()" id="loginBtn" class="w-full bg-indigo-600 text-white py-5 rounded-3xl font-bold shadow-xl shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all flex items-center justify-center text-lg">
                            <span>เข้าสู่ระบบ</span>
                            <i class="fa-solid fa-right-to-bracket ml-3"></i>
                        </button>
                        
                        <div class="flex items-center my-8">
                            <div class="flex-1 h-px bg-slate-100"></div>
                            <span class="px-6 text-[10px] font-bold text-slate-300 uppercase tracking-widest">หรือแสกนบัตร</span>
                            <div class="flex-1 h-px bg-slate-100"></div>
                        </div>

                        <button onclick="startScanner()" class="w-full bg-emerald-50 text-emerald-600 border-2 border-emerald-100 py-5 rounded-3xl font-bold hover:bg-emerald-100 active:scale-95 transition-all flex items-center justify-center">
                            <i class="fa-solid fa-camera mr-3 text-xl"></i>
                            สแกนรหัสจากบัตร
                        </button>
                    </div>
                </div>

                <!-- Scanner Overlay (Hidden by default) -->
                <div id="scannerOverlay" class="hidden fixed inset-0 z-50 bg-slate-900 flex flex-col p-6">
                    <div class="flex justify-between items-center text-white mb-8">
                        <h3 class="text-lg font-bold">แสกนบัตรนักเรียน</h3>
                        <button onclick="stopScanner()" class="w-12 h-12 flex items-center justify-center bg-white/10 rounded-full text-2xl"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div id="reader" class="w-full aspect-square bg-slate-800 rounded-[3rem] overflow-hidden mb-10 border-4 border-emerald-500 shadow-2xl shadow-emerald-500/20"></div>
                    <div class="text-center text-white/60">
                        <p class="text-sm italic">จัดวางรหัสให้ตรงกับช่องแสกน</p>
                        <p class="text-[10px] uppercase tracking-widest mt-2 opacity-40">AI Barcode/QR Scanner Active</p>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- ========================================== -->
        <!-- 2. DASHBOARD VIEW (LOGGED IN) -->
        <!-- ========================================== -->
        <div class="max-w-md mx-auto px-4 pt-8">
            <!-- Top Header -->
            <div class="flex items-center justify-between mb-8 px-2">
                <div>
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">ยินดีต้อนรับ</h2>
                    <p class="text-xl font-black text-slate-800"><?= $student['prefix'] . $student['first_name'] ?></p>
                </div>
                <a href="<?= $BASE ?>/portal/<?= $classroom['room_code'] ?>/logout" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-red-500 shadow-sm border border-slate-100">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>

            <!-- Profile Summary Card -->
            <div class="bg-white rounded-[2.5rem] shadow-xl p-8 border border-white mb-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full -mr-16 -mt-16"></div>
                <div class="flex items-center mb-8 relative z-10">
                    <div class="w-20 h-20 bg-indigo-600 rounded-3xl flex items-center justify-center text-white text-3xl font-black shadow-lg shadow-indigo-200">
                        <?= mb_substr($student['first_name'], 0, 1) ?>
                    </div>
                    <div class="ml-5">
                        <p class="text-lg font-bold text-slate-800"><?= $student['prefix'] . $student['first_name'] . ' ' . $student['last_name'] ?></p>
                        <p class="text-xs font-bold text-slate-400">เลขที่ <?= $student['student_number'] ?> | <?= $student['student_code'] ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 relative z-10">
                    <div class="bg-slate-50 p-5 rounded-[2rem] border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-widest">การเข้าเรียน</p>
                        <p class="text-3xl font-black text-indigo-600"><?= $student['attendance_percent'] ?><span class="text-sm ml-1">%</span></p>
                        <div class="w-full bg-slate-200 h-1 rounded-full mt-3 overflow-hidden">
                            <div class="bg-indigo-500 h-full" style="width: <?= $student['attendance_percent'] ?>%"></div>
                        </div>
                    </div>
                    <div class="bg-slate-50 p-5 rounded-[2rem] border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-widest">มาเรียนแล้ว</p>
                        <p class="text-3xl font-black text-emerald-600"><?= $student['attendance_present'] ?><span class="text-sm ml-1">คาบ</span></p>
                        <p class="text-[10px] text-slate-400 mt-2">จากทั้งหมด <?= $student['attendance_total'] ?> คาบ</p>
                    </div>
                </div>
            </div>

            <!-- Homework / Tasks Section -->
            <div class="bg-white rounded-[2.5rem] shadow-xl p-8 border border-white mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-black text-slate-800">ภารกิจ & งานมอบหมาย</h3>
                </div>

                <div class="space-y-4">
                    <?php if (empty($student['assignments'])): ?>
                        <div class="text-center py-8">
                            <p class="text-sm text-slate-400 italic">ยังไม่มีงานมอบหมายในวิชานี้</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($student['assignments'] as $task): ?>
                            <div class="flex items-center p-4 rounded-2xl border <?= $task['status'] == 'graded' ? 'bg-emerald-50 border-emerald-100' : 'bg-slate-50 border-slate-100' ?>">
                                <div class="w-10 h-10 <?= $task['status'] == 'graded' ? 'bg-emerald-500 text-white' : 'bg-white text-slate-400' ?> rounded-xl flex items-center justify-center mr-4 shrink-0 shadow-sm">
                                    <i class="fa-solid <?= $task['status'] == 'graded' ? 'fa-check-double' : 'fa-book' ?>"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-slate-800"><?= htmlspecialchars($task['title']) ?></p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] font-bold <?= strtotime($task['due_date']) < time() && $task['status'] == 'pending' ? 'text-red-500' : 'text-slate-400' ?>">
                                            เดดไลน์: <?= date('d/m/Y', strtotime($task['due_date'])) ?>
                                        </span>
                                        <?php if ($task['status'] == 'graded'): ?>
                                            <span class="text-[10px] font-bold text-emerald-600">| คะแนน: <?= $task['score'] ?> / <?= $task['max_score'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <?php if ($task['status'] == 'graded'): ?>
                                        <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">ตรวจแล้ว</span>
                                    <?php elseif ($task['status'] == 'submitted'): ?>
                                        <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest">ส่งแล้ว</span>
                                    <?php else: ?>
                                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">ค้างส่ง</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Subject Info Card -->
            <div class="bg-indigo-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-indigo-200 relative overflow-hidden">
                <div class="absolute bottom-0 right-0 w-32 h-32 bg-white/10 rounded-full -mb-16 -mr-16"></div>
                <h3 class="text-xs font-bold text-indigo-200 uppercase tracking-widest mb-4">ข้อมูลวิชาเรียน</h3>
                <p class="text-xl font-bold mb-1"><?= htmlspecialchars($classroom['subject_name']) ?></p>
                <p class="text-sm opacity-80 mb-6">รหัสวิชา: <?= htmlspecialchars($classroom['subject_code']) ?></p>
                <div class="inline-flex items-center bg-white/20 px-4 py-2 rounded-2xl text-[10px] font-bold uppercase tracking-widest">
                    <i class="fa-solid fa-hashtag mr-2"></i> รหัสห้อง: <?= $classroom['room_code'] ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Scripts -->
    <script>
        const ROOM_CODE = '<?= $classroom['room_code'] ?>';
        const BASE_URL = '<?= $BASE ?>';
        let html5QrCode = null;

        async function loginStudent() {
            const input = document.getElementById('studentCodeInput');
            const code = input.value.trim();
            if(!code) return alert('กรุณากรอกเลขประจำตัวนักเรียน');
            
            await doLogin(code);
        }

        async function doLogin(studentCode) {
            const btn = document.getElementById('loginBtn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> กำลังตรวจสอบ...';

            try {
                const response = await fetch(`${BASE_URL}/portal/${ROOM_CODE}/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ student_code: studentCode })
                });

                const result = await response.json();
                if (result.success) {
                    location.reload(); // Reload to show dashboard (session set)
                } else {
                    alert(result.message || 'ไม่พบข้อมูลนักเรียนในระบบ');
                }
            } catch (error) {
                console.error(error);
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อระบบ');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        function startScanner() {
            document.getElementById('scannerOverlay').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: { width: 280, height: 280 } };

            html5QrCode.start(
                { facingMode: "environment" }, 
                config, 
                (decodedText) => {
                    console.log("Scanned:", decodedText);
                    doLogin(decodedText);
                },
                (errorMessage) => { /* ignore */ }
            ).catch((err) => {
                alert("ไม่สามารถเปิดกล้องได้: " + err);
                stopScanner();
            });
        }

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    document.getElementById('scannerOverlay').classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }).catch(err => console.log(err));
            } else {
                document.getElementById('scannerOverlay').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }
    </script>
</body>
</html>
