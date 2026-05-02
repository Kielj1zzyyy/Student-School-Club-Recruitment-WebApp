<?php
$conn = new mysqli("localhost", "root", "", "it26b_StudentClubRecruitment");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>