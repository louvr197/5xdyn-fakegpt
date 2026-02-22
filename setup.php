<?php
/**
 * One-time setup script for InfinityFree deployment
 * Visit this URL once: https://yourdomainhere.com/setup.php
 * Then DELETE this file immediately after!
 */

// Security: Only allow from localhost or once
if (file_exists(__DIR__ . '/.setup-completed')) {
    die('Setup already completed. Please delete setup.php file.');
}

$output = [];
$errors = [];

try {
    // Change to project root
    chdir(__DIR__);
    
    $output[] = "🚀 Starting CVBuilder Pro setup...\n";
    
    // 1. Check environment
    $output[] = "📋 Environment Checks:";
    $output[] = "- PHP Version: " . phpversion();
    $output[] = "- Composer: " . (shell_exec('composer --version') ?: 'Not found');
    
    // 2. Load environment
    if (!file_exists('.env')) {
        $errors[] = "❌ .env file not found! Create it first.";
    } else {
        $output[] = "✅ .env file found";
    }
    
    // 3. Install/update dependencies
    $output[] = "\n📦 Installing Composer dependencies...";
    $install_output = shell_exec('composer install --optimize-autoloader --no-dev 2>&1');
    $output[] = $install_output;
    
    // 4. Generate APP_KEY if missing
    if (strlen(getenv('APP_KEY')) < 10) {
        $output[] = "\n🔑 Generating APP_KEY...";
        $key_output = shell_exec('php artisan key:generate 2>&1');
        $output[] = $key_output;
    }
    
    // 5. Run migrations
    $output[] = "\n🗄️  Running database migrations...";
    $migrate_output = shell_exec('php artisan migrate --force 2>&1');
    $output[] = $migrate_output;
    
    // 6. Cache configurations
    $output[] = "\n💾 Caching configurations...";
    $cache_output = shell_exec('php artisan config:cache 2>&1');
    $output[] = $cache_output;
    
    // 7. Cache routes
    $output[] = "\n🛣️  Caching routes...";
    $route_output = shell_exec('php artisan route:cache 2>&1');
    $output[] = $route_output;
    
    // 8. Create marker file
    touch('.setup-completed');
    
    $output[] = "\n✅ SETUP COMPLETED SUCCESSFULLY!";
    $output[] = "\n⚠️  IMPORTANT:";
    $output[] = "1. DELETE this setup.php file immediately!";
    $output[] = "2. Visit your site: https://yourdomain.com";
    $output[] = "3. Check Laravel logs if there are issues: storage/logs/laravel.log";

} catch (Exception $e) {
    $errors[] = $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CVBuilder Pro - Setup</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        pre {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            border-left: 4px solid #0066cc;
        }
        .error {
            color: #d32f2f;
            font-weight: bold;
        }
        .success {
            color: #388e3c;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 CVBuilder Pro - Setup</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <h2>❌ Errors occurred:</h2>
                <pre><?php echo implode("\n", $errors); ?></pre>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($output)): ?>
            <div class="success">
                <h2>Setup Output:</h2>
                <pre><?php echo implode("\n", $output); ?></pre>
            </div>
        <?php endif; ?>
        
        <hr>
        <p style="color: #d32f2f; font-weight: bold;">
            ⚠️  IMPORTANT: Delete this setup.php file from your server immediately!
        </p>
        <p>
            If you see any errors above, check your database credentials in .env file.
        </p>
    </div>
</body>
</html>
