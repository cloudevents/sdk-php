<?php

namespace CloudEvents;

interface CloudEventInterface
{
    public function getSpecVersion(): string;
}
