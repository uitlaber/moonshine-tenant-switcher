@if (! empty($options))
    <form
        method="POST"
        action="{{ $action }}"
        x-data
        @change="$el.submit()"
        class="ms-tenant-switcher"
        title="Текущий проект"
        style="display:flex;align-items:center;gap:.4rem;"
    >
        @csrf

        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.6" stroke="currentColor"
             style="width:1.1rem;height:1.1rem;opacity:.7;flex:none;">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.95 8.95 0 0 0 4.5-1.207M12 21a8.95 8.95 0 0 1-4.5-1.207M3.6 9h16.8M3.6 15h16.8M12 3a13.4 13.4 0 0 0 0 18 13.4 13.4 0 0 0 0-18Z" />
        </svg>

        <select
            name="{{ $field }}"
            aria-label="Текущий проект"
            style="appearance:auto;border:1px solid rgba(127,127,127,.35);border-radius:.5rem;
                   padding:.35rem .55rem;font-size:.85rem;line-height:1.2;background:transparent;
                   color:inherit;cursor:pointer;width:100%;max-width:14rem;"
        >
            @foreach ($options as $id => $label)
                <option value="{{ $id }}" @selected((string) $id === (string) $current)>{{ $label }}</option>
            @endforeach
        </select>
    </form>
@endif
