<?php
class TicketOffice {
    private array $events = [];

    public function addEvent(Event $event): void {
        $this->events[] = $event;
    }

    public function findByVenue(string $venue): array {
        $result = [];
        foreach ($this->events as $event) {
            if ($event->getVenue() === $venue) {
                $result[] = $event;
            }
        }
        return $result;
    }

    public function soldOutList(): array {
        $result = [];
        foreach ($this->events as $event) {
            if ($event instanceof TicketedEvent && $event->getSeatsLeft() === 0) {
                $result[] = $event;
            }
        }
        return $result;
    }
    
    public function getAllEvents(): array {
        return $this->events;
    }
}