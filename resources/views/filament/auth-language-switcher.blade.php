<nav class="cl-auth-language" aria-label="Language switcher">
    <a
        @class(['is-active' => app()->getLocale() === 'ar'])
        href="{{ route('language.switch', ['locale' => 'ar']) }}"
    >
        العربية
    </a>
    <a
        @class(['is-active' => app()->getLocale() === 'en'])
        href="{{ route('language.switch', ['locale' => 'en']) }}"
    >
        English
    </a>
</nav>
