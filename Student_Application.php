<?php
session_start();

if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

$timeout = 1800;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: ../logIn.php");
    exit();
}

$_SESSION['last_activity'] = time();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../logIn.php");
    exit();
}

$fullName = trim($_SESSION['name'] ?? '');
if (empty($fullName)) {
    $fullName = 'Student';
}

$nameParts = explode(' ', $fullName);
$firstName = $nameParts[0] ?? 'Student';

$safeFullName = htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8');
$safeFirstName = htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>My Applications</title>

<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>

<style>
.material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400;
}
.level-1-card {
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 0.75rem;
}
</style>
</head>

<body class="bg-slate-100 font-[Inter]">

<!-- ✅ SIDEBAR (SAME AS DASHBOARD) -->
<aside class="fixed left-0 top-0 h-full w-64 bg-slate-50 border-r z-50 flex flex-col">
    <div class="p-6">
        <h1 class="font-black text-blue-900">Student Portal</h1>
        <p class="text-xs text-slate-500">Academic Year 2025-26</p>
    </div>

    <nav class="flex-1 px-3 space-y-1">
        <a href="Student_Dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-100">
            <span class="material-symbols-outlined">dashboard</span>
            Dashboard
        </a>

        <!-- ACTIVE BUTTON -->
        <a href="Student_Application.php" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-50 text-blue-700 font-bold border-r-4 border-blue-700">
            <span class="material-symbols-outlined">assignment</span>
            My Applications
        </a>

        <a href="Student_ClubDashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-100">
            <span class="material-symbols-outlined">groups</span>
            Clubs
        </a>

        <a href="Student_Announcement.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-100">
            <span class="material-symbols-outlined">campaign</span>
            Announcements
        </a>
    </nav>

    <div class="p-3">
        <a href="../logOut.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-100">
            <span class="material-symbols-outlined">logout</span>
            Logout
        </a>
    </div>
</aside>

<!-- ✅ TOP NAVBAR (SAME AS DASHBOARD) -->
<header class="ml-64 h-16 flex items-center justify-between px-6 bg-white border-b">
    <input type="text" placeholder="Search..." class="bg-slate-100 px-4 py-2 rounded-lg w-1/3"/>

    <div class="flex items-center gap-4">
        <span class="material-symbols-outlined">notifications</span>

        <div class="text-right">
            <p class="font-bold"><?= $safeFullName ?></p>
            <p class="text-xs text-slate-500">Student</p>
        </div>

        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center font-bold">
            <?= strtoupper(substr($safeFirstName, 0, 1)) ?>
        </div>
    </div>
</header>

<!-- ✅ MAIN CONTENT -->
<main class="ml-64 p-8">

<h1 class="text-2xl font-bold mb-6">My Applications</h1>

<!-- SAMPLE APPLICATION CARD -->
<div class="level-1-card p-6 mb-6">
    <h2 class="font-bold text-lg">Robotics Club</h2>
    <p class="text-sm text-slate-500">Position: Developer</p>

    <div class="mt-4 flex justify-between items-center">
        <span class="bg-yellow-200 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold">
            Pending
        </span>

        <button class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-800">
            View Details
        </button>
    </div>
</div>

<div class="level-1-card p-6">
    <h2 class="font-bold text-lg">Debate Club</h2>
    <p class="text-sm text-slate-500">Position: Member</p>

    <div class="mt-4 flex justify-between items-center">
        <span class="bg-green-200 text-green-800 px-3 py-1 rounded-full text-xs font-bold">
            Approved
        </span>

        <button class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-800">
            View Details
        </button>
    </div>
</div>

</main>

</body>
</html>