<?php
// Simple JSON file storage helpers
function storage_get_path(string $name): string {
    $dir = __DIR__ . '/../data';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir . '/' . $name . '.json';
}

function storage_read(string $name): array {
    $path = storage_get_path($name);
    if (!file_exists($path)) {
        return [];
    }
    $json = file_get_contents($path);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function storage_write(string $name, array $data): bool {
    $path = storage_get_path($name);
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return file_put_contents($path, $json) !== false;
}

function storage_find(string $name, callable $predicate): ?array {
    $items = storage_read($name);
    foreach ($items as $i) {
        if ($predicate($i)) {
            return $i;
        }
    }
    return null;
}

function storage_upsert(string $name, array $item, string $idField = 'id'): array {
    $items = storage_read($name);
    if (empty($item[$idField])) {
        // create
        $item[$idField] = uniqid();
        $items[] = $item;
    } else {
        // update
        $found = false;
        foreach ($items as $k => $v) {
            if (isset($v[$idField]) && $v[$idField] === $item[$idField]) {
                $items[$k] = $item;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $items[] = $item;
        }
    }
    storage_write($name, $items);
    return $item;
}

function storage_delete(string $name, string $id, string $idField = 'id'): bool {
    $items = storage_read($name);
    $new = [];
    $deleted = false;
    foreach ($items as $v) {
        if (isset($v[$idField]) && $v[$idField] === $id) {
            $deleted = true;
            continue;
        }
        $new[] = $v;
    }
    storage_write($name, $new);
    return $deleted;
}
