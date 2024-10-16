<a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning mb-1">
    <i class="bi bi-pencil-square"></i>
</a>

<a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-success mb-1">
    <i class="bi bi-eye"></i>
</a>

<a href="{{ route('users.destroy', $user->id) }}" class="btn btn-sm btn-primary mb-1"
    onclick="event.preventDefault(); document.getElementById('delete-form-{{ $user->id }}').submit();">
    <i class="bi bi-trash"></i>
</a>

<form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST"
    class="d-none">
    @csrf
    @method('DELETE')
</form>
