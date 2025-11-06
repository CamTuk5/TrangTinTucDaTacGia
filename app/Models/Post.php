<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','slug','content','category_id','user_id',
        'status','published_at','views',
    ];

    protected $casts = [
        'category_id'  => 'integer',
        'user_id'      => 'integer',
        'views'        => 'integer',
        'published_at' => 'datetime',
    ];

    public function user(){ return $this->belongsTo(User::class, 'user_id'); }
    public function category(){ return $this->belongsTo(Category::class); }
    public function comments(){ return $this->hasMany(Comment::class); }
    public function votes(){ return $this->hasMany(Vote::class); }

    // quan trọng: bài publish phải có published_at
    public function scopePublished($q){
        return $q->where('status','published')->whereNotNull('published_at');
    }
}
