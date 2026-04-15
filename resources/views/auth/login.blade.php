<h2>Login</h2>

@if(session('error'))
    <p>{{ session('error') }}</p>
@endif

<form method="POST" action="/login">
    @csrf
    <input type="email" name="email" placeholder="Email"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <button type="submit">Login</button>
</form>

<a href="/register">Register</a>