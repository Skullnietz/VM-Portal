@if($unreadNotifications->isEmpty())
    <div class="text-center p-5 text-muted">
        <i class="fas fa-bell-slash fa-3x mb-3"></i>
        <h5>¡Estás al día!</h5>
        <p>No tienes notificaciones nuevas.</p>
    </div>
@else
    <div class="list-group list-group-flush">
        @foreach($unreadNotifications as $notification)
            <div class="list-group-item list-group-item-action flex-column align-items-start p-3 notification-item">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1 text-primary">
                        <i class="fas fa-info-circle mr-2"></i> {{ $notification->Txt_Nombre ?? 'Notificación' }}
                    </h5>
                    <small class="text-muted">
                        <i class="far fa-clock"></i>
                        {{ \Carbon\Carbon::parse($notification->Fecha)->diffForHumans() }}
                    </small>
                </div>
                <p class="mb-2 text-dark">{{ $notification->description }}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-secondary">
                        {{ \Carbon\Carbon::parse($notification->Fecha)->format('d M Y, h:i A') }}
                    </small>
                    <button class="btn btn-sm btn-outline-success mark-as-read" data-id="{{ $notification->id }}"
                        title="Marcar como leída">
                        <i class="fas fa-check"></i> Marcar como leída
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif