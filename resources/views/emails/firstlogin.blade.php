@component('mail::message')
Dear {{ $user->member->firstname }} {{ $user->member->lastname }}

# First Login Verification

To verify your access, please enter the following code into the "Verification Code" box of the password change form.

{{ $user->resetcode }}    

If you were not expecting this email, please delete it and inform your organisation.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
