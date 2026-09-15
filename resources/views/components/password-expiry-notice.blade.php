{{--
    Advance warning shown on every page while a password is close to expiring.
    $password_expiry_notice is shared by the ForcePasswordChange middleware, so
    the banner disappears on its own once the password is changed.
--}}
@if(!empty($password_expiry_notice))
    <div class="alert alert-warning d-flex align-items-center mb-5" role="alert">
        <i class="la la-clock fs-2 me-3"></i>
        <span>{{ $password_expiry_notice }}</span>
    </div>
@endif
