<?php namespace AppTupir\Catchphrase\Models;

use Rainlab\User\Models\User;
use October\Rain\Database\Model;

class Catchphrase extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'apptupir_catchphrases';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'title',
        'lyrics',
        'is_published',
        'user_id',
        'audio',
    ];

    /**
     * @var array Attributes to be cast to native types
     */

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'title'        => 'required',
        'user_id'      => 'required',
        'audio'        => 'required',
        'is_published' => 'boolean',
    ];

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = [];

    /**
     * @var array Attributes to be cast to Argon (Carbon) instances
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $hasOneThrough = [];
    public $hasManyThrough = [];
    public $belongsTo = [
        'user' => User::class
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function scopeIsPublished($query)
    {
        return $query->where('is_published', true);
    }
}