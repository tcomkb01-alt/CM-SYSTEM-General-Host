<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activate License - CM_System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-slate-950 text-white min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full">
        <div class="glass p-8 rounded-3xl shadow-2xl space-y-6">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600/20 text-blue-500 rounded-2xl mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h1 class="text-3xl font-bold">System Activation</h1>
                <p class="text-slate-400 mt-2"><?= htmlspecialchars($reason ?? 'Enter your license key to continue.') ?></p>
            </div>

            <form id="activationForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">License Key</label>
                    <input type="text" name="license_key" placeholder="XXXX-XXXX-XXXX-XXXX" 
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase tracking-widest text-center font-mono">
                </div>
                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 rounded-xl transition-all shadow-lg shadow-blue-600/20 transform hover:scale-[1.02] active:scale-95">
                    Activate Now
                </button>
            </form>

            <div id="message" class="hidden p-4 rounded-xl text-sm text-center"></div>

            <div class="pt-6 border-t border-slate-800 text-center">
                <p class="text-xs text-slate-500">
                    Domain: <span class="text-slate-300"><?= $_SERVER['HTTP_HOST'] ?></span><br>
                    Need help? <a href="https://tcomkb.com/support" class="text-blue-400 hover:underline">Contact Support</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('activationForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = e.target.querySelector('button');
            const msg = document.getElementById('message');
            const formData = new FormData(e.target);

            btn.disabled = true;
            btn.innerHTML = '<span class="animate-pulse">Verifying...</span>';
            msg.classList.add('hidden');

            try {
                // Use relative path to support any domain/subfolder
                const currentPath = window.location.pathname.replace('/license/activate', '');
                const res = await fetch(currentPath + '/license/submit', {
                    method: 'POST',
                    body: formData
                });
                
                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch(e) {
                    throw new Error("Server Response Error: " + text.substring(0, 100));
                }

                if (data.status === 'success') {
                    msg.innerHTML = data.message;
                    msg.className = 'p-4 rounded-xl text-sm text-center bg-green-500/20 text-green-400 block';
                    setTimeout(() => window.location.href = currentPath + '/login', 2000);
                } else {
                    msg.innerHTML = data.message;
                    msg.className = 'p-4 rounded-xl text-sm text-center bg-red-500/20 text-red-400 block';
                    btn.disabled = false;
                    btn.innerHTML = 'Activate Now';
                }
            } catch (err) {
                msg.innerHTML = err.message || 'Connection error. Please try again.';
                msg.className = 'p-4 rounded-xl text-sm text-center bg-red-500/20 text-red-400 block';
                btn.disabled = false;
                btn.innerHTML = 'Activate Now';
            }
        });
    </script>
</body>
</html>
