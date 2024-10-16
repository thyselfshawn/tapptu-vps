<a href="{{ route('venues.edit', $item->slug) }}" class="btn btn-sm btn-warning mb-1">
    <i class="bi bi-pencil-square"></i>
</a>
<a href="{{ route('venues.show', $item->slug) }}" target="_blank" class="btn btn-sm btn-success mb-1">
    <i class="bi bi-eye"></i>
</a>
@if (auth()->user()->role == 'admin')
    <a href="{{ route('venues.destroy', $item->slug) }}" class="btn btn-sm btn-danger mb-1"
        onclick="event.preventDefault(); document.getElementById('delete-form-{{ $item->slug }}').submit();">
        <i class="bi bi-trash"></i>
    </a>

    <form id="delete-form-{{ $item->slug }}" action="{{ route('venues.destroy', $item->slug) }}" method="POST"
        class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endif
