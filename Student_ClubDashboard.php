<?php
session_start();
include '../db.php';

// --- SESSION SECURITY ---
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../logIn.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all available clubs[cite: 2]
$result = $conn->query("SELECT * FROM clubs ORDER BY club_name ASC");
$clubs = $result->fetch_all(MYSQLI_ASSOC);

// --- UI HELPERS ---
$fullName = trim($_SESSION['name'] ?? 'Student');
$firstName = explode(' ', $fullName)[0];
$safeFullName = htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8');
$safeFirstName = htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discover Clubs | Student Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
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

<!-- SIDEBAR[cite: 2] -->
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
        <a href="Student_ClubDashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-900 text-white font-bold shadow-lg shadow-blue-200 transition-all">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">groups</span>
            <span class="text-sm font-bold">Clubs</span>
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

<!-- TOP NAVBAR[cite: 2] -->
<header class="ml-64 h-20 glass-nav border-b flex items-center justify-between px-8 sticky top-0 z-40">
    <div class="relative group">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors">search</span>
        <input id="searchInput" type="text" placeholder="Search clubs..." class="bg-slate-100 border-none px-10 py-2.5 rounded-xl w-80 text-sm focus:ring-2 focus:ring-blue-100 outline-none transition-all">
    </div>

    <div class="flex items-center gap-5">
        <div class="text-right">
            <p class="font-black text-slate-900 leading-none"><?= $safeFullName ?></p>
            <p class="text-[10px] text-blue-600 font-black uppercase tracking-widest mt-1">NBSCian Student</p>
        </div>
        <div class="w-12 h-12 bg-gradient-to-tr from-blue-900 to-blue-700 rounded-2xl flex items-center justify-center font-bold text-white shadow-lg shadow-blue-100 uppercase">
            <?= strtoupper(substr($safeFirstName, 0, 1)) ?>
        </div>
    </div>
</header>

<!-- MAIN CONTENT[cite: 2] -->
<main class="ml-64 p-10">
    <div class="mb-10">
        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Discover Clubs</h1>
        <p class="text-slate-500 mt-2">Find your community and make your mark on campus.</p>
    </div>
    
    <div id="clubGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($clubs as $club): ?>
        <div class="club-card level-1-card p-6 hover:border-blue-400 hover:-translate-y-1 transition-all duration-300 group" data-name="<?= strtolower($club['club_name']) ?>">
            <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center font-black text-2xl text-blue-900 border border-slate-100 group-hover:bg-blue-900 group-hover:text-white transition-all duration-500 mb-6 uppercase">
                <?= substr($club['club_name'], 0, 1) ?>
            </div>
            
            <h3 class="text-xl font-black text-slate-900 tracking-tight mb-1 group-hover:text-blue-900"><?= htmlspecialchars($club['club_name']) ?></h3>
            <p class="text-xs font-bold text-blue-600 mb-4 uppercase tracking-widest"><?= $club['category'] ?></p>
            
            <p class="text-sm text-slate-500 mb-8 line-clamp-3 leading-relaxed">
                <?= htmlspecialchars($club['description'] ?? 'No description available.') ?>
            </p>
            
            <button onclick="confirmJoin('<?= htmlspecialchars($club['club_name'], ENT_QUOTES) ?>', <?= $club['id'] ?>)" 
                    class="w-full py-4 bg-blue-900 text-white font-black rounded-2xl text-sm hover:bg-blue-800 transition-all active:scale-95 shadow-xl shadow-blue-100 group-hover:shadow-blue-200">
                Join Organization
            </button>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<script>
    function confirmJoin(clubName, clubId) {
        // Confirmation logic[cite: 2]
        const proceed = confirm("Do you want to apply for membership in " + clubName + "?");
        if (proceed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'process_application.php';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'club_id';
            input.value = clubId;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Live Search Logic[cite: 2]
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.club-card').forEach(card => {
            card.style.display = card.getAttribute('data-name').includes(query) ? 'block' : 'none';
        });
    });
</script>
</body>
</html>