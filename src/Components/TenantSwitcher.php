<?php

namespace Atlon\MoonShineTenantSwitcher\Components;

use Atlon\MoonShineTenantSwitcher\TenantManager;
use MoonShine\UI\Components\MoonShineComponent;

/**
 * Компонент для шапки MoonShine: выпадающий список тенантов с авто-сабмитом.
 * Добавляется в layout через topBarSlot(): [TenantSwitcher::make()].
 */
class TenantSwitcher extends MoonShineComponent
{
    protected string $view = 'tenant-switcher::switcher';

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        $manager = app(TenantManager::class);

        return [
            'options' => $manager->options(),
            'current' => $manager->currentId(),
            'action' => route('moonshine.tenant-switch'),
            'field' => 'tenant',
        ];
    }
}
