<?php
$BASE = $_ENV['APP_URL'] ?? '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เช็คชื่อเข้าเรียน - <?= htmlspecialchars($session['subject_name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        body { font-family: 'Prompt', sans-serif; }
        .glass { background: rgba(255,255,255,0.85); backdrop-filter: blur(20px); }
        #html5-qrcode-button-camera-permission, #html5-qrcode-anchor-scan-type-change { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-600 via-violet-600 to-purple-700 flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <!-- Header Card -->
    <div class="text-center text-white mb-6">
        <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-graduation-cap text-3xl"></i>
        </div>
        <h1 class="text-2xl font-bold">เช็คชื่อเข้าเรียน</h1>
        <p class="text-indigo-200 text-sm mt-1"><?= htmlspecialchars($session['subject_name']) ?> | คาบที่ <?= $session['period_number'] ?></p>
        <p class="text-indigo-300 text-xs mt-1">วันที่ <?= date('d/m/Y', strtotime($session['session_date'])) ?></p>
    </div>

    <!-- Main Card -->
    <div class="glass rounded-3xl shadow-2xl overflow-hidden">
        <!-- Tab Switcher -->
        <div class="flex bg-slate-100 m-4 p-1 rounded-2xl">
            <button id="tabManual" onclick="switchTab('manual')" class="flex-1 py-2.5 rounded-xl text-sm font-bold transition-all bg-white text-indigo-600 shadow-sm">
                <i class="fa-solid fa-keyboard mr-1"></i> กรอกเลขประจำตัว
            </button>
            <button id="tabScan" onclick="switchTab('scan')" class="flex-1 py-2.5 rounded-xl text-sm font-medium transition-all text-slate-500">
                <i class="fa-solid fa-camera mr-1"></i> สแกนบัตรนักเรียน
            </button>
        </div>

        <!-- Manual Input -->
        <div id="panelManual" class="px-6 pb-6">
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">เลขประจำตัวนักเรียน</label>
                <input type="text" id="studentCodeInput" placeholder="เช่น 12345" inputmode="numeric"
                       class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-2xl text-center text-xl font-bold tracking-widest focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
            </div>
            <button onclick="submitCheckin()" id="submitBtn"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-2xl font-bold text-sm shadow-lg transition-all flex items-center justify-center">
                <i class="fa-solid fa-check-circle mr-2"></i> ยืนยันเช็คชื่อ
            </button>
        </div>

        <!-- Camera Scan -->
        <div id="panelScan" class="px-6 pb-6 hidden">
            <p class="text-center text-xs text-slate-500 mb-3">นำบาร์โค้ดบนบัตรนักเรียนมาวางหน้ากล้อง</p>
            <div id="qr-reader" class="rounded-2xl overflow-hidden border-2 border-slate-200 mb-3" style="min-height:250px"></div>
            <button onclick="switchTab('manual')" class="w-full bg-slate-100 text-slate-600 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-200 transition-all">
                <i class="fa-solid fa-keyboard mr-1"></i> กลับไปกรอกเลข
            </button>
        </div>
    </div>

    <!-- Result Message -->
    <div id="resultBox" class="hidden mt-4 rounded-2xl p-5 text-center shadow-lg"></div>
</div>

<script>
const BASE_URL = '<?= $BASE ?>';
const SESSION_ID = <?= $session['id'] ?>;
let html5QrCode = null;

function switchTab(tab) {
    const tabManual = document.getElementById('tabManual');
    const tabScan = document.getElementById('tabScan');
    const panelManual = document.getElementById('panelManual');
    const panelScan = document.getElementById('panelScan');

    if (tab === 'manual') {
        tabManual.className = 'flex-1 py-2.5 rounded-xl text-sm font-bold transition-all bg-white text-indigo-600 shadow-sm';
        tabScan.className = 'flex-1 py-2.5 rounded-xl text-sm font-medium transition-all text-slate-500';
        panelManual.classList.remove('hidden');
        panelScan.classList.add('hidden');
        stopScanner();
    } else {
        tabScan.className = 'flex-1 py-2.5 rounded-xl text-sm font-bold transition-all bg-white text-indigo-600 shadow-sm';
        tabManual.className = 'flex-1 py-2.5 rounded-xl text-sm font-medium transition-all text-slate-500';
        panelScan.classList.remove('hidden');
        panelManual.classList.add('hidden');
        startScanner();
    }
}

function startScanner() {
    if (html5QrCode) return;
    html5QrCode = new Html5Qrcode("qr-reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 280, height: 150 } },
        (decodedText) => {
            // Barcode scanned - use as student code
            stopScanner();
            document.getElementById('studentCodeInput').value = decodedText.trim();
            switchTab('manual');
            submitCheckin();
        },
        () => {} // ignore errors
    ).catch(err => {
        document.getElementById('qr-reader').innerHTML = '<div class="p-8 text-center text-slate-400"><i class="fa-solid fa-camera-slash text-3xl mb-2"></i><p class="text-sm">ไม่สามารถเปิดกล้องได้</p><p class="text-xs mt-1">กรุณาอนุญาตการเข้าถึงกล้อง</p></div>';
    });
}

function stopScanner() {
    if (html5QrCode) {
        html5QrCode.stop().catch(() => {});
        html5QrCode = null;
    }
}

function translateThaiToEn(text) {
    const map = {
        'ๅ': '1', '/': '2', '-': '3', 'ภ': '4', 'ถ': '5', 'ุ': '6', 'ึ': '7', 'ค': '8', 'ต': '9', 'จ': '0',
        'ๆ': '1', '๑': '2', '๒': '3', '๓': '4', '๔': '5', 'ู': '6', '฿': '7', '๕': '8', '๖': '9', '๗': '0'
    };
    return text.split('').map(c => map[c] || c).join('');
}

async function submitCheckin() {
    let code = document.getElementById('studentCodeInput').value.trim();
    if (!code) {
        showResult(false, 'กรุณากรอกเลขประจำตัว');
        return;
    }

    // แปลงภาษาไทยเป็นเลขอังกฤษ
    code = translateThaiToEn(code);

    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> กำลังตรวจสอบ...';

    try {
        const response = await fetch(`${BASE_URL}/attendance/checkin/${SESSION_ID}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ student_code: code })
        });
        const result = await response.json();
        if (result.success) {
            showResult(true, `${result.student_name}<br><span class="text-sm opacity-80">เช็คชื่อเข้าเรียนสำเร็จ!</span>`);
            document.getElementById('studentCodeInput').value = '';
        } else {
            showResult(false, result.message);
        }
    } catch (e) {
        showResult(false, 'เกิดข้อผิดพลาดในการเชื่อมต่อ');
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-check-circle mr-2"></i> ยืนยันเช็คชื่อ';
}

function showResult(success, msg) {
    const box = document.getElementById('resultBox');
    box.classList.remove('hidden');
    if (success) {
        box.className = 'mt-4 rounded-2xl p-5 text-center shadow-lg bg-emerald-500 text-white';
        box.innerHTML = `<i class="fa-solid fa-circle-check text-4xl mb-2"></i><p class="font-bold text-lg">${msg}</p>`;
    } else {
        box.className = 'mt-4 rounded-2xl p-5 text-center shadow-lg bg-red-500 text-white';
        box.innerHTML = `<i class="fa-solid fa-circle-xmark text-4xl mb-2"></i><p class="font-bold">${msg}</p>`;
    }
    setTimeout(() => box.classList.add('hidden'), 4000);
}

// Enter key support
document.getElementById('studentCodeInput').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') submitCheckin();
});
</script>
</body>
</html>
