<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />

    <title>{{ config('app.name') }} - Authorization</title>

    <link rel="manifest" href="/manifest.json" />
    <meta name="theme-color" content="#ffffff" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite(['resources/js/passport.ts', 'resources/css/app.css'])
</head>
<body>
<div
    id="app"
    data-csrf="{{ csrf_token() }}"
    data-client="{{ json_encode($client) }}"
    data-scopes="{{ json_encode([...$scopes, ['description' => 'Access your profile']]) }}"
    data-auth-token="{{ $authToken }}"
    data-state="{{ $request->state }}"
    data-approve-endpoint="{{ route('passport.authorizations.approve') }}"
    data-deny-endpoint="{{ route('passport.authorizations.deny') }}"
><div>
</body>
</html>
