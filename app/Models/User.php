<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    // Add this helper to User model
    public function isAdmin(): bool {
        return $this->role === 'admin';
    }
    public function isStaff(): bool {
        return $this->role === 'staff';
    }

    public function isPatient(): bool {
        return $this->role === 'patient';
    }
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public static function doctors()
    {
        return static::where('role', 'admin')->orderBy('name');
    }

    public function resolvePatientRecord(): Patient
    {
        $existing = $this->patient;

        if ($existing) {
            return $existing;
        }

        $byEmail = Patient::where('email', $this->email)->first();

        if ($byEmail) {
            if (! $byEmail->user_id) {
                $byEmail->update(['user_id' => $this->id]);
            }

            return $byEmail;
        }

        return Patient::create([
            'user_id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);
    }
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'license_number',
        'specialization'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
