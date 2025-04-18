<?php
function timeAgo($timestamp) {
    $now = time(); # time now
    $diff = $now - strtotime($timestamp); // diff between now and commint time

    if ($diff < 60) {
        return "Just now"; 
    } elseif ($diff < 3600) {
        return floor($diff / 60) . " minutes ago"; 
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . " hours ago"; 
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . " days ago";
        return date("Y-m-d", strtotime($timestamp)); 
    }
}
