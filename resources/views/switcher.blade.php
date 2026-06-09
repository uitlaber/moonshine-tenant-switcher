@if (! empty($options))
    @php($currentLabel = $options[$current] ?? null)

    <form
        method="POST"
        action="{{ $action }}"
        class="mts"
        x-data="{ open: false }"
        @click.outside="open = false"
        @keydown.escape.window="open = false"
    >
        @csrf

        <button
            type="button"
            class="mts__trigger"
            :class="{ 'mts__trigger--open': open }"
            @click="open = ! open"
            aria-haspopup="listbox"
            :aria-expanded="open"
            title="Текущий проект"
        >
            <span class="mts__badge">{{ mb_strtoupper(mb_substr((string) ($currentLabel ?? '·'), 0, 1)) }}</span>

            <span class="mts__text">
                <span class="mts__caption">Проект</span>
                <span class="mts__name">{{ $currentLabel ?? 'Выбрать сайт' }}</span>
            </span>

            <svg class="mts__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
            </svg>
        </button>

        <div
            class="mts__panel"
            role="listbox"
            x-show="open"
            x-transition.origin.top.duration.150ms
            x-cloak
        >
            @foreach ($options as $id => $label)
                @php($isCurrent = (string) $id === (string) $current)
                <button
                    type="submit"
                    name="{{ $field }}"
                    value="{{ $id }}"
                    role="option"
                    aria-selected="{{ $isCurrent ? 'true' : 'false' }}"
                    class="mts__item {{ $isCurrent ? 'mts__item--active' : '' }}"
                    @disabled($isCurrent)
                >
                    <span class="mts__badge mts__badge--sm">{{ mb_strtoupper(mb_substr((string) $label, 0, 1)) }}</span>
                    <span class="mts__item-name">{{ $label }}</span>

                    @if ($isCurrent)
                        <svg class="mts__check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                        </svg>
                    @endif
                </button>
            @endforeach
        </div>
    </form>

    <style>
        [x-cloak] { display: none !important; }

        .mts {
            position: relative;
            width: 100%;
            margin-bottom: .5rem;
        }

        .mts__trigger {
            display: flex;
            align-items: center;
            gap: .6rem;
            width: 100%;
            padding: .45rem .55rem;
            border: 1px solid var(--color-base-stroke, rgba(127, 127, 127, .3));
            border-radius: var(--radius-lg, .65rem);
            background: transparent;
            color: var(--color-base-text, inherit);
            cursor: pointer;
            text-align: left;
            transition: background-color .15s, border-color .15s;
        }

        .mts__trigger:hover,
        .mts__trigger--open {
            background: color-mix(in srgb, var(--color-base-text, #888) 7%, transparent);
            border-color: color-mix(in srgb, var(--color-primary, #6366f1) 45%, var(--color-base-stroke, #ccc));
        }

        .mts__badge {
            flex: none;
            display: grid;
            place-items: center;
            width: 1.85rem;
            height: 1.85rem;
            border-radius: .5rem;
            font-size: .8rem;
            font-weight: 700;
            line-height: 1;
            color: var(--color-primary, #6366f1);
            background: color-mix(in srgb, var(--color-primary, #6366f1) 16%, transparent);
        }

        .mts__badge--sm {
            width: 1.55rem;
            height: 1.55rem;
            font-size: .72rem;
            border-radius: .4rem;
        }

        .mts__text {
            display: flex;
            flex-direction: column;
            min-width: 0;
            line-height: 1.15;
            flex: 1 1 auto;
        }

        .mts__caption {
            font-size: .62rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            opacity: .55;
        }

        .mts__name {
            font-size: .9rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mts__chevron {
            flex: none;
            width: 1rem;
            height: 1rem;
            opacity: .6;
            transition: transform .2s;
        }

        .mts__trigger--open .mts__chevron {
            transform: rotate(180deg);
        }

        .mts__panel {
            position: absolute;
            z-index: 50;
            top: calc(100% + .35rem);
            left: 0;
            right: 0;
            padding: .3rem;
            border: 1px solid var(--color-base-stroke, rgba(127, 127, 127, .3));
            border-radius: var(--radius-lg, .65rem);
            background: var(--color-base, #fff);
            color: var(--color-base-text, inherit);
            box-shadow: 0 10px 30px -12px rgba(0, 0, 0, .35);
        }

        .mts__item {
            display: flex;
            align-items: center;
            gap: .55rem;
            width: 100%;
            padding: .45rem .5rem;
            border: 0;
            border-radius: .5rem;
            background: transparent;
            color: inherit;
            font-size: .88rem;
            text-align: left;
            cursor: pointer;
            transition: background-color .12s;
        }

        .mts__item:not(.mts__item--active):hover {
            background: color-mix(in srgb, var(--color-base-text, #888) 8%, transparent);
        }

        .mts__item--active {
            background: color-mix(in srgb, var(--color-primary, #6366f1) 13%, transparent);
            color: var(--color-primary, #6366f1);
            cursor: default;
        }

        .mts__item-name {
            flex: 1 1 auto;
            min-width: 0;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mts__check {
            flex: none;
            width: 1rem;
            height: 1rem;
        }
    </style>
@endif
