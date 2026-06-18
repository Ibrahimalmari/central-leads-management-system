@php
    $items = $items ?? [];
@endphp

<div class="cl-extra-data" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    @if (empty($items))
        <div class="cl-extra-data__empty">
            {{ __('admin.messages.no_extra_data') }}
        </div>
    @else
        <div class="cl-extra-data__grid">
            @foreach ($items as $item)
                <div @class([
                    'cl-extra-data__item',
                    'cl-extra-data__item--wide' => in_array($item['type'], ['long', 'url'], true),
                ])>
                    <div class="cl-extra-data__label">
                        <span>{{ $item['label'] }}</span>
                        @if ($item['key'] !== $item['label'])
                            <code>{{ $item['key'] }}</code>
                        @endif
                    </div>

                    <div class="cl-extra-data__value">
                        @if ($item['type'] === 'url')
                            <a href="{{ $item['value'] }}" target="_blank" rel="noopener noreferrer">
                                {{ $item['value'] }}
                            </a>
                        @elseif ($item['type'] === 'email')
                            <a href="mailto:{{ $item['value'] }}">{{ $item['value'] }}</a>
                        @elseif ($item['type'] === 'empty')
                            <span class="cl-extra-data__muted">-</span>
                        @else
                            {{ $item['value'] }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
