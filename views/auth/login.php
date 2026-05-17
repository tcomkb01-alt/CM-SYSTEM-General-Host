<!DOCTYPE html>
<html lang="th" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Classroom Management System</title>
    <meta name="base-url" content="<?= htmlspecialchars($_ENV['APP_URL'] ?? '') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="<?= $_ENV['APP_URL'] ?? '' ?>/css/swal-custom.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="font-sans antialiased text-slate-900 min-h-screen flex bg-white">

    <!-- Left Side: Image/Branding (Hidden on small screens) -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-indigo-900 items-center justify-center overflow-hidden">
        <!-- Background Image (Beautiful library/workspace) -->
        <div class="absolute inset-0">
            <img class="object-cover w-full h-full opacity-40 mix-blend-multiply" src="https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=2000&auto=format&fit=crop" alt="Workspace">
            <div class="absolute inset-0 bg-gradient-to-t from-indigo-900/90 via-indigo-900/40 to-transparent"></div>
        </div>
        
        <!-- Branding Text -->
        <div class="relative z-10 text-center px-12 animate__animated animate__fadeInLeft">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white/10 backdrop-blur-md mb-8 border border-white/20 shadow-2xl">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-extrabold text-white tracking-tight mb-4 drop-shadow-lg">
                Classroom<br><span class="text-indigo-300">Management System</span>
            </h1>
            <p class="text-indigo-100 text-lg max-w-md mx-auto font-light">
                ยกระดับการจัดการชั้นเรียนของคุณด้วยแพลตฟอร์มที่ทันสมัย สะดวก และปลอดภัย
            </p>
        </div>
    </div>

    <!-- Right Side: Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-24 relative">
        <div class="w-full max-w-md animate__animated animate__fadeInRight">
            
            <!-- Mobile Logo (Visible only on mobile) -->
            <div class="lg:hidden text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-600 mb-4 shadow-lg shadow-indigo-200">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900">Classroom System</h2>
            </div>

            <!-- Desktop Heading -->
            <div class="hidden lg:block mb-10">
                <h2 class="text-3xl font-bold text-slate-900 mb-2">Login System</h2>
                <p class="text-slate-500">กรุณากรอกข้อมูลเพื่อเข้าสู่ระบบการจัดการ</p>
            </div>

            <form class="space-y-6" id="loginForm">
                <?= csrfField() ?>
                
                <div>
                    <label for="username" class="block text-sm font-medium text-slate-700 mb-2">ชื่อผู้ใช้งาน</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input id="username" name="username" type="text" required class="block w-full pl-11 pr-4 py-3.5 border border-slate-200 rounded-xl text-slate-900 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 sm:text-sm" placeholder="กรอกชื่อผู้ใช้งานของคุณ">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">รหัสผ่าน</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" required class="block w-full pl-11 pr-4 py-3.5 border border-slate-200 rounded-xl text-slate-900 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 sm:text-sm" placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        <span>เข้าสู่ระบบ</span>
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </div>
            </form>
            
    <div class="mt-12 text-center">
                <p class="text-xs text-slate-400 font-medium">
                    &copy; <?= date('Y') ?> Classroom Management System. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <!-- Cookie Consent Banner -->
    <div id="cookieBanner" class="fixed bottom-0 inset-x-0 pb-2 sm:pb-5 z-50 hidden animate__animated animate__slideInUp">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
            <div class="p-3 sm:p-4 rounded-xl bg-slate-900/95 backdrop-blur shadow-2xl border border-slate-800">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
                    <div class="flex items-start sm:items-center w-full sm:w-auto flex-1">
                        <span class="flex p-2 rounded-lg bg-indigo-600 shrink-0 mt-1 sm:mt-0">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path>
                            </svg>
                        </span>
                        <p class="ml-3 font-medium text-white text-xs sm:text-sm leading-relaxed sm:leading-normal">
                            <span class="sm:hidden">เว็บไซต์นี้ใช้คุกกี้เพื่อประสบการณ์ที่ดีในการใช้งานของคุณ</span>
                            <span class="hidden sm:inline">เราใช้คุกกี้เพื่อพัฒนาประสิทธิภาพและประสบการณ์ที่ดีในการใช้เว็บไซต์ของคุณ</span>
                        </p>
                    </div>
                    <div class="flex-shrink-0 w-full sm:w-auto flex flex-row gap-2 sm:gap-3">
                        <a href="<?= rtrim($_ENV['APP_URL'] ?? '', '/') ?>/cookie-policy" class="flex-1 sm:flex-none flex items-center justify-center px-3 py-2 border border-slate-700 rounded-lg shadow-sm text-xs sm:text-sm font-medium text-slate-300 bg-transparent hover:bg-slate-800 transition-colors">
                            อ่านนโยบาย
                        </a>
                        <button id="acceptCookies" class="flex-1 sm:flex-none flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-xs sm:text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                            ยอมรับทั้งหมด
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ใช้ base URL จาก meta tag (dynamic)
        const BASE_URL = document.querySelector('meta[name="base-url"]')?.content || '';

        // Cookie Banner Logic
        document.addEventListener('DOMContentLoaded', () => {
            const banner = document.getElementById('cookieBanner');
            const acceptBtn = document.getElementById('acceptCookies');
            
            if (!localStorage.getItem('cookie_consent')) {
                banner.classList.remove('hidden');
            }

            acceptBtn.addEventListener('click', () => {
                localStorage.setItem('cookie_consent', 'accepted');
                banner.classList.replace('animate__slideInUp', 'animate__slideOutDown');
                setTimeout(() => banner.classList.add('hidden'), 1000);
            });
        });

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(BASE_URL + '/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'กำลังเข้าสู่ระบบ...',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = BASE_URL + result.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด',
                        text: result.message
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้'
                });
                console.error(error);
            }
        });
    </script>
</body>
</html>
