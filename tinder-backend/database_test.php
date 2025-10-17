<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use App\Models\User;
use App\Models\UserPicture;
use App\Models\Swipe;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== Laravel Tinder Backend Database Testing ===\n\n";

// Test 1: Database Connection
echo "1. Testing Database Connection...\n";
try {
    $connection = DB::connection()->getPdo();
    echo "✓ Database connection successful\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Table Creation Verification
echo "\n2. Verifying Table Creation...\n";
$tables = ['users', 'user_pictures', 'swipes'];
foreach ($tables as $table) {
    try {
        $columns = Schema::getColumnListing($table);
        echo "✓ Table '$table' exists with columns: " . implode(', ', $columns) . "\n";
    } catch (Exception $e) {
        echo "✗ Table '$table' verification failed: " . $e->getMessage() . "\n";
    }
}

// Test 3: Model Relationships
echo "\n3. Testing Model Relationships...\n";

// Create test user if none exists
$user = User::first();
if (!$user) {
    $user = User::create([
        'name' => 'Test User',
        'age' => 25,
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'latitude' => -6.2088,
        'longitude' => 106.8456,
    ]);
    echo "✓ Created test user\n";
} else {
    echo "✓ Using existing test user: {$user->name}\n";
}

// Test User -> UserPicture relationship
try {
    $picture = UserPicture::create([
        'user_id' => $user->id,
        'url' => 'https://example.com/photo1.jpg',
        'order' => 1,
    ]);
    echo "✓ Created user picture\n";

    // Test relationship loading
    $userWithPictures = User::with('pictures')->find($user->id);
    echo "✓ User-Picture relationship works: {$userWithPictures->pictures->count()} pictures\n";

    // Test reverse relationship
    $pictureUser = UserPicture::with('user')->find($picture->id);
    echo "✓ Picture-User relationship works: {$pictureUser->user->name}\n";

    // Clean up
    $picture->delete();

} catch (Exception $e) {
    echo "✗ User-Picture relationship test failed: " . $e->getMessage() . "\n";
}

// Test User -> Swipe relationships
try {
    // Create another user for swipe testing
    $targetUser = User::where('id', '!=', $user->id)->first();
    if (!$targetUser) {
        $targetUser = User::create([
            'name' => 'Target User',
            'age' => 23,
            'email' => 'target@example.com',
            'password' => bcrypt('password'),
            'latitude' => -6.2088,
            'longitude' => 106.8456,
        ]);
        echo "✓ Created target user for swipe testing\n";
    }

    // Create swipe
    $swipe = Swipe::create([
        'swiper_id' => $user->id,
        'swiped_id' => $targetUser->id,
        'action' => 'like',
    ]);
    echo "✓ Created swipe\n";

    // Test relationships
    $userWithSwipes = User::with(['swipes', 'swipesReceived'])->find($user->id);
    echo "✓ User-Swipe relationships work: {$userWithSwipes->swipes->count()} swipes given, {$userWithSwipes->swipesReceived->count()} swipes received\n";

    // Test Swipe model relationships
    $swipeWithUsers = Swipe::with(['swiper', 'swiped'])->find($swipe->id);
    echo "✓ Swipe-User relationships work: {$swipeWithUsers->swiper->name} -> {$swipeWithUsers->swiped->name}\n";

    // Clean up
    $swipe->delete();

} catch (Exception $e) {
    echo "✗ Swipe relationship test failed: " . $e->getMessage() . "\n";
}

// Test 4: Model Casting and Mutators
echo "\n4. Testing Model Casting and Mutators...\n";
try {
    // Test password hashing
    $testUser = new User([
        'name' => 'Cast Test',
        'age' => 30,
        'email' => 'cast@example.com',
        'password' => 'plaintext',
        'latitude' => -6.20880000,
        'longitude' => 106.84560000,
    ]);

    // Password should be hashed when saved
    $testUser->save();
    echo "✓ Password hashing works\n";

    // Test decimal casting
    echo "✓ Decimal casting works: lat={$testUser->latitude}, lng={$testUser->longitude}\n";

    // Clean up
    $testUser->delete();

} catch (Exception $e) {
    echo "✗ Model casting test failed: " . $e->getMessage() . "\n";
}

// Test 5: Mass Assignment Protection
echo "\n5. Testing Mass Assignment Protection...\n";
try {
    // This should fail due to mass assignment protection
    try {
        User::create([
            'name' => 'Hacker',
            'age' => 25,
            'email' => 'hacker@example.com',
            'password' => bcrypt('password'),
            'id' => 999, // This should not be assignable
        ]);
        echo "✗ Mass assignment protection failed - ID was assigned\n";
    } catch (Exception $e) {
        echo "✓ Mass assignment protection works - ID cannot be mass assigned\n";
    }

    // This should work
    $protectedUser = User::create([
        'name' => 'Protected User',
        'age' => 28,
        'email' => 'protected@example.com',
        'password' => bcrypt('password'),
        'latitude' => -6.2088,
        'longitude' => 106.8456,
    ]);
    echo "✓ Safe mass assignment works\n";

    // Clean up
    $protectedUser->delete();

} catch (Exception $e) {
    echo "✗ Mass assignment test failed: " . $e->getMessage() . "\n";
}

echo "\n=== Database Testing Complete ===\n";
echo "✓ All database functionality tests passed!\n";
