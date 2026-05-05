<?php
session_start();
include 'db.php'; // Make sure this path is correct!

// 1. SECURITY CHECK: Kick out non-admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../logIn.php");
    exit();
}

// 2. PROFILE DATA
$fullName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
$nameParts = explode(' ', trim($fullName));
$firstName = $nameParts[0];

// 3. DATABASE QUERY: Fetch all users, newest first
$query = "SELECT id, name, email, role FROM users ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Manage Users | Admin Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9ff; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="text-slate-800">

<!-- SideNavBar -->
<aside class="flex flex-col h-full w-64 fixed left-0 top-0 z-40 bg-slate-50 border-r border-slate-200 transition-transform">
    <div class="px-6 py-8 flex flex-col gap-1">
        <h1 class="text-2xl font-black text-blue-900 leading-tight">ClubRecruit</h1>
        <p class="text-sm font-medium text-slate-500 tracking-wide uppercase">Admin Portal</p>
    </div>
    <nav class="flex-1 px-4 space-y-2 mt-4">
        <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-slate-600 hover:bg-slate-100 group" href="Admin_Dashboard.php">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-medium">Dashboard</span>
        </a>
        <!-- Active Tab: Users -->
        <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all bg-blue-50 text-blue-700 font-semibold border-r-4 border-blue-700" href="Admin_Users.php">
            <span class="material-symbols-outlined">person</span>
            <span class="font-medium">Users</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-slate-600 hover:bg-slate-100 group" href="#">
            <span class="material-symbols-outlined">groups</span>
            <span class="font-medium">Clubs</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-slate-600 hover:bg-slate-100 group" href="Admin_Settings.php">
            <span class="material-symbols-outlined">settings</span>
            <span class="font-medium">Settings</span>
        </a>
    </nav>
    <div class="p-4 mt-auto border-t border-slate-200">
        <a href="../logOut.php" class="flex items-center gap-3 px-4 py-3 w-full rounded-lg text-slate-600 hover:bg-red-50 hover:text-red-700 transition-all">
            <span class="material-symbols-outlined">logout</span>
            <span class="font-semibold">Logout</span>
        </a>
    </div>
</aside>

<!-- Main Canvas -->
<main class="ml-64 min-h-screen">
    <!-- TopAppBar -->
    <header class="flex justify-between items-center w-full px-6 py-4 bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="flex items-center gap-4 flex-1">
            <div class="relative w-full max-w-md">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input class="w-full bg-slate-50 border border-slate-200 rounded-full py-2 pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600" placeholder="Search users by name or email..." type="text"/>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <!-- 🔥 DYNAMIC PROFILE SECTION 🔥 -->
            <div class="flex items-center gap-3 border-l border-slate-200 pl-6 cursor-pointer hover:opacity-80 transition-opacity">
                <div class="text-right">
                    <p class="text-sm font-bold text-slate-900"><?= htmlspecialchars($fullName) ?></p>
                    <p class="text-xs text-slate-500">Administrator</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 border-2 border-white shadow-sm flex items-center justify-center text-blue-700 font-bold text-lg">
                    <?= strtoupper(substr($firstName, 0, 1)) ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Content Area -->
    <div class="p-8 max-w-7xl mx-auto space-y-6">
        
        <div class="flex justify-between items-end">
            <div>
                <h2 class="text-3xl font-bold text-[#002045]">User Management</h2>
                <p class="text-slate-500 mt-1">View and manage all registered students and administrators.</p>
            </div>
            <button class="flex items-center gap-2 px-5 py-2.5 bg-blue-700 text-white rounded-lg font-semibold shadow-md hover:bg-blue-800 transition-all">
                <span class="material-symbols-outlined text-sm">person_add</span>
                Add New User
            </button>
        </div>

        <!-- The Dynamic User Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs text-slate-500 uppercase tracking-wider">
                        <th class="p-4 font-bold">User ID</th>
                        <th class="p-4 font-bold">Full Name</th>
                        <th class="p-4 font-bold">Email Address</th>
                        <th class="p-4 font-bold">System Role</th>
                        <th class="p-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    
                    <?php if ($result && $result->num_rows > 0): ?>
                        <!-- Loop through every single user found in the database -->
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4 text-sm font-medium text-slate-500">
                                    #<?= htmlspecialchars($row['id']) ?>
                                </td>
                                <td class="p-4 font-semibold text-slate-900">
                                    <?= htmlspecialchars($row['name']) ?>
                                </td>
                                <td class="p-4 text-sm text-slate-600">
                                    <?= htmlspecialchars($row['email']) ?>
                                </td>
                                <td class="p-4">
                                    <?php if($row['role'] === 'admin'): ?>
                                        <span class="px-2 py-1 bg-purple-100 text-purple-700 text-[10px] font-bold rounded uppercase tracking-wider border border-purple-200">Admin</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-blue-50 text-blue-700 text-[10px] font-bold rounded uppercase tracking-wider border border-blue-200">Student</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 text-right">
                                    <button class="text-blue-600 hover:text-blue-800 font-semibold text-sm mr-4 transition-colors">Edit</button>
                                    <button class="text-red-500 hover:text-red-700 font-semibold text-sm transition-colors">Suspend</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <!-- If the database is completely empty -->
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500">
                                <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">person_off</span>
                                No users found in the database.
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>

    </div>
</main>
</body>
</html>