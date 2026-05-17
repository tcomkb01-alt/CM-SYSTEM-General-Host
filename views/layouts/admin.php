<!DOCTYPE html>
<html lang="th" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> - Classroom Management</title>
    <meta name="csrf-token" content="<?= \Core\Session::get('csrf_token') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $_ENV['APP_URL'] ?? '' ?>/css/swal-custom.css">
    <style>
        body { font-family: 'Prompt', 'Inter', sans-serif; }
        .sidebar-item-active { background-color: rgba(99, 102, 241, 0.1); color: #4f46e5; border-right: 4px solid #4f46e5; }
        .app-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .app-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body class="h-full">
    <div class="flex h-full overflow-hidden bg-slate-50">
        <!-- Mobile Sidebar Overlay -->
        <div id="mobileSidebar" class="fixed inset-0 flex z-50 md:hidden hidden" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" aria-hidden="true" onclick="toggleMobileMenu()"></div>
            <div id="mobileSidebarContent" class="relative flex-1 flex flex-col max-w-xs w-full bg-white transition-all transform -translate-x-full duration-300">
                <div class="absolute top-0 right-0 -mr-12 pt-4">
                    <button onclick="toggleMobileMenu()" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <i class="fa-solid fa-xmark text-white text-xl"></i>
                    </button>
                </div>
                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-shrink-0 flex items-center px-4 mb-8">
                        <i class="fa-solid fa-graduation-cap text-indigo-600 text-3xl mr-2"></i>
                        <span class="text-xl font-bold text-slate-800 tracking-tight">CM System</span>
                    </div>
                    <nav class="px-2 space-y-1">
                        <a href="<?= $_ENV['APP_URL'] ?? '' ?>/admin/dashboard" class="group flex items-center px-4 py-3 text-base font-medium rounded-md transition-all <?= strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false ? 'sidebar-item-active' : 'text-slate-600' ?>">
                            <i class="fa-solid fa-chart-pie mr-4 text-xl"></i> แผงควบคุม
                        </a>
                        <a href="<?= $_ENV['APP_URL'] ?? '' ?>/admin/apps" class="group flex items-center px-4 py-3 text-base font-medium rounded-md transition-all <?= strpos($_SERVER['REQUEST_URI'], 'apps') !== false || strpos($_SERVER['REQUEST_URI'], 'classrooms') !== false || strpos($_SERVER['REQUEST_URI'], 'students') !== false ? 'sidebar-item-active' : 'text-slate-600' ?>">
                            <i class="fa-solid fa-table-cells-large mr-4 text-xl"></i> แอปพลิเคชัน
                        </a>
                    </nav>
                </div>
                <div class="flex-shrink-0 flex border-t border-slate-100 p-4 bg-slate-50">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border-2 border-white shadow-sm overflow-hidden">
                            <?php if (\Core\Session::get('user_avatar')): ?>
                                <img src="<?= \Core\Session::get('user_avatar') ?>" class="w-full h-full object-cover sidebar-user-avatar">
                            <?php else: ?>
                                <?= mb_substr(\Core\Session::get('user_name', 'A'), 0, 1) ?>
                            <?php endif; ?>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold text-slate-800"><?= \Core\Session::get('user_name') ?? 'Admin' ?></p>
                            <a href="<?= $_ENV['APP_URL'] ?? '' ?>/logout" class="text-xs font-medium text-red-500">ออกจากระบบ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Static Sidebar for Desktop -->
        <aside class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-white border-r border-slate-200">
                <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
                    <div class="flex items-center flex-shrink-0 px-4 mb-10">
                        <i class="fa-solid fa-graduation-cap text-indigo-600 text-3xl mr-2"></i>
                        <span class="text-xl font-bold text-slate-800 tracking-tight">CM System</span>
                    </div>
                    <nav class="flex-1 px-3 space-y-2">
                        <a href="<?= $_ENV['APP_URL'] ?? '' ?>/admin/dashboard" class="group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all <?= strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false ? 'sidebar-item-active' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' ?>">
                            <i class="fa-solid fa-chart-pie mr-3 text-lg"></i> แผงควบคุม
                        </a>
                        <a href="<?= $_ENV['APP_URL'] ?? '' ?>/admin/apps" class="group flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all <?= strpos($_SERVER['REQUEST_URI'], 'apps') !== false || strpos($_SERVER['REQUEST_URI'], 'classrooms') !== false || strpos($_SERVER['REQUEST_URI'], 'students') !== false ? 'sidebar-item-active' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' ?>">
                            <i class="fa-solid fa-table-cells-large mr-3 text-lg"></i> แอปพลิเคชัน
                        </a>
                    </nav>
                </div>
                <div class="flex-shrink-0 flex border-t border-slate-100 p-4">
                    <div class="flex items-center w-full px-2">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border-2 border-white shadow-sm overflow-hidden">
                            <?php if (\Core\Session::get('user_avatar')): ?>
                                <img src="<?= \Core\Session::get('user_avatar') ?>" class="w-full h-full object-cover sidebar-user-avatar">
                            <?php else: ?>
                                <?= mb_substr(\Core\Session::get('user_name', 'A'), 0, 1) ?>
                            <?php endif; ?>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold text-slate-800 leading-tight"><?= \Core\Session::get('user_name') ?? 'Admin' ?></p>
                            <a href="<?= $_ENV['APP_URL'] ?? '' ?>/logout" class="text-[10px] font-bold text-red-400 hover:text-red-600 uppercase tracking-tighter">Sign Out</a>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 w-0 overflow-hidden">
            <!-- Mobile Topbar -->
            <header class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-40">
                <div class="flex items-center">
                    <i class="fa-solid fa-graduation-cap text-indigo-600 text-2xl mr-2"></i>
                    <span class="font-bold text-slate-800 tracking-tight">CM System</span>
                </div>
                <button onclick="toggleMobileMenu()" class="p-2 text-slate-500 hover:bg-slate-50 rounded-lg">
                    <i class="fa-solid fa-bars-staggered text-xl"></i>
                </button>
            </header>

            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-6 md:py-10">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-10">
                        <?php echo $content ?? ''; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('mobileSidebar');
            const content = document.getElementById('mobileSidebarContent');
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('-translate-x-full');
                }, 10);
            } else {
                content.classList.add('-translate-x-full');
                setTimeout(() => {
                    sidebar.classList.add('hidden');
                }, 300);
            }
        }
    </script>
</body>
</html>
