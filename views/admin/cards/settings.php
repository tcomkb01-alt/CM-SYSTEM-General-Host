<?php
/**
 * Student Card Settings View
 * @var array $settings
 */
ob_start();
?>

<div class="mb-10 flex justify-between items-end">
    <div>
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li><a href="/CM_System/admin/cards" class="text-sm font-medium text-slate-400 hover:text-rose-600">บัตรนักเรียน</a></li>
                <li><i class="fa-solid fa-chevron-right text-slate-300 text-[10px] mx-2"></i></li>
                <li class="text-sm font-bold text-slate-800">ตั้งค่าข้อมูลบนบัตร</li>
            </ol>
        </nav>
        <h1 class="text-3xl font-black text-slate-800 mb-2">ตั้งค่าข้อมูลโรงเรียน</h1>
        <p class="text-slate-500">ปรับแต่งชื่อโรงเรียนและโลโก้ที่จะปรากฏบนบัตรนักเรียน</p>
    </div>
</div>

<div class="max-w-4xl">
    <form action="/CM_System/admin/cards/settings" method="POST" enctype="multipart/form-data" class="space-y-6">
        <?= csrfField() ?>
        
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Logo Upload -->
                <div class="flex flex-col items-center justify-center p-6 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200">
                    <div id="previewContainer" class="mb-4">
                        <?php if (!empty($settings['school_logo'])): ?>
                            <img id="logoPreview" src="<?= $settings['school_logo'] ?>" class="w-32 h-32 object-contain" alt="Logo">
                        <?php else: ?>
                            <div id="logoPlaceholder" class="w-32 h-32 bg-white rounded-full flex items-center justify-center shadow-inner">
                                <i class="fa-solid fa-image text-4xl text-slate-200"></i>
                            </div>
                            <img id="logoPreview" src="" class="w-32 h-32 object-contain hidden" alt="Logo">
                        <?php endif; ?>
                    </div>
                    
                    <label class="cursor-pointer bg-white px-6 py-2 rounded-xl text-xs font-black text-slate-600 shadow-sm border border-slate-100 hover:bg-slate-50 transition-colors">
                        <span>เลือกไฟล์โลโก้</span>
                        <input type="file" name="school_logo" id="logoInput" class="hidden" accept="image/*">
                    </label>
                    <p class="text-[10px] text-slate-400 mt-2">แนะนำไฟล์ PNG โปร่งใส</p>
                </div>

                <!-- Text Settings -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">ชื่อโรงเรียน (ภาษาไทย)</label>
                        <input type="text" name="school_name" value="<?= htmlspecialchars($settings['school_name']) ?>" class="w-full px-5 py-3 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-rose-500 font-bold text-slate-700" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">ชื่อย่อ/Tagline (ภาษาอังกฤษ)</label>
                        <input type="text" name="school_name_en" value="<?= htmlspecialchars($settings['school_name_en']) ?>" class="w-full px-5 py-3 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-rose-500 font-bold text-slate-700" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">ปีการศึกษา</label>
                        <input type="text" name="academic_year" value="<?= htmlspecialchars($settings['academic_year']) ?>" class="w-full px-5 py-3 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-rose-500 font-bold text-slate-700" placeholder="เช่น 2567" required>
                    </div>
                </div>
            </div>

            <div class="mt-10 flex justify-end">
                <button type="submit" class="bg-rose-600 text-white px-10 py-4 rounded-2xl font-black shadow-xl shadow-rose-200 hover:bg-rose-700 hover:-translate-y-1 transition-all">
                    บันทึกการตั้งค่า
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('logoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const preview = document.getElementById('logoPreview');
            const placeholder = document.getElementById('logoPlaceholder');
            
            preview.src = event.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        }
        reader.readAsDataURL(file);
    }
});
</script>

<?php
$content = ob_get_clean();
include ROOT . '/views/layouts/admin.php';
?>
