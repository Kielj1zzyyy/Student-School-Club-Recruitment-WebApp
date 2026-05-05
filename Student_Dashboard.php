<?php
session_start();

// Check if the user is logged in and is a student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../logIn.php");
    exit();
}

// Fetch the user's name from the session (fallback to 'Student' if not found)
$fullName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Student';

// Extract just the first name for the casual "Welcome Back" greeting
$nameParts = explode(' ', trim($fullName));
$firstName = $nameParts[0];
?>

<!DOCTYPE html>
<html class="light" lang="en"><head>
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
                        "h3": ["Inter"],
                        "body-lg": ["Inter"],
                        "h2": ["Inter"],
                        "h1": ["Inter"],
                        "button": ["Inter"],
                        "body-sm": ["Inter"],
                        "body-md": ["Inter"],
                        "label-caps": ["Inter"]
                    },
                    "fontSize": {
                        "h3": ["24px", {"lineHeight": "1.4", "letterSpacing": "0em", "fontWeight": "600"}],
                        "body-lg": ["18px", {"lineHeight": "1.6", "letterSpacing": "0em", "fontWeight": "400"}],
                        "h2": ["30px", {"lineHeight": "1.3", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "h1": ["40px", {"lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "button": ["16px", {"lineHeight": "1", "letterSpacing": "0.01em", "fontWeight": "600"}],
                        "body-sm": ["14px", {"lineHeight": "1.5", "letterSpacing": "0em", "fontWeight": "400"}],
                        "body-md": ["16px", {"lineHeight": "1.5", "letterSpacing": "0em", "fontWeight": "400"}],
                        "label-caps": ["12px", {"lineHeight": "1", "letterSpacing": "0.05em", "fontWeight": "700"}]
                    }
                }
            }
        }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .level-1-card {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 32, 69, 0.1);
            border-radius: 0.75rem;
        }
        .level-2-hover:hover {
            box-shadow: 0 8px 16px rgba(0, 32, 69, 0.15);
        }
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-surface font-body-md text-on-surface">
<!-- SideNavBar Anchor -->
<aside class="fixed left-0 top-0 h-full flex flex-col pt-4 pb-8 h-screen w-64 border-r bg-slate-50 border-slate-200 z-50">
<div class="px-6 mb-8">
<h1 class="text-lg font-black text-blue-900">Student Portal</h1>
<p class="text-xs text-slate-500 font-medium">Academic Year 2025-26</p>
</div>
<nav class="flex-1 px-3 space-y-1">
<a class="cursor-pointer flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-50 text-blue-700 font-bold border-r-4 border-blue-700 hover:translate-x-1 transition-transform duration-200" href="Student_Dashboard.php">
<span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
<span>Dashboard</span>
</a>
<a class="cursor-pointer flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-100 hover:translate-x-1 transition-transform duration-200" href="Student_Application.php">
<span class="material-symbols-outlined" data-icon="assignment">assignment</span>
<span>My Applications</span>
</a>
<a class="cursor-pointer flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-100 hover:translate-x-1 transition-transform duration-200" href="Student_ClubDashboard.php">
<span class="material-symbols-outlined" data-icon="groups">groups</span>
<span>Clubs</span>
</a>
<a class="cursor-pointer flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-100 hover:translate-x-1 transition-transform duration-200" href="Student_Announcement.php">
<span class="material-symbols-outlined" data-icon="campaign">campaign</span>
<span>Announcements</span>
</a>
</nav>
<div class="px-3 mt-auto">
<a class="cursor-pointer flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 hover:bg-slate-100 hover:translate-x-1 transition-transform duration-200" href="../logOut.php">
<span class="material-symbols-outlined" data-icon="logout">logout</span>
<span>Logout</span>
</a>
</div>
</aside>
<!-- TopNavBar Anchor -->
<header class="flex justify-between items-center h-16 px-6 w-full sticky top-0 z-40 bg-white border-b border-slate-200 shadow-sm ml-64" style="width: calc(100% - 16rem);">
<div class="flex items-center flex-1 max-w-xl">
<div class="relative w-full">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
<input class="w-full bg-slate-50 border-slate-200 rounded-lg py-2 pl-10 pr-4 text-sm focus:ring-primary focus:border-primary" placeholder="Search clubs, events, or applications..." type="text"/>
</div>
</div>
<div class="flex items-center gap-4">
<button class="p-2 text-slate-600 hover:bg-slate-50 transition-colors rounded-full relative">
<span class="material-symbols-outlined" data-icon="notifications">notifications</span>
<span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full border-2 border-white"></span>
</button>
<button class="p-2 text-slate-600 hover:bg-slate-50 transition-colors rounded-full">
<span class="material-symbols-outlined" data-icon="help_outline">help_outline</span>
</button>
<div class="h-8 w-px bg-slate-200 mx-2"></div>
<div class="flex items-center gap-3 cursor-pointer hover:opacity-80 transition-opacity">
<div class="text-right">
<!-- 🔥 DYNAMIC FULL NAME HERE 🔥 -->
<p class="text-sm font-bold text-slate-900"><?= htmlspecialchars($fullName) ?></p>
<p class="text-xs text-slate-500">Student Portal</p>
</div>
<div class="w-10 h-10 rounded-full border-2 border-primary-fixed bg-blue-100 flex items-center justify-center text-blue-800 font-bold">
    <!-- Initial instead of generic image -->
    <?= strtoupper(substr($firstName, 0, 1)) ?>
