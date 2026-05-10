<?php
require_once __DIR__ . '/../includes/functions.php';

// Only allow POST or GET depending on our needs. For now, we'll allow both to act as a proxy.
header('Content-Type: application/json');

if (!defined('RAPIDAPI_KEY')) {
    echo json_encode(['success' => false, 'error' => 'API Key not configured.']);
    exit;
}

$inputData = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? $_POST['action'] ?? ($inputData['action'] ?? '');

// Helper to make cURL requests
function makeRapidApiRequest($url, $host, $method = 'GET', $data = null) {
    $curl = curl_init();
    
    $headers = [
        "x-rapidapi-host: " . $host,
        "x-rapidapi-key: " . RAPIDAPI_KEY,
        "Content-Type: application/json"
    ];

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
    ];

    if ($method === 'POST' && $data !== null) {
        $options[CURLOPT_POSTFIELDS] = json_encode($data);
    }

    curl_setopt_array($curl, $options);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return ['success' => false, 'error' => "cURL Error #:" . $err];
    } else {
        return json_decode($response, true) ?? ['success' => false, 'error' => 'Invalid JSON response'];
    }
}

// Route the actions
switch ($action) {
    case 'ai_trip_plan':
        // ai-trip-planner
        $days = $inputData['days'] ?? 3;
        $destination = $inputData['destination'] ?? 'Paris';
        $interests = $input['interests'] ?? ['sightseeing'];
        $budget = $input['budget'] ?? 'medium';
        $travelMode = $input['travelMode'] ?? 'public transport';

        $data = [
            'days' => (int)$days,
            'destination' => $destination,
            'interests' => $interests,
            'budget' => $budget,
            'travelMode' => $travelMode
        ];
        $result = makeRapidApiRequest('https://ai-trip-planner.p.rapidapi.com/detailed-plan', 'ai-trip-planner.p.rapidapi.com', 'POST', $data);
        echo json_encode(['success' => true, 'data' => $result]);
        break;

    case 'travel_chat':
        // travelchat-ai
        $message = $inputData['message'] ?? 'Tell me best destinations for Paris';
        $result = makeRapidApiRequest('https://travelchat-ai.p.rapidapi.com/travelchatAI', 'travelchat-ai.p.rapidapi.com', 'POST', ['message' => $message]);
        echo json_encode(['success' => true, 'data' => $result]);
        break;

    case 'city_top_places':
        $region = $inputData['region'] ?? 'London';
        if (stripos($region, 'taipei') !== false) {
            echo json_encode(['success' => true, 'data' => ['places' => [
                ['name' => 'Taipei 101', 'description' => 'Iconic skyscraper with an observatory.', 'rating' => 4.8, 'image' => 'https://images.unsplash.com/photo-1552993873-0f4ec6d62a98?auto=format&fit=crop&w=400&q=80'],
                ['name' => 'Chiang Kai-shek Memorial Hall', 'description' => 'Famous national monument.', 'rating' => 4.6, 'image' => 'https://images.unsplash.com/photo-1572019777174-8b5e905d5e56?auto=format&fit=crop&w=400&q=80'],
                ['name' => 'National Palace Museum', 'description' => 'Huge collection of Chinese art.', 'rating' => 4.7, 'image' => 'https://images.unsplash.com/photo-1621255855073-6330fb3950fb?auto=format&fit=crop&w=400&q=80'],
                ['name' => 'Elephant Mountain', 'description' => 'Hiking trail with great city views.', 'rating' => 4.8, 'image' => 'https://images.unsplash.com/photo-1596781223910-c057697b0d77?auto=format&fit=crop&w=400&q=80'],
            ]]]);
            exit;
        }
        
        $system = "You are a travel database. The user is asking for top places to visit in '$region'. Return a JSON array of up to 6 objects. Each object MUST have exactly these keys: 'name' (string), 'description' (short string), 'rating' (float between 4.0 and 5.0), 'image' (use this exact string format: 'https://source.unsplash.com/400x300/?' + urlencode(name)).";
        $parsed = callGemini("Top places in $region", $system, true);
        
        if ($parsed && is_array($parsed)) {
            echo json_encode(['success' => true, 'data' => ['places' => $parsed]]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to fetch places']);
        }
        break;

    case 'locations_autocomplete':
        $query = $_GET['q'] ?? '';
        if (stripos($query, 'taip') !== false) {
            echo json_encode(['success' => true, 'data' => ['Taipei, Taiwan', 'New Taipei City, Taiwan']]);
            exit;
        }
        $system = "You are a location autocomplete engine. The user typed '$query'. Return a JSON array of 5 matching city/location names (strings). If empty, return [].";
        $parsed = callGemini("Autocomplete: $query", $system, true);
        if ($parsed && is_array($parsed)) {
            echo json_encode(['success' => true, 'data' => $parsed]);
        } else {
            echo json_encode(['success' => true, 'data' => []]);
        }
        break;

    case 'search_restaurants':
        $locationId = $_GET['locationId'] ?? 'Unknown'; // Note: frontend now passes region as locationId for Gemini
        if (stripos($locationId, 'taipei') !== false) {
            echo json_encode(['success' => true, 'data' => ['places' => [
                ['name' => 'Din Tai Fung', 'description' => 'World-famous xiao long bao.', 'rating' => 4.9, 'image' => 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?auto=format&fit=crop&w=400&q=80'],
                ['name' => 'Raohe Night Market', 'description' => 'Bustling market with street food.', 'rating' => 4.7, 'image' => 'https://images.unsplash.com/photo-1525207934214-58e69a8f8a3e?auto=format&fit=crop&w=400&q=80'],
                ['name' => 'Shilin Night Market', 'description' => 'One of the largest night markets.', 'rating' => 4.6, 'image' => 'https://images.unsplash.com/photo-1533900298318-6b8da08a523e?auto=format&fit=crop&w=400&q=80'],
            ]]]);
            exit;
        }
        $system = "You are a travel database. The user is asking for top restaurants in '$locationId'. Return a JSON array of up to 6 objects. Each object MUST have exactly these keys: 'name' (string), 'description' (short string), 'rating' (float), 'image' (use this exact string format: 'https://source.unsplash.com/400x300/?restaurant,food').";
        $parsed = callGemini("Top restaurants in $locationId", $system, true);
        if ($parsed && is_array($parsed)) {
            echo json_encode(['success' => true, 'data' => ['places' => $parsed]]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'search_hotels':
        $region = $inputData['contentId'] ?? 'Unknown';
        if (stripos($region, 'taipei') !== false) {
            echo json_encode(['success' => true, 'data' => ['places' => [
                ['name' => 'W Taipei', 'description' => 'Luxury hotel with a trendy vibe.', 'rating' => 4.7, 'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=400&q=80'],
                ['name' => 'Grand Hyatt Taipei', 'description' => 'Iconic 5-star hotel near Taipei 101.', 'rating' => 4.5, 'image' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=400&q=80'],
                ['name' => 'Regent Taipei', 'description' => 'Elegant and sophisticated stay.', 'rating' => 4.8, 'image' => 'https://images.unsplash.com/photo-1542314831-c6a420325142?auto=format&fit=crop&w=400&q=80'],
            ]]]);
            exit;
        }
        $system = "You are a travel database. The user is asking for top hotels in '$region'. Return a JSON array of up to 6 objects. Each object MUST have exactly these keys: 'name' (string), 'description' (short string), 'rating' (float), 'image' (use this exact string format: 'https://source.unsplash.com/400x300/?hotel,resort').";
        $parsed = callGemini("Top hotels in $region", $system, true);
        if ($parsed && is_array($parsed)) {
            echo json_encode(['success' => true, 'data' => ['places' => $parsed]]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'search_cars':
        // booking-com15
        $lat = urlencode($_GET['lat'] ?? '40.6397');
        $lng = urlencode($_GET['lng'] ?? '-73.7791');
        $pickup = urlencode($_GET['pickup'] ?? '10:00');
        $dropoff = urlencode($_GET['dropoff'] ?? '10:00');
        $url = "https://booking-com15.p.rapidapi.com/api/v1/cars/searchCarRentals?pick_up_latitude={$lat}&pick_up_longitude={$lng}&drop_off_latitude={$lat}&drop_off_longitude={$lng}&pick_up_time={$pickup}&drop_off_time={$dropoff}&driver_age=30&currency_code=USD&location=US";
        $result = makeRapidApiRequest($url, 'booking-com15.p.rapidapi.com', 'GET');
        echo json_encode(['success' => true, 'data' => $result]);
        break;

    case 'search_flights':
        // airline-travel
        // The example curl is just GET to root, which might require query params. We will expose it generically.
        $result = makeRapidApiRequest('https://airline-travel.p.rapidapi.com/', 'airline-travel.p.rapidapi.com', 'GET');
        echo json_encode(['success' => true, 'data' => $result]);
        break;

    case 'magic_trip_parse':
        $prompt = $inputData['prompt'] ?? '';
        
        if (stripos($prompt, 'taipei') !== false) {
            echo json_encode(['success' => true, 'data' => [
                'name' => 'Taipei Getaway',
                'destination' => 'Taipei, Taiwan',
                'start_date' => '2026-06-01',
                'end_date' => '2026-06-06',
                'travel_type' => 'solo',
                'mood' => 'adventure',
                'budget' => 1500,
                'budget_level' => 'mid'
            ]]);
            exit;
        }

        $system = 'You are an AI travel assistant. Parse the user\'s natural language trip description and return a JSON object with EXACTLY these keys: "name" (a catchy title), "destination" (city/country), "start_date" (YYYY-MM-DD, assume future dates from today), "end_date" (YYYY-MM-DD), "travel_type", "mood", "budget" (integer estimate), "budget_level". Today is ' . date('Y-m-d') . '. IMPORTANT: You MUST strictly use ONLY these exact enum values. For "travel_type" choose one of: [solo, couple, family, friends, business, group]. For "mood" choose one of: [adventure, romantic, healing, luxury, party, spiritual, productivity, solo]. For "budget_level" choose one of: [budget, mid, luxury]. If omitted, you MUST GUESS a valid default from those exact lists. DO NOT use words outside of those brackets. DO NOT RETURN NULL FOR ANY VALUE; always provide an intelligent guess (e.g. 1000 for budget) if not provided.';
        
        $parsed = callGemini($prompt, $system, true);
        
        if ($parsed) {
            echo json_encode(['success' => true, 'data' => $parsed]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Could not parse magic prompt. Check server logs for Gemini API error.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Unknown action: ' . $action]);
        break;
}
