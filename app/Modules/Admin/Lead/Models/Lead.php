<?php

namespace App\Modules\Admin\Lead\Models;

use App\Modules\Admin\LeadComment\Models\LeadComment;
use App\Modules\Admin\Sources\Models\Source;
use App\Modules\Admin\Status\Models\Status;
use App\Modules\Admin\Unit\Models\Unit;
use App\Modules\Admin\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    const DONE_STATUS = 3;

    protected $fillable = [
        'link',
        'phone',
        'source_id',
        'unit_id',
        'is_processed',
        'is_express_delivery',
        'is_add_sale',
    ];

    public function source()
    {
        return $this->belongsTo(Source::class); // Лид может принадлежать многим ссылкам; Будем получать ссылку Лида
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function comments()
    {
        return $this->hasmany(LeadComment::class); // У Лида может быть много комментариев
    }

    public function lastComment()
    { // Последний комментарий
        return $this->comments()->where('comment_value', '!=', NULL)->orderBy('id', 'desc')->first();
    }

    public function getLeads()
    {
        return $this-> // Берем лиды
        with(['source', 'unit', 'status', 'user'])-> // указываем связи
        whereBetween('status_id', [1, 2])-> // будем выбирать лиды у которых ид статусов равны 1 или 2
        orWhere([ // или
            ['status_id', 3],
            ['updated_at', '>', \DB::raw('DATE_SUB(NOW(), INTERVAL 24 HOUR)')] // выбираем лиды, которые были завершены в пределах 24 часов
        ])->
        orderBy('created_at')->
        get();
    }

    public function statuses() {
        return $this->belongsToMany(Status::class);
    }

    public function getArchive()
    {
        return $this->
        with(['statuses','source','unit'])->
        where('status_id', self::DONE_STATUS)->
        where('updated_at','<',\DB::raw('DATE_SUB(NOW(), INTERVAL 24 HOUR)'))-> // Лиды, которые менялись 24 часа назад или больше
        orderBy('updated_at','DESC')->
        paginate(config('settings.pagination'));
    }

}
