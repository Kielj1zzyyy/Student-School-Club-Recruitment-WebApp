<?php
session_start();
include 'db.php'; 

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($name) || empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Account already exists.";
        } else {
            $role = 'student'; 
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (name, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $username, $email, $hashed_password, $role); 

            if ($stmt->execute()) {
                $success = "Account created! You can now log in.";
            } else {
                $error = "Registration failed.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Register | NBSC Recruit</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <style>
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
            -webkit-font-smoothing: antialiased;
        }
        .campus-overlay {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.90) 0%, rgba(30, 58, 138, 0.75) 100%),
                url('https://images.unsplash.com/photo-1541339907198-e08759dfc3f0?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }
        .form-input {
            transition: all 0.2s ease-in-out;
        }
        .form-input:focus {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.1);
        }
    </style>
</head>
<body class="bg-[#f8f9ff] text-slate-900 min-h-screen flex flex-col">

<!-- Header -->
<header class="bg-white border-b border-slate-200 sticky top-0 z-50">
    <div class="flex justify-between items-center w-full px-8 h-20 max-w-7xl mx-auto">
        <div class="flex items-center gap-3">
            <img src="Src/NBSC_Logo.png" alt="NBSC Logo" class="h-10 w-auto" />
            <div class="w-px h-6 bg-slate-200 hidden md:block"></div>
            <span class="text-xl font-black text-blue-900 tracking-tighter uppercase">NBSC <span class="font-medium text-slate-400">Recruit</span></span>
        </div>
        <a class="px-5 py-2.5 text-xs font-black uppercase tracking-widest text-blue-900 hover:bg-slate-50 rounded-xl transition-all" href="logIn.php">Sign In</a>
    </div>
</header>

<!-- Main Registration Section -->
<main class="flex-grow flex items-center justify-center campus-overlay p-6">
    <div class="w-full max-w-xl bg-white rounded-[2.5rem] shadow-2xl overflow-hidden">
        <!-- Accent Top Bar -->
        <div class="h-2 bg-blue-900 w-full"></div>

        <div class="p-10 md:p-14">
            <div class="text-center mb-10">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-blue-900 text-3xl">person_add</span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Join the Community</h2>
                <p class="text-slate-500 font-medium mt-2">Create your student account to get started.</p>
            </div>

            <!-- Status Messages -->
            <?php if ($success): ?>
                <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-sm font-semibold flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                    <?= htmlspecialchars($success) ?>
                    <a href="logIn.php" class="ml-auto underline decoration-2 underline-offset-4">Log in</a>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="mb-8 p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-sm font-semibold flex items-center gap-3">
                    <span class="material-symbols-outlined text-rose-600">error</span>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="Register.php" method="POST" class="space-y-6">
                <!-- Name -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block ml-1" for="name">Full Name</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">badge</span>
                        <input name="name" id="name" type="text" required placeholder="Juan Dela Cruz" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                        class="form-input w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none text-sm font-semibold placeholder:text-slate-400"/>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Username -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block ml-1" for="username">Student ID</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl">tag</span>
                            <input name="username" id="username" type="text" required placeholder="2026-XXXX" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" 
                            class="form-input w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none text-sm font-semibold placeholder:text-slate-400"/>
                        </div>
                    </div>
                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block ml-1" for="email">Institutional Email</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl">mail</span>
                            <input name="email" id="email" type="email" required placeholder="name@nbsc.edu" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                            class="form-input w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none text-sm font-semibold placeholder:text-slate-400"/>
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block ml-1" for="password">Password</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl">lock</span>
                        <input name="password" id="password" type="password" required placeholder="At least 8 characters" 
                        class="form-input w-full pl-12 pr-12 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none text-sm font-semibold placeholder:text-slate-400"/>
                        <button type="button" onclick="togglePassword('password', 'eyeIcon1')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-900 transition-colors">
                            <span id="eyeIcon1" class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block ml-1" for="confirm_password">Confirm Password</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl">lock_reset</span>
                        <input name="confirm_password" id="confirm_password" type="password" required placeholder="Repeat password" 
                        class="form-input w-full pl-12 pr-12 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none text-sm font-semibold placeholder:text-slate-400"/>
                        <button type="button" onclick="togglePassword('confirm_password', 'eyeIcon2')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-900 transition-colors">
                            <span id="eyeIcon2" class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-2">
                    <input type="checkbox" required name="terms" class="mt-1 w-5 h-5 rounded-lg border-slate-200 text-blue-900 focus:ring-blue-900/10"/>
                    <label class="text-xs font-medium text-slate-500 leading-relaxed">
                        I agree to the <a href="#" class="text-blue-900 font-bold hover:underline">Terms of Service</a> and 
                        <a href="#" class="text-blue-900 font-bold hover:underline">Privacy Policy</a>.
                    </label>
                </div>

                <button type="submit" class="w-full bg-blue-900 text-white font-black text-sm uppercase tracking-[0.2em] py-5 rounded-[1.5rem] shadow-xl shadow-blue-100 hover:bg-blue-800 active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                    Register Now
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </form>

            <p class="text-center text-xs font-bold mt-10 text-slate-400 uppercase tracking-widest">
                Already have an account? <a href="logIn.php" class="text-blue-900 hover:underline">Sign in here</a>
            </p>
        </div>
    </div>
</main>

<footer class="bg-white border-t border-slate-200 py-10">
    <div class="max-w-7xl mx-auto px-8 flex flex-col md:flex-row justify-between items-center gap-6">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em]">© 2026 NBSC Recruit. All rights reserved.</p>
        <div class="flex gap-8">
            <a class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-blue-900" href="#">Privacy</a>
            <a class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-blue-900" href="#">Terms</a>
        </div>
    </div>
</footer>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === "password") {
        input.type = "text";
        icon.textContent = "visibility_off";
    } else {
        input.type = "password";
        icon.textContent = "visibility";
    }
}
</script>

</body>
</html>