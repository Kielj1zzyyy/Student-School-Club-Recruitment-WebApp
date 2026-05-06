<?php
session_start();
include '../db.php';

// --- 1. SESSION SECURITY & TIMEOUT[cite: 3] ---
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

// --- 2. DATA FETCHING[cite: 3] ---
$fullName = trim($_SESSION['name'] ?? 'Student');
$firstName = explode(' ', $fullName)[0];
$safeFullName = htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8');
$safeFirstName = htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');

/* FETCH CLUBS FOR UPDATES[cite: 3] */
$result = $conn->query("SELECT * FROM clubs LIMIT 5");
$clubs = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Announcements | Student Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400; }
        .level-1-card { 
            background: white; 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.03), 0 4px 6px -4px rgba(0, 0, 0, 0.03);
            border-radius: 1.25rem; 
            border: 1px solid #f1f5f9; 
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

<!-- SIDEBAR[cite: 3] -->
<aside class="fixed left-0 top-0 h-full w-64 bg-white border-r z-50 flex flex-col">
    <div class="p-8">
        <h1 class="font-black text-blue-900 text-xl tracking-tighter uppercase">Student Portal</h1>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Academic Year 2025-26</p>
    </div>
    <nav class="flex-1 px-4 space-y-2">
        <a href="Student_Dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-100 transition-all group">
            <span class="material-symbols-outlined group-hover:text-blue-600 transition-colors">dashboard</span>
            <span class="font-semibold text-sm">Dashboard</span>
        </a>
        <a href="Student_Application.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-100 transition-all group">
            <span class="material-symbols-outlined group-hover:text-blue-600 transition-colors">assignment</span>
            <span class="font-semibold text-sm">My Applications</span>
        </a>
        <a href="Student_ClubDashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-100 transition-all group">
            <span class="material-symbols-outlined group-hover:text-blue-600 transition-colors">groups</span>
            <span class="font-semibold text-sm">Clubs</span>
        </a>
        <a href="Student_Announcement.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-900 text-white font-bold shadow-lg shadow-blue-200 transition-all">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">campaign</span>
            <span class="text-sm">Announcements</span>
        </a>
    </nav>
    <div class="p-4 border-t">
        <a href="../logOut.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-500 hover:bg-red-50 transition-all font-bold text-sm">
            <span class="material-symbols-outlined">logout</span> Logout
        </a>
    </div>
</aside>

<!-- TOP NAVBAR[cite: 3] -->
<header class="ml-64 h-20 glass-nav border-b flex items-center justify-between px-8 sticky top-0 z-40">
    <h2 class="font-black text-slate-900 text-lg uppercase tracking-tight">Bulletin Board</h2>

    <div class="flex items-center gap-5">
        <div class="text-right">
            <p class="font-black text-slate-900 leading-none"><?= $safeFullName ?></p>
            <p class="text-[10px] text-blue-600 font-black uppercase tracking-widest mt-1">Active Student</p>
        </div>
        <div class="w-12 h-12 bg-gradient-to-tr from-blue-900 to-blue-700 rounded-2xl flex items-center justify-center font-bold text-white shadow-lg shadow-blue-100 uppercase">
            <?= strtoupper(substr($safeFirstName, 0, 1)) ?>
        </div>
    </div>
</header>

<!-- MAIN CONTENT[cite: 3] -->
<main class="ml-64 p-10">
    <!-- Hero Banner[cite: 3] -->
    <div class="relative overflow-hidden rounded-[2rem] bg-blue-900 p-10 mb-10 text-white shadow-2xl shadow-blue-200">
        <div class="relative z-10">
            <h2 class="text-4xl font-black mb-2">University Announcements</h2>
            <p class="text-blue-100 opacity-90 text-lg">Stay updated with the latest campus news and club activities.</p>
        </div>
        <div class="absolute right-[-20px] top-[-20px] opacity-10">
            <span class="material-symbols-outlined" style="font-size: 200px;">campaign</span>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-10">
        <!-- Club Updates[cite: 3] -->
        <div class="col-span-12 lg:col-span-8 space-y-6">
            <h3 class="text-2xl font-black text-slate-900 flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-blue-600">notifications_active</span>
                Club Updates
            </h3>
            
            <?php foreach ($clubs as $club): ?>
            <div class="level-1-card p-8 group hover:border-blue-400 transition-all duration-300">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center font-black text-blue-900 border border-blue-100 group-hover:bg-blue-900 group-hover:text-white transition-all">
                            <?= substr($club['club_name'], 0, 1) ?>
                        </div>
                        <div>
                            <h4 class="font-black text-lg text-slate-900"><?= htmlspecialchars($club['club_name']) ?></h4>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">New Update</p>
                        </div>
                    </div>
                    <span class="bg-blue-50 text-blue-700 text-[10px] font-black px-3 py-1 rounded-full uppercase">Recent</span>
                </div>
                
                <h5 class="font-bold text-slate-800 mb-2">Latest Club Announcement</h5>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed">
                    <?= htmlspecialchars($club['description']) ?>
                </p>
                
                <button class="px-6 py-2.5 bg-slate-900 text-white font-bold rounded-xl text-xs hover:bg-blue-900 transition-all shadow-lg shadow-slate-200 active:scale-95">
                    Read Full Message
                </button>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Right Side: Campus News[cite: 3] -->
        <div class="col-span-12 lg:col-span-4 space-y-6">
            <h3 class="text-2xl font-black text-slate-900 flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-blue-600">newspaper</span>
                Campus News
            </h3>
            
            <div class="space-y-4">
                <div class="level-1-card p-6 border-l-4 border-l-blue-600">
                    <h4 class="font-black text-slate-900 mb-1">Enrollment Opens</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Enrollment for next semester starts next week. Prepare your documents.</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase mt-3 tracking-widest">May 10, 2026</p>
                </div>

                <div class="level-1-card p-6 border-l-4 border-l-amber-500">
                    <h4 class="font-black text-slate-900 mb-1">Library Update</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Extended hours during exam week. Open until 10:00 PM starting Monday.</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase mt-3 tracking-widest">May 12, 2026</p>
                </div>

                <div class="level-1-card p-6 border-l-4 border-l-green-500">
                    <h4 class="font-black text-slate-900 mb-1">Student Event</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Join the campus festival this Friday. Various food stalls and live music!</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase mt-3 tracking-widest">May 15, 2026</p>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>