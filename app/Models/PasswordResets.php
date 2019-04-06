<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PasswordResets
 *
 * @property string $email
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResets newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResets newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResets query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResets whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResets whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResets whereToken($value)
 * @mixin \Eloquent
 */
class PasswordResets extends Model
{
    protected $table = 'password_resets';

}
