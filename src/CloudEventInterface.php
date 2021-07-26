<?php

declare(strict_types=1);

namespace CloudEvents;

interface CloudEventInterface
{
    public function getSpecVersion(): string;
}
