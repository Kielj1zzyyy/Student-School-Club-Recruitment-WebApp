<?php
session_start();
include 'db.php'; // Uses the clean connection file

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // Check if email exists in the users table[cite: 11]
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            // Generate a secure random token[cite: 11]
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Clean old requests and save new token to the database[cite: 11]
            $del = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $del->bind_param("s", $email);
            $del->execute();

            $ins = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $ins->bind_param("sss", $email, $token, $expires);
            $ins->execute();

            // Generic success message to prevent email fishing[cite: 11]
            $success = "If that email is registered, a reset link has been sent. Check your inbox.";
        } else {
            // Always show success even if not found for security[cite: 11]
            $success = "If that email is registered, a reset link has been sent. Check your inbox.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Forgot Password | CampusClubRecruit</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <style>
    .material-symbols-outlined {
      font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24;
      vertical-align: middle;
    }
  </style>
</head>
<body class="bg-[#f8f9ff] font-['Inter'] text-[#0d1c2e] min-h-screen flex flex-col">

<!-- Header -->
<header class="bg-white border-b border-slate-200 shadow-sm flex justify-between items-center h-16 px-6 md:px-12 w-full fixed top-0 z-50">
  <span class="text-xl font-bold tracking-tight text-blue-900">CampusClubRecruit</span>
  <a class="text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors px-3 py-2 rounded-lg" href="logIn.php">Back to Login</a>
</header>

<!-- Main -->
<main class="flex-grow flex items-center justify-center pt-16 pb-12">
  <div class="relative z-10 w-full max-w-[440px] px-6">
    <div class="bg-white rounded-xl shadow-[0_4px_20px_rgba(0,32,69,0.1)] border border-slate-100 overflow-hidden">

      <!-- Accent bar -->
      <div class="h-2 bg-[#1960a3] w-full"></div>

      <div class="p-8 md:p-10">

        <div class="flex flex-col items-center text-center mb-8">
          <div class="w-16 h-16 bg-[#e5eeff] rounded-full flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[#002045] text-3xl">lock_reset</span>
          </div>
          <h1 class="text-2xl font-semibold text-[#002045] mb-2">Forgot Password?</h1>
          <p class="text-sm text-[#43474e] max-w-[300px]">
            Enter your registered institutional email and we'll send you a password reset link.
          </p>
        </div>

        <!-- Success Message -->
        <?php if ($success): ?>
          <div class="mb-6 px-4 py-3 bg-green-50 border border-green-300 text-green-700 rounded-lg text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
            <?= htmlspecialchars($success) ?>
          </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if ($error): ?>
          <div class="mb-6 px-4 py-3 bg-red-50 border border-red-300 text-red-700 rounded-lg text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-red-500 text-lg">error</span>
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <form action="forgot-password.php" method="POST" class="space-y-6">
          <div class="space-y-2">
            <label class="text-xs font-bold text-[#43474e] uppercase tracking-widest block" for="email">Institutional Email</label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#74777f]">mail</span>
              <input
                class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-[#1960a3]/20 focus:border-[#1960a3] outline-none transition-all text-base"
                id="email" name="email" type="email"
                placeholder="student@nbsc.edu"
                required
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
              />
            </div>
          </div>

          <button
            class="w-full bg-[#ECC94B] text-[#002045] font-semibold py-4 rounded-lg shadow-md hover:opacity-90 active:opacity-80 transition-all flex justify-center items-center gap-2"
            type="submit"
          >
            Send Reset Link
            <span class="material-symbols-outlined text-xl">arrow_forward</span>
          </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
          <a class="inline-flex items-center gap-2 text-sm text-[#1960a3] font-semibold hover:text-[#002045] transition-colors" href="logIn.php">
            <span class="material-symbols-outlined text-lg">chevron_left</span>
            Return to Login
          </a>
        </div>
      </div>

      <!-- Security badge -->
      <div class="bg-slate-50 py-4 px-8 flex justify-center items-center gap-2">
        <span class="material-symbols-outlined text-sm text-slate-400" style="font-variation-settings:'FILL' 1">verified_user</span>
        <span class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Secure University Portal</span>
      </div>
    </div>
  </div>
</main>

<footer class="bg-slate-50 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center py-8 px-6 md:px-12 w-full mt-auto">
  <p class="text-xs text-slate-500 mb-4 md:mb-0">© 2026 NBSC CampusClubRecruit. All rights reserved.</p>
  <div class="flex gap-6">
    <a class="text-xs text-slate-500 hover:text-blue-800" href="#">Privacy Policy</a>
    <a class="text-xs text-slate-500 hover:text-blue-800" href="#">IT Help Desk</a>
  </div>
</footer>
</body>
</html>