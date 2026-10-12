<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$status = $_POST['status'] ?? '';

if (!$id || !in_array($status, allowedStatuses(), true)) {
    http_response_code(400);
    exit('Invalid ticket ID or status.');
}

$tickets = loadTickets();

if (!findTicketById($tickets, $id)) {
    http_response_code(404);
    exit('Ticket not found.');
}

if (!updateTicketStatus($tickets, $id, $status)) {
    http_response_code(500);
    exit('Could not update ticket.');
}

if (!saveTickets($tickets)) {
    http_response_code(500);
    exit('Could not save ticket status.');
}

header('Location: detail.php?id=' . $id);
exit;

http_response_code(501);
echo 'Status update not implemented yet.';
