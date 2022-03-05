@component('mail::message')
Dear {{ $user->member->firstname }} {{ $user->member->lastname }}

# Password Reset

Your password for {{ config('app.name') }} at {{ env('APP_URL') }} has been reset successfully. You should now be able to log in with your last name and a code sent to this email address.

If you were not expecting this email, please contact your organisation.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
