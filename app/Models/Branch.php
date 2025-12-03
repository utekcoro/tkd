<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'customer_id',
        'photo',
        'auth_accurate',
        'session_accurate',
        'accurate_api_token',
        'accurate_signature_secret',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'branch_user');
    }

    public function setAuthAccurateAttribute($value)
    {
        $this->attributes['auth_accurate'] = $value ? Crypt::encryptString($value) : null;
    }

    public function setSessionAccurateAttribute($value)
    {
        $this->attributes['session_accurate'] = $value ? Crypt::encryptString($value) : null;
    }

    public function setAccurateApiTokenAttribute($value)
    {
        $this->attributes['accurate_api_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function setAccurateSignatureSecretAttribute($value)
    {
        $this->attributes['accurate_signature_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getAuthAccurateAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function getSessionAccurateAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function getAccurateApiTokenAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function getAccurateSignatureSecretAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }
}
