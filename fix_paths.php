<?php

$rootDir = __DIR__;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir . '/pages')
);

$phpFiles = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach ($phpFiles as $file) {
    $filePath = $file[0];
    $fileContent = file_get_contents($filePath);
    
    if (strpos($fileContent, '$base_url =') !== false) {
        continue;
    }
    
    $relativePath = str_replace($rootDir, '', $filePath);
    $pathDepth = substr_count($relativePath, '/') - 1;
    
    $baseUrlPath = str_repeat('../', $pathDepth);
    
    $newContent = preg_replace(
        '/^<\?php(\s*)/i',
        "<?php\n// Define base URL for assets\n\$base_url = '$baseUrlPath';\n\n",
        $fileContent
    );
    
    file_put_contents($filePath, $newContent);
    
    echo "Added base URL to: " . $filePath . PHP_EOL;
}

echo "Finished updating PHP files with base URL." . PHP_EOL;
?> 