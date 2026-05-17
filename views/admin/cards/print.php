<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&family=Cinzel:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --card-dark:   #0D1B2A;
            --card-mid:    #122338;
            --card-deep:   #071020;
            --gold:        #C9A227;
            --gold-light:  #E2C060;
            --gold-dim:    #8B6914;
            --text-white:  #FFFFFF;
            --text-blue:   #93C5FD;
            --text-slate:  #CBD5E1;
            --footer-bg:   #FFFFFF;
            --card-w: 86mm;
            --card-h: 54mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            margin: 0;
            padding: 0;
            background: #07111e;
            font-family: 'Sarabun', sans-serif;
        }

        /* ─── A4 Page ─── */
        .page {
            width: 210mm;
            height: 297mm;
            padding: 7mm 12mm;
            margin: 10mm auto;
            background: white;
            box-shadow: 0 0 40px rgba(0,0,0,0.5);
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: repeat(5, 54mm);
            gap: 2.5mm 10mm;
            page-break-after: always;
            justify-items: center;
            align-items: center;
        }

        @media print {
            body { background: none; }
            .page { margin: 0; box-shadow: none; padding: 7mm 12mm; }
            .no-print { display: none !important; }
            .only-print { display: block !important; }
        }

        .only-print { display: none; }

        /* ─── Card Shell ─── */
        .card {
            width: var(--card-w);
            height: var(--card-h);
            background-color: var(--card-dark);
            border-radius: 3.5mm;
            position: relative;
            overflow: hidden;
            color: white;
        }

        /* ─── Outer Gold Frame ─── */
        .frame-outer {
            position: absolute;
            inset: 1.8mm;
            border: 0.6pt solid var(--gold);
            border-radius: 2.5mm;
            z-index: 20;
            pointer-events: none;
        }
        .frame-inner {
            position: absolute;
            inset: 2.6mm;
            border: 0.25pt solid var(--gold);
            border-radius: 2mm;
            z-index: 20;
            opacity: 0.4;
            pointer-events: none;
        }

        /* ─── Mandala / Geometric Pattern SVG ─── */
        .bg-pattern {
            position: absolute;
            top: 0; right: 0;
            width: 100%; height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        /* ─── Gold Side Bar ─── */
        .side-bar {
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3.2pt;
            background: var(--gold);
            z-index: 10;
        }

        /* ─── Header ─── */
        .header {
            position: absolute;
            top: 4mm;
            left: 5.5mm;
            right: 4mm;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 2mm;
        }

        .logo-ring {
            width: 9mm;
            height: 9mm;
            border-radius: 50%;
            border: 0.7pt solid var(--gold);
            background: rgba(201,162,39,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }
        .logo-ring img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 1pt;
        }
        .logo-ring i {
            font-size: 5pt;
            color: var(--gold);
        }

        .school-name {
            font-size: 7pt;
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: 0.01em;
            /* clamp to one line, truncate if too long */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 55mm;
        }
        .school-sub {
            font-size: 4.5pt;
            color: var(--gold);
            font-weight: 300;
            letter-spacing: 0.02em;
            margin-top: 0.5pt;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 55mm;
        }

        /* ─── Gold Divider with Diamond ─── */
        .divider {
            position: absolute;
            top: 15mm;
            left: 4.5mm;
            right: 4.5mm;
            height: 2pt;
            z-index: 10;
            display: flex;
            align-items: center;
        }
        .divider-line {
            flex: 1;
            height: 0.6pt;
            background: var(--gold);
            opacity: 0.55;
        }
        .divider-diamond {
            width: 3.5pt;
            height: 3.5pt;
            background: var(--gold);
            transform: rotate(45deg);
            margin: 0 2pt;
            opacity: 0.85;
            flex-shrink: 0;
        }

        /* ─── Photo Box ─── */
        /* Card body (below header divider) = 54mm - 16.5mm header - 11mm footer = 26.5mm usable
           Photo: 18mm wide x 22mm tall — keeps good proportion without crowding */
        .photo-box {
            position: absolute;
            top: 17.5mm;
            left: 5mm;
            width: 18mm;
            height: 22mm;
            background: var(--card-deep);
            border: 0.7pt solid var(--gold);
            z-index: 10;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .photo-box:hover {
            border-color: var(--text-white);
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
        }
        .photo-box:hover .upload-overlay {
            opacity: 1;
        }
        .upload-overlay {
            position: absolute;
            inset: 0;
            background: rgba(13, 27, 42, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 15;
            color: var(--gold);
            font-size: 8pt;
        }
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        /* Corner ticks */
        .photo-box::before,
        .photo-box::after {
            content: '';
            position: absolute;
            width: 2.5mm; height: 2.5mm;
            border-color: var(--gold);
            border-style: solid;
            z-index: 12;
        }
        .photo-box::before {
            top: -1pt; left: -1pt;
            border-width: 1.2pt 0 0 1.2pt;
        }
        .photo-box::after {
            bottom: -1pt; right: -1pt;
            border-width: 0 1.2pt 1.2pt 0;
        }
        .photo-corner-tr {
            position: absolute;
            top: -1pt; right: -1pt;
            width: 2.5mm; height: 2.5mm;
            border-top: 1.2pt solid var(--gold);
            border-right: 1.2pt solid var(--gold);
            z-index: 12;
        }
        .photo-corner-bl {
            position: absolute;
            bottom: -1pt; left: -1pt;
            width: 2.5mm; height: 2.5mm;
            border-bottom: 1.2pt solid var(--gold);
            border-left: 1.2pt solid var(--gold);
            z-index: 12;
        }

        /* ─── Info Section ─── */
        .info-section {
            position: absolute;
            top: 17.5mm;
            left: 25.5mm;
            right: 4.5mm;
            z-index: 10;
        }

        .st-name {
            font-size: 9pt;
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: 0.01em;
            margin-bottom: 1.2mm;
            padding-right: 16mm; /* Increased space for larger QR */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .qr-section {
            position: absolute;
            top: -1mm;
            right: 0;
            width: 14mm;
            height: 14mm;
            border: 0.5pt solid var(--gold);
            padding: 0.8mm;
            background: white; /* White background for better scannability */
        }
        .qr-section img {
            width: 100%;
            height: 100%;
        }

        /* ─── Student Number Badge ─── */
        .st-number-badge {
            position: absolute;
            top: 4.5mm;
            right: 5mm;
            width: 7.5mm;
            height: 7.5mm;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: var(--card-deep);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9pt;
            font-weight: 900;
            z-index: 30;
            box-shadow: 0 0 8px rgba(201,162,39,0.4);
            border: 0.5pt solid var(--card-dark);
        }
        .st-number-label {
            position: absolute;
            top: -2.5mm;
            font-size: 3pt;
            color: var(--gold);
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.1em;
        }

        /* รหัสประจำตัว badge ใต้ชื่อ */
        .st-code-badge {
            display: inline-flex;
            align-items: center;
            gap: 2pt;
            background: rgba(201,162,39,0.10);
            border: 0.5pt solid rgba(201,162,39,0.45);
            border-radius: 0.8mm;
            padding: 0.6mm 1.8mm;
            margin-bottom: 1.5mm;
        }
        .st-code-label {
            font-size: 5pt;
            color: rgba(201,162,39,0.65);
            font-weight: 400;
            letter-spacing: 0.03em;
        }
        .st-code-value {
            font-size: 6pt;
            color: var(--gold);
            font-weight: 700;
            letter-spacing: 0.07em;
        }

        .st-class {
            font-size: 6pt;
            color: var(--text-blue);
            font-weight: 300;
            letter-spacing: 0.02em;
            margin-bottom: 2mm;
        }

        /* ID (เลขบัตรประชาชน) box */
        .id-box {
            border: 0.6pt solid var(--gold);
            background: rgba(201,162,39,0.08);
            padding: 1mm 2mm;
            border-radius: 1mm;
            font-size: 5.5pt;
            font-weight: 600;
            color: var(--gold);
            display: block;
            letter-spacing: 0.02em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ─── Lower Divider ─── */
        .divider-lower {
            position: absolute;
            bottom: 12.5mm;
            left: 4.5mm;
            right: 4.5mm;
            height: 2pt;
            z-index: 10;
            display: flex;
            align-items: center;
        }

        /* ─── Footer ─── */
        .footer-box {
            position: absolute;
            bottom: 0; left: 0;
            width: 100%;
            height: 11mm;
            background: var(--footer-bg);
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center; /* Center Barcode */
            padding: 0 3.5mm;
            border-top: 0.9pt solid var(--gold);
        }
        .footer-box::before {
            content: '';
            position: absolute;
            top: 1.2pt; left: 0; right: 0;
            height: 0.3pt;
            background: var(--gold);
            opacity: 0.35;
        }

        .barcode-wrap {
            height: 8.5mm;
            width: auto;
            max-width: 52mm;
            overflow: hidden;          /* clip บรรทัดตัวเลขออก */
            flex-shrink: 0;
            margin-bottom: 1.5mm;      /* Push up slightly */
        }
        .barcode-img {
            height: 8mm;
            width: 35mm;
            image-rendering: pixelated;
            image-rendering: crisp-edges;
            display: block;
        }
        .qr-img {
            height: 20mm;
            width: 20mm;
            flex-shrink: 0;
        }

        /* ─── Controls ─── */
        .controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-family: 'Sarabun', sans-serif;
            font-size: 14px;
            text-decoration: none;
            color: white;
            cursor: pointer;
            border: none;
        }
        .btn-print {
            background: linear-gradient(135deg, #C9A227, #a8821d);
            color: #0D1B2A;
        }
        .btn-back { background: #334155; }
    </style>
</head>
<body>

<div class="controls no-print">
    <a href="/CM_System/admin/cards" class="btn btn-back">← ย้อนกลับ</a>
    <button onclick="window.print()" class="btn btn-print">🖨 พิมพ์บัตร (A4 · 10 ใบ)</button>
</div>

<?php
$chunks = array_chunk($students, 10);
foreach ($chunks as $pageStudents):
?>
<div class="page">
    <?php foreach ($pageStudents as $std): ?>
    <div class="card">

        <!-- ═══ Background SVG Pattern ═══ -->
        <svg class="bg-pattern" viewBox="0 0 245 153" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
            <!-- Top-right mandala -->
            <g opacity="0.10">
                <circle cx="245" cy="0" r="72"  fill="none" stroke="#C9A227" stroke-width="0.6"/>
                <circle cx="245" cy="0" r="55"  fill="none" stroke="#C9A227" stroke-width="0.5"/>
                <circle cx="245" cy="0" r="40"  fill="none" stroke="#C9A227" stroke-width="0.4"/>
                <circle cx="245" cy="0" r="26"  fill="none" stroke="#C9A227" stroke-width="0.35"/>
                <line x1="245" y1="0" x2="173" y2="72"  stroke="#C9A227" stroke-width="0.3"/>
                <line x1="245" y1="0" x2="190" y2="78"  stroke="#C9A227" stroke-width="0.3"/>
                <line x1="245" y1="0" x2="208" y2="80"  stroke="#C9A227" stroke-width="0.3"/>
                <line x1="245" y1="0" x2="165" y2="55"  stroke="#C9A227" stroke-width="0.3"/>
                <line x1="245" y1="0" x2="160" y2="32"  stroke="#C9A227" stroke-width="0.3"/>
                <line x1="245" y1="0" x2="220" y2="72"  stroke="#C9A227" stroke-width="0.3"/>
                <line x1="245" y1="0" x2="173" y2="20"  stroke="#C9A227" stroke-width="0.3"/>
            </g>
            <!-- Bottom-left mandala -->
            <g opacity="0.07">
                <circle cx="0" cy="153" r="60" fill="none" stroke="#C9A227" stroke-width="0.6"/>
                <circle cx="0" cy="153" r="44" fill="none" stroke="#C9A227" stroke-width="0.4"/>
                <circle cx="0" cy="153" r="28" fill="none" stroke="#C9A227" stroke-width="0.35"/>
                <line x1="0" y1="153" x2="60" y2="93"  stroke="#C9A227" stroke-width="0.3"/>
                <line x1="0" y1="153" x2="44" y2="100" stroke="#C9A227" stroke-width="0.3"/>
                <line x1="0" y1="153" x2="30" y2="105" stroke="#C9A227" stroke-width="0.3"/>
                <line x1="0" y1="153" x2="60" y2="120" stroke="#C9A227" stroke-width="0.3"/>
            </g>
            <!-- Subtle diagonal lines mid area -->
            <g opacity="0.04" stroke="#C9A227" stroke-width="0.5">
                <line x1="80"  y1="0"   x2="0"   y2="80"/>
                <line x1="110" y1="0"   x2="0"   y2="110"/>
                <line x1="140" y1="0"   x2="30"  y2="110"/>
                <line x1="170" y1="0"   x2="60"  y2="110"/>
            </g>
        </svg>

        <!-- ═══ Frames ═══ -->
        <div class="frame-outer"></div>
        <div class="frame-inner"></div>

        <!-- ═══ Left Gold Bar ═══ -->
        <div class="side-bar"></div>

        <!-- ═══ Student Number ═══ -->
        <div class="st-number-badge">
            <span class="st-number-label">เลขที่</span>
            <?= $std['student_number'] ?>
        </div>

        <!-- ═══ Header ═══ -->
        <div class="header">
            <div class="logo-ring">
                <?php if (!empty($settings['school_logo'])): ?>
                    <img src="<?= $settings['school_logo'] ?>" alt="Logo">
                <?php else: ?>
                    <i class="fa-solid fa-graduation-cap"></i>
                <?php endif; ?>
            </div>
            <div>
                <div class="school-name"><?= htmlspecialchars($settings['school_name']) ?></div>
                <div class="school-sub"><?= htmlspecialchars($settings['school_name_en']) ?> &nbsp;•&nbsp; <?= $settings['academic_year'] ?></div>
            </div>
        </div>

        <!-- ═══ Divider ═══ -->
        <div class="divider">
            <div class="divider-line"></div>
            <div class="divider-diamond"></div>
            <div class="divider-line"></div>
        </div>

        <!-- ═══ Photo ═══ -->
        <div class="photo-box no-print" onclick="triggerUpload(<?= $std['id'] ?>)">
            <div class="upload-overlay"><i class="fa-solid fa-camera"></i></div>
            <input type="file" id="upload-<?= $std['id'] ?>" class="hidden" style="display:none" onchange="handleUpload(this, <?= $std['id'] ?>)" accept="image/*">
            <img src="<?= !empty($std['avatar']) ? $std['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($std['first_name']) . '&background=0D1B2A&color=C9A227&size=128' ?>" id="img-<?= $std['id'] ?>" alt="Photo">
            <div class="photo-corner-tr"></div>
            <div class="photo-corner-bl"></div>
        </div>
        
        <!-- Photo for Printing (No hover effect) -->
        <div class="photo-box only-print">
            <img src="<?= !empty($std['avatar']) ? $std['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($std['first_name']) . '&background=0D1B2A&color=C9A227&size=128' ?>" alt="Photo">
            <div class="photo-corner-tr"></div>
            <div class="photo-corner-bl"></div>
        </div>

        <!-- ═══ Info ═══ -->
        <div class="info-section">
            <div class="qr-section">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode($std['student_code']) ?>" alt="QR">
            </div>
            <div class="st-name"><?= htmlspecialchars($std['prefix'] . $std['first_name'] . ' ' . $std['last_name']) ?></div>
            <div class="st-code-badge">
                <span class="st-code-label">รหัสประจำตัว</span>
                <span class="st-code-value"><?= htmlspecialchars($std['student_code']) ?></span>
            </div>
            <div class="st-class">
                <?php if (!empty($std['class_name'])): ?>
                    <?= htmlspecialchars($std['class_name']) ?>
                <?php endif; ?>
            </div>
            <div class="id-box">
                เลขบัตรประชาชน&nbsp;:&nbsp;<?= substr($std['student_code'], 0, 1) ?>-XXXX-XXXXX-<?= substr($std['student_code'], -2) ?>
            </div>
        </div>

        <!-- ═══ Lower Divider ═══ -->
        <div class="divider-lower">
            <div class="divider-line"></div>
            <div class="divider-diamond"></div>
            <div class="divider-line"></div>
        </div>

        <!-- ═══ Footer ═══ -->
        <div class="footer-box">
            <div class="barcode-wrap">
                <img src="https://bwipjs-api.metafloor.com/?bcid=code128&text=<?= urlencode($std['student_code']) ?>&includetext=false&scale=3"
                     class="barcode-img" alt="Barcode">
            </div>
        </div>

    </div>
    <?php endforeach; ?>
</div>
<?php endforeach; ?>

<script>
    function triggerUpload(studentId) {
        document.getElementById('upload-' + studentId).click();
    }
    async function handleUpload(input, studentId) {
        if (!input.files || !input.files[0]) return;
        const formData = new FormData();
        formData.append('photo', input.files[0]);
        formData.append('student_id', studentId);
        formData.append('csrf_token', '<?= \Core\Session::get('csrf_token') ?>');
        try {
            const response = await fetch('/CM_System/admin/students/upload-photo', {
                method: 'POST',
                body: formData
            });
            const text = await response.text();
            try {
                const result = JSON.parse(text);
                if (result.status === 'success') {
                    const images = document.querySelectorAll('#img-' + studentId);
                    images.forEach(img => { img.src = result.url + '?t=' + new Date().getTime(); });
                    const card = input.closest('.card');
                    if (card) {
                        const printImg = card.querySelector('.only-print img');
                        if (printImg) printImg.src = result.url + '?t=' + new Date().getTime();
                    }
                } else {
                    alert('เกิดข้อผิดพลาด: ' + result.message);
                }
            } catch (e) {
                // If not JSON, it's likely a PHP Error/Scary screen
                console.error('Server Response:', text);
                alert('Server Error: ' + text.substring(0, 500));
            }
        } catch (error) {
            alert('Network Error: ' + error.message);
        }
    }
</script>
</body>
</html>