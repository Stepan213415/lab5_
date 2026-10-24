<?php
// classes/CapacitorType.php — довідникова сутність «Тип конденсатора»
class CapacitorType
{
    public function __construct(
        private int $id,
        private string $name
    ) {}

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }

    public function __toString(): string
    {
        return $this->name;
    }
}
