<?php

namespace App\Http\Livewire\Campanas;

use App\Models\Campana;
use App\Models\ConfiguracionSistema;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Consulta extends Component
{
    use WithPagination;
    use AuthorizesRequests;
    use WithFileUploads;
    
    public $modalEditar = false;
    public $modalCrear = false;
    public $modalEliminar = false;
    public $campana_id;
    public $busqueda;
    public $filtroEstado = '';
    public $filtroCategoria = '';

    // Configuración de la sección
    public $seccionHabilitada = true;
    
    // Carrusel automático
    public $carruselAutomatico = false;
    public $intervaloCarrusel = 5; // Tiempo en segundos

    // Campos del formulario
    public $titulo;
    public $descripcion;
    public $imagen;
    public $video;
    public $archivo_pdf;
    public $categoria = 'campana';
    public $estado = 'borrador';
    public $fecha_inicio;
    public $fecha_fin;
    public $visible = true;
    public $color_texto = '#000000';
    public $fuente_texto = 'Inter';
    public $tamano_texto = '16px';

    // Para previsualización
    public $imagenActual;
    public $videoActual;
    public $pdfActual;

    protected $listeners = ['cerrarModal', 'confirmarEliminar'];

    protected $rules = [
        'titulo' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'imagen' => 'nullable|file|mimes:jpg,jpeg,png,gif,bmp,webp,svg|max:5120',
        'video' => 'nullable|file|mimes:mp4,webm,ogg,mov,avi|max:51200',
        'archivo_pdf' => 'nullable|file|mimes:pdf|max:10240',
        'categoria' => 'required|in:campana,noticia,urgente,servicio',
        'estado' => 'required|in:borrador,publicado,programado',
        'fecha_inicio' => 'nullable|date',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        'visible' => 'boolean',
        'color_texto' => 'nullable|string|max:20',
        'fuente_texto' => 'nullable|string|max:50',
        'tamano_texto' => 'nullable|string|max:20',
        'intervaloCarrusel' => 'nullable|integer|min:3|max:30',
    ];

    protected $messages = [
        'titulo.required' => 'El título es obligatorio.',
        'fecha_fin.after_or_equal' => 'La fecha fin debe ser posterior o igual a la fecha inicio.',
        'imagen.mimes' => 'La imagen debe ser: jpg, jpeg, png, gif, bmp, webp o svg.',
        'imagen.max' => 'La imagen no debe superar 5MB.',
        'video.mimes' => 'El video debe ser: mp4, webm, ogg, mov o avi.',
        'video.max' => 'El video no debe superar 50MB.',
        'archivo_pdf.mimes' => 'El archivo debe ser PDF.',
        'archivo_pdf.max' => 'El PDF no debe superar 10MB.',
    ];

    public function mount()
    {
        $this->authorize('admin.servicios.consultar');
        $this->seccionHabilitada = ConfiguracionSistema::estaHabilitado('seccion_novedades_campanas_habilitada');
        
        // Cargar estado del carrusel desde la configuración
        $this->carruselAutomatico = ConfiguracionSistema::estaHabilitado('carrusel_campanas_automatico');
        $intervaloGuardado = ConfiguracionSistema::obtener('carrusel_campanas_intervalo');
        $this->intervaloCarrusel = $intervaloGuardado ? (int)$intervaloGuardado : 5;
    }

    public function toggleSeccionNovedades()
    {
        $nuevoValor = $this->seccionHabilitada ? '0' : '1';
        ConfiguracionSistema::establecer(
            'seccion_novedades_campanas_habilitada',
            $nuevoValor,
            'Habilitar/Deshabilitar la sección de Novedades y Campañas en la vista principal'
        );
        $this->seccionHabilitada = !$this->seccionHabilitada;
        
        $mensaje = $this->seccionHabilitada 
            ? 'Sección de Novedades y Campañas HABILITADA en la vista principal.' 
            : 'Sección de Novedades y Campañas DESHABILITADA en la vista principal.';
        session()->flash('message', $mensaje);
    }

    public function toggleCarruselAutomatico()
    {
        $this->carruselAutomatico = !$this->carruselAutomatico;
        
        // Guardar en la configuración del sistema
        ConfiguracionSistema::establecer(
            'carrusel_campanas_automatico',
            $this->carruselAutomatico ? '1' : '0',
            'Estado del carrusel automático de campañas'
        );
        
        $mensaje = $this->carruselAutomatico 
            ? 'Carrusel automático ACTIVADO. Las campañas se desplazarán cada ' . $this->intervaloCarrusel . ' segundos.' 
            : 'Carrusel automático DESACTIVADO.';
        session()->flash('message', $mensaje);
        $this->emit('carruselToggled', 
            $this->carruselAutomatico,
            $this->intervaloCarrusel
        );
    }

    public function updatedIntervaloCarrusel()
    {
        // Guardar el intervalo en la configuración del sistema
        ConfiguracionSistema::establecer(
            'carrusel_campanas_intervalo',
            (string)$this->intervaloCarrusel,
            'Intervalo en segundos del carrusel automático de campañas'
        );
        
        if ($this->carruselAutomatico) {
            $this->emit('carruselToggled', 
                true,
                $this->intervaloCarrusel
            );
        }
    }

    public function render()
    {
        $query = Campana::query();

        if ($this->busqueda) {
            $query->where(function($q) {
                $q->where('titulo', 'like', '%' . $this->busqueda . '%')
                  ->orWhere('descripcion', 'like', '%' . $this->busqueda . '%');
            });
        }

        if ($this->filtroEstado) {
            $query->where('estado', $this->filtroEstado);
        }

        if ($this->filtroCategoria) {
            $query->where('categoria', $this->filtroCategoria);
        }

        $campanas = $query->orderBy('orden')->orderByDesc('created_at')->paginate(10);

        // Estadísticas
        $totalActivos = Campana::where('estado', 'publicado')->where('visible', true)->count();
        $totalProgramados = Campana::where('estado', 'programado')->count();
        $totalCampanas = Campana::count();

        return view('livewire.campanas.consulta', [
            'campanas' => $campanas,
            'totalActivos' => $totalActivos,
            'totalProgramados' => $totalProgramados,
            'totalCampanas' => $totalCampanas,
        ]);
    }

    public function abrirModalCrear()
    {
        $this->resetFormulario();
        $this->modalCrear = true;
    }

    public function abrirModalEditar($id)
    {
        $this->resetFormulario();
        $campana = Campana::findOrFail($id);
        $this->campana_id = $campana->id;
        $this->titulo = $campana->titulo;
        $this->descripcion = $campana->descripcion;
        $this->categoria = $campana->categoria;
        $this->estado = $campana->estado;
        $this->fecha_inicio = $campana->fecha_inicio ? $campana->fecha_inicio->format('Y-m-d') : null;
        $this->fecha_fin = $campana->fecha_fin ? $campana->fecha_fin->format('Y-m-d') : null;
        $this->visible = $campana->visible;
        $this->color_texto = $campana->color_texto;
        $this->fuente_texto = $campana->fuente_texto;
        $this->tamano_texto = $campana->tamano_texto;
        $this->imagenActual = $campana->imagen;
        $this->videoActual = $campana->video;
        $this->pdfActual = $campana->archivo_pdf;
        $this->modalEditar = true;
    }

    public function guardar()
    {
        $this->validate();

        $data = [
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'categoria' => $this->categoria,
            'estado' => $this->estado,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'visible' => $this->visible,
            'color_texto' => $this->color_texto,
            'fuente_texto' => $this->fuente_texto,
            'tamano_texto' => $this->tamano_texto,
            'user_id' => Auth::id(),
        ];

        // Procesar imagen
        if ($this->imagen) {
            $imagenPath = $this->imagen->store('campanas/imagenes', 'public');
            $data['imagen'] = $imagenPath;
        }

        // Procesar video
        if ($this->video) {
            $videoPath = $this->video->store('campanas/videos', 'public');
            $data['video'] = $videoPath;
        }

        // Procesar PDF
        if ($this->archivo_pdf) {
            $pdfPath = $this->archivo_pdf->store('campanas/pdfs', 'public');
            $data['archivo_pdf'] = $pdfPath;
        }

        Campana::create($data);

        $this->cerrarModal();
        session()->flash('message', 'Campaña creada exitosamente.');
    }

    public function actualizar()
    {
        $this->validate();

        $campana = Campana::findOrFail($this->campana_id);

        $data = [
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'categoria' => $this->categoria,
            'estado' => $this->estado,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'visible' => $this->visible,
            'color_texto' => $this->color_texto,
            'fuente_texto' => $this->fuente_texto,
            'tamano_texto' => $this->tamano_texto,
        ];

        // Procesar imagen
        if ($this->imagen) {
            // Eliminar imagen anterior
            if ($campana->imagen) {
                Storage::disk('public')->delete($campana->imagen);
            }
            $imagenPath = $this->imagen->store('campanas/imagenes', 'public');
            $data['imagen'] = $imagenPath;
        }

        // Procesar video
        if ($this->video) {
            // Eliminar video anterior
            if ($campana->video) {
                Storage::disk('public')->delete($campana->video);
            }
            $videoPath = $this->video->store('campanas/videos', 'public');
            $data['video'] = $videoPath;
        }

        // Procesar PDF
        if ($this->archivo_pdf) {
            // Eliminar PDF anterior
            if ($campana->archivo_pdf) {
                Storage::disk('public')->delete($campana->archivo_pdf);
            }
            $pdfPath = $this->archivo_pdf->store('campanas/pdfs', 'public');
            $data['archivo_pdf'] = $pdfPath;
        }

        $campana->update($data);

        $this->cerrarModal();
        session()->flash('message', 'Campaña actualizada exitosamente.');
    }

    public function confirmarEliminar($id)
    {
        $this->campana_id = $id;
        $this->modalEliminar = true;
    }

    public function eliminar()
    {
        $campana = Campana::findOrFail($this->campana_id);
        
        // Eliminar archivos asociados
        if ($campana->imagen) {
            Storage::disk('public')->delete($campana->imagen);
        }
        if ($campana->video) {
            Storage::disk('public')->delete($campana->video);
        }
        if ($campana->archivo_pdf) {
            Storage::disk('public')->delete($campana->archivo_pdf);
        }

        $campana->delete();

        $this->cerrarModal();
        session()->flash('message', 'Campaña eliminada exitosamente.');
    }

    public function toggleVisible($id)
    {
        $campana = Campana::findOrFail($id);
        $campana->update(['visible' => !$campana->visible]);
    }

    public function cerrarModal()
    {
        $this->modalCrear = false;
        $this->modalEditar = false;
        $this->modalEliminar = false;
        $this->resetFormulario();
    }

    public function resetFormulario()
    {
        $this->reset([
            'campana_id', 'titulo', 'descripcion', 'imagen', 'video', 'archivo_pdf',
            'categoria', 'estado', 'fecha_inicio', 'fecha_fin', 'visible',
            'color_texto', 'fuente_texto', 'tamano_texto', 'imagenActual', 'videoActual', 'pdfActual'
        ]);
        $this->categoria = 'campana';
        $this->estado = 'borrador';
        $this->visible = true;
        $this->color_texto = '#000000';
        $this->fuente_texto = 'Inter';
        $this->tamano_texto = '16px';
    }

    public function updatedImagen()
    {
        $this->validateOnly('imagen');
    }

    public function updatedArchivoPdf()
    {
        $this->validateOnly('archivo_pdf');
    }

    public function updatedVideo()
    {
        $this->validateOnly('video');
    }
}
