<?php

const DATA_FILE = __DIR__ . '/../data/tickets.json';
const UPLOAD_DIR = __DIR__ . '/../uploads/';

function allowedCategories(): array
{
    return ['technical', 'billing', 'account', 'other'];
}

function allowedStatuses(): array
{
    return ['Open', 'In Progress', 'Resolved'];
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function loadTickets(): array
{
    if (!file_exists(DATA_FILE)) {
        return [];
    }

    $json = file_get_contents(DATA_FILE);
    $tickets = json_decode($json, true);

    return is_array($tickets) ? $tickets : [];
}

function saveTickets(array $tickets): bool
{
    $json = json_encode($tickets, JSON_PRETTY_PRINT);
    if ($json === false) {
    return false;
    }
    return file_put_contents(DATA_FILE, $json) !== false;
}

function findTicketById(array $tickets, int $id): ?array
{
    foreach ($tickets as $ticket) {
        if ((int)$ticket['id'] === $id) {
            return $ticket;
        }
    }

    return null;
}

function nextTicketId(array $tickets): int
{
    if (empty($tickets)) {
        return 1;
    }
    $ids = array_column($tickets, 'id');
    return max($ids) + 1;
}

function filterTickets(
    array $tickets,
    string $query = '',
    string $status = '',
    string $category = ''
): array {
    if ($query !== '') {
        $tickets = array_filter($tickets, function ($ticket) use ($query) {
            return stripos($ticket['title'], $query) !== false ||
                stripos($ticket['description'], $query) !== false;
        });
    }

    if ($status !== '') {
        $tickets = array_filter($tickets, function ($ticket) use ($status) {
            return $ticket['status'] === $status;
        });
    }

    if ($category !== '') {
        $tickets = array_filter($tickets, function ($ticket) use ($category) {
            return $ticket['category'] === $category;
        });
    }

    return $tickets;
}

function updateTicketStatus(array &$tickets, int $id, string $newStatus): bool
{
    foreach ($tickets as &$ticket) {
        if ((int)$ticket['id'] === $id) {
            $ticket['status'] = $newStatus;
            return true;
        }
    }

    return false;
}
