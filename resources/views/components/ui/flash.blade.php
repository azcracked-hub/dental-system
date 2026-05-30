@if (session('success'))
    <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
@endif

@if (session('error'))
    <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
@endif

@if (session('warning'))
    <x-ui.alert type="warning">{{ session('warning') }}</x-ui.alert>
@endif

@if (session('info'))
    <x-ui.alert type="info">{{ session('info') }}</x-ui.alert>
@endif
