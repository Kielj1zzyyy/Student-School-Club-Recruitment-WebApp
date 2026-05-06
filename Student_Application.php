<?php
session_start();
include '../db.php';

// --- 1. SESSION SECURITY & TIMEOUT ---
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

// --- 2. DATA FETCHING ---
$user_id = $_SESSION['user_id'];

// Fetch real applications from your SQL table
$query = "SELECT ca.status, ca.applied_at, c.club_name, c.category 
          FROM club_applications ca
          JOIN clubs c ON ca.club_id = c.id
          WHERE ca.user_id = ? 
          ORDER BY ca.applied_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// --- 3. UI HELPERS ---
$fullName = trim($_SESSION['name'] ?? 'Student');
$firstName = explode(' ', $fullName)[0];
$safeFullName = htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8');
$safeFirstName = htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');

// Count statuses for the Top Stats row
$totalApps = count($applications);
$approvedCount = count(array_filter($applications, fn($a) => $a['status'] == 'Approved'));
$pendingCount = count(array_filter($applications, fn($a) => $a['status'] == 'Pending'));
?>

<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>My Applications | Student Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet"/>
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

<!-- SIDEBAR -->
<aside class="fixed left-0 top-0 h-full w-64 bg-white border-r z-50 flex flex-col">
    <div class="p-8">
        <h1 class="font-black text-blue-900 text-xl tracking-tighter">CLUB<span class="text-blue-500">SYNC</span></h1>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Student Edition</p>
    </div>
    <nav class="flex-1 px-4 space-y-2">
        <a href="Student_Dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-100 transition-all group">
            <span class="material-symbols-outlined group-hover:text-blue-600 transition-colors">dashboard</span>
            <span class="font-semibold text-sm">Dashboard</span>
        </a>
        <a href="Student_Application.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-900 text-white font-bold shadow-lg shadow-blue-200">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">assignment</span>
            <span class="text-sm">My Applications</span>
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

<!-- TOP NAVBAR -->
<header class="ml-64 h-20 glass-nav border-b flex items-center justify-between px-8 sticky top-0 z-40">
    <div class="relative group">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors">search</span>
        <input type="text" placeholder="Search applications..." class="bg-slate-100 border-none px-10 py-2.5 rounded-xl w-80 text-sm focus:ring-2 focus:ring-blue-100 outline-none transition-all">
    </div>

    <div class="flex items-center gap-5">
        <div class="text-right">
            <p class="font-black text-slate-900 leading-none"><?= $safeFullName ?></p>
            <p class="text-[10px] text-blue-600 font-black uppercase tracking-widest mt-1">Academic Year 25-26</p>
        </div>
        <div class="w-12 h-12 bg-gradient-to-tr from-blue-900 to-blue-700 rounded-2xl flex items-center justify-center font-bold text-white shadow-lg shadow-blue-100 uppercase">
            <?= substr($safeFirstName, 0, 1) ?>
        </div>
    </div>
</header>

<!-- MAIN CONTENT -->
<main class="ml-64 p-10">
    <div class="mb-10">
        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Application Tracker</h1>
        <p class="text-slate-500 mt-2">Manage and monitor your recruitment status across all organizations.</p>
    </div>

    <!-- 1. STATS OVERVIEW ROW -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 flex items-center gap-5 shadow-sm">
            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">folder_shared</span>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Total Sent</p>
                <p class="text-3xl font-black text-slate-900"><?= $totalApps ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-100 flex items-center gap-5 shadow-sm">
            <div class="w-14 h-14 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">verified</span>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Approved</p>
                <p class="text-3xl font-black text-slate-900"><?= $approvedCount ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-100 flex items-center gap-5 shadow-sm">
            <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl">hourglass_top</span>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Pending</p>
                <p class="text-3xl font-black text-slate-900"><?= $pendingCount ?></p>
            </div>
        </div>
    </div>

    <!-- 2. APPLICATION LIST -->
    <div class="space-y-4 max-w-5xl">
        <?php if (empty($applications)): ?>
            <div class="bg-white p-20 rounded-3xl border-2 border-dashed border-slate-200 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-5xl text-slate-300">post_add</span>
                </div>
                <h2 class="text-2xl font-black text-slate-900">No applications yet</h2>
                <p class="text-slate-500 mt-2 mb-8">Start your journey by joining student organizations today.</p>
                <a href="Student_ClubDashboard.php" class="px-8 py-4 bg-blue-900 text-white rounded-2xl font-black hover:bg-blue-800 transition-all shadow-xl shadow-blue-100 inline-block">
                    Explore Clubs
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($applications as $app): 
                $statusColor = "bg-amber-100 text-amber-700 border-amber-200";
                if($app['status'] == 'Approved') $statusColor = "bg-green-100 text-green-700 border-green-200";
                if($app['status'] == 'Rejected') $statusColor = "bg-red-100 text-red-700 border-red-200";
            ?>
            <div class="level-1-card p-6 flex items-center justify-between hover:border-blue-400 hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center font-black text-2xl text-blue-900 border border-slate-100 group-hover:bg-blue-900 group-hover:text-white transition-all duration-500">
                        <?= substr($app['club_name'], 0, 1) ?>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight"><?= htmlspecialchars($app['club_name']) ?></h3>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1"><?= $app['category'] ?></p>
                        
                        <!-- 3. VISUAL PROGRESS STEPPER -->
                        <div class="mt-4 flex items-center gap-2">
                            <div class="h-1.5 w-12 rounded-full bg-blue-600"></div>
                            <div class="h-1.5 w-12 rounded-full <?= ($app['status'] == 'Pending') ? 'bg-slate-200 animate-pulse' : 'bg-blue-600' ?>"></div>
                            <div class="h-1.5 w-12 rounded-full <?= ($app['status'] == 'Approved') ? 'bg-green-500' : ($app['status'] == 'Rejected' ? 'bg-red-500' : 'bg-slate-200') ?>"></div>
                            <span class="text-[10px] text-slate-400 font-black uppercase ml-2 tracking-tighter">
                                <?= $app['status'] == 'Pending' ? 'Under Review' : 'Finalized' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <span class="<?= $statusColor ?> border px-5 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest block mb-3">
                        <?= $app['status'] ?>
                    </span>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                        Submitted: <?= date('M d, Y', strtotime($app['applied_at'])) ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

</body>
</html>