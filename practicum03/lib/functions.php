<?php
function formatDate(string $date): string {
    return date('d.m.Y', strtotime($date));
}

function calcRevenue(float $price, int $seats): float {
    return $price * $seats;
}