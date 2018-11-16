@yield('content')

{{ __('email.email_company_franchise') }}
© 2014 - {{ Carbon\Carbon::now()->year }}, {{ config('invoice.accounts.default.company') }} {{ __('app.web') }}

