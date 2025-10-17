<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use App\Models\User;
use App\Models\UserPicture;
use App\Models\Swipe;
use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== Laravel Tinder Backend API Testing ===\n\n";

$baseUrl = 'http://localhost:8000/api';

// Test 1: Authentication Middleware
echo "1. Testing Authentication Middleware...\n";

// Clean up existing test data first
User::where('email', 'like', '%api-test%@example.com')->delete();
User::where('email', 'like', '%other%@example.com')->delete();
User::where('email', 'like', '%pagination%@example.com')->delete();

// Create test user for API testing
$testUser = User::create([
    'name' => 'API Test User',
    'age' => 25,
    'email' => 'api-test' . time() . '@example.com',
    'password' => bcrypt('password'),
    'latitude' => -6.2088,
    'longitude' => 106.8456,
]);

// Create user pictures for testing
UserPicture::create([
    'user_id' => $testUser->id,
    'url' => 'https://example.com/photo1.jpg',
    'order' => 1,
]);

echo "✓ Created test user for API testing\n";

// Test unauthenticated request (should fail)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/users/recommendations');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 401) {
    echo "✓ Authentication middleware works - unauthenticated request rejected\n";
} else {
    echo "✗ Authentication middleware failed - expected 401, got $httpCode\n";
}

// Test 2: Authentication and Token Generation
echo "\n2. Testing Authentication and Token Generation...\n";

// Create Sanctum token for the test user
$token = $testUser->createToken('api-test-token')->plainTextToken;
echo "✓ Generated Sanctum token\n";

// Test authenticated request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/user');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Authorization: Bearer ' . $token,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $userData = json_decode($response, true);
    echo "✓ Authentication works - got user data: {$userData['name']}\n";
} else {
    echo "✗ Authentication failed - expected 200, got $httpCode\n";
    echo "Response: $response\n";
}

// Test 3: API Endpoints with Proper Request/Response
echo "\n3. Testing API Endpoints...\n";

// Create another user for recommendations testing
$otherUser = User::create([
    'name' => 'Other User',
    'age' => 23,
    'email' => 'other@example.com',
    'password' => bcrypt('password'),
    'latitude' => -6.2000,
    'longitude' => 106.8000,
]);

$otherUserPicture = UserPicture::create([
    'user_id' => $otherUser->id,
    'url' => 'https://example.com/other-photo.jpg',
    'order' => 1,
]);

// Test GET /api/users/recommendations
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/users/recommendations');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Authorization: Bearer ' . $token,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✓ GET /users/recommendations works - returned {$data['meta']['total']} users\n";
} else {
    echo "✗ GET /users/recommendations failed - expected 200, got $httpCode\n";
}

// Test POST /api/swipes
$swipeData = [
    'swiped_id' => $otherUser->id,
    'action' => 'like'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/swipes');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($swipeData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 201) {
    $data = json_decode($response, true);
    echo "✓ POST /swipes works - created swipe with action: {$data['swipe']['action']}\n";
} else {
    echo "✗ POST /swipes failed - expected 201, got $httpCode\n";
    echo "Response: $response\n";
}

// Test GET /api/users/liked
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/users/liked');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Authorization: Bearer ' . $token,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✓ GET /users/liked works - returned {$data['meta']['total']} liked users\n";
} else {
    echo "✗ GET /users/liked failed - expected 200, got $httpCode\n";
}

// Test 4: Error Handling and Validation
echo "\n4. Testing Error Handling and Validation...\n";

// Test invalid swipe action
$invalidSwipeData = [
    'swiped_id' => $otherUser->id,
    'action' => 'invalid_action'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/swipes');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($invalidSwipeData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 422) {
    echo "✓ Validation works - invalid action rejected with 422\n";
} else {
    echo "✗ Validation failed - expected 422, got $httpCode\n";
}

// Test non-existent user swipe
$nonExistentSwipeData = [
    'swiped_id' => 99999,
    'action' => 'like'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/swipes');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($nonExistentSwipeData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 422) {
    echo "✓ Validation works - non-existent user rejected with 422\n";
} else {
    echo "✗ Validation failed - expected 422, got $httpCode\n";
}

