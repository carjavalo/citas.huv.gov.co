<?php

namespace App\Http\Livewire\Configuracion;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;

class MaintenanceMode extends Component
{
    public $roles = [];
    public $is_maintenance_on = false;
    public $selected_roles = []; // Roles seleccionados para bloquear
    public $message = 'El sistema se encuentra en mantenimiento temporal. Por favor intente más tarde.';

    public function mount()
    {
        // Cargar todos los roles Excepto Super Admin (ese nunca se bloquea)
        $this->roles = Role::where('name', '!=', 'Super Admin')->pluck('name')->toArray();
        
        $this->loadConfig();
    }

    public function loadConfig()
    {
        if (Storage::disk('local')->exists('maintenance_mode.json')) {
            $config = json_decode(Storage::disk('local')->get('maintenance_mode.json'), true);
            $this->is_maintenance_on = $config['enabled'] ?? false;
            $this->selected_roles = $config['blocked_roles'] ?? [];
            $this->message = $config['message'] ?? $this->message;
        }
    }

    public function toggleMaintenance($nuevoEstado)
    {
        // Fix crítico: Convertir correctamente string "false" a booleano false
        // El cast (bool)"false" devuelve true en PHP, lo cual causaba que nunca se desactivara.
        if ($nuevoEstado === 'false' || $nuevoEstado === false || $nuevoEstado === 0 || $nuevoEstado === '0') {
            $nuevoEstado = false;
        } else {
            $nuevoEstado = true;
        }
        
        $this->is_maintenance_on = $nuevoEstado;
        $this->guardarConfig();
    }

    public function save()
    {
        // Guardar todo (toggle + roles + mensaje)
        $this->guardarConfig();
    }

    private function guardarConfig()
    {
        $config = [
            'enabled' => (bool) $this->is_maintenance_on,
            'blocked_roles' => $this->selected_roles,
            'message' => $this->message,
            'updated_at' => now()->toDateTimeString(),
            'updated_by' => auth()->user()->name
        ];

        Storage::disk('local')->put('maintenance_mode.json', json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        \Log::info('Configuración de mantenimiento actualizada', [
            'user'    => auth()->user()->name,
            'enabled' => $config['enabled'],
            'roles'   => $config['blocked_roles'],
        ]);

        $this->emit('alertSuccess', 'Configuración de mantenimiento actualizada correctamente.');
    }

    public function render()
    {
        return view('livewire.configuracion.maintenance-mode')
            ->layout('layouts.app');
    }
}
