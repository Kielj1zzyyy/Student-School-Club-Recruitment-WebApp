<?php
session_start();
include '../db.php';

$user_id = $_SESSION['user_id'] ?? 0;

$result = $conn->query("SELECT * FROM clubs");
$clubs = $result->fetch_all(MYSQLI_ASSOC);

$fullName = trim($_SESSION['name'] ?? 'Student');
$nameParts = explode(' ', $fullName);
$firstName = $nameParts[0] ?? 'Student';

$safeFullName = htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8');
$safeFirstName = htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clubs - Student Portal</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- ✅ FIXED: Added missing Material Symbols Link -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800">

<!-- ✅ SIDEBAR (UNIFIED) -->
<aside class="fixed left-0 top-0 h-full w-64 bg-slate-50 border-r z-50 flex flex-col">
    <div class="p-6">
        <h1 class="font-black text-blue-900 text-lg">Student Portal</h1>
        <p class="text-xs text-slate-500 font-medium">Academic Year 2025-26</p>
    </div>

    <nav class="flex-1 px-3 space-y-1">
        <!-- Dashboard -->
        <a href="Student_Dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-100 hover:translate-x-1 transition-transform duration-200">
            <span class="material-symbols-outlined">dashboard</span>
            <span>Dashboard</span>
        </a>

        <!-- My Applications -->
        <a href="Student_Application.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-100 hover:translate-x-1 transition-transform duration-200">
            <span class="material-symbols-outlined">assignment</span>
            <span>My Applications</span>
        </a>

        <!-- Clubs (ACTIVE BUTTON) -->
        <a href="Student_ClubDashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-50 text-blue-700 font-bold border-r-4 border-blue-700 hover:translate-x-1 transition-transform duration-200">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">groups</span>
            <span>Clubs</span>
        </a>

        <!-- Announcements -->
        <a href="Student_Announcement.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-100 hover:translate-x-1 transition-transform duration-200">
            <span class="material-symbols-outlined">campaign</span>
            <span>Announcements</span>
        </a>
    </nav>

    <!-- Logout -->
    <div class="p-3 mt-auto">
        <a href="../logOut.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-100 hover:translate-x-1 transition-transform duration-200">
            <span class="material-symbols-outlined">logout</span>
            <span>Logout</span>
        </a>
    </div>
</aside>

<!-- HEADER -->
<header class="ml-64 h-16 bg-white border-b flex items-center justify-between px-6 sticky top-0 z-40">
    <div class="relative">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
        <input id="searchInput" type="text" placeholder="Search clubs..." 
               class="w-80 pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all">
    </div>

    <div class="flex items-center gap-3">
        <div class="text-right">
            <p class="text-sm font-bold text-slate-900"><?= $safeFullName ?></p>
            <p class="text-xs text-slate-500">Student Portal</p>
        </div>
        <div class="w-10 h-10 bg-blue-100 border-2 border-white shadow-sm rounded-full flex items-center justify-center font-bold text-blue-700">
            <?= strtoupper(substr($safeFirstName, 0, 1)) ?>
        </div>
    </div>
</header>

<!-- MAIN -->
<main class="ml-64 p-8 min-h-screen">
    <!-- TITLE -->
    <div class="mb-8">
        <h1 class="text-3xl font-black text-blue-900">Discover Clubs</h1>
        <p class="text-slate-500 text-sm mt-1">Explore and join organizations that match your interests.</p>
    </div>

    <!-- FILTERS -->
    <div class="flex gap-2 mb-8 flex-wrap">
        <button onclick="filterCategory('All')" class="cat-btn px-5 py-2 bg-blue-600 text-white rounded-full text-sm font-bold shadow-sm">All</button>
        <button onclick="filterCategory('Academic')" class="cat-btn px-5 py-2 bg-white border border-slate-200 rounded-full text-sm font-medium hover:bg-slate-50 transition-colors">Academic</button>
        <button onclick="filterCategory('Arts & Culture')" class="cat-btn px-5 py-2 bg-white border border-slate-200 rounded-full text-sm font-medium hover:bg-slate-50 transition-colors">Arts</button>
        <button onclick="filterCategory('Sports')" class="cat-btn px-5 py-2 bg-white border border-slate-200 rounded-full text-sm font-medium hover:bg-slate-50 transition-colors">Sports</button>
        <button onclick="filterCategory('Technology')" class="cat-btn px-5 py-2 bg-white border border-slate-200 rounded-full text-sm font-medium hover:bg-slate-50 transition-colors">Tech</button>
    </div>

    <!-- GRID -->
    <div id="clubGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($clubs as $club): ?>
        <div class="club-card bg-white p-6 rounded-2xl border border-slate-200 hover:shadow-xl hover:border-blue-200 transition-all duration-300 group"
             data-name="<?= strtolower($club['club_name']) ?>"
             data-category="<?= $club['category'] ?>">
            
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center font-black text-blue-700 text-xl group-hover:scale-110 transition-transform">
                    <?= strtoupper(substr($club['club_name'], 0, 1)) ?>
                </div>
                <div>
                    <h3 class="font-bold text-blue-900 group-hover:text-blue-600 transition-colors"><?= htmlspecialchars($club['club_name']) ?></h3>
                    <span class="text-[10px] uppercase tracking-wider font-bold px-2 py-0.5 bg-slate-100 text-slate-500 rounded"><?= $club['category'] ?></span>
                </div>
            </div>

            <p class="text-sm text-slate-500 mb-6 line-clamp-2 leading-relaxed">
                <?= htmlspecialchars($club['description']) ?>
            </p>

            <form method="POST" action="apply.php">
                <input type="hidden" name="club_id" value="<?= $club['id'] ?>">
                <button class="w-full py-3 bg-blue-900 text-white font-bold rounded-xl text-sm hover:bg-blue-800 hover:shadow-lg transition-all active:scale-95">
                    Join Club
                </button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<script>
    const searchInput = document.getElementById("searchInput");
    const cards = document.querySelectorAll(".club-card");

    searchInput.addEventListener("input", function () {
        const value = this.value.toLowerCase();
        cards.forEach(card => {
            const name = card.dataset.name;
            card.style.display = name.includes(value) ? "block" : "none";
        });
    });

    function filterCategory(category) {
        cards.forEach(card => {
            const cat = card.dataset.category;
            card.style.display = (category === "All" || cat === category) ? "block" : "none";
        });
    }
</script>

</body>
</html>