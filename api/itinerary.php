<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'save') {
    $tripId = (int)($_POST['trip_id'] ?? 0);
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $date = $_POST['date'] ?? null;
    $time = $_POST['time'] ?? null;
    $location = trim($_POST['location'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (!$tripId || !$title) {
        echo json_encode(['success' => false, 'error' => 'Missing title or trip ID']);
        exit;
    }

    $data = [
        'trip_id' => $tripId,
        'title' => $title,
        'date' => $date,
        'time' => $time,
        'location' => $location,
        'notes' => $notes,
    ];

    if ($id > 0) {
        db()->update('itinerary_sections', $data, 'id = ?', [$id]);
    } else {
        // get max order
        $max = db()->fetchOne("SELECT MAX(order_index) as m FROM itinerary_sections WHERE trip_id=?", [$tripId]);
        $data['order_index'] = ($max['m'] ?? 0) + 1;
        $id = db()->insert('itinerary_sections', $data);
    }

    echo json_encode(['success' => true, 'id' => $id]);
    exit;
}

if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    $tripId = (int)($_POST['trip_id'] ?? 0);
    if ($id && $tripId) {
        db()->query("DELETE FROM itinerary_sections WHERE id = ? AND trip_id = ?", [$id, $tripId]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

if ($action === 'reorder') {
    $tripId = (int)($_POST['trip_id'] ?? 0);
    $order = json_decode($_POST['order'] ?? '[]', true);
    
    if ($tripId && is_array($order)) {
        foreach ($order as $index => $itemId) {
            db()->update('itinerary_sections', ['order_index' => $index], 'id = ? AND trip_id = ?', [$itemId, $tripId]);
        }
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Unknown action']);
