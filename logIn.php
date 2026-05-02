<?php
session_start();
include 'db.php'; // Ensure your connection file is named db.php

$error = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Use password_verify for security
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            // Role-based redirection[cite: 9, 12]
            if ($user['role'] === 'admin') {
                header("Location: Admin/Admin_Dashboard.php");
            } else {
                header("Location: Student/Student_Dashboard.php");
            }
            exit();
        } else {
            $error = "Wrong password. Please try again.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Login - NBSC-CampusClubRecruit</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "on-surface-variant": "#43474e",
            "background": "#f8f9ff",
            "primary-container": "#1a365d",
            "outline-variant": "#c4c6cf",
            "surface-container-lowest": "#ffffff",
            "primary": "#002045",
            "on-surface": "#0d1c2e",
            "outline": "#74777f"
          },
        },
      },
    }
  </script>
  <style>
    .material-symbols-outlined {
      font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24;
      vertical-align: middle;
    }
    .campus-overlay {
      background-image: linear-gradient(rgba(0,32,69,0.8), rgba(0,32,69,0.6)),
        url(https://lh3.googleusercontent.com/aida-public/AB6AXuCs1AAPLCphxTbVVDwvzBY-U-UovPcKRzbgnTJyIRduhp6lHn1KjMHkSe9LfdoqTtqPaNJM-9_-kuyBy3NgbtwdKljycX_QPGiu0WmgKYJGZEnKM817fMju57dIIXnU8mH3tiYl7g4jlzKCQ_fqmgquwvvo6qeyKObh958vDEYxlMIiQkqXNwDoYukUpDIqaWUCSj3J7LwtoTYmrYmLdLGpd0j_aeAgnNTIJd3L720xvTAr8nCL5JCkCp7oJBrHzloeWvoa5efJr40O);
      background-size: cover;
      background-position: center;
    }
  </style>
</head>
<body class="bg-[#f8f9ff] font-sans text-[#0d1c2e] min-h-screen flex flex-col">

<!-- Header -->
<header class="bg-[#F7FAFC] border-b border-[#E2E8F0] shadow-sm sticky top-0 z-50">
  <div class="flex justify-between items-center w-full px-6 py-4 max-w-7xl mx-auto">
    <span class="text-xl font-bold text-[#1A365D] font-['Inter'] tracking-tight">CampusClubRecruit</span>
    <a class="text-[#4A5568] hover:bg-slate-100 transition-colors px-3 py-1 rounded-lg font-['Inter'] font-semibold" href="index.html">Back to Menu</a>
  </div>
</header>

<!-- Main -->
<main class="flex-grow flex items-center justify-center campus-overlay p-6">
  <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-10 border border-[#c4c6cf]/30">

    <div class="text-center mb-8">
      <h1 class="text-3xl font-semibold text-[#002045] mb-1">Welcome Back</h1>
      <p class="text-[#43474e] text-base">Sign in to access your recruitment portal</p>
    </div>

    <!-- Error message display[cite: 12] -->
    <?php if (!empty($error)): ?>
      <div class="mb-4 px-4 py-3 bg-red-50 border border-red-300 text-red-700 rounded-lg text-sm">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- Main Styled Form[cite: 12] -->
    <form method="POST" action="logIn.php" class="space-y-6">

      <!-- Email -->
      <div class="space-y-1">
        <label class="text-xs font-bold text-[#43474e] uppercase tracking-widest block" for="email">Institutional Email</label>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#74777f]">mail</span>
          <input
            class="w-full pl-10 pr-4 py-3 bg-white border border-[#E2E8F0] rounded-lg focus:ring-2 focus:ring-[#2B6CB0] focus:border-[#2B6CB0] transition-all outline-none text-base"
            id="email"
            name="email"
            placeholder="student@nbsc.edu"
            required
            type="email"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
          />
        </div>
      </div>

      <!-- Password -->
      <div class="space-y-1">
        <label class="text-xs font-bold text-[#43474e] uppercase tracking-widest block" for="password">Password</label>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#74777f]">lock</span>
          <input
            class="w-full pl-10 pr-4 py-3 bg-white border border-[#E2E8F0] rounded-lg focus:ring-2 focus:ring-[#2B6CB0] focus:border-[#2B6CB0] transition-all outline-none text-base"
            id="password"
            name="password"
            placeholder="••••••••"
            required
            type="password"
          />
        </div>
      </div>

      <!-- Remember me + Forgot -->
      <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 cursor-pointer">
          <input class="w-4 h-4 rounded border-[#E2E8F0] text-[#2B6CB0] focus:ring-[#2B6CB0]" type="checkbox" name="remember"/>
          <span class="text-sm text-[#43474e]">Remember Me</span>
        </label>
        <a class="text-sm text-[#2B6CB0] font-semibold hover:underline" href="forgot-pass.php">Forgot Password?</a>
      </div>

      <!-- Submit button linked to the logic above[cite: 12] -->
      <button
        class="w-full bg-[#ECC94B] hover:opacity-90 active:opacity-80 text-[#002045] font-semibold text-base py-4 rounded-lg shadow-md transition-all flex items-center justify-center gap-2"
        name="login"
        type="submit"
      >
        Sign In
        <span class="material-symbols-outlined">login</span>
      </button>
    </form>

    <div class="mt-8 pt-6 border-t border-[#c4c6cf]/30 text-center">
      <p class="text-base text-[#43474e]">
        New to the community?
        <a class="text-[#2B6CB0] font-bold hover:underline ml-1" href="Register.php">Create Account</a>
      </p>
    </div>

  </div>
</main>

<!-- Footer -->
<footer class="bg-[#F7FAFC] border-t border-[#E2E8F0]">
  <div class="flex flex-col md:flex-row justify-between items-center w-full px-8 py-10 gap-4 max-w-7xl mx-auto">
    <div>
      <span class="text-lg font-bold text-[#1A365D]">CampusClubRecruit</span>
      <p class="text-sm text-[#718096]">© 2026 NBSC Recruitment Portal. All rights reserved.</p>
    </div>
    <div class="flex gap-8">
      <a class="text-sm text-[#718096] hover:text-[#2B6CB0] underline" href="#">Privacy Policy</a>
      <a class="text-sm text-[#718096] hover:text-[#2B6CB0] underline" href="#">Terms of Service</a>
      <a class="text-sm text-[#718096] hover:text-[#2B6CB0] underline" href="#">Support</a>
    </div>
  </div>
</footer>

</body>
</html>