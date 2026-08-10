<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body style="font-family:Arial,sans-serif;line-height:1.7">

<h2>Nauja užklausa</h2>

<hr>

<p>
<b>Vardas:</b><br>
{{ $data['name'] }}
</p>

<p>
<b>El. paštas:</b><br>
{{ $data['email'] }}
</p>

<p>
<b>Telefonas:</b><br>
{{ $data['phone'] ?? '-' }}
</p>

<p>
<b>Įmonė:</b><br>
{{ $data['company'] ?? '-' }}
</p>

<p>
<b>Paslauga:</b><br>
{{ $data['service'] }}
</p>

<p>
<b>Žinutė:</b>
</p>

<p>
{{ $data['message'] }}
</p>

</body>

</html>