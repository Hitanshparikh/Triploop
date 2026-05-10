<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'save_bulk') {
    $tripId = (int)($_POST['trip_id'] ?? 0);
    $sections = $_POST['sections'] ?? [];

    if (!$tripId) {
        echo json_encode(['success' => false, 'error' => 'Missing trip ID']);
        exit;
    }

    // Delete existing and re-insert for simplicity in builder
    db()->query("DELETE FROM itinerary_sections WHERE trip_id = ?", [$tripId]);

    foreach ($sections as $index => $sec) {
        if (empty($sec['title'])) continue;
        
        $data = [
            'trip_id' => $tripId,
            'title' => trim($sec['title']),
            'date' => $sec['date'] ?? null,
            'time' => $sec['time'] ?? null,
            'location' => trim($sec['location'] ?? ''),
            'notes' => trim($sec['notes'] ?? ''),
            'order_index' => (int)$index
        ];
        db()->insert('itinerary_sections', $data);
    }

    echo json_encode(['success' => true]);
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
