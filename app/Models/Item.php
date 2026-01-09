<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\InventarioHistorial;
use App\Models\Area;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\InventoryMovement;

class Item extends Model
{
    protected $connection = 'tenant';

    public function resolveRouteBinding($value, $field = null)
    {
        // 1) Ajusta la conexión tenant según el usuario autenticado
        if ($user = Auth::user()) {
            $dbName = $user->peluqueria->db;                               // el nombre dynamic de la BD
            Config::set('database.connections.tenant.database', $dbName);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }

        // 2) Resuelve el modelo usando esa conexión
        $field = $field ?: $this->getRouteKeyName();
        return $this->on('tenant')
                    ->where($field, $value)
                    ->firstOrFail();
    }
    use HasFactory;

    protected $table = 'items';
	

    /**
     * Atributos asignables en masa.
     */
    protected $fillable = [
        'nombre',
        'cantidad',
        'costo',
        'valor',
        'tipo',
        'area',
        'type',
        'sku',
        'stock',
        'track_inventory',
        'sale_price',
        'cost_price',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'costo' => 'decimal:2',
        'stock' => 'decimal:3',
        'track_inventory' => 'boolean',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    public function movimientos()
    {
        return $this->hasMany(InventarioHistorial::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function areaRelation()
    {
        return $this->belongsTo(Area::class, 'area');
    }

    public function getStatusLabelAttribute(): string
    {
        if (! $this->inventariable && ! $this->track_inventory) {
            return 'No inventariable';
        }

        $stock = (float) ($this->stock ?? 0);
        $minimum = (float) ($this->cantidad ?? 0);

        if ($stock <= 0) {
            return 'Agotado';
        }

        if ($stock > 0 && $stock <= $minimum) {
            return 'Agotándose';
        }

        return 'Disponible';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status_label) {
            'Disponible' => 'bg-green-100 text-green-700',
            'Agotándose' => 'bg-amber-100 text-amber-700',
            'Agotado' => 'bg-red-100 text-red-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $query->when($filters['search'] ?? null, function (Builder $builder, $search) {
            $builder->where(function (Builder $inner) use ($search) {
                $inner->where('nombre', 'like', '%' . $search . '%')
                    ->orWhere('sku', 'like', '%' . $search . '%');
            });
        });

        $query->when($filters['tipo'] ?? null, function (Builder $builder, $tipo) {
            $builder->where('tipo', $tipo);
        });

        $query->when($filters['area'] ?? null, function (Builder $builder, $area) {
            $builder->where('area', $area);
        });

        $query->when($filters['status'] ?? null, function (Builder $builder, $status) {
            if ($status === 'no_inventariable') {
                $builder->where('inventariable', false)->where('track_inventory', false);
                return;
            }

            $builder->where(function (Builder $inner) {
                $inner->where('inventariable', true)->orWhere('track_inventory', true);
            });

            if ($status === 'agotado') {
                $builder->where('stock', '<=', 0);
            }

            if ($status === 'agotandose') {
                $builder->where('stock', '>', 0)
                    ->whereRaw('stock <= coalesce(cantidad, 0)');
            }

            if ($status === 'disponible') {
                $builder->whereRaw('stock > coalesce(cantidad, 0)');
            }
        });

        return $query;
    }
}
