<?php
session_start();
include 'db.php';

$error = '';
$success = '';
$token_valid = false;
$email_to_reset = '';

// 1. Check if there is a token in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // 2. Look up the token in the database and check if it's expired
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $token_valid = true;
        $row = $result->fetch_assoc();
        $email_to_reset = $row['email'];
    } else {
        $error = "This reset link is invalid or has expired. Please request a new one.";
    }
} else {
    // If someone tries to visit this page without a token, send them back
    header("Location: forgot-password.php");
    exit();
}

// 3. Handle the form submission for the new password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token_valid) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        $error = "Please fill out all fields.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match. Please try again.";
    } else {
        // Hash the new password securely!
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the users table
        $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->bind_param("ss", $hashed_password, $email_to_reset);
        
        if ($update->execute()) {
            // Delete the used token so it can't be used again
            $del = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $del->bind_param("s", $email_to_reset);
            $del->execute();

            $success = "Password successfully updated! You can now log in.";
            $token_valid = false; // Hide the form so they don't submit it again
        } else {
            $error = "Failed to update password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create New Password | CampusClubRecruit</title>
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
  <div class="flex items-center gap-3">
    <img src="Src\NBSC_Logo.png" alt="NBSC Logo" class="h-8 w-auto" />
    <span class="text-xl font-bold tracking-tight text-[#1A365D]">CampusClubRecruit</span>
  </div>
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
            <span class="material-symbols-outlined text-[#002045] text-3xl">password</span>
          </div>
          <h1 class="text-2xl font-semibold text-[#002045] mb-2">Create New Password</h1>
          <p class="text-sm text-[#43474e]">Enter your new secure password below.</p>
        </div>

        <!-- Success Message -->
        <?php if ($success): ?>
          <div class="mb-6 px-4 py-3 bg-green-50 border border-green-300 text-green-700 rounded-lg text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <?= htmlspecialchars($success) ?>
          </div>
          <a href="logIn.php" class="w-full bg-[#ECC94B] hover:opacity-90 active:opacity-80 text-[#002045] font-semibold py-4 rounded-lg shadow-md transition-all flex justify-center items-center gap-2">
            Go to Login
            <span class="material-symbols-outlined">login</span>
          </a>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if ($error): ?>
          <div class="mb-6 px-4 py-3 bg-red-50 border border-red-300 text-red-700 rounded-lg text-sm flex items-start gap-2">
            <span class="material-symbols-outlined text-red-500 mt-0.5">error</span>
            <?= htmlspecialchars($error) ?>
          </div>
          
          <!-- If the token is invalid, give them a button to try again -->
          <?php if (!$token_valid && !$success): ?>
            <a href="forgot-password.php" class="w-full bg-[#e5eeff] hover:bg-[#d4e4fc] text-[#1960a3] font-semibold py-4 rounded-lg transition-all flex justify-center items-center gap-2 mt-4">
              Request New Link
            </a>
          <?php endif; ?>
        <?php endif; ?>

        <!-- Password Form -->
        <?php if ($token_valid && !$success): ?>
          <form method="POST" action="" class="space-y-6">
            
            <div class="space-y-2">
              <label class="text-xs font-bold text-[#43474e] uppercase tracking-widest block" for="new_password">New Password</label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#74777f]">lock</span>
                <input name="new_password" id="new_password" type="password" required placeholder="At least 8 characters" class="w-full pl-10 pr-10 py-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-[#1960a3]/20 focus:border-[#1960a3] outline-none transition-all text-base"/>
                <button type="button" onclick="togglePassword('new_password', 'eyeIcon1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#74777f]">
                  <span id="eyeIcon1" class="material-symbols-outlined">visibility</span>
                </button>
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-xs font-bold text-[#43474e] uppercase tracking-widest block" for="confirm_password">Confirm New Password</label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#74777f]">lock_reset</span>
                <input name="confirm_password" id="confirm_password" type="password" required placeholder="Re-enter your password" class="w-full pl-10 pr-10 py-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-[#1960a3]/20 focus:border-[#1960a3] outline-none transition-all text-base"/>
                <button type="button" onclick="togglePassword('confirm_password', 'eyeIcon2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#74777f]">
                  <span id="eyeIcon2" class="material-symbols-outlined">visibility</span>
                </button>
              </div>
            </div>

            <button type="submit" class="w-full bg-[#ECC94B] hover:opacity-90 active:opacity-80 text-[#002045] font-semibold py-4 rounded-lg shadow-md transition-all flex justify-center items-center gap-2">
              Update Password
              <span class="material-symbols-outlined text-xl">save</span>
            </button>
          </form>
        <?php endif; ?>

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