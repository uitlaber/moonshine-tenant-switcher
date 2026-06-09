<?php

namespace Atlon\MoonShineTenantSwitcher;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Центральная точка работы с текущим тенантом (проектом).
 *
 * Регистрируется синглтоном, поэтому резолв текущего id и список тенантов
 * мемоизируются в пределах запроса (важно: currentId() дёргается на каждый
 * запрос к скоупящейся модели).
 */
class TenantManager
{
    /** @var Collection<int, Model>|null */
    private ?Collection $memoTenants = null;

    private bool $currentIdResolved = false;

    private int|string|null $memoCurrentId = null;

    public function config(string $key, mixed $default = null): mixed
    {
        return config("tenant-switcher.$key", $default);
    }

    /**
     * Активен ли скоуп: только когда аутентифицирован указанный guard
     * (по умолчанию moonshine). На публичном фронте и в консоли — false.
     */
    public function isActive(): bool
    {
        $model = $this->config('tenant_model');

        if (! $model) {
            return false;
        }

        return Auth::guard($this->config('guard', 'moonshine'))->check();
    }

    public function foreignKey(): string
    {
        return (string) $this->config('foreign_key', 'tenant_id');
    }

    public function sessionKey(): string
    {
        return (string) $this->config('session_key', 'moonshine_tenant_id');
    }

    public function labelColumn(): string
    {
        return (string) $this->config('label_column', 'name');
    }

    /**
     * @return class-string<Model>|null
     */
    public function tenantModel(): ?string
    {
        return $this->config('tenant_model');
    }

    /**
     * Список доступных тенантов (мемоизирован на запрос).
     *
     * @return Collection<int, Model>
     */
    public function tenants(): Collection
    {
        if ($this->memoTenants !== null) {
            return $this->memoTenants;
        }

        $model = $this->tenantModel();

        if (! $model) {
            return $this->memoTenants = new Collection;
        }

        $query = $model::query();

        if ($active = $this->config('active_column')) {
            $query->where($active, true);
        }

        $query->orderBy($this->config('order_column', 'id'));

        return $this->memoTenants = $query->get();
    }

    /**
     * Опции для выпадающего списка: [id => label].
     *
     * @return array<int|string, string>
     */
    public function options(): array
    {
        return $this->tenants()
            ->mapWithKeys(fn (Model $t) => [$t->getKey() => (string) $t->{$this->labelColumn()}])
            ->all();
    }

    /**
     * Текущий id тенанта. Если в сессии пусто — берётся первый доступный
     * (и сохраняется в сессию, т.к. режим «всегда ровно один тенант»).
     */
    public function currentId(): int|string|null
    {
        if ($this->currentIdResolved) {
            return $this->memoCurrentId;
        }

        $this->currentIdResolved = true;

        $id = session($this->sessionKey());

        if ($id !== null && $this->isValidId($id)) {
            return $this->memoCurrentId = $id;
        }

        $first = $this->tenants()->first();

        if ($first) {
            $this->setCurrent($first->getKey());

            return $this->memoCurrentId = $first->getKey();
        }

        return $this->memoCurrentId = null;
    }

    public function current(): ?Model
    {
        $id = $this->currentId();

        if ($id === null) {
            return null;
        }

        return $this->tenants()->first(fn (Model $t) => (string) $t->getKey() === (string) $id);
    }

    public function setCurrent(int|string $id): void
    {
        session([$this->sessionKey() => $id]);
        $this->currentIdResolved = true;
        $this->memoCurrentId = $id;
    }

    public function isValidId(int|string|null $id): bool
    {
        if ($id === null) {
            return false;
        }

        return $this->tenants()->contains(fn (Model $t) => (string) $t->getKey() === (string) $id);
    }
}
