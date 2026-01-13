<?php

try {
    $db = new SQLite3('database/database.sqlite');
    $results = $db->query('SELECT * FROM advertisements');
    $ads = [];
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $ads[] = $row;
    }
    
    echo "Total advertisements: " . count($ads) . "\n\n";
    
    foreach ($ads as $ad) {
        echo "ID: " . $ad['id'] . "\n";
        echo "Title: " . $ad['title'] . "\n";
        echo "Media Type: " . $ad['media_type'] . "\n";
        echo "Media Path: " . $ad['media_path'] . "\n";
        echo "Link URL: " . ($ad['link_url'] ?? 'null') . "\n";
        echo "Is Active: " . ($ad['is_active'] ? 'Yes' : 'No') . "\n";
        echo "Sort Order: " . $ad['sort_order'] . "\n";
        echo "---\n";
    }
    
    if (empty($ads)) {
        echo "No advertisements found in the database.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}