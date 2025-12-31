<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Traits\BelongsToTenant;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use BelongsToTenant, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'avatar',
        'password',
        'language',
        'phone_verified', // Ajouté
        'is_active',
        'last_login_ip', // Optionnel
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified' => 'boolean',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        // 'password' => 'hashed', // Retiré - Laravel hash automatiquement
    ];

    protected $appends = [
        'full_name',
        'initials',
        'avatar_url',
    ];

    /**
     * Relation : Tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdInvoices()
    {
        return $this->hasMany(Invoice::class, 'created_by');
    }

    public function createdQuotes()
    {
        return $this->hasMany(Quote::class, 'created_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ==================== ACCESSORS ====================

    /**
     * Obtenir le nom complet
     */
    public function getFullNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return trim("{$this->first_name} {$this->last_name}");
        }

        return $this->name;
    }

    /**
     * Obtenir les initiales
     */
    public function getInitialsAttribute(): string
    {
        $firstName = trim($this->first_name ?? '');
        $lastName = trim($this->last_name ?? '');

        if (empty($firstName) && empty($lastName) && empty($this->name)) {
            return '??';
        }

        if (empty($firstName) && empty($lastName)) {
            // Utiliser le nom complet s'il n'y a pas de prénom/nom séparés
            $nameParts = explode(' ', $this->name);
            $firstInitial = $nameParts[0] ? substr($nameParts[0], 0, 1) : '';
            $secondInitial = isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : '';
            return strtoupper($firstInitial . $secondInitial);
        }

        $initials = '';

        if (!empty($firstName)) {
            $initials .= strtoupper(mb_substr($firstName, 0, 1));
        }

        if (!empty($lastName)) {
            $initials .= strtoupper(mb_substr($lastName, 0, 1));
        }

        return $initials ?: '??';
    }

    /**
     * Obtenir l'avatar URL
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            // Vérifier si c'est déjà une URL complète
            if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
                return $this->avatar;
            }

            // Sinon, chercher dans storage
            if (Str::startsWith($this->avatar, 'avatars/') || Str::startsWith($this->avatar, 'public/avatars/')) {
                return asset('storage/' . ltrim($this->avatar, 'public/'));
            }

            return asset('storage/avatars/' . $this->avatar);
        }

        // Gravatar fallback
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=200";
    }

    // ==================== METHODS ====================

    /**
     * Enregistrer la dernière connexion
     */
    public function updateLastLogin(?string $ip = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip ?? request()->ip(),
        ]);
    }

    /**
     * Vérifier si l'utilisateur est admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    public function canAccessModule(string $module): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        // Vérifier la permission via Spatie
        return $this->hasPermissionTo("access.{$module}") ||
            $this->hasPermissionTo("{$module}.*");
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        // Utiliser la méthode de Spatie
        return $this->hasPermissionTo($permission);
    }

    /**
     * Vérifier si l'email est vérifié
     */
    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Scope : Utilisateurs actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope : Utilisateurs du tenant courant
     */
    public function scopeForCurrentTenant($query)
    {
        return $query->where('tenant_id', auth()->user()->tenant_id ?? null);
    }

    /**
     * Scope : Chercher par nom ou email
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
        });
    }
}
