<?php
namespace Deljdlx\Interfaces;

interface Cache
{
    public function get(string $key): string|false;
    public function set(string $key, mixed $value): void;
}