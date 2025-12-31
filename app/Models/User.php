<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Support\Str;
use App\Traits\BelongsToTenant;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'avatar_url',
        'role',
        'permissions',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // ==================== RELATIONS ====================

    public function tenant()
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

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getInitialsAttribute(): string
    {
        $firstInitial = $this->first_name ? substr($this->first_name, 0, 1) : '';
        $lastInitial = $this->last_name ? substr($this->last_name, 0, 1) : '';
        return strtoupper($firstInitial . $lastInitial);
    }

    public function getAvatarAttribute(): string
    {
        if ($this->avatar_url) {
            return $this->avatar_url;
        }

        // Avatar par défaut avec initiales
        return "https://ui-avatars.com/api/?name={$this->full_name}&color=fff&background=0ea5e9";
    }

    // ==================== METHODS ====================

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['owner', 'admin']);
    }

    public function canAccessModule(string $module): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        return $this->permissions[$module] ?? false;
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        return $this->permissions[$permission] ?? false;
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $firstName = trim($this->first_name ?? '');
        $lastName = trim($this->last_name ?? '');

        if (empty($firstName) && empty($lastName)) {
            return '??';
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
}
