<?php

namespace Uitlaber\MoonShineTenantSwitcher\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Uitlaber\MoonShineTenantSwitcher\TenantManager;

/**
 * Глобальный скоуп: фильтрует модель по текущему тенанту, но только когда
 * скоуп активен (аутентифицирован admin-guard). Сам способ фильтрации
 * делегируется модели через applyTenantScope() — это позволяет моделям
 * с непрямой связью (например, через relation) переопределить условие.
 */
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $manager = app(TenantManager::class);

        if (! $manager->isActive()) {
            return;
        }

        $tenantId = $manager->currentId();

        if ($tenantId === null) {
            return;
        }

        /** @phpstan-ignore-next-line модель использует трейт BelongsToTenant */
        $model->applyTenantScope($builder, $tenantId);
    }
}
