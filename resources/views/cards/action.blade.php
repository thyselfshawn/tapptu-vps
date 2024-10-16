@if (auth()->user()->role == 'admin')
    <a href="{{ route('cards.edit', $item->id) }}" class="btn btn-sm btn-warning mb-1"><i
            class="bi bi-pencil-square"></i></a>
@endif
<a href="{{ route('guest.qr_card', ['card' => $item->uuid]) }}" target="_blank" class="btn btn-sm btn-success mb-1"><i
        class="bi bi-eye"></i></a>
@if ($item->firstVenue())
    <a href="{{ route('guest.view_card', ['venue' => $item->firstVenue()->slug, 'card' => $item->uuid]) }}"
        target="_blank" class="btn btn-sm btn-primary mb-1"><i class="bi bi-link"></i></a>
@else
    <a href="{{ route('guest.check_card', ['card' => $item->uuid]) }}" target="_blank"
        class="btn btn-sm btn-primary mb-1"><i class="bi bi-link"></i></a>
@endif
