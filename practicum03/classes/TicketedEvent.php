<?php
require_once 'Event.php';

class TicketedEvent extends Event {
    private float $price;
    private int $seatsLeft;

    public function __construct(string $title, string $date, string $venue, float $price, int $seatsLeft) {
        parent::__construct($title, $date, $venue);
        $this->price = $price;
        $this->seatsLeft = $seatsLeft;
    }

    public function getInfo(): string {
        $baseInfo = parent::getInfo();
        $revenue = calcRevenue($this->price, $this->seatsLeft);
        return "{$baseInfo} — Ціна: {$this->price} ₴, Залишилось місць: {$this->seatsLeft}, Потенційний дохід: {$revenue} ₴";
    }
    
    public function getSeatsLeft(): int {
        return $this->seatsLeft;
    }
}