</div>
</div>
</div>
</header>
<main class="ml-64 p-8 bg-surface min-h-[calc(100vh-4rem)]">
<!-- Hero Banner -->
<div class="relative overflow-hidden rounded-xl bg-primary-container p-10 mb-8 flex flex-col md:flex-row items-center justify-between">
<div class="relative z-10 text-white max-w-2xl">
<!-- 🔥 DYNAMIC FIRST NAME HERE 🔥 -->
<h2 class="font-h1 text-h1 mb-2">Welcome Back, <?= htmlspecialchars($firstName) ?>!</h2>
<p class="font-body-lg text-body-lg text-blue-100 opacity-90 mb-6">Your recruitment journey is looking promising. You have <span class="text-brand-gold font-bold">2 active applications</span> and 4 new club recommendations waiting for you.</p>
<div class="flex gap-4">
<button class="bg-brand-gold text-primary font-button text-button px-6 py-3 rounded-lg hover:shadow-lg transition-shadow">View Status Updates</button>
<button class="bg-primary border border-blue-400/30 text-white font-button text-button px-6 py-3 rounded-lg hover:bg-primary-container transition-colors">Explore Clubs</button>
</div>
</div>
<div class="absolute right-0 top-0 h-full w-1/3 opacity-20 hidden md:block">
<img alt="Abstract university architecture" class="object-cover h-full w-full" data-alt="modern university building glass facade reflecting a clear blue sky, architectural and academic aesthetic" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAaqoFefzJV7PwSz8xu85m6xIobHj692KtuNnZbJ8lgjoRi5nrjnqJfO1czgoiuULOR_0r-1GeadJD3VyWVNzOYzRMd8X7lRFlf5l98ZFDzQ2_QhiL0s-65PHaIegEvySU1qphPLNbpMsvev-c0RmmSIpF8TqSx085i-y8MqoeHTjdD-Moe6q-yzPxMhPBUPt_pWyOWu-nISBqiunLLennxIs6oFBtRaPED_cusTvrM7UTAx_cT-j30QDAVDEnMGYtriXZ7ptZUAtme"/>
</div>
</div>
<!-- Bento Grid Layout -->
<div class="grid grid-cols-12 gap-gutter">
<!-- Application Progress Card -->
<div class="col-span-12 lg:col-span-8 level-1-card level-2-hover transition-all p-6 border-t-4 border-secondary">
<div class="flex items-center justify-between mb-6">
<h3 class="font-h3 text-h3 flex items-center gap-2">
<span class="material-symbols-outlined text-secondary" data-icon="pending_actions">pending_actions</span>
                        Application Progress
                    </h3>
