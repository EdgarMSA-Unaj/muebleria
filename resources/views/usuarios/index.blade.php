@extends('layouts.app')

@section('title', 'Usuarios & Roles')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="brand-font mb-1 text-dark">Usuarios del Sistema & Permisos</h3>
            <span class="text-secondary" style="font-size: 13.5px;">Gestión de accesos para administración, vendedores, jefes de almacén y contabilidad</span>
        </div>
        <button class="btn text-white fw-semibold rounded-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal" style="background: var(--primary-orange);">
            <i class="fa-solid fa-user-shield"></i>
            <span>+ Nuevo Usuario</span>
        </button>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="stat-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="custom-table mb-0">
                <thead>
                    <tr>
                        <th>Nombre y Apellido</th>
                        <th>Correo Electrónico</th>
                        <th>Teléfono</th>
                        <th>Rol Asignado</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle" style="width: 36px; height: 36px; font-size: 13px;">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-primary-subtle text-primary" style="font-size: 10px;">Sesión Actual</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-secondary">{{ $user->email }}</td>
                            <td>{{ $user->phone ?? 'Sin registrar' }}</td>
                            <td>
                                @foreach($user->roles as $rol)
                                    <span class="badge" style="background: rgba(124, 58, 237, 0.12); color: #7c3aed; font-size: 11.5px;">
                                        <i class="fa-solid fa-shield me-1"></i> {{ $rol->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="fa-solid fa-circle-check me-1"></i> Activo
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted" style="font-size: 12px;">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <button class="btn btn-sm btn-light border py-1 px-2" data-bs-toggle="modal" data-bs-target="#editarUsuarioModal{{ $user->id }}" title="Editar Usuario y Rol">
                                        <i class="fa-regular fa-pen-to-square text-primary"></i>
                                    </button>

                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('usuarios.toggle', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas cambiar el estado de acceso de este usuario?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light border py-1 px-2" title="{{ $user->is_active ? 'Desactivar acceso' : 'Activar acceso' }}">
                                                <i class="fa-solid {{ $user->is_active ? 'fa-ban text-warning' : 'fa-check text-success' }}"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('usuarios.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border py-1 px-2" title="Eliminar Usuario">
                                                <i class="fa-regular fa-trash-can text-danger"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modales de Edición (Fuera de table para evitar parpadeo) -->
@foreach($usuarios as $user)
<div class="modal fade" id="editarUsuarioModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title brand-font">Editar Usuario: {{ $user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('usuarios.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre Completo</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nueva Contraseña (Opcional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para mantener la actual">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teléfono Celular</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rol Asignado</label>
                        <select name="role" class="form-select" required>
                            @php $currentRole = $user->roles->first()?->name; @endphp
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $currentRole === $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if($user->id !== auth()->id())
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activoSwitch{{ $user->id }}" {{ $user->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="activoSwitch{{ $user->id }}">Usuario Activo en el Sistema</label>
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white" style="background: var(--primary-orange);">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Nuevo Usuario -->
<div class="modal fade" id="nuevoUsuarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title brand-font">Crear Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre Completo</label>
                        <input type="text" name="name" class="form-control" placeholder="Ej. Ana Lucía Morales" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" placeholder="ana@muebleria.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña Inicial</label>
                        <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teléfono Celular</label>
                        <input type="text" name="phone" class="form-control" placeholder="+51 987 111 222">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rol Asignado</label>
                        <select name="role" class="form-select" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white" style="background: var(--primary-orange);">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
