<?php

namespace Uitlaber\MoonShineTenantSwitcher\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Uitlaber\MoonShineTenantSwitcher\TenantManager;

class SwitchTenantController extends Controller
{
    public function __invoke(Request $request, TenantManager $tenants): RedirectResponse
    {
        $id = $request->input('tenant');

        if ($tenants->isValidId($id)) {
            $tenants->setCurrent($id);
        }

        return redirect($this->targetUrl($request));
    }

    /**
     * Куда вернуть после смены тенанта.
     *
     * Страница конкретной записи (форма-редактирование / детально, `?resourceItem=…`)
     * принадлежит прежнему тенанту — после смены запись уходит из скоупа и страница
     * отдаёт 404. В этом случае уводим на список того же ресурса; если ресурс не
     * определить — на домашнюю. В остальных случаях возвращаемся на прежний URL.
     */
    private function targetUrl(Request $request): string
    {
        $home = config('moonshine.home_url') ?: route((string) config('moonshine.home_route', 'moonshine.index'));
        $referer = (string) $request->headers->get('referer', '');

        if ($referer === '') {
            return $home;
        }

        $parts = parse_url($referer);
        parse_str($parts['query'] ?? '', $query);

        // Сегменты пути: /admin/resource/{resourceUri}/{pageUri}/{resourceItem?}
        $segments = array_values(array_filter(explode('/', $parts['path'] ?? ''), static fn ($s): bool => $s !== ''));
        $resourcePos = array_search('resource', $segments, true);

        if ($resourcePos === false) {
            return $referer;
        }

        $resourceUri = $segments[$resourcePos + 1] ?? null;
        // Третий сегмент после resource — это id записи (или «create» для новой).
        $itemSegment = $segments[$resourcePos + 3] ?? null;

        $itemBound = isset($query['resourceItem'])
            || isset($query['resourceItems'])
            || ($itemSegment !== null && $itemSegment !== 'create');

        if (! $itemBound) {
            return $referer;
        }

        if ($resourceUri !== null) {
            $resource = moonshine()->getResources()->findByUri($resourceUri);

            if ($resource !== null && method_exists($resource, 'getIndexPageUrl')) {
                return $resource->getIndexPageUrl();
            }
        }

        return $home;
    }
}