<a class="text-secondary text-sm font-semibold hover:underline" href="#">View All</a>
</div>
<div class="space-y-4">
<!-- Application Item 1 -->
<div class="p-4 bg-surface-container-low rounded-xl flex items-center justify-between group">
<div class="flex items-center gap-4">
<div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center border border-slate-200 shadow-sm">
<span class="material-symbols-outlined text-primary" data-icon="robot_2">robot_2</span>
</div>
<div>
<h4 class="font-bold text-primary">Robotics &amp; AI Club</h4>
<p class="text-sm text-slate-500">Technical Team Lead Role</p>
</div>
</div>
<div class="text-right">
<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-brand-gold text-primary mb-1">
<span class="material-symbols-outlined text-sm mr-1" data-icon="event">event</span>
                                Interview Scheduled
                            </span>
<p class="text-xs text-slate-400">Oct 24, 2025 • 2:00 PM</p>
</div>
</div>
<!-- Application Item 2 -->
<div class="p-4 bg-surface-container-low rounded-xl flex items-center justify-between group">
<div class="flex items-center gap-4">
<div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center border border-slate-200 shadow-sm">
<span class="material-symbols-outlined text-primary" data-icon="movie">movie</span>
</div>
<div>
<h4 class="font-bold text-primary">Independent Film Society</h4>
<p class="text-sm text-slate-500">General Member Application</p>
</div>
</div>
<div class="text-right">
<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-surface-variant text-secondary mb-1">
<span class="material-symbols-outlined text-sm mr-1" data-icon="hourglass_empty">hourglass_empty</span>
                                Pending Review
                            </span>
<p class="text-xs text-slate-400">Submitted Oct 18, 2025</p>
</div>
</div>
</div>
</div>
<!-- Recent Activity Card -->
<div class="col-span-12 lg:col-span-4 level-1-card level-2-hover transition-all p-6">
<h3 class="font-h3 text-h3 mb-6 flex items-center gap-2">
<span class="material-symbols-outlined text-primary" data-icon="history">history</span>
                    Recent Activity
                </h3>
<div class="space-y-6 relative before:absolute before:left-3 before:top-2 before:bottom-2 before:w-px before:bg-slate-200">
<div class="relative pl-10">
<div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center z-10">
<span class="material-symbols-outlined text-xs text-blue-700" data-icon="check_circle">check_circle</span>
</div>
<p class="text-sm font-medium text-slate-900">Application submitted to Debate Union</p>
<p class="text-xs text-slate-400">2 hours ago</p>
</div>
<div class="relative pl-10">
<div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center z-10">
<span class="material-symbols-outlined text-xs text-amber-700" data-icon="notifications">notifications</span>
</div>
<p class="text-sm font-medium text-slate-900">Interview invite from Robotics Club</p>
<p class="text-xs text-slate-400">Yesterday, 4:15 PM</p>
</div>
<div class="relative pl-10">
<div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center z-10">
<span class="material-symbols-outlined text-xs text-slate-600" data-icon="bookmark">bookmark</span>
</div>
<p class="text-sm font-medium text-slate-900">Bookmarked 'Global Business Networking'</p>
<p class="text-xs text-slate-400">Oct 20, 2025</p>
</div>
</div>
</div>
<!-- Upcoming Deadlines -->
<div class="col-span-12 lg:col-span-5 level-1-card level-2-hover transition-all p-6">
<h3 class="font-h3 text-h3 mb-6 flex items-center gap-2">
<span class="material-symbols-outlined text-error" data-icon="alarm">alarm</span>
                    Upcoming Deadlines
                </h3>
<ul class="space-y-4">
<li class="flex items-start gap-4 p-3 hover:bg-surface-container-low rounded-lg transition-colors border-l-4 border-error">
<div class="bg-error-container text-error font-bold rounded-lg p-2 text-center min-w-[50px]">
<span class="block text-xs uppercase">Oct</span>
<span class="block text-lg">25</span>
</div>
<div>
<p class="font-bold text-primary">Equity Investment Group</p>
<p class="text-sm text-slate-500">Final interview slot signup deadline</p>
</div>
</li>
<li class="flex items-start gap-4 p-3 hover:bg-surface-container-low rounded-lg transition-colors border-l-4 border-primary">
<div class="bg-primary-container text-white font-bold rounded-lg p-2 text-center min-w-[50px]">
<span class="block text-xs uppercase">Oct</span>
<span class="block text-lg">28</span>
</div>
<div>
<p class="font-bold text-primary">Pre-Law Society</p>
<p class="text-sm text-slate-500">Member registration closes</p>
</div>
</li>
</ul>
</div>
<!-- Recommended for You Section -->
<div class="col-span-12 lg:col-span-7 level-1-card level-2-hover transition-all p-6">
<div class="flex items-center justify-between mb-6">
<h3 class="font-h3 text-h3 flex items-center gap-2">
<span class="material-symbols-outlined text-brand-gold" data-icon="stars" style="font-variation-settings: 'FILL' 1;">stars</span>
                        Recommended for You
                    </h3>
<div class="flex gap-1">
<span class="px-2 py-1 bg-blue-50 text-secondary text-[10px] font-bold rounded uppercase">Based on interests: AI, Cinema, Law</span>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<!-- Recommendation Card 1 -->
<div class="border border-slate-100 rounded-xl overflow-hidden hover:border-secondary/30 transition-colors">
<div class="h-24 bg-slate-200 relative">
<img alt="Networking event" class="w-full h-full object-cover" data-alt="students in professional attire networking in a modern conference hall, warm ambient lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDvE9Qu-cJg8u3rx0hBiyfzWk9y4aDSXXWoDUvRc5F0G9mtpQtEwsZkkzKCnI1ZGo49nWfQ-mch7yjwcfyjwc_ENy_VZ29pv-QT4-mQkBKP6Umy2hXWCFdzgVZxMap8OzOkyt5bqGFYK3uH9lXT0Gd7qF-_RhqT5a8QAF4xVH29pRoPKfrovrtcR7vtIp1hbkF7FoTfsXakRwRCZWyI-i2idzCbd6vG2IOCKRro59mraIBEK6lDaB1TzKHS_1V7Z9U7h2_QuAe-iXfz"/>
<div class="absolute inset-0 bg-gradient-to-t from-primary/80 to-transparent"></div>
<span class="absolute bottom-2 left-3 text-white font-bold text-sm">Fintech Collective</span>
</div>
<div class="p-3">
<p class="text-xs text-slate-500 mb-3 line-clamp-2">Exploration of disruptive financial technologies and algorithmic trading.</p>
<div class="flex flex-wrap gap-2">
<span class="text-[10px] font-bold px-2 py-0.5 bg-blue-50 text-secondary rounded">STEM</span>
<span class="text-[10px] font-bold px-2 py-0.5 bg-blue-50 text-secondary rounded">Finance</span>
</div>
</div>
</div>
<!-- Recommendation Card 2 -->
<div class="border border-slate-100 rounded-xl overflow-hidden hover:border-secondary/30 transition-colors">
<div class="h-24 bg-slate-200 relative">
<img alt="Film set" class="w-full h-full object-cover" data-alt="professional movie camera on set with blurred lights in background, cinematic production style" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA_dmGtvMeIsNDiwYXRV5TYVK_HRBJCeaEH-ucteWl3FebpOqQvFaJOerlu3_uOpMiD6FEVctOe1WK3FZa5D0EYIS7tDldSNoawqi0dsySOWDa92-McwQk61xcmX7WAlg6TjrkkpOSMD_-h5Ip0eRbNF9IvrSf4GBQJm4UNkyJB6JHqCWYDZ3FRxW-re0ikgtlHsWnbs8q157EKlhzO_TazlFlNppU5O73cb4QIqALWcJlkCcTOBEKOtDihlLsK2VNI12mmYxk1vshU"/>
<div class="absolute inset-0 bg-gradient-to-t from-primary/80 to-transparent"></div>
<span class="absolute bottom-2 left-3 text-white font-bold text-sm">Media Arts Guild</span>
</div>
<div class="p-3">
<p class="text-xs text-slate-500 mb-3 line-clamp-2">Creative community for digital storytelling and post-production workshops.</p>
<div class="flex flex-wrap gap-2">
<span class="text-[10px] font-bold px-2 py-0.5 bg-blue-50 text-secondary rounded">Arts</span>
<span class="text-[10px] font-bold px-2 py-0.5 bg-blue-50 text-secondary rounded">Design</span>
</div>
</div>
</div>
</div>
</div>
</div>
</main>
</body></html>