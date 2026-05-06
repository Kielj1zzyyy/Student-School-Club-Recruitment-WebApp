<?php
session_start();
include '../db.php';

// --- 1. SESSION SECURITY & TIMEOUT[cite: 1] ---
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

// --- 2. DATA FETCHING[cite: 1] ---
$user_id = $_SESSION['user_id'];

// Fetch real applications with join for club details
$appStmt = $conn->prepare("
    SELECT ca.id, ca.status, ca.applied_at, c.club_name, c.category
    FROM club_applications ca
    JOIN clubs c ON ca.club_id = c.id
    WHERE ca.user_id = ?
    ORDER BY ca.applied_at DESC
");
$appStmt->bind_param("i", $user_id);
$appStmt->execute();
$applications = $appStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// --- 3. UI HELPERS[cite: 1] ---
$fullName = trim($_SESSION['name'] ?? 'Student');
$firstName = explode(' ', $fullName)[0];
$safeFullName = htmlspecialchars($fullName);
$safeFirstName = htmlspecialchars($firstName);

$appCount = count($applications);
$approvedCount = count(array_filter($applications, fn($a) => $a['status'] == 'approved'));
$pendingCount = count(array_filter($applications, fn($a) => $a['status'] == 'pending'));
?>

<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Dashboard | Student Portal</title>
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

<!-- SIDEBAR[cite: 1] -->
<aside class="fixed left-0 top-0 h-full w-64 bg-white border-r z-50 flex flex-col">
    <div class="p-8">
        <h1 class="font-black text-blue-900 text-xl tracking-tighter uppercase">Student Portal</h1>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Academic Year 2025-26</p>
    </div>
    <nav class="flex-1 px-4 space-y-2">
        <a href="Student_Dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-900 text-white font-bold shadow-lg shadow-blue-200 transition-all">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">dashboard</span>
            <span class="text-sm">Dashboard</span>
        </a>
        <a href="Student_Application.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-100 transition-all group">
            <span class="material-symbols-outlined group-hover:text-blue-600 transition-colors">assignment</span>
            <span class="font-semibold text-sm">My Applications</span>
        </a>
        <a href="Student_ClubDashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-100 transition-all group">
            <span class="material-symbols-outlined group-hover:text-blue-600 transition-colors">groups</span>
            <span class="font-semibold text-sm">Clubs</span>
        </a>
        <a href="Student_Announcement.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-100 transition-all group">
            <span class="material-symbols-outlined group-hover:text-blue-600 transition-colors">campaign</span>
            <span class="font-semibold text-sm">Announcements</span>
        </a>
    </nav>
    <div class="p-4 border-t">
        <a href="../logOut.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-500 hover:bg-red-50 transition-all font-bold text-sm">
            <span class="material-symbols-outlined">logout</span> Logout
        </a>
    </div>
</aside>

<!-- TOP NAVBAR[cite: 1] -->
<header class="ml-64 h-20 glass-nav border-b flex items-center justify-between px-8 sticky top-0 z-40">
    <div class="relative group">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors">search</span>
        <input type="text" placeholder="Search clubs..." class="bg-slate-100 border-none px-10 py-2.5 rounded-xl w-80 text-sm focus:ring-2 focus:ring-blue-100 outline-none transition-all">
    </div>

    <div class="flex items-center gap-5">
        <div class="text-right">
            <p class="font-black text-slate-900 leading-none"><?= $safeFullName ?></p>
            <p class="text-[10px] text-blue-600 font-black uppercase tracking-widest mt-1">Student Member</p>
        </div>
        <div class="w-12 h-12 bg-gradient-to-tr from-blue-900 to-blue-700 rounded-2xl flex items-center justify-center font-bold text-white shadow-lg shadow-blue-100 uppercase">
            <?= strtoupper(substr($safeFirstName, 0, 1)) ?>
        </div>
    </div>
</header>

<!-- MAIN CONTENT[cite: 1] -->
<main class="ml-64 p-10">
    <!-- Hero Banner with Stats -->
    <div class="relative overflow-hidden rounded-[2rem] bg-blue-900 p-10 mb-10 flex flex-col md:flex-row items-center justify-between text-white shadow-2xl shadow-blue-200">
        <div class="relative z-10 max-w-2xl">
            <h2 class="text-4xl font-black mb-2">Welcome Back, <?= $safeFirstName ?>!</h2>
            <p class="text-blue-100 opacity-90 mb-6 text-lg">
                Your recruitment journey is looking promising. You have <span class="text-yellow-400 font-bold"><?= $appCount ?> active application<?= $appCount != 1 ? 's' : '' ?></span>.
            </p>
            <div class="flex gap-4">
                <a href="Student_Application.php" class="bg-white text-blue-900 font-bold px-6 py-3 rounded-xl hover:bg-blue-50 transition-all">View Updates</a>
            </div>
        </div>
        <div class="hidden md:flex gap-6 mt-6 md:mt-0">
             <div class="bg-white/10 backdrop-blur-md p-6 rounded-3xl border border-white/20 text-center w-32">
                <p class="text-3xl font-black"><?= $appCount ?></p>
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-70">Applied</p>
             </div>
             <div class="bg-white/10 backdrop-blur-md p-6 rounded-3xl border border-white/20 text-center w-32">
                <p class="text-3xl font-black"><?= $approvedCount ?></p>
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-70 text-green-300">Joined</p>
             </div>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-10">
        <!-- Application Progress Section (DYNAMIC)[cite: 1] -->
        <div class="col-span-12 lg:col-span-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600">pending_actions</span>
                    Active Progress
                </h3>
                <a href="Student_Application.php" class="text-blue-600 text-sm font-bold hover:underline uppercase tracking-widest">See All</a>
            </div>
            
            <div class="space-y-4">
                <?php if (!empty($applications)): ?>
                    <?php foreach ($applications as $app): 
                        // Logic for status coloring
                        $statusClass = "bg-amber-100 text-amber-700 border-amber-200"; // Pending
                        if($app['status'] == 'approved') $statusClass = "bg-green-100 text-green-700 border-green-200";
                        if($app['status'] == 'rejected') $statusClass = "bg-red-100 text-red-700 border-red-200";
                        if($app['status'] == 'interview') $statusClass = "bg-blue-100 text-blue-700 border-blue-200";
                    ?>
                    <div class="level-1-card p-6 flex items-center justify-between hover:border-blue-400 hover:-translate-y-1 transition-all duration-300 group">
                        <div class="flex items-center gap-6">
                            <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center font-black text-xl text-blue-900 border border-slate-100 group-hover:bg-blue-900 group-hover:text-white transition-all duration-500">
                                <?= substr($app['club_name'], 0, 1) ?>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-slate-900 tracking-tight"><?= htmlspecialchars($app['club_name']) ?></h4>
                                
                                <!-- Progress Stepper Design -->
                                <div class="mt-3 flex items-center gap-2">
                                    <div class="h-1.5 w-10 rounded-full bg-blue-600"></div>
                                    <div class="h-1.5 w-10 rounded-full <?= ($app['status'] == 'pending') ? 'bg-slate-200 animate-pulse' : 'bg-blue-600' ?>"></div>
                                    <div class="h-1.5 w-10 rounded-full <?= ($app['status'] == 'approved') ? 'bg-green-500' : ($app['status'] == 'rejected' ? 'bg-red-500' : 'bg-slate-200') ?>"></div>
                                    <span class="text-[10px] text-slate-400 font-black uppercase ml-2 tracking-tighter">
                                        <?= ucfirst($app['status']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                             <span class="<?= $statusClass ?> border px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest">
                                <?= $app['status'] ?>
                            </span>
                            <p class="text-[10px] text-slate-400 font-bold uppercase mt-3">Applied: <?= date("M d, Y", strtotime($app['applied_at'])) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white p-12 rounded-3xl border-2 border-dashed border-slate-200 text-center">
                        <span class="material-symbols-outlined text-slate-200 text-5xl mb-4">assignment_late</span>
                        <p class="text-slate-500 font-bold">No applications found. Time to join a club!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Side: Recent Activity[cite: 1] -->
        <div class="col-span-12 lg:col-span-4">
            <h3 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600">history</span>
                Timeline
            </h3>
            <div class="level-1-card p-8 relative overflow-hidden">
                <div class="absolute left-10 top-10 bottom-10 w-px bg-slate-100"></div>
                <div class="space-y-8">
                    <div class="relative pl-8">
                        <div class="absolute left-[-5px] top-1 w-2.5 h-2.5 rounded-full bg-blue-600 shadow-[0_0_0_4px_rgba(37,99,235,0.1)]"></div>
                        <p class="text-sm font-black text-slate-900 leading-none">Portal Accessed</p>
                        <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-widest">Just now</p>
                    </div>
                    <div class="relative pl-8">
                        <div class="absolute left-[-5px] top-1 w-2.5 h-2.5 rounded-full bg-slate-300"></div>
                        <p class="text-sm font-black text-slate-500 leading-none italic">End of recent logs</p>
                        <p class="text-xs text-slate-300 font-bold mt-1 uppercase tracking-widest">History ends</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>