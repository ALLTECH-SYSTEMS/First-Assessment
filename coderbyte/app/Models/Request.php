<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Request extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'owner_id',
        'photographer_id',
        'product',
        'location',
        'LQT',
        'HRI',
        'status',
        'approve',
    ];

    /**
     * Status types
     *
     * @var array
     */
    protected $status = [
        '1' => 'Assigned',
        '2' => 'Not assigned',
    ];

    /**
     * Approve types
     *
     * @var array
     */
    protected $approve = [
        '1' => 'Approved',
        '2' => 'Rejected',
    ];

    public function productOwner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function photographer()
    {
        return $this->belongsTo(User::class, 'photographer_id');
    }
}
