@php
    $isPassword = $mode === 'password';
@endphp

<section class="cl-auth-intro">
    <p class="cl-auth-intro__eyebrow">{{ __('admin.auth.secure_area') }}</p>
    <h1 class="cl-auth-intro__title">
        {{ $isPassword ? __('admin.auth.password_title') : __('admin.auth.login_title') }}
    </h1>
    <p class="cl-auth-intro__body">
        {{ $isPassword ? __('admin.auth.password_body') : __('admin.auth.login_body') }}
    </p>
</section>
