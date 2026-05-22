<?php
// ============================================================
//  TEMPORARY TEST FILE — DELETE AFTER TESTING
//  library_system/test_register.php
// ============================================================

$conn = new mysqli('localhost', 'root', '', 'library_system');

echo "<style>body{font-family:sans-serif;padding:30px;}
      .pass{color:green;font-weight:bold;}
      .fail{color:red;font-weight:bold;}
      code{background:#f4f4f4;padding:4px 8px;
           border-radius:4px;display:block;
           margin:8px 0;font-size:0.9rem;}
      </style>";

echo "<h2>🔍 Register Debug Test</h2>";

// ── Test 1: Connection ────────────────────────────────────────
if ($conn->connect_error) {
    echo "<p class='fail'>❌ Connection failed: "
         . $conn->connect_error . "</p>";
    exit;
}
echo "<p class='pass'>✅ Database connected</p>";

// ── Test 2: Show users table columns ─────────────────────────
echo "<h3>Users Table Columns:</h3>";
$cols = $conn->query("SHOW COLUMNS FROM users");
if ($cols) {
    echo "<table border='1' cellpadding='8' 
               style='border-collapse:collapse;'>
          <tr style='background:#f0f0f0;'>
              <th>Column</th>
              <th>Type</th>
              <th>Null</th>
              <th>Default</th>
          </tr>";
    while ($col = $cols->fetch_assoc()) {
        echo "<tr>
                <td><strong>{$col['Field']}</strong></td>
                <td>{$col['Type']}</td>
                <td>{$col['Null']}</td>
                <td>{$col['Default']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p class='fail'>❌ Cannot read users table</p>";
}

// ── Test 3: Try the INSERT prepare ───────────────────────────
echo "<h3>Testing INSERT Statement:</h3>";

$sql = "INSERT INTO users 
        (FirstName, LastName, Email, Password, Gender,
         Address, PostalCode, PostOfficeNumber, Contact)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo "<p class='fail'>❌ Prepare FAILED</p>";
    echo "<p>MySQL Error: <strong>" 
         . $conn->error . "</strong></p>";
    echo "<hr><h3>🔧 Fix:</h3>";

    // Check if Address column is missing
    $check = $conn->query(
        "SHOW COLUMNS FROM users LIKE 'Address'"
    );
    if ($check->num_rows === 0) {
        echo "<p class='fail'>
              ❌ Address column is MISSING 
              from users table!
              </p>";
        echo "<p>Run this SQL in phpMyAdmin to fix it:</p>";
        echo "<code>
              ALTER TABLE users 
              ADD COLUMN Address VARCHAR(255) 
              NOT NULL DEFAULT '' 
              AFTER Gender;
              </code>";
    }
} else {
    echo "<p class='pass'>
          ✅ INSERT prepare succeeded! 
          Register should work.
          </p>";
    $stmt->close();
}

// ── Test 4: Try a real insert ─────────────────────────────────
echo "<h3>Testing Full Registration:</h3>";

$first   = 'Test';
$last    = 'User';
$email   = 'debugtest@example.com';
$pass    = password_hash('Password@1', PASSWORD_BCRYPT);
$gender  = 'Male';
$address = 'Madang Town';
$postal  = '111';
$po      = 'PO Box 1';
$contact = '70000099';

$stmt2 = $conn->prepare($sql);
if ($stmt2) {
    $stmt2->bind_param(
        'sssssssss',
        $first, $last, $email, $pass,
        $gender, $address, $postal, $po, $contact
    );
    if ($stmt2->execute()) {
        echo "<p class='pass'>
              ✅ Test user inserted successfully!
              </p>";
        // Clean up test record
        $conn->query(
            "DELETE FROM users 
             WHERE Email = 'debugtest@example.com'"
        );
        echo "<p class='pass'>
              ✅ Test record cleaned up
              </p>";
    } else {
        echo "<p class='fail'>
              ❌ Insert failed: " 
              . $stmt2->error . "
              </p>";
    }
    $stmt2->close();
}

$conn->close();
echo "<hr>
      <p><strong>
      ⚠️ Delete test_register.php after testing!
      </strong></p>";
?>