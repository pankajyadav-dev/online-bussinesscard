<?php
/**
 * Utility script to add base URL to PHP files in subdirectories
 * 
 * Run this script to automatically add the base URL variable to PHP files
 * in subdirectories for proper path resolution.
 */

// Define the root directory
$rootDir = __DIR__;

// Get all PHP files in the pages directory and its subdirectories
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir . '/pages')
);

$phpFiles = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

// Process each PHP file
foreach ($phpFiles as $file) {
    $filePath = $file[0];
    $fileContent = file_get_contents($filePath);
    
    // Skip if the base URL is already defined
    if (strpos($fileContent, '$base_url =') !== false) {
        continue;
    }
    
    // Get the relative path depth
    $relativePath = str_replace($rootDir, '', $filePath);
    $pathDepth = substr_count($relativePath, '/') - 1; // -1 because /pages/ is the first level
    
    // Create the appropriate base URL string
    $baseUrlPath = str_repeat('../', $pathDepth);
    
    // Insert the base URL definition at the beginning of the file after the PHP opening tag
    $newContent = preg_replace(
        '/^<\?php(\s*)/i',
        "<?php\n// Define base URL for assets\n\$base_url = '$baseUrlPath';\n\n",
        $fileContent
    );
    
    // Write the modified content back to the file
    file_put_contents($filePath, $newContent);
    
    echo "Added base URL to: " . $filePath . PHP_EOL;
}

echo "Finished updating PHP files with base URL." . PHP_EOL;
?> 