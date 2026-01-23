#!/usr/bin/env php
<?php
/**
 * Barcode System Test Script
 * Run this to verify barcode system is working correctly
 */

require __DIR__ . '/vendor/autoload.php';

echo "\n=== Barcode System Test ===\n\n";

// Test 1: Check if service exists
echo "Test 1: Checking BarcodeService class...\n";
if (class_exists('App\Services\BarcodeService')) {
    echo "✓ BarcodeService found\n";
} else {
    echo "✗ BarcodeService not found\n";
    exit(1);
}

// Test 2: Check if Picqer is installed
echo "\nTest 2: Checking Picqer/Barcode...\n";
try {
    $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
    echo "✓ Picqer BarcodeGeneratorPNG available\n";
} catch (Exception $e) {
    echo "✗ Picqer BarcodeGeneratorPNG error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Try generating a barcode
echo "\nTest 3: Generating sample barcode...\n";
try {
    $base64 = \App\Services\BarcodeService::generateBarcodeImage('ADDC-000001');
    if ($base64) {
        echo "✓ Barcode generated successfully (" . strlen($base64) . " bytes)\n";
    } else {
        echo "✗ Barcode generation failed\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "✗ Error generating barcode: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 4: Check if controller exists
echo "\nTest 4: Checking BarcodeController...\n";
if (class_exists('App\Http\Controllers\Admin\BarcodeController')) {
    echo "✓ BarcodeController found\n";
} else {
    echo "✗ BarcodeController not found\n";
    exit(1);
}

// Test 5: Check if view exists
echo "\nTest 5: Checking scanner view...\n";
$viewPath = __DIR__ . '/resources/views/admin/barcode/scanner.blade.php';
if (file_exists($viewPath)) {
    echo "✓ Scanner view found\n";
} else {
    echo "✗ Scanner view not found\n";
    exit(1);
}

echo "\n=== All Tests Passed! ===\n";
echo "\nBarcode system is ready to use!\n";
echo "Access the scanner at: /admin/barcode/scanner\n";
echo "\n";
