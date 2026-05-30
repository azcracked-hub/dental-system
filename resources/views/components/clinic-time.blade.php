@props(['time'])

{{ \App\Support\ClinicTime::to12Hour($time) }}
