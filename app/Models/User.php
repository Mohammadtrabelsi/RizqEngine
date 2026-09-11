<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, HasRoles, InteractsWithMedia, Notifiable, RecordsActivity, TracksUserActions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = ['media'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
            ->useFallbackUrl('https://www.gravatar.com/avatar/'.md5('test@mail.com'));
    }

    public function scopeIsActive(Builder $builder)
    {
        return $builder->where('is_active', 1);
    }

    /**
     * Issue a fresh API token for this user, storing only its SHA-256 hash.
     *
     * The raw token is returned once and never persisted in plain text; the
     * caller must hand it to the client immediately. Calling this again
     * rotates (and therefore revokes) any previously issued token.
     */
    public function generateApiToken(): string
    {
        $token = Str::random(60);

        $this->forceFill(['api_token' => hash('sha256', $token)])->save();

        return $token;
    }

    /**
     * Revoke this user's API token, if any.
     */
    public function revokeApiToken(): void
    {
        $this->forceFill(['api_token' => null])->save();
    }

    /**
     * Uppercase initials derived from the user's name (max two letters),
     * used by the dashboard avatar pill.
     */
    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name)) ?: [];
        $parts = array_values(array_filter($parts));

        if ($parts === []) {
            return '?';
        }

        $letters = count($parts) === 1
            ? mb_substr($parts[0], 0, 2)
            : mb_substr($parts[0], 0, 1).mb_substr($parts[count($parts) - 1], 0, 1);

        return mb_strtoupper($letters);
    }

    /**
     * Human-readable label of the user's primary role for the shift card.
     */
    public function getRoleLabelAttribute(): string
    {
        return (string) ($this->getRoleNames()->first() ?? __('nav.staff_roles'));
    }
}
