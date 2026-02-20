@component('mail::message')
# ¡Hola {{ $owner->name }}!

Tu usuario del portal de tutores ya está disponible.

- **Usuario (correo):** {{ $owner->email }}
- **Contraseña temporal:** {{ $plainPassword }}

Por seguridad, cambia esta contraseña después de iniciar sesión.

Gracias,<br>
{{ config('app.name') }}
@endcomponent
