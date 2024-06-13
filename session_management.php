<?php
session_start(); // Start or resume an existing session

// Function to check if user is logged in
function isAuthenticated()
{
    return isset($_SESSION['user_id']); // Adjust depending on how you've stored this in session
}