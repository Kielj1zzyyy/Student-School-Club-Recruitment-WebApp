<?php
session_start();

// RECENT CHANGE: Added error reporting to force any hidden login errors to display on screen
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

$error = '';

$email = $_COOKIE['user_email'] ?? '';
$password = $_COOKIE['user_password'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            if (!empty($_POST['remember'])) {
                setcookie("user_email", $email, time() + (86400 * 30), "/");
                setcookie("user_password", $password, time() + (86400 * 30), "/"); 
            } else {
                setcookie("user_email", "", time() - 3600, "/");
                setcookie("user_password", "", time() - 3600, "/");
            }

            // RECENT CHANGE: Removed duplicate session role assignment here

            // Route them to the correct FOLDERS
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

  <script>
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

<header class="bg-[#F7FAFC] border-b border-[#E2E8F0] shadow-sm sticky top-0 z-50">
  <div class="flex justify-between items-center w-full px-6 py-4 max-w-7xl mx-auto">
    
    <div class="flex items-center gap-3">
      <!-- RECENT CHANGE: Fixed backslash in image source path to prevent broken images on some servers -->
      <img src="Src/NBSC_Logo.png" alt="NBSC Logo" class="h-8 w-auto" />
      <span class="text-xl font-bold text-[#1A365D] tracking-tight">NBSC-CampusClubRecruit</span>
    </div>

    <a class="text-[#4A5568] hover:bg-slate-100 transition-colors px-3 py-1 rounded-lg font-semibold" href="index.html">Back to Menu</a>
  </div>
</header>

<main class="flex-grow flex items-center justify-center campus-overlay p-6">
  <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-10 border border-[#c4c6cf]/30">

    <div class="text-center mb-8">
      <h1 class="text-3xl font-semibold text-[#002045] mb-1">Welcome Back</h1>
      <p class="text-[#43474e] text-base">Sign in to access your recruitment portal</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="mb-4 px-4 py-3 bg-red-50 border border-red-300 text-red-700 rounded-lg text-sm">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-6">

      <div class="space-y-1">
        <label class="text-xs font-bold text-[#43474e] uppercase tracking-widest block">Institutional Email</label>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#74777f]">mail</span>
          <input
            class="w-full pl-10 pr-4 py-3 bg-[#F1F5F9] border border-[#CBD5E1] rounded-lg focus:ring-2 focus:ring-[#2B6CB0] outline-none"
            name="email"
            type="email"
            required
            value="<?= htmlspecialchars($email) ?>"
          />
        </div>
      </div>

      <div class="space-y-1">
        <label class="text-xs font-bold text-[#43474e] uppercase tracking-widest block">Password</label>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#74777f]">lock</span>

          <input
            id="password"
            class="w-full pl-10 pr-10 py-3 bg-[#F1F5F9] border border-[#CBD5E1] rounded-lg focus:ring-2 focus:ring-[#2B6CB0] outline-none"
            name="password"
            type="password"
            required
            value="<?= htmlspecialchars($password) ?>"
          />

          <button type="button" onclick="togglePassword()"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#74777f]">
            <span id="eyeIcon" class="material-symbols-outlined">visibility</span>
          </button>

        </div>
      </div>

      <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="remember">
          <span class="text-sm text-[#43474e]">Remember Me</span>
        </label>
        <a class="text-sm text-[#2B6CB0] font-semibold hover:underline" href="forgot-pass.php">Forgot Password?</a>
      </div>

      <button
        class="w-full bg-[#ECC94B] hover:opacity-90 text-[#002045] font-semibold py-4 rounded-lg shadow-md flex items-center justify-center gap-2"
        name="login"
        type="submit">
        Sign In
        <span class="material-symbols-outlined">login</span>
      </button>

    </form>

  </div>
</main>

<script>
function togglePassword() {
  const input = document.getElementById("password");
  const icon = document.getElementById("eyeIcon");

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