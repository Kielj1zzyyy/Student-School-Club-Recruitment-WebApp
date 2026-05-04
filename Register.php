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

    // Validation checks
    if (empty($name) || empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check for duplicates
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Account already exists.";
        } else {
            $role = 'student'; // Security: Always hardcoded to student

            // Hash the password securely
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (name, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
            // Bind the $hashed_password, NOT the plain $password
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
  <title>Register | CampusClubRecruit</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <style>
    .material-symbols-outlined {
      font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24;
      vertical-align: middle;
    }
    .campus-overlay {
      background-image: linear-gradient(rgba(0,32,69,0.75), rgba(0,32,69,0.55)),
        url(https://lh3.googleusercontent.com/aida-public/AB6AXuAOLz1-5Nz-IYsXgYT8loTZ_aunFy6O-m6KCD9NM8nPUrbLRXhdEhVgHPsdDF2eEaEqb-_Ya7T6OpfEfSlqkbdrCM4nafcXEIv8tqueLOWQ3HMFqdABO7hrtsNZSabeVHlyMaTEuqHShnpmIW9HvGg3ClHLCH5ctrh4d37EGBL7D5iIh37dqt6PY7IB7bpyVkrXi2Z44PvqKzBiI7_3Ag9xEnTgenxGXwVF6SgRaVKtmizGqfbR7iX3NkKSds3f18kAP97L2n03zI46);
      background-size: cover;
      background-position: center;
    }
  </style>
</head>
<body class="bg-[#f8f9ff] font-['Inter'] text-[#0d1c2e] min-h-screen flex flex-col">

<!-- Header -->
<header class="bg-[#F7FAFC] border-b border-[#E2E8F0] shadow-sm sticky top-0 z-50">
  <div class="flex justify-between items-center w-full px-6 py-4 max-w-7xl mx-auto">
    <div class="flex items-center gap-3">
      <img src="Src\NBSC_Logo.png" alt="NBSC Logo" class="h-8 w-auto" />
      <span class="text-xl font-bold text-[#1A365D] tracking-tight">CampusClubRecruit</span>
    </div>
    <a class="text-[#4A5568] hover:bg-slate-100 transition-colors px-3 py-1 rounded-lg font-semibold text-sm" href="logIn.php">Back to Login</a>
  </div>
</header>

<!-- Main -->
<main class="flex-grow flex items-center justify-center campus-overlay p-6">
  <div class="w-full max-w-xl bg-white rounded-xl shadow-lg overflow-hidden">

    <div class="h-2 bg-[#1960a3] w-full"></div>

    <div class="p-8 md:p-10">

      <div class="text-center mb-8">
        <div class="w-16 h-16 bg-[#e5eeff] rounded-full flex items-center justify-center mx-auto mb-4">
          <span class="material-symbols-outlined text-[#002045] text-3xl">person_add</span>
        </div>
        <h2 class="text-2xl font-bold text-[#1A365D]">Create Student Account</h2>
        <p class="text-[#43474e] text-sm mt-1">Register to join clubs and organizations at NBSC</p>
      </div>

      <!-- Success message -->
      <?php if ($success): ?>
        <div class="mb-6 px-4 py-3 bg-green-50 border border-green-300 text-green-700 rounded-lg text-sm flex items-center gap-2">
          <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
          <?= htmlspecialchars($success) ?>
          <a href="logIn.php" class="ml-auto font-semibold underline text-green-700">Log in now</a>
        </div>
      <?php endif; ?>

      <!-- Error message -->
      <?php if ($error): ?>
        <div class="mb-6 px-4 py-3 bg-red-50 border border-red-300 text-red-700 rounded-lg text-sm flex items-center gap-2">
          <span class="material-symbols-outlined text-red-500 text-lg">error</span>
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form action="Register.php" method="POST" class="space-y-5">
        <div>
          <label class="text-xs font-bold text-[#43474e] uppercase tracking-widest block mb-1" for="name">Full Name</label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#74777f] text-xl">badge</span>
            <input name="name" id="name" type="text" required placeholder="Juan Dela Cruz" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" class="w-full pl-10 pr-4 py-3 border border-[#E2E8F0] rounded-lg focus:ring-2 focus:ring-[#2B6CB0] outline-none transition-all text-base"/>
          </div>
        </div>

        <div>
          <label class="text-xs font-bold text-[#43474e] uppercase tracking-widest block mb-1" for="username">Student ID / Username</label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#74777f] text-xl">tag</span>
            <input name="username" id="username" type="text" required placeholder="2026-00123" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" class="w-full pl-10 pr-4 py-3 border border-[#E2E8F0] rounded-lg focus:ring-2 focus:ring-[#2B6CB0] outline-none transition-all text-base"/>
          </div>
        </div>

        <div>
          <label class="text-xs font-bold text-[#43474e] uppercase tracking-widest block mb-1" for="email">Institutional Email</label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#74777f] text-xl">mail</span>
            <input name="email" id="email" type="email" required placeholder="student@nbsc.edu" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" class="w-full pl-10 pr-4 py-3 border border-[#E2E8F0] rounded-lg focus:ring-2 focus:ring-[#2B6CB0] outline-none transition-all text-base"/>
          </div>
        </div>

        <div>
          <label class="text-xs font-bold text-[#43474e] uppercase tracking-widest block mb-1" for="password">Password</label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#74777f] text-xl">lock</span>
            <input name="password" id="password" type="password" required placeholder="At least 8 characters" class="w-full pl-10 pr-10 py-3 border border-[#E2E8F0] rounded-lg focus:ring-2 focus:ring-[#2B6CB0] outline-none transition-all text-base"/>
            <button type="button" onclick="togglePassword('password', 'eyeIcon1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#74777f]">
              <span id="eyeIcon1" class="material-symbols-outlined">visibility</span>
            </button>
          </div>
        </div>

        <div>
          <label class="text-xs font-bold text-[#43474e] uppercase tracking-widest block mb-1" for="confirm_password">Confirm Password</label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#74777f] text-xl">lock_reset</span>
            <input name="confirm_password" id="confirm_password" type="password" required placeholder="Re-enter your password" class="w-full pl-10 pr-10 py-3 border border-[#E2E8F0] rounded-lg focus:ring-2 focus:ring-[#2B6CB0] outline-none transition-all text-base"/>
            <button type="button" onclick="togglePassword('confirm_password', 'eyeIcon2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#74777f]">
              <span id="eyeIcon2" class="material-symbols-outlined">visibility</span>
            </button>
          </div>
        </div>

        <div class="flex items-start gap-3 text-sm text-[#43474e]">
          <input type="checkbox" required name="terms" class="mt-1 w-4 h-4 rounded border-[#E2E8F0] text-[#2B6CB0]"/>
          <label>I agree to the <a href="#" class="text-[#2B6CB0] font-semibold hover:underline">Terms of Service</a> and <a href="#" class="text-[#2B6CB0] font-semibold hover:underline">Privacy Policy</a></label>
        </div>

        <button type="submit" class="w-full bg-[#ECC94B] hover:opacity-90 active:opacity-80 text-[#002045] font-bold py-4 rounded-lg shadow-md transition-all flex items-center justify-center gap-2 text-base">
          Create Account
          <span class="material-symbols-outlined">person_add</span>
        </button>
      </form>

      <p class="text-center text-sm mt-6 text-[#43474e]">Already have an account? <a href="logIn.php" class="text-[#2B6CB0] font-bold hover:underline">Sign in</a></p>
    </div>
  </div>
</main>

<footer class="bg-[#F7FAFC] border-t border-[#E2E8F0] mt-auto">
  <div class="flex flex-col md:flex-row justify-between items-center w-full px-8 py-10 gap-4 max-w-7xl mx-auto">
    <p class="text-sm text-[#718096]">© 2026 NBSC CampusClubRecruit. All rights reserved.</p>
    <div class="flex gap-6">
      <a class="text-sm text-[#718096] hover:text-[#2B6CB0] underline" href="#">Privacy Policy</a>
      <a class="text-sm text-[#718096] hover:text-[#2B6CB0] underline" href="#">Terms of Service</a>
    </div>
  </div>
</footer>

<!-- SCRIPT FOR PASSWORD TOGGLE -->
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