// Test 5: Pagination Functionality
echo "\n5. Testing Pagination Functionality...\n";

// Create multiple users for pagination testing
$users = [];
for ($i = 1; $i <= 15; $i++) {
    $user = User::create([
        'name' => "Pagination User $i",
        'age' => 20 + $i,
        'email' => "pagination$i@example.com",
        'password' => bcrypt('password'),
        'latitude' => -6.2000 + ($i * 0.001),
        'longitude' => 106.8000 + ($i * 0.001),
    ]);

    UserPicture::create([
        'user_id' => $user->id,
        'url' => "https://example.com/photo$i.jpg",
        'order' => 1,
    ]);

    $users[] = $user;
}

echo "✓ Created 15 users for pagination testing\n";

// Test pagination - page 1
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/users/recommendations?page=1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Authorization: Bearer ' . $token,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    $perPage = $data['meta']['per_page'];
    $total = $data['meta']['total'];
    $currentCount = count($data['data']);
    echo "✓ Pagination works - page 1: $currentCount users shown, $perPage per page, $total total\n";
} else {
    echo "✗ Pagination failed - expected 200, got $httpCode\n";
}

// Test pagination - page 2
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/users/recommendations?page=2');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Authorization: Bearer ' . $token,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    $currentCount = count($data['data']);
    echo "✓ Pagination works - page 2: $currentCount users shown\n";
} else {
    echo "✗ Pagination failed - expected 200, got $httpCode\n";
}

// Test 6: Edge Cases
echo "\n6. Testing Edge Cases...\n";

// Test swiping on yourself (should be prevented by business logic)
$selfSwipeData = [
    'swiped_id' => $testUser->id,
    'action' => 'like'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/swipes');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($selfSwipeData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 201) {
    echo "⚠ Self-swipe allowed - may need business logic validation\n";
} else {
    echo "✓ Self-swipe prevented with status $httpCode\n";
}

// Test duplicate swipe (swipe on same user twice)
$duplicateSwipeData = [
    'swiped_id' => $otherUser->id,
    'action' => 'nope'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/swipes');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($duplicateSwipeData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 201) {
    echo "⚠ Duplicate swipe allowed - may need business logic validation\n";
} else {
    echo "✓ Duplicate swipe prevented with status $httpCode\n";
}

// Cleanup
echo "\n7. Cleaning up test data...\n";
foreach ($users as $user) {
    $user->delete();
}
$testUser->delete();
$otherUser->delete();
$otherUserPicture->delete();

$swipes = Swipe::where('swiper_id', $testUser->id)->get();
foreach ($swipes as $swipe) {
    $swipe->delete();
}

echo "✓ Cleaned up test data\n";

// Test 8: API Response Format Verification
echo "\n8. Testing API Response Format...\n";

// Test that responses include proper JSON structure
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/users/recommendations');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Authorization: Bearer ' . $token,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);

    // Check for required pagination fields
    $requiredFields = ['data', 'links', 'meta'];
    $missingFields = [];

    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            $missingFields[] = $field;
        }
    }

    if (empty($missingFields)) {
        echo "✓ API response format correct - includes data, links, and meta\n";
    } else {
        echo "✗ API response format incorrect - missing: " . implode(', ', $missingFields) . "\n";
    }

    // Check UserResource format
    if (isset($data['data'][0])) {
        $userFields = ['id', 'name', 'age', 'latitude', 'longitude', 'pictures', 'created_at', 'updated_at'];
        $userData = $data['data'][0];
        $missingUserFields = [];

        foreach ($userFields as $field) {
            if (!array_key_exists($field, $userData)) {
                $missingUserFields[] = $field;
            }
        }

        if (empty($missingUserFields)) {
            echo "✓ UserResource format correct - includes all required fields\n";
        } else {
            echo "✗ UserResource format incorrect - missing: " . implode(', ', $missingUserFields) . "\n";
        }
    }
} else {
    echo "✗ Could not test response format - API call failed\n";
}

echo "\n=== API Testing Complete ===\n";
echo "✓ All API functionality tests completed!\n";
