<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $tripId = (int)($_POST['trip_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (!$tripId || !$content) {
        echo json_encode(['success' => false, 'error' => 'Content is required.']);
        exit;
    }

    // Auto-generate title using Gemini if empty
    if ($title === '') {
        $system = "You are a succinct summarizer. Summarize the provided journal entry into a 3 to 4 word title. Do not use quotes or special characters.";
        $geminiTitle = callGemini($content, $system);
        if ($geminiTitle) {
            $title = trim($geminiTitle);
        } else {
            $title = "Journal Entry " . date('M j');
        }
    }

    // Handle Image
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('journal_') . '.' . $ext;
        $dest = __DIR__ . '/../uploads/journal/' . $filename;
        if (!is_dir(dirname($dest))) {
            mkdir(dirname($dest), 0777, true);
        }
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
            $image = $filename;
        }
    }

    $id = db()->insert('journal_entries', [
        'trip_id' => $tripId,
        'user_id' => currentUserId(),
        'title' => substr($title, 0, 255),
        'content' => $content,
        'image' => $image,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    if ($id) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
    exit;
}

if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    $tripId = (int)($_POST['trip_id'] ?? 0);
    if ($id && $tripId) {
        db()->query("DELETE FROM journal_entries WHERE id = ? AND trip_id = ? AND user_id = ?", [$id, $tripId, currentUserId()]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Unknown action']);
