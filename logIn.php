<?php
session_start();

// Error reporting for login debugging[cite: 1]
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
  <!-- Using Inter with specific professional weights[cite: 2, 3] -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

  <style>
    body { 
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
        -webkit-font-smoothing: antialiased;
    }
    .material-symbols-outlined {
      font-variation-settings: "FILL" 0, "wght" 300, "GRAD" 0, "opsz" 24;
      vertical-align: middle;
    }
    .campus-overlay {
      background: linear-gradient(135deg, rgba(15, 23, 42, 0.96) 0%, rgba(30, 58, 138, 0.85) 100%),
        url('https://images.unsplash.com/photo-1541339907198-e08759dfc3f0?q=80&w=2070&auto=format&fit=crop');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
    }
    .login-card {
      background: #ffffff;
      box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    /* Smooth transition for input focus[cite: 2] */
    .input-transition {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
  </style>
</head>

<body class="bg-slate-900 min-h-screen flex flex-col campus-overlay">

<header class="bg-white border-b border-slate-200 sticky top-0 z-50">
  <div class="flex justify-between items-center w-full px-8 py-5 max-w-7xl mx-auto">
    
    <div class="flex items-center gap-4">
      <img src="Src/NBSC_Logo.png" alt="NBSC Logo" class="h-10 w-auto" />
      <div class="w-px h-6 bg-slate-200"></div>
      <span class="text-xl font-black text-blue-900 tracking-tighter uppercase">NBSC <span class="font-medium text-slate-400">Club Recruit Portal</span></span>
    </div>

    <a class="text-slate-600 hover:text-blue-800 px-5 py-2 rounded-xl font-bold text-xs bg-slate-50 border border-slate-100 transition-all uppercase tracking-widest" href="index.html">Back to Menu</a>
  </div>
</header>

<main class="flex-grow flex items-center justify-center p-6">
  <div class="max-w-md w-full login-card rounded-[2.5rem] p-10 md:p-14">

    <div class="text-center mb-12">
      <h1 class="text-4xl font-black text-slate-900 mb-3 tracking-tight">Welcome Back</h1>
      <p class="text-slate-500 text-sm font-medium leading-relaxed px-4">Sign in to access your recruitment portal</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="mb-8 px-5 py-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl text-[13px] font-semibold flex items-center gap-3">
        <span class="material-symbols-outlined text-lg">error</span>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-7">

      <div class="space-y-2.5">
        <label class="text-[11px] font-extrabold text-blue-900 uppercase tracking-[0.15em] ml-1">Institutional Email</label>
        <div class="relative group">
          <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors">mail</span>
          <input
            class="input-transition w-full pl-12 pr-4 py-4.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-blue-600 outline-none font-semibold text-slate-900 text-sm"
            name="email"
            type="email"
            required
            placeholder="name@nbsc.edu.ph"
            value="<?= htmlspecialchars($email) ?>"
          />
        </div>
      </div>

      <div class="space-y-2.5">
        <label class="text-[11px] font-extrabold text-blue-900 uppercase tracking-[0.15em] ml-1">Password</label>
        <div class="relative group">
          <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors">lock</span>

          <input
            id="password"
            class="input-transition w-full pl-12 pr-12 py-4.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-blue-600 outline-none font-semibold text-slate-900 text-sm"
            name="password"
            type="password"
            required
            placeholder="••••••••"
            value="<?= htmlspecialchars($password) ?>"
          />

          <button type="button" onclick="togglePassword()"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-700 transition-colors">
            <span id="eyeIcon" class="material-symbols-outlined">visibility</span>
          </button>

        </div>
      </div>

      <div class="flex items-center justify-between px-1">
        <label class="flex items-center gap-3 cursor-pointer group">
          <input type="checkbox" name="remember" class="w-5 h-5 rounded-md border-slate-300 text-blue-600 focus:ring-blue-100 transition-all">
          <span class="text-[13px] font-bold text-slate-500 group-hover:text-slate-800 transition-colors">Remember Me</span>
        </label>
        <a class="text-[13px] font-bold text-blue-700 hover:text-blue-800 underline underline-offset-4 decoration-blue-200" href="forgot-pass.php">Forgot Password?</a>
      </div>

      <button
        class="w-full bg-blue-900 hover:bg-blue-800 text-white font-black py-5 rounded-[1.25rem] shadow-2xl shadow-blue-200 transition-all flex items-center justify-center gap-3 active:scale-[0.98] text-sm uppercase tracking-widest"
        name="login"
        type="submit">
        Sign In
        <span class="material-symbols-outlined text-lg">login</span>
      </button>

    </form>

    <div class="mt-10 pt-10 border-t border-slate-100 text-center">
        <p class="text-[11px] font-extrabold text-slate-300 uppercase tracking-[0.3em]">Northern Bukidnon State College</p>
    </div>

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