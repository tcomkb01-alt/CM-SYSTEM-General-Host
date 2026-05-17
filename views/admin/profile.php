<?php
/**
 * @var array $user
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
                <span class="text-sm font-bold text-slate-800 tracking-tight">ข้อมูลส่วนตัว</span>
            </div>
        </li>
    </ol>
</nav>

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">ข้อมูลส่วนตัว</h1>
        <p class="text-slate-500 text-sm">จัดการข้อมูลบัญชีของคุณและตั้งค่าความปลอดภัย</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Sidebar / Photo -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 text-center">
                <div class="relative inline-block mb-4 group">
                    <div class="w-32 h-32 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-4xl font-bold border-4 border-white shadow-md overflow-hidden" id="avatarPreview">
                        <?php if ($user['avatar']): ?>
                            <img src="<?= htmlspecialchars($user['avatar']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= mb_substr($user['display_name'], 0, 1) ?>
                        <?php endif; ?>
                    </div>
                    <input type="file" id="avatarInput" class="hidden" accept="image/*">
                    <button type="button" id="uploadAvatarBtn" class="absolute bottom-0 right-0 bg-white p-2 rounded-full shadow-lg border border-slate-200 text-slate-600 hover:text-indigo-600 transition-all">
                        <i class="fa-solid fa-camera text-sm"></i>
                    </button>
                </div>
                <h3 class="font-bold text-slate-800"><?= htmlspecialchars($user['display_name']) ?></h3>
                <p class="text-xs text-slate-500 uppercase tracking-widest mt-1"><?= strtoupper($user['role']) ?></p>
            </div>

            <div class="bg-indigo-600 p-6 rounded-2xl shadow-lg text-white">
                <div class="flex items-center mb-4">
                    <i class="fa-solid fa-shield-halved text-2xl mr-3 opacity-50"></i>
                    <h4 class="font-bold">ระดับความปลอดภัย</h4>
                </div>
                <div class="w-full bg-indigo-400/30 rounded-full h-2 mb-2">
                    <div class="bg-white h-2 rounded-full" style="width: 85%"></div>
                </div>
                <p class="text-[10px] opacity-80">บัญชีของคุณได้รับการป้องกันด้วย CSRF Protection และรหัสผ่านที่เข้ารหัสแล้ว</p>
            </div>
        </div>

        <!-- Main Form -->
        <div class="md:col-span-2 space-y-8">
            <!-- Basic Info Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 flex items-center">
                        <i class="fa-solid fa-user-gear mr-2 text-indigo-500"></i> แก้ไขข้อมูลทั่วไป
                    </h3>
                </div>
                <form id="profileForm" class="p-6 space-y-4">
                    <?= csrfField() ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">ชื่อที่แสดง (Display Name)</label>
                            <input type="text" name="display_name" value="<?= htmlspecialchars($user['display_name']) ?>" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">ชื่อผู้ใช้งาน (Username)</label>
                            <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled class="w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-xl text-slate-400 cursor-not-allowed">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">อีเมล (Email)</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">เบอร์โทรศัพท์ (Phone)</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-indigo-700 shadow-md transition-all">
                            บันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 flex items-center">
                        <i class="fa-solid fa-key mr-2 text-amber-500"></i> เปลี่ยนรหัสผ่าน
                    </h3>
                </div>
                <form id="passwordForm" class="p-6 space-y-4">
                    <?= csrfField() ?>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">รหัสผ่านปัจจุบัน</label>
                        <input type="password" name="current_password" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">รหัสผ่านใหม่</label>
                            <input type="password" name="new_password" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" name="confirm_password" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="bg-slate-800 text-white px-6 py-2 rounded-xl font-bold hover:bg-slate-900 shadow-md transition-all">
                            เปลี่ยนรหัสผ่าน
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const BASE_URL = '<?= $_ENV['APP_URL'] ?? '' ?>';

// Handle Profile Update
document.getElementById('profileForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch(BASE_URL + '/admin/profile/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: result.message }).then(() => window.location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้' });
    }
});

// Handle Password Update
document.getElementById('passwordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Demo Mode Lock
    Swal.fire({
        icon: 'warning',
        title: 'ระบบถูกล็อคชั่วคราว',
        text: 'ขออภัย ไม่สามารถเปลี่ยนรหัสผ่านได้ในขณะนี้ เพื่อป้องกันผู้ใช้งานอื่นไม่สามารถเข้าสู่ระบบในโหมด Demo',
        confirmButtonText: 'เข้าใจแล้ว',
        confirmButtonColor: '#4f46e5'
    });
    return;

    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch(BASE_URL + '/admin/profile/password', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: result.message }).then(() => e.target.reset());
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้' });
    }
});

// Handle Avatar Upload
const avatarInput = document.getElementById('avatarInput');
const uploadBtn = document.getElementById('uploadAvatarBtn');
const avatarPreview = document.getElementById('avatarPreview');

uploadBtn.addEventListener('click', () => avatarInput.click());

avatarInput.addEventListener('change', async (e) => {
    if (e.target.files.length === 0) return;

    const file = e.target.files[0];
    
    // Show loading state
    uploadBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-sm"></i>';
    uploadBtn.disabled = true;

    const formData = new FormData();
    formData.append('avatar', file);

    try {
        const response = await fetch(BASE_URL + '/admin/profile/avatar', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="csrf_token"]').value
            },
            body: formData
        });
        const result = await response.json();
        
        if (result.success) {
            // Update preview
            avatarPreview.innerHTML = `<img src="${result.avatar_url}" class="w-full h-full object-cover">`;
            
            // Also update the sidebar avatar if it exists
            const sidebarAvatars = document.querySelectorAll('.sidebar-user-avatar');
            sidebarAvatars.forEach(img => img.src = result.avatar_url);

            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: result.message });
        } else {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: result.message });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถอัปโหลดภาพได้' });
    } finally {
        uploadBtn.innerHTML = '<i class="fa-solid fa-camera text-sm"></i>';
        uploadBtn.disabled = false;
        avatarInput.value = ''; // Reset input
    }
});
</script>

<?php
$content = ob_get_clean();
include ROOT . '/views/layouts/admin.php';
?>
