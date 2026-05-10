<?php
/**
 * JourneyOS AI — Trips API
 * Handles: create, update, delete, list, share
 */
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$action = input('action', '');
$userId = currentUserId();

switch ($action) {

    case 'create':
        if (requestMethod() !== 'POST') redirect('/pages/create-trip.php');
        
        $name        = trim(input('name', ''));
        $destination = trim(input('destination', ''));
        $description = trim(input('description', ''));
        $startDate   = input('start_date', '');
        $endDate     = input('end_date', '');
        $mood        = input('mood', 'adventure');
        $travelType  = input('travel_type', 'solo');
        $budgetTotal = floatval(input('budget_total', 0));
        $budgetLevel = input('budget_level', 'mid');
        $currency    = input('currency', 'USD');
        $aiGenerate  = input('ai_generate', 0) ? 1 : 0;

        if (!$name) {
            setFlash('error', 'Please enter a trip name.');
            redirect('/pages/create-trip.php');
        }

        // Handle cover image
        $coverImage = '';
        if (!empty($_FILES['cover_image']['name'])) {
            $upload = uploadFile($_FILES['cover_image'], 'covers');
            if (isset($upload['filename'])) $coverImage = $upload['filename'];
        }

        $shareToken = generateToken(16);

        $tripId = db()->insert('trips', [
            'user_id'      => $userId,
            'name'         => $name,
            'destination'  => $destination,
            'description'  => $description,
            'cover_image'  => $coverImage,
            'start_date'   => $startDate ?: null,
            'end_date'     => $endDate ?: null,
            'mood'         => $mood,
            'travel_type'  => $travelType,
            'status'       => 'planning',
            'budget_total' => $budgetTotal,
            'budget_level' => $budgetLevel,
            'currency'     => $currency,
            'ai_generate'  => $aiGenerate,
            'share_token'  => $shareToken,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        // Create itinerary sections if provided
        $sections = $_POST['sections'] ?? [];
        if (is_array($sections)) {
            foreach ($sections as $i => $sec) {
                if (empty($sec['title'])) continue;
                db()->insert('itinerary_sections', [
                    'trip_id'      => $tripId,
                    'title'        => trim($sec['title']),
                    'section_type' => $sec['type'] ?? 'travel',
                    'start_date'   => $sec['start_date'] ?? null,
                    'end_date'     => $sec['end_date'] ?? null,
                    'budget'       => floatval($sec['budget'] ?? 0),
                    'status'       => 'planned',
                    'order_index'  => $i,
                    'created_at'   => date('Y-m-d H:i:s'),
                ]);
            }
        }

        setFlash('success', 'Trip "' . $name . '" created successfully!');
        redirect('/pages/my-trips.php');
        break;

    case 'update':
        if (requestMethod() !== 'POST') redirect('/pages/my-trips.php');
        $tripId = intval(input('trip_id', 0));
        $trip = db()->fetch("SELECT * FROM trips WHERE id=? AND user_id=?", [$tripId, $userId]);
        if (!$trip) { setFlash('error', 'Trip not found.'); redirect('/pages/my-trips.php'); }

        $data = [
            'name'         => trim(input('name', $trip['name'])),
            'destination'  => trim(input('destination', $trip['destination'] ?? '')),
            'description'  => trim(input('description', $trip['description'] ?? '')),
            'start_date'   => input('start_date', $trip['start_date']),
            'end_date'     => input('end_date', $trip['end_date']),
            'mood'         => input('mood', $trip['mood']),
            'travel_type'  => input('travel_type', $trip['travel_type']),
            'status'       => input('status', $trip['status']),
            'budget_total' => floatval(input('budget_total', $trip['budget_total'])),
        ];

        if (!empty($_FILES['cover_image']['name'])) {
            $upload = uploadFile($_FILES['cover_image'], 'covers');
            if (isset($upload['filename'])) $data['cover_image'] = $upload['filename'];
        }

        db()->update('trips', $data, 'id=? AND user_id=?', [$tripId, $userId]);
        setFlash('success', 'Trip updated.');
        redirect('/pages/my-trips.php');
        break;

    case 'delete':
        $tripId = intval(input('trip_id', 0));
        $trip = db()->fetch("SELECT id FROM trips WHERE id=? AND user_id=?", [$tripId, $userId]);
        if ($trip) {
            db()->delete('trips', 'id=?', [$tripId]);
            setFlash('success', 'Trip deleted.');
        }
        if (isAjaxRequest()) jsonResponse(['success' => true]);
        redirect('/pages/my-trips.php');
        break;

    case 'list':
        $status = input('status', '');
        $sql = "SELECT * FROM trips WHERE user_id=?";
        $params = [$userId];
        if ($status) { $sql .= " AND status=?"; $params[] = $status; }
        $sql .= " ORDER BY created_at DESC";
        $trips = db()->fetchAll($sql, $params);
        jsonResponse(['success' => true, 'data' => $trips]);
        break;

    case 'share':
        $tripId = intval(input('trip_id', 0));
        $trip = db()->fetch("SELECT * FROM trips WHERE id=? AND user_id=?", [$tripId, $userId]);
        if (!$trip) jsonResponse(['error' => 'Trip not found'], 404);
        $isPublic = $trip['is_public'] ? 0 : 1;
        db()->update('trips', ['is_public' => $isPublic], 'id=?', [$tripId]);
        jsonResponse(['success' => true, 'is_public' => $isPublic, 'share_url' => APP_URL . '/pages/shared-trip.php?token=' . $trip['share_token']]);
        break;

    default:
        redirect('/pages/my-trips.php');
}
