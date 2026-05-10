<?php
/**
 * JourneyOS AI — Packing API
 * Handles: list, save, toggle, add_item, delete_item, reset
 */
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$action = input('action', '');
$userId = currentUserId();
$tripId = intval(input('trip_id', 0));

switch ($action) {

    case 'get':
        if (!$tripId) jsonResponse(['error' => 'Trip ID required'], 400);
        $lists = db()->fetchAll("SELECT * FROM packing_lists WHERE trip_id=? AND user_id=? ORDER BY category", [$tripId, $userId]);
        $result = [];
        foreach ($lists as $list) {
            $list['items'] = json_decode($list['items'] ?? '[]', true);
            $result[] = $list;
        }
        jsonResponse(['success' => true, 'data' => $result]);
        break;

    case 'save':
        if (requestMethod() !== 'POST') jsonResponse(['error' => 'POST required'], 405);
        if (!$tripId) jsonResponse(['error' => 'Trip ID required'], 400);

        $category = trim(input('category', 'General'));
        $items = input('items', '[]');
        if (is_string($items)) $items = json_decode($items, true);
        if (!is_array($items)) $items = [];

        $existing = db()->fetch("SELECT id FROM packing_lists WHERE trip_id=? AND user_id=? AND category=?", [$tripId, $userId, $category]);
        if ($existing) {
            db()->update('packing_lists', ['items' => json_encode($items)], 'id=?', [$existing['id']]);
        } else {
            db()->insert('packing_lists', [
                'trip_id'    => $tripId,
                'user_id'    => $userId,
                'category'   => $category,
                'items'      => json_encode($items),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        jsonResponse(['success' => true]);
        break;

    case 'init':
        // Initialize default packing categories for a trip
        if (!$tripId) jsonResponse(['error' => 'Trip ID required'], 400);
        $defaults = [
            'Documents' => [
                ['name' => 'Passport', 'checked' => false],
                ['name' => 'Visa (if required)', 'checked' => false],
                ['name' => 'Travel Insurance', 'checked' => false],
                ['name' => 'Flight Tickets', 'checked' => false],
                ['name' => 'Hotel Confirmation', 'checked' => false],
                ['name' => 'ID Card / Driving License', 'checked' => false],
            ],
            'Clothing' => [
                ['name' => 'T-shirts / Tops', 'checked' => false],
                ['name' => 'Pants / Shorts', 'checked' => false],
                ['name' => 'Underwear', 'checked' => false],
                ['name' => 'Socks', 'checked' => false],
                ['name' => 'Jacket / Sweater', 'checked' => false],
                ['name' => 'Sleepwear', 'checked' => false],
                ['name' => 'Comfortable Shoes', 'checked' => false],
            ],
            'Electronics' => [
                ['name' => 'Phone + Charger', 'checked' => false],
                ['name' => 'Power Bank', 'checked' => false],
                ['name' => 'Camera', 'checked' => false],
                ['name' => 'Headphones', 'checked' => false],
                ['name' => 'Universal Adapter', 'checked' => false],
            ],
            'Toiletries' => [
                ['name' => 'Toothbrush & Toothpaste', 'checked' => false],
                ['name' => 'Shampoo & Body Wash', 'checked' => false],
                ['name' => 'Sunscreen', 'checked' => false],
                ['name' => 'Deodorant', 'checked' => false],
                ['name' => 'Medications', 'checked' => false],
            ],
            'Essentials' => [
                ['name' => 'Wallet / Cash', 'checked' => false],
                ['name' => 'Backpack / Day Bag', 'checked' => false],
                ['name' => 'Water Bottle', 'checked' => false],
                ['name' => 'Snacks', 'checked' => false],
                ['name' => 'Sunglasses', 'checked' => false],
            ],
        ];

        foreach ($defaults as $cat => $items) {
            $existing = db()->fetch("SELECT id FROM packing_lists WHERE trip_id=? AND user_id=? AND category=?", [$tripId, $userId, $cat]);
            if (!$existing) {
                db()->insert('packing_lists', [
                    'trip_id'    => $tripId,
                    'user_id'    => $userId,
                    'category'   => $cat,
                    'items'      => json_encode($items),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        jsonResponse(['success' => true, 'message' => 'Default packing lists created']);
        break;

    case 'reset':
        if (!$tripId) jsonResponse(['error' => 'Trip ID required'], 400);
        $category = trim(input('category', ''));
        if ($category) {
            $list = db()->fetch("SELECT * FROM packing_lists WHERE trip_id=? AND user_id=? AND category=?", [$tripId, $userId, $category]);
            if ($list) {
                $items = json_decode($list['items'] ?? '[]', true);
                foreach ($items as &$item) $item['checked'] = false;
                db()->update('packing_lists', ['items' => json_encode($items)], 'id=?', [$list['id']]);
            }
        } else {
            $lists = db()->fetchAll("SELECT * FROM packing_lists WHERE trip_id=? AND user_id=?", [$tripId, $userId]);
            foreach ($lists as $list) {
                $items = json_decode($list['items'] ?? '[]', true);
                foreach ($items as &$item) $item['checked'] = false;
                db()->update('packing_lists', ['items' => json_encode($items)], 'id=?', [$list['id']]);
            }
        }
        jsonResponse(['success' => true]);
        break;

    default:
        jsonResponse(['error' => 'Invalid action'], 400);
}
