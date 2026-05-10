<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

// Helper to update trip's total spent
function updateTripBudget($tripId) {
    $total = db()->fetchOne("SELECT SUM(amount) as total FROM trip_expenses WHERE trip_id = ?", [$tripId]);
    $sum = $total['total'] ?? 0;
    db()->query("UPDATE trips SET budget_spent = ? WHERE id = ?", [$sum, $tripId]);
    return $sum;
}

if ($action === 'create_magic') {
    $tripId = (int)($_POST['trip_id'] ?? 0);
    $prompt = trim($_POST['prompt'] ?? '');
    
    if (!$tripId || !$prompt) {
        echo json_encode(['success' => false, 'error' => 'Missing input']);
        exit;
    }

    $system = 'You are an AI expense parser. The user will give you a sentence describing a purchase. Return a JSON object with exactly these keys: "vendor" (string, the place or item name), "amount" (float, the cost), "category" (string, must be one of: Transport, Food, Accommodation, Activities, Shopping, Other), "currency" (string, 3-letter code e.g. USD, EUR. If not mentioned, return "USD").';
    
    $parsed = callGemini($prompt, $system, true);
    
    if ($parsed && isset($parsed['amount'])) {
        $vendor = substr($parsed['vendor'] ?? 'Expense', 0, 100);
        $amount = (float)($parsed['amount']);
        $cat = $parsed['category'] ?? 'Other';
        $curr = $parsed['currency'] ?? 'USD';

        $id = db()->insert('trip_expenses', [
            'trip_id' => $tripId,
            'category' => $cat,
            'vendor' => $vendor,
            'amount' => $amount,
            'currency' => $curr,
            'date' => date('Y-m-d')
        ]);
        
        $newTotal = updateTripBudget($tripId);
        
        echo json_encode(['success' => true, 'expense' => $parsed, 'new_total' => $newTotal]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Could not extract expense from text.']);
    }
    exit;
}

if ($action === 'create') {
    $tripId = (int)($_POST['trip_id'] ?? 0);
    $amount = (float)($_POST['amount'] ?? 0);
    $vendor = trim($_POST['vendor'] ?? '');
    $cat = trim($_POST['category'] ?? 'Other');

    if ($tripId && $amount > 0 && $vendor) {
        db()->insert('trip_expenses', [
            'trip_id' => $tripId,
            'category' => $cat,
            'vendor' => $vendor,
            'amount' => $amount,
            'currency' => 'USD',
            'date' => date('Y-m-d')
        ]);
        $newTotal = updateTripBudget($tripId);
        echo json_encode(['success' => true, 'new_total' => $newTotal]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid data']);
    }
    exit;
}

if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    $tripId = (int)($_POST['trip_id'] ?? 0);
    if ($id && $tripId) {
        db()->query("DELETE FROM trip_expenses WHERE id = ? AND trip_id = ?", [$id, $tripId]);
        $newTotal = updateTripBudget($tripId);
        echo json_encode(['success' => true, 'new_total' => $newTotal]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Unknown action']);
