<?php
require_once __DIR__ . '/../includes/functions.php';

// Only allow POST or GET depending on our needs. For now, we'll allow both to act as a proxy.
header('Content-Type: application/json');

if (!defined('RAPIDAPI_KEY')) {
    echo json_encode(['success' => false, 'error' => 'API Key not configured.']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

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
        $input = json_decode(file_get_contents('php://input'), true);
        $days = $input['days'] ?? 3;
        $destination = $input['destination'] ?? 'Paris';
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
        $input = json_decode(file_get_contents('php://input'), true);
        $message = $input['message'] ?? 'Tell me best destinations for Paris';
        $result = makeRapidApiRequest('https://travelchat-ai.p.rapidapi.com/travelchatAI', 'travelchat-ai.p.rapidapi.com', 'POST', ['message' => $message]);
        echo json_encode(['success' => true, 'data' => $result]);
        break;

    case 'city_top_places':
        $input = json_decode(file_get_contents('php://input'), true);
        $region = $input['region'] ?? 'London';
        
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
        $system = "You are a travel database. The user is asking for top restaurants in '$locationId'. Return a JSON array of up to 6 objects. Each object MUST have exactly these keys: 'name' (string), 'description' (short string), 'rating' (float), 'image' (use this exact string format: 'https://source.unsplash.com/400x300/?restaurant,food').";
        $parsed = callGemini("Top restaurants in $locationId", $system, true);
        if ($parsed && is_array($parsed)) {
            echo json_encode(['success' => true, 'data' => ['places' => $parsed]]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'search_hotels':
        $input = json_decode(file_get_contents('php://input'), true);
        $region = $input['contentId'] ?? 'Unknown';
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
        $input = json_decode(file_get_contents('php://input'), true);
        $prompt = $input['prompt'] ?? '';
        
        $system = 'You are an AI travel assistant. Parse the user\'s natural language trip description and return a JSON object with EXACTLY these keys: "name" (a catchy title), "destination" (city/country), "start_date" (YYYY-MM-DD, assume future dates from today), "end_date" (YYYY-MM-DD), "travel_type" (must be one of: solo, couple, family, friends, business, group), "mood" (must be one of: adventure, romantic, healing, luxury, party, spiritual, productivity, solo), "budget" (integer estimate), "budget_level" (must be one of: budget, mid, luxury). Today is ' . date('Y-m-d') . '. IMPORTANT: If the user omits any detail (like mood, budget, or type), you MUST GUESS an intelligent default. DO NOT RETURN NULL FOR ANY VALUE.';
        
        $parsed = callGemini($prompt, $system, true);
        
        if ($parsed) {
            echo json_encode(['success' => true, 'data' => $parsed]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Could not parse magic prompt.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Unknown action: ' . $action]);
        break;
}
