<?php

namespace Uitlaber\MoonShineTenantSwitcher\Contracts;

/**
 * Реализуется моделью пользователя админки, чтобы ограничить список
 * доступных ему тенантов. Если метод возвращает null — доступ ко всем
 * (например, супер-админ).
 */
interface HasTenantAccess
{
    /**
     * @return array<int|string>|null null = доступ ко всем тенантам
     */
    public function accessibleTenantIds(): ?array;
}
