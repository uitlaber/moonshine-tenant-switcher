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
        $home = moonshineConfig()->getHomeUrl() ?: route(moonshineConfig()->getHomeRoute());
        $referer = (string) $request->headers->get('referer', '');

        if ($referer === '') {
            return $home;
        }

        $parts = parse_url($referer);
        parse_str($parts['query'] ?? '', $query);

        $itemBound = isset($query['resourceItem']) || isset($query['resourceItems']);

        if (! $itemBound) {
            return $referer;
        }

        if (preg_match('~/resource/([^/?]+)~', $parts['path'] ?? '', $matches)) {
            $resource = moonshine()->getResources()->findByUri($matches[1]);

            if ($resource !== null && method_exists($resource, 'getIndexPageUrl')) {
                return $resource->getIndexPageUrl();
            }
        }

        return $home;
    }
}
