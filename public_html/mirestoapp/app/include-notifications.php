<!-- Sistema de Notificaciones -->
<style>
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: linear-gradient(135deg, #ea5455 0%, #f093fb 100%);
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(234, 84, 85, 0.4);
        animation: pulse 2s infinite;
    }

    .notification-item {
        padding: 1rem;
        border-bottom: 1px solid #e7eaf3;
        transition: all 0.2s ease;
    }

    .notification-item:hover {
        background: rgba(102, 126, 234, 0.05);
        cursor: pointer;
    }

    .notification-item.unread {
        background: rgba(102, 126, 234, 0.08);
        border-left: 3px solid #667eea;
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .notification-dropdown {
        min-width: 350px;
        max-height: 500px;
        overflow-y: auto;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        border-radius: 0.5rem;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }
</style>

<li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
    <a
        class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
        href="javascript:void(0);"
        data-bs-toggle="dropdown"
        data-bs-auto-close="outside"
        aria-expanded="false">
        <i class="ri-notification-2-line ri-22px"></i>
        <span class="notification-badge" id="notificationCount">3</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end notification-dropdown">
        <li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
                <h6 class="mb-0 me-auto">Notificaciones</h6>
                <div class="dropdown-notifications-actions">
                    <a href="javascript:void(0)" class="dropdown-notifications-read">
                        <span class="badge badge-dot"></span>
                        <span class="text-muted small">Marcar todas como leídas</span>
                    </a>
                </div>
            </div>
        </li>
        <li class="dropdown-notifications-list scrollable-container" id="notificationsList">
            <!-- Las notificaciones se cargarán aquí dinámicamente -->
            <div class="notification-item unread">
                <div class="d-flex gap-3">
                    <div class="notification-icon bg-label-success">
                        <i class="ri-calendar-check-line ri-lg"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Nuevo Turno Confirmado</h6>
                        <small class="text-muted">
                            El paciente Juan Pérez confirmó su turno para mañana a las 10:00
                        </small>
                        <p class="mb-0 mt-1">
                            <small class="text-primary">Hace 5 minutos</small>
                        </p>
                    </div>
                </div>
            </div>

            <div class="notification-item unread">
                <div class="d-flex gap-3">
                    <div class="notification-icon bg-label-warning">
                        <i class="ri-time-line ri-lg"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Turno Próximo</h6>
                        <small class="text-muted">
                            Recordatorio: Tiene un turno en 30 minutos
                        </small>
                        <p class="mb-0 mt-1">
                            <small class="text-warning">Hace 10 minutos</small>
                        </p>
                    </div>
                </div>
            </div>

            <div class="notification-item">
                <div class="d-flex gap-3">
                    <div class="notification-icon bg-label-info">
                        <i class="ri-user-add-line ri-lg"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Nuevo Paciente Registrado</h6>
                        <small class="text-muted">
                            María González se ha registrado en el sistema
                        </small>
                        <p class="mb-0 mt-1">
                            <small class="text-muted">Hace 1 hora</small>
                        </p>
                    </div>
                </div>
            </div>

            <div class="notification-item">
                <div class="d-flex gap-3">
                    <div class="notification-icon bg-label-danger">
                        <i class="ri-close-circle-line ri-lg"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Turno Cancelado</h6>
                        <small class="text-muted">
                            Carlos Rodríguez canceló su turno del 25/01
                        </small>
                        <p class="mb-0 mt-1">
                            <small class="text-muted">Hace 2 horas</small>
                        </p>
                    </div>
                </div>
            </div>
        </li>
        <li class="border-top">
            <div class="d-grid p-3">
                <a class="btn btn-primary btn-sm d-flex align-items-center justify-content-center" href="javascript:void(0);">
                    <i class="ri-notification-3-line me-1"></i>
                    Ver todas las notificaciones
                </a>
            </div>
        </li>
    </ul>
</li>

<script>
    // Sistema de notificaciones en tiempo real
    class NotificationSystem {
        constructor() {
            this.notificationCount = 3; // Contador inicial
            this.checkInterval = 30000; // Revisar cada 30 segundos
            this.init();
        }

        init() {
            this.updateBadge();
            this.startPolling();
            this.attachEventListeners();
        }

        updateBadge() {
            const badge = document.getElementById('notificationCount');
            if (badge) {
                badge.textContent = this.notificationCount;
                badge.style.display = this.notificationCount > 0 ? 'flex' : 'none';
            }
        }

        startPolling() {
            // Simular polling de notificaciones
            setInterval(() => {
                this.fetchNotifications();
            }, this.checkInterval);
        }

        fetchNotifications() {
            // Aquí harías una llamada AJAX real a tu backend
            // fetch('notificaciones_ajax.php')
            //   .then(response => response.json())
            //   .then(data => {
            //     this.notificationCount = data.unread_count;
            //     this.updateBadge();
            //     this.renderNotifications(data.notifications);
            //   });

            // Por ahora simulamos
            console.log('Checking for new notifications...');
        }

        renderNotifications(notifications) {
            const container = document.getElementById('notificationsList');
            if (!container) return;

            container.innerHTML = '';
            notifications.forEach(notif => {
                const item = this.createNotificationItem(notif);
                container.appendChild(item);
            });
        }

        createNotificationItem(notif) {
            const div = document.createElement('div');
            div.className = `notification-item ${notif.read ? '' : 'unread'}`;
            div.innerHTML = `
      <div class="d-flex gap-3">
        <div class="notification-icon bg-label-${notif.type}">
          <i class="${notif.icon} ri-lg"></i>
        </div>
        <div class="flex-grow-1">
          <h6 class="mb-1">${notif.title}</h6>
          <small class="text-muted">${notif.message}</small>
          <p class="mb-0 mt-1">
            <small class="text-${notif.type}">${notif.time}</small>
          </p>
        </div>
      </div>
    `;
            return div;
        }

        attachEventListeners() {
            // Marcar todas como leídas
            document.querySelector('.dropdown-notifications-read')?.addEventListener('click', () => {
                this.markAllAsRead();
            });
        }

        markAllAsRead() {
            this.notificationCount = 0;
            this.updateBadge();

            // Marcar visualmente todas como leídas
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
            });

            // Aquí harías la llamada AJAX para marcar en el backend
            // fetch('notificaciones_mark_read.php', { method: 'POST' });
        }
    }

    // Inicializar sistema de notificaciones cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        window.notificationSystem = new NotificationSystem();
    });
</script>