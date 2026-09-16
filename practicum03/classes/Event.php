<?php
class Event {
    protected string $title;
    protected string $date;
    protected string $venue;

    public function __construct(string $title, string $date, string $venue) {
        $this->title = $title;
        $this->date = $date;
        $this->venue = $venue;
    }

    public function getInfo(): string {
        return "{$this->title} (Дата: " . formatDate($this->date) . ", Місце: {$this->venue})";
    }
    
    public function getVenue(): string {
        return $this->venue;
    }
}