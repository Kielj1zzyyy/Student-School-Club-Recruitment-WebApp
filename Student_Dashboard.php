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

$fullName = trim($_SESSION['name'] ?? 'Student');
$nameParts = explode(' ', $fullName);
$firstName = $nameParts[0] ?? 'Student';

$safeFullName = htmlspecialchars($fullName);
$safeFirstName = htmlspecialchars($firstName);

$appStmt = $conn->prepare("
SELECT ca.id, ca.status, ca.applied_at, c.club_name
FROM club_applications ca
JOIN clubs c ON ca.club_id = c.id
WHERE ca.user_id = ?
ORDER BY ca.applied_at DESC
");

$appStmt->bind_param("i", $user_id);
$appStmt->execute();
$applications = $appStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$appCount = count($applications);
?>

<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Student Portal Overview - CampusClubRecruit</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-container-lowest": "#ffffff",
                        "on-primary": "#ffffff",
                        "on-tertiary": "#ffffff",
                        "surface-variant": "#d4e4fc",
                        "outline": "#74777f",
                        "on-surface": "#0d1c2e",
                        "error-container": "#ffdad6",
                        "on-primary-fixed": "#001b3c",
                        "on-tertiary-fixed-variant": "#633f0f",
                        "secondary-fixed": "#d3e4ff",
                        "on-secondary": "#ffffff",
                        "on-primary-fixed-variant": "#2d476f",
                        "surface-container-highest": "#d4e4fc",
                        "on-secondary-container": "#00477f",
                        "inverse-on-surface": "#eaf1ff",
                        "on-tertiary-fixed": "#2b1700",
                        "primary": "#002045",
                        "on-secondary-fixed": "#001c38",
                        "surface-bright": "#f8f9ff",
                        "on-primary-container": "#86a0cd",
                        "on-error-container": "#93000a",
                        "surface-container": "#e5eeff",
                        "secondary-fixed-dim": "#a2c9ff",
                        "primary-fixed-dim": "#adc7f7",
                        "error": "#ba1a1a",
                        "primary-container": "#1a365d",
                        "tertiary": "#321b00",
                        "outline-variant": "#c4c6cf",
                        "surface-container-high": "#dce9ff",
                        "on-tertiary-container": "#c6955e",
                        "surface-container-low": "#eff4ff",
                        "inverse-surface": "#223144",
                        "on-error": "#ffffff",
                        "inverse-primary": "#adc7f7",
                        "on-background": "#0d1c2e",
                        "surface-dim": "#ccdbf4",
                        "tertiary-container": "#4f2e00",
                        "on-surface-variant": "#43474e",
                        "secondary-container": "#7db6ff",
                        "secondary": "#1960a3",
                        "tertiary-fixed-dim": "#f2bc82",
                        "on-secondary-fixed-variant": "#004881",
                        "background": "#f8f9ff",
                        "surface": "#f8f9ff",
                        "surface-tint": "#455f88",
                        "primary-fixed": "#d6e3ff",
                        "tertiary-fixed": "#ffddba",
                        "brand-gold": "#ECC94B",
                        "brand-navy": "#002045"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "xl": "64px",
                        "container-max": "1280px",
                        "md": "24px",
                        "base": "8px",
                        "lg": "40px",
                        "gutter": "24px",
                        "sm": "12px",
                        "xs": "4px"
                    },
                    "fontFamily": {
                        "h3": ["Inter"], "body-lg": ["Inter"], "h2": ["Inter"], "h1": ["Inter"],
                        "button": ["Inter"], "body-sm": ["Inter"], "body-md": ["Inter"], "label-caps": ["Inter"]
                    },
                    "fontSize": {
                        "h3": ["24px", {"lineHeight": "1.4", "fontWeight": "600"}],
                        "body-lg": ["18px", {"lineHeight": "1.6", "fontWeight": "400"}],
                        "h2": ["30px", {"lineHeight": "1.3", "fontWeight": "600"}],
                        "h1": ["40px", {"lineHeight": "1.2", "fontWeight": "700"}],
                        "button": ["16px", {"lineHeight": "1", "fontWeight": "600"}],
                        "body-sm": ["14px", {"lineHeight": "1.5", "fontWeight": "400"}],
                        "body-md": ["16px", {"lineHeight": "1.5", "fontWeight": "400"}],
                        "label-caps": ["12px", {"lineHeight": "1", "fontWeight": "700"}]
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .level-1-card { background-color: white; box-shadow: 0 2px 4px rgba(0, 32, 69, 0.1); border-radius: 0.75rem; }
        .level-2-hover:hover { box-shadow: 0 8px 16px rgba(0, 32, 69, 0.15); }
    </style>
</head>
<body class="bg-surface font-body-md text-on-surface">

<aside class="fixed left-0 top-0 h-full flex flex-col pt-4 pb-8 h-screen w-64 border-r bg-slate-50 border-slate-200 z-50">
    <div class="px-6 mb-8">
        <h1 class="text-lg font-black text-blue-900">Student Portal</h1>
        <p class="text-xs text-slate-500 font-medium">Academic Year 2025-26</p>
    </div>
    <nav class="flex-1 px-3 space-y-1">
        <a class="cursor-pointer flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-50 text-blue-700 font-bold border-r-4 border-blue-700" href="Student_Dashboard.php">
            <span class="material-symbols-outlined">dashboard</span><span>Dashboard</span>
        </a>
        <a class="cursor-pointer flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-100" href="Student_Application.php">
            <span class="material-symbols-outlined">assignment</span><span>My Applications</span>
        </a>
        <a class="cursor-pointer flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-100" href="Student_ClubDashboard.php">
            <span class="material-symbols-outlined">groups</span><span>Clubs</span>
        </a>
        <a class="cursor-pointer flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-100" href="Student_Announcement.php">
            <span class="material-symbols-outlined">campaign</span><span>Announcements</span>
        </a>
    </nav>
    <div class="px-3 mt-auto">
        <a class="cursor-pointer flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-100" href="../logOut.php">
            <span class="material-symbols-outlined">logout</span><span>Logout</span>
        </a>
    </div>
</aside>

<header class="flex justify-between items-center h-16 px-6 w-full sticky top-0 z-40 bg-white border-b border-slate-200 shadow-sm ml-64" style="width: calc(100% - 16rem);">
    <div class="flex items-center flex-1 max-w-xl">
        <div class="relative w-full">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
            <input class="w-full bg-slate-50 border-slate-200 rounded-lg py-2 pl-10 pr-4 text-sm" placeholder="Search clubs..." type="text"/>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <div class="flex items-center gap-3">
            <div class="text-right">
                <p class="text-sm font-bold text-slate-900"><?= $safeFullName ?></p>
                <p class="text-xs text-slate-500">Student Portal</p>
            </div>
            <div class="w-10 h-10 rounded-full border-2 border-primary-fixed bg-blue-100 flex items-center justify-center text-blue-800 font-bold">
                <?= strtoupper(substr($safeFirstName, 0, 1)) ?>
            </div>
        </div>
    </div>
</header>

<main class="ml-64 p-8 bg-surface min-h-[calc(100vh-4rem)]">
    <!-- Hero Banner -->
    <div class="relative overflow-hidden rounded-xl bg-primary-container p-10 mb-8 flex flex-col md:flex-row items-center justify-between">
        <div class="relative z-10 text-white max-w-2xl">
            <h2 class="font-h1 text-h1 mb-2">Welcome Back, <?= $safeFirstName ?>!</h2>
            <p class="font-body-lg text-body-lg text-blue-100 opacity-90 mb-6">
                Your recruitment journey is looking promising. You have 
                <span class="text-brand-gold font-bold"><?= $appCount ?> active application<?= $appCount != 1 ? 's' : '' ?></span>.
            </p>
            <div class="flex gap-4">
                <button class="bg-brand-gold text-primary font-button px-6 py-3 rounded-lg">View Status Updates</button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-gutter">
        <!-- Application Progress Card (DYNAMIC) -->
        <div class="col-span-12 lg:col-span-8 level-1-card p-6 border-t-4 border-secondary">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-h3 text-h3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">pending_actions</span>
                    Application Progress
                </h3>
                <a class="text-secondary text-sm font-semibold hover:underline" href="Student_Application.php">View All</a>
            </div>
            
            <div class="space-y-4">
                <?php if (!empty($applications)): ?>
                    <?php foreach ($applications as $app): ?>
                        <div class="p-4 bg-surface-container-low rounded-xl flex items-center justify-between group border border-transparent hover:border-slate-200 transition-all">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center border border-slate-200 shadow-sm">
                                    <span class="material-symbols-outlined text-primary">groups</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-primary"><?= htmlspecialchars($app['club_name']) ?></h4>
                                    <p class="text-sm text-slate-500">Applied: <?= date("M d, Y", strtotime($app['applied_at'])) ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <?php 
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'interview' => 'bg-blue-100 text-blue-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700'
                                    ];
                                    $statusLabel = ucfirst($app['status']);
                                    $currentClass = $statusClasses[$app['status']] ?? 'bg-slate-100 text-slate-700';
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold <?= $currentClass ?>">
                                    <?= $statusLabel ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-10">
                        <span class="material-symbols-outlined text-slate-300 text-5xl mb-2">assignment_late</span>
                        <p class="text-slate-500">No applications yet. Start exploring clubs!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activity (Static Placeholder) -->
        <div class="col-span-12 lg:col-span-4 level-1-card p-6">
            <h3 class="font-h3 text-h3 mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">history</span>Recent Activity
            </h3>
            <div class="space-y-6 relative before:absolute before:left-3 before:top-2 before:bottom-2 before:w-px before:bg-slate-200">
                <div class="relative pl-10">
                    <div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center z-10">
                        <span class="material-symbols-outlined text-xs text-blue-700">check_circle</span>
                    </div>
                    <p class="text-sm font-medium text-slate-900">Portal accessed successfully</p>
                    <p class="text-xs text-slate-400">Just now</p>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>