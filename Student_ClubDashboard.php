<?php
session_start();
include '../db.php';

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

$user_id = $_SESSION['user_id'];


$stmt = $conn->prepare("
SELECT 
    ca.id,
    c.club_name,
    c.category,
    ca.status,
    ca.applied_at
FROM club_applications ca
JOIN clubs c ON ca.club_id = c.id
WHERE ca.user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


$result = $conn->query("SELECT * FROM clubs");
$clubs = $result->fetch_all(MYSQLI_ASSOC);


$fullName = trim($_SESSION['name'] ?? 'Student');
$nameParts = explode(' ', $fullName);
$firstName = $nameParts[0] ?? 'Student';

$safeFullName = htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8');
$safeFirstName = htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Discover Clubs - CampusClubRecruit</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>

<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                "primary-fixed-dim": "#adc7f7",
                "inverse-primary": "#adc7f7",
                "on-tertiary-fixed": "#2b1700",
                "secondary-fixed-dim": "#a2c9ff",
                "primary-container": "#1a365d",
                "surface-container-lowest": "#ffffff",
                "surface-container-high": "#dce9ff",
                "surface-dim": "#ccdbf4",
                "surface-container-low": "#eff4ff",
                "background": "#f8f9ff",
                "surface-container": "#e5eeff",
                "on-tertiary-container": "#c6955e",
                "inverse-surface": "#223144",
                "outline-variant": "#c4c6cf",
                "on-error-container": "#93000a",
                "on-secondary-container": "#00477f",
                "on-primary-fixed": "#001b3c",
                "tertiary-container": "#4f2e00",
                "surface-bright": "#f8f9ff",
                "secondary": "#1960a3",
                "on-secondary-fixed": "#001c38",
                "tertiary-fixed": "#ffddba",
                "primary-fixed": "#d6e3ff",
                "on-tertiary-fixed-variant": "#633f0f",
                "on-secondary-fixed-variant": "#004881",
                "inverse-on-surface": "#eaf1ff",
                "error-container": "#ffdad6",
                "on-surface": "#0d1c2e",
                "on-surface-variant": "#43474e",
                "error": "#ba1a1a",
                "on-primary-fixed-variant": "#2d476f",
                "surface-variant": "#d4e4fc",
                "outline": "#74777f",
                "surface-container-highest": "#d4e4fc",
                "secondary-container": "#7db6ff",
                "on-error": "#ffffff",
                "on-tertiary": "#ffffff",
                "surface-tint": "#455f88",
                "primary": "#002045",
                "tertiary-fixed-dim": "#f2bc82",
                "on-background": "#0d1c2e",
                "on-primary-container": "#86a0cd",
                "on-primary": "#ffffff",
                "secondary-fixed": "#d3e4ff",
                "surface": "#f8f9ff",
                "on-secondary": "#ffffff",
                "tertiary": "#321b00"
            },
            "borderRadius": {
                "DEFAULT": "0.25rem",
                "lg": "0.5rem",
                "xl": "0.75rem",
                "full": "9999px"
            },
            "spacing": {
                "md": "24px",
                "base": "8px",
                "xl": "64px",
                "container-max": "1280px",
                "sm": "12px",
                "gutter": "24px",
                "xs": "4px",
                "lg": "40px"
            },
            "fontFamily": {
                "h3": ["Inter"],
                "button": ["Inter"],
                "h2": ["Inter"],
                "body-sm": ["Inter"],
                "body-lg": ["Inter"],
                "label-caps": ["Inter"],
                "body-md": ["Inter"],
                "h1": ["Inter"]
            },
            "fontSize": {
                "h3": ["24px", {"lineHeight": "1.4", "letterSpacing": "0em", "fontWeight": "600"}],
                "button": ["16px", {"lineHeight": "1", "letterSpacing": "0.01em", "fontWeight": "600"}],
                "h2": ["30px", {"lineHeight": "1.3", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                "body-sm": ["14px", {"lineHeight": "1.5", "letterSpacing": "0em", "fontWeight": "400"}],
                "body-lg": ["18px", {"lineHeight": "1.6", "letterSpacing": "0em", "fontWeight": "400"}],
                "label-caps": ["12px", {"lineHeight": "1", "letterSpacing": "0.05em", "fontWeight": "700"}],
                "body-md": ["16px", {"lineHeight": "1.5", "letterSpacing": "0em", "fontWeight": "400"}],
                "h1": ["40px", {"lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700"}]
            }
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            background-color: #f7fafc;
        }
    </style>
</head>
<body class="font-body-md text-on-surface">
<!-- SideNavBar Shell -->
<aside class="fixed left-0 top-0 flex flex-col justify-between py-6 h-screen w-64 border-r border-slate-200 bg-white shadow-[4px_0_24px_rgba(26,54,93,0.05)] font-['Inter'] antialiased tracking-tight">
<div class="flex flex-col">
<div class="px-6 mb-10">
<div class="text-xl font-bold text-[#1A365D]">CampusClubs</div>
<div class="text-xs text-slate-500 uppercase tracking-widest mt-1">Student Portal</div>
</div>
<nav class="flex flex-col space-y-1">
<a class="flex items-center px-6 py-3 text-slate-500 hover:text-[#1A365D] hover:bg-slate-50 transition-all duration-200 active:scale-95" href="Student_Dashboard.php">
<span class="material-symbols-outlined mr-3" data-icon="dashboard">dashboard</span>
<span class="font-medium">Dashboard</span>
</a>
<a class="flex items-center px-6 py-3 text-slate-500 hover:text-[#1A365D] hover:bg-slate-50 transition-all duration-200 active:scale-95" href="Student_Application.php">
<span class="material-symbols-outlined mr-3" data-icon="assignment_ind">assignment_ind</span>
<span class="font-medium">My Applications</span>
</a>
<!-- Active Tab: Clubs -->
<a class="flex items-center px-6 py-3 text-[#2B6CB0] font-semibold border-r-4 border-[#2B6CB0] bg-blue-50/50 transition-all duration-150" href="Student_ClubDashboard.php">
<span class="material-symbols-outlined mr-3" data-icon="groups">groups</span>
<span class="font-medium">Clubs</span>
</a>
<a class="flex items-center px-6 py-3 text-slate-500 hover:text-[#1A365D] hover:bg-slate-50 transition-all duration-200 active:scale-95" href="Student_Announcement.php">
<span class="material-symbols-outlined mr-3" data-icon="campaign">campaign</span>
<span class="font-medium">Announcements</span>
</a>
</nav>
</div>
<div class="px-4">
<a class="flex items-center w-full px-4 py-3 text-slate-500 hover:text-error hover:bg-red-50 rounded-lg transition-all duration-200 active:scale-95" href="../logOut.php">
<span class="material-symbols-outlined mr-3" data-icon="logout">logout</span>
<span class="font-medium">Logout</span>
</a>
</div>
</aside>
<!-- TopNavBar Shell -->
<header class="fixed top-0 left-64 right-0 h-16 bg-[#F7FAFC]/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-8 z-10">
<div class="flex items-center flex-1 max-w-xl">
<div class="relative w-full group">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-[#2B6CB0]" data-icon="search">search</span>
<input id="searchInput"
class="w-full bg-white border border-slate-200 rounded-lg py-2 pl-10 pr-4 text-sm"
placeholder="Search clubs..." type="text"/>
</div>
</div>
<div class="flex items-center space-x-4">
<button class="text-slate-600 hover:bg-white p-2 rounded-lg transition-all relative">
<span class="material-symbols-outlined" data-icon="notifications">notifications</span>
<span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full border-2 border-[#F7FAFC]"></span>
</button>
<button class="text-slate-600 hover:bg-white p-2 rounded-lg transition-all">
<span class="material-symbols-outlined" data-icon="settings">settings</span>
</button>

<div class="h-8 w-px bg-slate-200 mx-2"></div>

<!-- 🔥 DYNAMIC PROFILE SECTION 🔥 -->
<div class="flex items-center gap-3 cursor-pointer hover:opacity-80 transition-opacity">
    <div class="text-right hidden sm:block">
        <p class="text-sm font-bold text-[#1A365D]"><?= $safeFullName ?></p>
        <p class="text-xs text-slate-500">Student Portal</p>
    </div>
    <div class="w-10 h-10 rounded-full border-2 border-primary-fixed bg-blue-100 flex items-center justify-center text-[#2B6CB0] font-bold shadow-sm">
        <?= strtoupper(substr($safeFirstName, 0, 1)) ?>
    </div>
</div>

</div>
</header>
<!-- Main Content Canvas -->
<main class="ml-64 pt-16 min-h-screen">
<div class="p-8 max-w-7xl mx-auto space-y-8">
<!-- Hero / Header Section -->
<section class="flex flex-col md:flex-row md:items-end justify-between gap-6">
<div>
<h1 class="font-h1 text-h1 text-primary">Discover Clubs</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant mt-2 max-w-2xl">
                        Find your community and elevate your student experience. Explore over 150 student-led organizations committed to academic excellence, creativity, and leadership.
                    </p>
</div>
<div class="flex items-center gap-3">
<div class="bg-white border border-slate-200 rounded-lg p-1 flex">
<button class="px-4 py-2 bg-blue-50 text-[#2B6CB0] rounded-md font-semibold text-sm">All Clubs</button>
<button class="px-4 py-2 text-slate-500 hover:bg-slate-50 rounded-md font-medium text-sm">My Interests</button>
</div>
</div>
</section>
<!-- Featured Banner (Asymmetric Layout) -->
<section class="grid grid-cols-1 md:grid-cols-12 gap-6">
<div class="md:col-span-8 relative overflow-hidden rounded-xl bg-primary-container h-64 flex items-center group cursor-pointer">
<img class="absolute inset-0 w-full h-full object-cover opacity-40 group-hover:scale-105 transition-transform duration-700" data-alt="University students collaborating on a technology project in a modern, glass-walled campus workspace, dramatic lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBJrk3EWBqWVSi91THGEfCr7AgKRUYLv8Y10KYFdACRO1Qki5PfhA9q32DifWeqOEYfBulLqH5zyuqq1KA-osUJkmiSV1ypFI57X_n7AnJik6xA6flWni967f-uAfTa9SVCjor5ARX1HtW7FUpYH5n_Pwufa0BQxgffSkSP1_zBuEVR7OYc_MTxPTIPXKJJMGrj73TJuTOXxp3mNNF6XLXhXCgtM7w75NuFM0mbUpQ6EKC08kpJlmkeauvYe3OYf42m6vpaPVFEGTM_"/>
<div class="relative z-10 p-10 space-y-4">
<span class="inline-block px-3 py-1 bg-[#ECC94B] text-[#002045] font-bold text-[10px] uppercase tracking-widest rounded">Featured Spotlight</span>
<h2 class="text-white font-h2 text-h2 max-w-md leading-tight">Join the Robotics Lab: Fall Recruitment Now Open</h2>
<button class="mt-4 px-6 py-2.5 bg-[#ECC94B] text-[#1A365D] font-button text-button rounded-lg hover:shadow-lg transition-all active:scale-95">Apply for Membership</button>
</div>
</div>
<div class="md:col-span-4 flex flex-col gap-6">
<div class="flex-1 bg-white p-6 rounded-xl border border-slate-200 shadow-[0_2px_4px_rgba(26,54,93,0.05)] hover:shadow-md transition-all">
<div class="flex items-center justify-between mb-4">
<span class="material-symbols-outlined text-[#2B6CB0] text-3xl" data-icon="stars" style="font-variation-settings: 'FILL' 1;">stars</span>
<span class="text-xs font-bold text-slate-400">DAILY PICK</span>
</div>
<h3 class="font-h3 text-h3 text-on-surface text-lg mb-2">Debate Society</h3>
<p class="text-on-surface-variant text-sm line-clamp-2">Master the art of persuasion and critical thinking in our award-winning society.</p>
<a class="inline-flex items-center text-[#2B6CB0] font-semibold text-sm mt-4 hover:underline" href="#">View details <span class="material-symbols-outlined text-xs ml-1" data-icon="arrow_forward">arrow_forward</span></a>
</div>
<div class="flex-1 bg-white p-6 rounded-xl border border-slate-200 shadow-[0_2px_4px_rgba(26,54,93,0.05)] hover:shadow-md transition-all">
<div class="flex items-center justify-between mb-4">
<span class="material-symbols-outlined text-[#2B6CB0] text-3xl" data-icon="event_note">event_note</span>
<span class="text-xs font-bold text-slate-400">UPCOMING</span>
</div>
<h3 class="font-h3 text-h3 text-on-surface text-lg mb-2">Club Fair 2024</h3>
<p class="text-on-surface-variant text-sm">Join us next Tuesday on the main quad to meet all 150+ organizations in person.</p>
</div>
</div>
</section>
<!-- Filter & Grid Controls -->
<div class="flex flex-wrap items-center justify-between gap-4 py-4 border-y border-slate-100">
<div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0 scrollbar-hide">
<button onclick="filterCategory('All')" class="cat-btn">All Categories</button>
<button onclick="filterCategory('Academic')" class="cat-btn">Academic</button>
<button onclick="filterCategory('Arts & Culture')" class="cat-btn">Arts & Culture</button>
<button onclick="filterCategory('Sports')" class="cat-btn">Sports</button>
<button onclick="filterCategory('Technology')" class="cat-btn">Technology</button>
<button onclick="filterCategory('Service')" class="cat-btn">Service</button>
</div>
<div class="flex items-center gap-3">
<span class="text-sm text-slate-500 font-medium">Sort by:</span>
<select class="bg-transparent border-none text-sm font-semibold text-primary focus:ring-0 cursor-pointer">
<option>Most Popular</option>
<option>Recently Added</option>
<option>Alphabetical</option>
</select>
</div>
</div>
<!-- Club Cards Grid -->
<section id="clubGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

<?php foreach ($clubs as $club): ?>

<div class="club-card bg-white rounded-xl border shadow group p-6"
     data-name="<?= strtolower($club['club_name']) ?>"
     data-category="<?= $club['category'] ?>">

    <h4 class="text-lg font-bold text-primary">
        <?= htmlspecialchars($club['club_name']) ?>
    </h4>

    <p class="text-sm text-slate-500 mt-2">
        <?= htmlspecialchars($club['description']) ?>
    </p>

    <p class="text-xs mt-2 text-slate-400">
        <?= $club['category'] ?>
    </p>

    <form method="POST" action="apply.php">
        <input type="hidden" name="club_id" value="<?= $club['id'] ?>">
        <button type="submit"
            class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg">
            Join Club
        </button>
    </form>

</div>

<?php endforeach; ?>

</section>
<!-- Load More or Pagination (Subtle) -->
<div class="flex flex-col items-center py-12 gap-4">
<button class="px-8 py-3 border border-slate-200 rounded-lg text-primary font-semibold hover:bg-slate-50 transition-all flex items-center gap-2">
                    Load More Organizations
                    <span class="material-symbols-outlined text-sm" data-icon="expand_more">expand_more</span>
</button>
<p class="text-xs text-slate-400">Showing 6 of 154 clubs</p>
</div>
</div>
</main>
<script>
const searchInput = document.getElementById("searchInput");
const cards = document.querySelectorAll(".club-card");

searchInput.addEventListener("input", function () {
    const value = this.value.toLowerCase();

    cards.forEach(card => {
        const name = card.dataset.name;
        const match = name.includes(value);
        card.style.display = match ? "block" : "none";
    });
});

function filterCategory(category) {
    cards.forEach(card => {
        const cat = card.dataset.category;
        card.style.display =
            (category === "All" || cat === category) ? "block" : "none";
    });
}
</script>
</body></html>