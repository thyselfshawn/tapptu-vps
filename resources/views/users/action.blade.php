<div class="btn-group" role="group" aria-label="User Actions">
    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning" title="Edit">
        <i class="bi bi-pencil-square"></i>
    </a>
    <a href="{{ route('users.show', $user->id) }}" class="btn btn-success" title="View">
        <i class="bi bi-eye"></i>
    </a>
    <a href="{{ route('users.destroy', $user->id) }}" class="btn btn-danger delete-btn"
        onclick="event.preventDefault(); document.getElementById('delete-form-{{ $user->id }}').submit();">
        <i class="bi bi-trash"></i>
    </a>

    <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST"
        class="d-none">
        @csrf
        @method('DELETE')
    </form>

</div>
