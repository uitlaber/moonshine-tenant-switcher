<?php

namespace Atlon\MoonShineTenantSwitcher\Concerns;

use Atlon\MoonShineTenantSwitcher\Scopes\TenantScope;
use Atlon\MoonShineTenantSwitcher\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Делает модель «принадлежащей тенанту»:
 *  - вешает глобальный скоуп TenantScope (фильтр по текущему тенанту в админке);
 *  - при создании автоматически проставляет внешний ключ тенанта.
 *
 * Модели с непрямой связью переопределяют applyTenantScope() и
 * shouldAutoAssignTenant().
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model): void {
            /** @var static $model */
            $manager = app(TenantManager::class);

            if (! $manager->isActive() || ! $model->shouldAutoAssignTenant()) {
                return;
            }

            $foreignKey = $model->getTenantForeignKey();
            $tenantId = $manager->currentId();

            if ($tenantId !== null && empty($model->getAttribute($foreignKey))) {
                $model->setAttribute($foreignKey, $tenantId);
            }
        });
    }

    public function getTenantForeignKey(): string
    {
        if (property_exists($this, 'tenantForeignKey') && $this->tenantForeignKey) {
            return $this->tenantForeignKey;
        }

        return app(TenantManager::class)->foreignKey();
    }

    public function shouldAutoAssignTenant(): bool
    {
        return true;
    }

    /**
     * Применить фильтр по тенанту. По умолчанию — прямой внешний ключ.
     * Переопределите для моделей со связью через relation.
     */
    public function applyTenantScope(Builder $builder, int|string $tenantId): void
    {
        $builder->where($this->qualifyColumn($this->getTenantForeignKey()), $tenantId);
    }
}
