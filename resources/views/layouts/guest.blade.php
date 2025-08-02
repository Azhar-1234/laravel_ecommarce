@auth
    <p>Welcome, {{ Auth::user()->name }}!</p>
@endauth

@guest
    <p>Please <a href="{{ route('login') }}">login</a> to access this page.</p>
@endguest