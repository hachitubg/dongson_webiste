<?php
echo "<h1>Testing Database Connection on VPS</h1>";

echo "<h2>Environment Detection:</h2>";
echo "DOCKER_ENV: " . (getenv('DOCKER_ENV') ? 'YES' : 'NO') . "<br>";
echo "/.dockerenv exists: " . (file_exists('/.dockerenv') ? 'YES' : 'NO') . "<br>";
echo "C:/xampp exists: " . (file_exists('C:/xampp') ? 'YES' : 'NO') . "<br>";
echo "/xampp exists: " . (file_exists('/xampp') ? 'YES' : 'NO') . "<br>";

$isDocker = getenv('DOCKER_ENV') !== false || file_exists('/.dockerenv');
$isProduction = !file_exists('C:/xampp') && !file_exists('/xampp') && !$isDocker;

echo "<br><strong>Detected Environment: ";
if ($isProduction) {
    echo "PRODUCTION</strong><br>";
} elseif ($isDocker) {
    echo "DOCKER</strong><br>";
} else {
    echo "LOCAL (XAMPP)</strong><br>";
}

echo "<hr>";

// Load config
require_once __DIR__ . '/includes/config.php';

echo "<h2>Database Configuration Loaded:</h2>";
echo "DB_HOST: " . DB_HOST . "<br>";
echo "DB_NAME: " . DB_NAME . "<br>";
echo "DB_USER: " . DB_USER . "<br>";
echo "DB_PASS: " . (DB_PASS ? str_repeat('*', strlen(DB_PASS)) : 'EMPTY') . "<br>";
echo "BASE_URL: " . BASE_URL . "<br>";

echo "<hr>";

echo "<h2>Testing Connection:</h2>";

// Test 1: Connect with loaded config
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "<p style='color: green;'>✅ <strong>SUCCESS!</strong> Connected to database successfully!</p>";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
    $result = $stmt->fetch();
    echo "<p>Categories in database: " . $result['total'] . "</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ <strong>ERROR:</strong> " . $e->getMessage() . "</p>";
    
    // Additional debugging
    echo "<h3>Debugging Information:</h3>";
    
    // Test if user can login to MySQL at all
    try {
        $testPdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS);
        echo "<p style='color: orange;'>⚠️ User can login to MySQL, but cannot access database '" . DB_NAME . "'</p>";
    } catch (PDOException $e2) {
        echo "<p style='color: red;'>❌ User cannot login to MySQL: " . $e2->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<h2>MySQL User Check (run on terminal):</h2>";
echo "<pre>sudo mysql -e \"SELECT user, host, plugin FROM mysql.user WHERE user='" . DB_USER . "';\"</pre>";

echo "<h2>Grant Privileges (if needed):</h2>";
echo "<pre>";
echo "sudo mysql\n";
echo "CREATE USER '" . DB_USER . "'@'localhost' IDENTIFIED BY '" . DB_PASS . "';\n";
echo "GRANT ALL PRIVILEGES ON " . DB_NAME . ".* TO '" . DB_USER . "'@'localhost';\n";
echo "FLUSH PRIVILEGES;\n";
echo "EXIT;\n";
echo "</pre>";
?>
