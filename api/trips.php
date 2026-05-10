<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    // Validate CSRF
    if (!validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
        exit;
    }

    $userId = currentUserId();
    $name = trim($_POST['name'] ?? 'My Awesome Trip');
    $destination = trim($_POST['destination'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $travelType = $_POST['travel_type'] ?? 'solo';
    $mood = $_POST['mood'] ?? 'adventure';
    $budget = floatval($_POST['budget'] ?? 0);
    $currency = $_POST['currency'] ?? 'USD';
    $budgetLevel = $_POST['budget_level'] ?? 'mid';
    
    $shareToken = bin2hex(random_bytes(16));

    // Handle Cover Image
    $coverImage = null;
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('cover_') . '.' . $ext;
        $dest = __DIR__ . '/../uploads/covers/' . $filename;
        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $dest)) {
            $coverImage = $filename;
        }
    }

    // Insert Trip
    $tripId = db()->insert('trips', [
        'user_id' => $userId,
        'name' => $name,
        'destination' => $destination,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'travel_type' => $travelType,
        'mood' => $mood,
        'budget_total' => $budget,
        'currency' => $currency,
        'budget_level' => $budgetLevel,
        'description' => $description,
        'cover_image' => $coverImage,
        'share_token' => $shareToken
    ]);

    if (!$tripId) {
        echo json_encode(['success' => false, 'error' => 'Failed to create trip']);
        exit;
    }

    // Determine if AI Generation is requested
    $aiGenerate = isset($_POST['ai_generate']) && $_POST['ai_generate'] === 'on';
    $activities = $_POST['activity_prefs'] ?? [];

    if ($aiGenerate && defined('RAPIDAPI_KEY') && RAPIDAPI_KEY !== '') {
        $days = (strtotime($endDate) - strtotime($startDate)) / 86400;
        $days = max(1, min($days, 14)); // Cap at 14 days for API limits
        
        $data = [
            'days' => (int)$days,
            'destination' => $destination,
            'interests' => $activities,
            'budget' => $budgetLevel,
            'travelMode' => $travelType === 'solo' ? 'walking' : 'public transport'
        ];

        $plan = [];
        if (stripos($destination, 'taipei') !== false) {
            $plan = [
                'plan' => [
                    [
                        'activities' => [
                            ['title' => 'Arrive and check-in to Taipei Hotel', 'description' => 'Settle down and freshen up.', 'time' => '14:00', 'location' => 'Taipei'],
                            ['title' => 'Explore Taipei 101', 'description' => 'Visit the iconic observatory for sunset views.', 'time' => '17:00', 'location' => 'Taipei 101'],
                            ['title' => 'Dinner at Din Tai Fung', 'description' => 'Enjoy world-class Xiao Long Bao.', 'time' => '19:30', 'location' => 'Taipei 101 Mall']
                        ]
                    ],
                    [
                        'activities' => [
                            ['title' => 'National Palace Museum', 'description' => 'Explore ancient Chinese artifacts.', 'time' => '09:30', 'location' => 'National Palace Museum'],
                            ['title' => 'Shilin Night Market', 'description' => 'Taste local street food like giant fried chicken.', 'time' => '18:00', 'location' => 'Shilin Night Market']
                        ]
                    ],
                    [
                        'activities' => [
                            ['title' => 'Hike Elephant Mountain', 'description' => 'Get the best view of the city skyline.', 'time' => '08:00', 'location' => 'Elephant Mountain'],
                            ['title' => 'Relax at Beitou Hot Springs', 'description' => 'Soak in the natural thermal baths.', 'time' => '15:00', 'location' => 'Beitou']
                        ]
                    ]
                ]
            ];
        } else {
            // Call the proxy internally since we are already in the backend
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://ai-trip-planner.p.rapidapi.com/detailed-plan",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "x-rapidapi-host: ai-trip-planner.p.rapidapi.com",
                    "x-rapidapi-key: " . RAPIDAPI_KEY
                ],
            ]);
            $response = curl_exec($curl);
            curl_close($curl);
            $plan = json_decode($response, true);
        }
        
        if ($plan && is_array($plan)) {
            // We need to parse the AI plan into itinerary_sections.
            // The rapidAPI ai-trip-planner usually returns an array of days or a structured format.
            // For robustness, we will create generic sections if the structure is unknown, 
            // or iterate if it's predictable. Let's assume an array of days containing activities.
            
            $dayIndex = 1;
            $orderIndex = 1;
            $currentDate = new DateTime($startDate);

            // If the API returns a 'plan' key or just an array
            $schedule = $plan['plan'] ?? $plan; 
            
            if (is_array($schedule)) {
                foreach ($schedule as $dayPlan) {
                    $dateStr = $currentDate->format('Y-m-d');
                    
                    // Try to extract activities
                    $acts = $dayPlan['activities'] ?? $dayPlan['events'] ?? [$dayPlan];
                    
                    foreach ($acts as $act) {
                        $title = is_array($act) ? ($act['title'] ?? $act['name'] ?? 'Activity') : (is_string($act) ? $act : 'Activity');
                        $desc = is_array($act) ? ($act['description'] ?? '') : '';
                        $time = is_array($act) ? ($act['time'] ?? '10:00') : '10:00';
                        $loc = is_array($act) ? ($act['location'] ?? $destination) : $destination;
                        
                        db()->insert('itinerary_sections', [
                            'trip_id' => $tripId,
                            'title' => substr($title, 0, 100),
                            'date' => $dateStr,
                            'time' => $time,
                            'location' => substr($loc, 0, 100),
                            'notes' => $desc,
                            'cost_estimate' => 0,
                            'order_index' => $orderIndex++
                        ]);
                    }
                    
                    $currentDate->modify('+1 day');
                    $dayIndex++;
                }
            }
        }
    } else {
        // Create an empty starter section
        db()->insert('itinerary_sections', [
            'trip_id' => $tripId,
            'title' => 'Arrive at ' . $destination,
            'date' => $startDate,
            'time' => '14:00',
            'location' => $destination,
            'notes' => 'Check into hotel.',
            'order_index' => 1
        ]);
    }

    // Initialize packing list defaults if requested or standard
    $cats = ['Documents', 'Clothing', 'Toiletries', 'Electronics', 'Essentials'];
    foreach ($cats as $cat) {
        $items = [['name' => 'Passport/ID', 'checked' => false]]; // sample item
        db()->insert('packing_lists', [
            'user_id' => $userId,
            'trip_id' => $tripId,
            'category' => $cat,
            'items' => json_encode($items)
        ]);
    }

    // Return to frontend with redirect URL
    echo json_encode([
        'success' => true,
        'redirect' => APP_URL . '/pages/dashboard.php'
    ]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
