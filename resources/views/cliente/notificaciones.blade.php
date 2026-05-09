@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Notificaciones'))

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <a href="#" onclick="goBack()" class="btn btn-outline-secondary btn-sm rounded-circle mr-2">
                    <i class="fas fa-arrow-left"></i>
                </a>
                {{ __('Mis Notificaciones') }}
            </h1>
        </div>
        <div class="col-sm-6 text-right">
            <button id="mark-all-read" class="btn btn-success btn-sm shadow-sm">
                <i class="fas fa-check-double mr-1"></i> Marcar todo como leído
            </button>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow card-primary card-outline">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-bell mr-1"></i>
                        Últimas Notificaciones
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-warning" id="notification-count">Actualizando...</span>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body p-0" id="notifications-container">
                    @include('cliente.partials.notificaciones_list')
                </div>

                <div class="card-footer text-center text-muted">
                    <small>Las notificaciones se actualizan automáticamente.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .notification-item {
        transition: background-color 0.2s;
        border-left: 4px solid transparent;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
        border-left: 4px solid #007bff;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        // Function to reload the notification list
        function loadNotifications() {
            $.ajax({
                url: "{{ route('notifications.renderList') }}",
                type: "GET",
                success: function (data) {
                    $('#notifications-container').html(data);
                    updateCount();
                },
                error: function () {
                    console.log("Error actualizando notificaciones.");
                }
            });
        }

        function updateCount() {
            // Simple count update based on list items, or could be passed from backend
            let count = $('#notifications-container .list-group-item').length;
            if (count > 0) {
                $('#notification-count').text(count).removeClass('badge-secondary').addClass('badge-warning').show();
                $('#mark-all-read').show();
            } else {
                $('#notification-count').hide();
                $('#mark-all-read').hide();
            }
        }

        // Auto-refresh every 10 seconds
        setInterval(loadNotifications, 10000);
        updateCount(); // Initial check

        // Mark single as read
        $(document).on('click', '.mark-as-read', function () {
            let notificationId = $(this).data("id");
            let button = $(this);
            let item = button.closest(".list-group-item");
            
            console.log("Marking notification " + notificationId + " as read.");

            // Construct URL safely
            // generated route: /mark-notification-as-read/PLACEHOLDER
            // We replace PLACEHOLDER with the actual ID
            let url = "{{ route('markNotificationAsRead', 'PLACEHOLDER') }}";
            url = url.replace('PLACEHOLDER', notificationId);

            // Optimistic UI update
            item.fadeOut("fast", function () {
                $(this).remove();
                updateCount();
            });

            $.ajax({
                url: url,
                type: "GET",
                error: function () {
                    console.error("Failed to mark notification as read");
                    // Optionally revert or alert
                    // alert("No se pudo marcar como leída.");
                    loadNotifications();
                }
            });
        });

        // Mark ALL as read
        $('#mark-all-read').click(function () {
            console.log("Mark all as read clicked.");
            if (typeof Swal === 'undefined') {
                 if(confirm("¿Estás seguro de marcar todas como leídas?")) {
                     markAllReadRequest();
                 }
                 return;
            }

            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esto marcará todas tus notificaciones como leídas.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, marcar todo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    markAllReadRequest();
                }
            })
        });

        function markAllReadRequest() {
            $.ajax({
                url: "{{ route('notifications.markAllRead') }}",
                type: "POST", 
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    if (response.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire(
                                '¡Listo!',
                           'Todas las notificaciones han sido marcadas como leídas.',
                                'success'
                            );
                        } else {
                            alert('Todas las notificaciones han sido marcadas como leídas.');
                        }
                        loadNotifications(); // Refresh to show empty state
                    }
                },
                error: function () {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire(
                            'Error',
                            'Hubo un problema al procesar la solicitud.',
                            'error'
                        );
                    } else {
                        alert('Hubo un problema al procesar la solicitud.');
                    }
                }
            });
        }
    });

    function goBack() {
        window.history.back();
    }
</script>
@stop