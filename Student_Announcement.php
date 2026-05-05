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

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-container-lowest": "#ffffff",
                        "on-surface-variant": "#43474e",
                        "on-secondary-container": "#00477f",
                        "primary-fixed": "#d6e3ff",
                        "on-error": "#ffffff",
                        "background": "#f8f9ff",
                        "error": "#ba1a1a",
                        "outline-variant": "#c4c6cf",
                        "surface": "#f8f9ff",
                        "tertiary-container": "#4f2e00",
                        "tertiary": "#321b00",
                        "surface-tint": "#455f88",
                        "inverse-primary": "#adc7f7",
                        "on-tertiary-fixed": "#2b1700",
                        "on-primary-fixed-variant": "#2d476f",
                        "on-primary-container": "#86a0cd",
                        "surface-bright": "#f8f9ff",
                        "on-secondary-fixed-variant": "#004881",
                        "on-primary-fixed": "#001b3c",
                        "secondary-container": "#7db6ff",
                        "on-tertiary-container": "#c6955e",
                        "on-secondary": "#ffffff",
                        "surface-container-low": "#eff4ff",
                        "on-error-container": "#93000a",
                        "tertiary-fixed-dim": "#f2bc82",
                        "surface-dim": "#ccdbf4",
                        "on-secondary-fixed": "#001c38",
                        "on-tertiary-fixed-variant": "#633f0f",
                        "surface-container-highest": "#d4e4fc",
                        "inverse-on-surface": "#eaf1ff",
                        "primary": "#002045",
                        "tertiary-fixed": "#ffddba",
                        "secondary-fixed": "#d3e4ff",
                        "outline": "#74777f",
                        "on-tertiary": "#ffffff",
                        "on-background": "#0d1c2e",
                        "secondary": "#1960a3",
                        "inverse-surface": "#223144",
                        "on-surface": "#0d1c2e",
                        "surface-container-high": "#dce9ff",
                        "secondary-fixed-dim": "#a2c9ff",
                        "primary-fixed-dim": "#adc7f7",
                        "primary-container": "#1a365d",
                        "error-container": "#ffdad6",
                        "surface-variant": "#d4e4fc",
                        "on-primary": "#ffffff",
                        "surface-container": "#e5eeff"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "container-max": "1280px",
                        "md": "24px",
                        "gutter": "24px",
                        "sm": "12px",
                        "base": "8px",
                        "lg": "40px",
                        "xl": "64px",
                        "xs": "4px"
                    },
                    "fontFamily": {
                        "h3": ["Inter"],
                        "h2": ["Inter"],
                        "body-lg": ["Inter"],
                        "label-caps": ["Inter"],
                        "h1": ["Inter"],
                        "body-sm": ["Inter"],
                        "body-md": ["Inter"],
                        "button": ["Inter"]
                    },
                    "fontSize": {
                        "h3": ["24px", {"lineHeight": "1.4", "letterSpacing": "0em", "fontWeight": "600"}],
                        "h2": ["30px", {"lineHeight": "1.3", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "body-lg": ["18px", {"lineHeight": "1.6", "letterSpacing": "0em", "fontWeight": "400"}],
                        "label-caps": ["12px", {"lineHeight": "1", "letterSpacing": "0.05em", "fontWeight": "700"}],
                        "h1": ["40px", {"lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "body-sm": ["14px", {"lineHeight": "1.5", "letterSpacing": "0em", "fontWeight": "400"}],
                        "body-md": ["16px", {"lineHeight": "1.5", "letterSpacing": "0em", "fontWeight": "400"}],
                        "button": ["16px", {"lineHeight": "1", "letterSpacing": "0.01em", "fontWeight": "600"}]
                    }
                },
            },
        }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E2E8F0;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-background text-on-surface font-body-md antialiased min-h-screen">
<!-- SideNavBar (Authority Source: Shared Components JSON) -->
<aside class="h-screen w-64 fixed left-0 top-0 border-r bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-none font-['Inter'] antialiased text-sm font-medium tracking-tight z-50">
<div class="flex flex-col h-full py-6 px-4">
<!-- Header Section -->
<div class="flex items-center gap-3 px-2 mb-10">
<div class="w-10 h-10 rounded-lg bg-primary-container flex items-center justify-center overflow-hidden">
<img alt="University Seal" class="w-8 h-8" data-alt="Official university crest with golden lion and sapphire shield, scholarly and prestigious aesthetic" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBZOinfILi_yNmNR_hnX4zhfpNgH-o9Od_Gl_HJSNcFdDM_i5g0bCwb0oJIcFLkRLNcMNXIVC5r6rePqKmWlf0CKxpUUu-Yu7Ut2ZhG8_SgD3PAdPvH_wVOjCNAUtt9vf6TtTZQPU161wGuIA0PxAohTYfNQUgfXduLhWr_woqgocvk9ME2RomcHqCgEl-Ia1zxmCXN7osclljb8JS4ZCtNCyE9xABgfkEip8PXDcItKAPP0ujr9ovx6g1pOpJHORBGtsnNVqNWsPqR"/>
</div>
<div>
<h1 class="text-xl font-black tracking-tighter text-blue-900 dark:text-blue-50">Student Portal</h1>
<p class="text-xs text-slate-500 font-semibold uppercase tracking-widest">Academic Excellence</p>
</div>
</div>
<!-- Navigation Links -->
<nav class="flex-1 space-y-1">
<a class="flex items-center gap-3 px-3 py-2.5 text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all duration-200 active:scale-95 transform" href="Student_Dashboard.php">
<span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
<span>Dashboard</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all duration-200 active:scale-95 transform" href="Student_Application.php">
<span class="material-symbols-outlined" data-icon="assignment">assignment</span>
<span>My Applications</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all duration-200 active:scale-95 transform" href="Student_ClubDashboard.php">
<span class="material-symbols-outlined" data-icon="groups">groups</span>
<span>Clubs</span>
</a>
<!-- Active Navigation: Announcements -->
<a class="flex items-center gap-3 px-3 py-2.5 text-blue-700 dark:text-blue-400 font-bold border-r-4 border-yellow-500 bg-blue-50/50 dark:bg-blue-900/20 transition-all duration-200 active:scale-95 transform" href="Student_Announcement.php">
<span class="material-symbols-outlined" data-icon="campaign">campaign</span>
<span>Announcements</span>
</a>
</nav>
<!-- Footer Section -->
<div class="mt-auto border-t border-slate-100 dark:border-slate-800 pt-4">
<a class="w-full flex items-center gap-3 px-3 py-2.5 text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all duration-200 active:scale-95 transform" href="../logOut.php">
<span class="material-symbols-outlined" data-icon="exit_to_app">exit_to_app</span>
<span>Logout</span>
</a>
</div>
</div>
</aside>
<!-- TopAppBar (Authority Source: Shared Components JSON) -->
<header class="fixed top-0 right-0 w-[calc(100%-16rem)] h-16 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 shadow-sm flex justify-between items-center px-8 font-['Inter'] text-sm font-semibold">
<div class="flex items-center gap-4">
<span class="text-lg font-bold text-blue-900 dark:text-white">Academic Dashboard</span>
<span class="h-4 w-px bg-slate-300 mx-2"></span>
<span class="text-slate-500 font-medium">Announcements</span>
</div>
<div class="flex items-center gap-6">
<!-- Search Bar -->
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
<input class="pl-10 pr-4 py-1.5 bg-slate-100 border-none rounded-full w-64 text-sm focus:ring-2 focus:ring-secondary transition-all" placeholder="Search announcements..." type="text"/>
</div>
<div class="flex items-center gap-4">
<button class="text-slate-500 hover:text-blue-600 transition-colors duration-200 relative">
<span class="material-symbols-outlined">notifications</span>
<span class="absolute top-0 right-0 w-2 h-2 bg-yellow-500 rounded-full border-2 border-white"></span>
</button>
<button class="text-slate-500 hover:text-blue-600 transition-colors duration-200">
<span class="material-symbols-outlined">help_outline</span>
</button>
<div class="h-8 w-8 rounded-full bg-slate-200 overflow-hidden border border-slate-300">
<img alt="User Profile" class="w-full h-full object-cover" data-alt="Professional headshot of a college student, clean background, friendly and academic appearance" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDuIrTBYWnd-KgqMj_ybm29lyujl0_nj3KN58XWuFmERIE9oSnhib_1OXEOrpq_l_GtmW72uOnt8txZifN14fc7etdzZc9vZLRRSA1LTkybCBEmzeGZhtavtT4jX5AGjK21BDENKqnWrLiBDIcPcrveMrT_f4GVXCFKTdi-EiJ-lMZdstvSIQGbXZR_mAzwmp01i2ImJ_p5jWNXzxmKHeY3-OEH-4kfFIyXpJhJzQGrgmSd2py0_Pd9ZE7tkrCUXNVpmXXU9kJUCWx8"/>
</div>
</div>
</div>
</header>
<!-- Main Content Canvas -->
<main class="ml-64 pt-16 min-h-screen">
<div class="max-w-7xl mx-auto p-md lg:p-xl">
<!-- Hero Announcement Banner (High-end UI) -->
<div class="relative overflow-hidden rounded-xl bg-primary text-white mb-lg shadow-xl">
<div class="absolute inset-0 opacity-20">
<div class="absolute inset-0 bg-gradient-to-r from-primary via-transparent to-transparent z-10"></div>
<img alt="University Library" class="w-full h-full object-cover" data-alt="Modern university library architecture with large glass windows and study spaces at twilight" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDO8xnsXUkFRrTk2OZ5-gLqto5KDB-M5HQxMbXVyWmQgJhYHnUP-VSFekDGiOqBPy0Ej592wZqglVM3sfmIu1_o6xs8oSThJRZkjOf71dpiI276qVOreODaCd_mVJQYSNwzhtKpRaCNsf2CXwcXZ4FSO_gu5kTQr35B_jwzQYRBU4Ff0_f-S5NlbTy6FQ9BXv5EydOHo6CyjJ6cYA5Pgh5s8vENmRtBXcqcqIuXfjR_dlfe6DhqMOoS0o0SztVDwV3Rvz4wtG3VDkjw"/>
</div>
<div class="relative z-20 p-8 md:p-12 flex flex-col items-start gap-4 max-w-2xl">
<span class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs font-bold uppercase tracking-wider border border-yellow-500/30">
<span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">priority_high</span>
                        University Urgent
                    </span>
<h2 class="font-h1 text-h1">2024 Research Grants Now Open for Applications</h2>
<p class="font-body-lg text-body-lg text-slate-300">Undergraduate research opportunities are now accepting proposals for the Summer session. Apply by March 15th.</p>
<button class="mt-4 px-6 py-3 bg-yellow-500 text-primary font-button text-button rounded-lg hover:bg-yellow-400 transition-all shadow-lg hover:-translate-y-0.5">
                        View Requirements
                    </button>
</div>
</div>
<!-- Grid Layout for Announcements -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter items-start">
<!-- Left Content: My Club Updates -->
<div class="lg:col-span-2 space-y-md">
<div class="flex items-center justify-between mb-2">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">groups</span>
<h3 class="font-h3 text-h3 text-primary">My Club Updates</h3>
</div>
<button class="text-secondary font-button text-sm hover:underline">Mark all as read</button>
</div>
<!-- Club Announcement Cards -->
<div class="space-y-4">
<!-- Card 1 -->
<div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border-l-4 border-secondary hover:shadow-md transition-shadow">
<div class="flex justify-between items-start mb-3">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-surface-container flex items-center justify-center">
<span class="material-symbols-outlined text-secondary">precision_manufacturing</span>
</div>
<div>
<h4 class="font-h3 text-body-md text-primary">Robotics Lab</h4>
<p class="text-xs text-on-surface-variant">Posted 2 hours ago</p>
</div>
</div>
<span class="px-2.5 py-0.5 bg-secondary-container text-on-secondary-container text-[10px] font-bold rounded uppercase tracking-tighter">New Recruitment</span>
</div>
<h5 class="font-h3 text-h3 mb-2">Fall Recruitment Now Open</h5>
<p class="text-on-surface-variant font-body-md mb-4">We are looking for programmers, hardware engineers, and UI designers to join our competition team for the upcoming National Robot-Sumo competition. Introductory meeting this Friday at 5 PM in Lab 402.</p>
<div class="flex items-center gap-4">
<button class="px-4 py-2 bg-secondary text-white font-button text-sm rounded-lg hover:bg-primary transition-colors">RSVP to Meeting</button>
<button class="px-4 py-2 bg-transparent border border-outline text-on-surface font-button text-sm rounded-lg hover:bg-surface-container transition-colors">Details</button>
</div>
</div>
<!-- Card 2 -->
<div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border-l-4 border-secondary hover:shadow-md transition-shadow">
<div class="flex justify-between items-start mb-3">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-surface-container flex items-center justify-center">
<span class="material-symbols-outlined text-secondary">edit_note</span>
</div>
<div>
<h4 class="font-h3 text-body-md text-primary">Writers Circle</h4>
<p class="text-xs text-on-surface-variant">Posted Yesterday</p>
</div>
</div>
<span class="px-2.5 py-0.5 bg-tertiary-fixed text-on-tertiary-fixed text-[10px] font-bold rounded uppercase tracking-tighter">Event</span>
</div>
<h5 class="font-h3 text-h3 mb-2">Poetry Slam Registration</h5>
<p class="text-on-surface-variant font-body-md mb-4">The annual Spring Poetry Slam is now open for performer registration. Slots are limited to 15 performers. Theme this year: "Unseen Connections". Please submit your draft by Monday.</p>
<div class="flex items-center gap-4">
<button class="px-4 py-2 bg-secondary text-white font-button text-sm rounded-lg hover:bg-primary transition-colors">Register Now</button>
<button class="px-4 py-2 bg-transparent border border-outline text-on-surface font-button text-sm rounded-lg hover:bg-surface-container transition-colors">Guidelines</button>
</div>
</div>
</div>
</div>
<!-- Right Content: General Campus News -->
<div class="space-y-md">
<div class="flex items-center gap-3 mb-2">
<span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">newspaper</span>
<h3 class="font-h3 text-h3 text-primary">Campus News</h3>
</div>
<div class="bg-surface-container-low rounded-xl p-4 space-y-4">
<!-- News Item 1 -->
<div class="bg-white p-4 rounded-lg shadow-sm border border-slate-100 flex flex-col gap-2">
<div class="flex justify-between items-center">
<span class="text-[11px] font-bold text-on-surface-variant uppercase tracking-widest">Registrar</span>
<span class="text-[10px] text-slate-400">Mar 12</span>
</div>
<h6 class="font-bold text-primary leading-tight hover:text-secondary cursor-pointer transition-colors">Course Registration for Summer Terms Opens Monday</h6>
<p class="text-body-sm text-on-surface-variant">Check your student portal for your specific time slot and clear any holds.</p>
</div>
<!-- News Item 2 -->
<div class="bg-white p-4 rounded-lg shadow-sm border border-slate-100 flex flex-col gap-2">
<div class="flex justify-between items-center">
<span class="text-[11px] font-bold text-on-surface-variant uppercase tracking-widest">Facility Management</span>
<span class="text-[10px] text-slate-400">Mar 11</span>
</div>
<h6 class="font-bold text-primary leading-tight hover:text-secondary cursor-pointer transition-colors">Main Library Maintenance Schedule</h6>
<p class="text-body-sm text-on-surface-variant">The 3rd floor will be closed this weekend for HVAC repairs. Quiet zones moved to 4th floor.</p>
</div>
<!-- News Item 3 -->
<div class="bg-white p-4 rounded-lg shadow-sm border border-slate-100 flex flex-col gap-2">
<div class="flex justify-between items-center">
<span class="text-[11px] font-bold text-on-surface-variant uppercase tracking-widest">Student Life</span>
<span class="text-[10px] text-slate-400">Mar 10</span>
</div>
<h6 class="font-bold text-primary leading-tight hover:text-secondary cursor-pointer transition-colors">Mental Health Awareness Week Begins</h6>
<p class="text-body-sm text-on-surface-variant">Join us for daily yoga, meditation sessions, and free wellness workshops.</p>
</div>
<button class="w-full py-2 bg-surface-container-highest text-primary font-button text-sm rounded-lg hover:bg-secondary-container transition-colors flex items-center justify-center gap-2">
                            View Archive
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</button>
</div>
<!-- Upcoming Events Card (Asymmetric Layout element) -->
<div class="bg-white rounded-xl shadow-lg p-6 relative overflow-hidden">
<div class="absolute top-0 right-0 w-32 h-32 bg-secondary opacity-5 rounded-bl-full -mr-8 -mt-8"></div>
<h4 class="font-h3 text-body-md text-primary mb-4 flex items-center gap-2">
<span class="material-symbols-outlined text-yellow-500">event</span>
                            Deadlines
                        </h4>
<ul class="space-y-4">
<li class="flex gap-4">
<div class="bg-slate-100 rounded text-center min-w-[48px] p-1 h-fit">
<span class="block text-[10px] font-bold uppercase text-on-surface-variant">Mar</span>
<span class="block text-xl font-black text-secondary">15</span>
</div>
<div>
<p class="font-bold text-sm text-primary">Grant Proposal Due</p>
<p class="text-[10px] text-on-surface-variant uppercase">Academic Office</p>
</div>
</li>
<li class="flex gap-4">
<div class="bg-slate-100 rounded text-center min-w-[48px] p-1 h-fit">
<span class="block text-[10px] font-bold uppercase text-on-surface-variant">Mar</span>
<span class="block text-xl font-black text-secondary">20</span>
</div>
<div>
<p class="font-bold text-sm text-primary">Club Budget Submission</p>
<p class="text-[10px] text-on-surface-variant uppercase">Student Union</p>
</div>
</li>
</ul>
</div>
</div>
</div>
</div>
</main>
<!-- Contextual FAB (Authority Source: Mandate - Only on Home/Dash, suppressed here as specified) -->
<!-- Suppressed on Announcements page per UX Goal instructions -->
</body></html>