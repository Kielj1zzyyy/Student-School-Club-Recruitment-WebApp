<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $club_id = $_POST['club_id'];

    // 1. Check if the user already has a pending or approved application
    $check = $conn->prepare("SELECT id FROM club_application WHERE user_id = ? AND club_id = ?");
    $check->bind_param("ii", $user_id, $club_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('You have already applied for this club.'); window.location.href='Student_ClubDashboard.php';</script>";
        exit();
    }

    // 2. Insert into the database
    $stmt = $conn->prepare("INSERT INTO club_application (user_id, club_id, status) VALUES (?, ?, 'Pending')");
    $stmt->bind_param("ii", $user_id, $club_id);

    if ($stmt->execute()) {
        header("Location: Student_Application.php?success=1");
    } else {
        echo "<script>alert('Error processing application.'); window.location.href='Student_ClubDashboard.php';</script>";
    }
} else {
    header("Location: Student_ClubDashboard.php");
}
?>