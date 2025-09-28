<?php
// A simple script to generate a password hash.
// Use this to create a secure password hash for your admin users.

$passwordToHash = 'admin123'; // Change this to the desired password

$hashedPassword = password_hash($passwordToHash, PASSWORD_DEFAULT);

echo "<h2>Password Hash Generator</h2>";
echo "<p><strong>Password:</strong> " . htmlspecialchars($passwordToHash) . "</p>";
echo "<p><strong>Hashed Password:</strong> " . htmlspecialchars($hashedPassword) . "</p>";
echo "<p>Copy the hashed password and insert it into the `password` column of your `admin_users` table.</p>";

?>