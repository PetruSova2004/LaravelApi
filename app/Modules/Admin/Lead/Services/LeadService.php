<?php
/**
 * Created by PhpStorm.
 * User: note
 * Date: 27.12.2020
 * Time: 11:48
 */

namespace App\Modules\Admin\Lead\Services;


use App\Modules\Admin\Lead\Models\Lead;
use App\Modules\Admin\LeadComment\Services\LeadCommentService;
use App\Modules\Admin\Status\Models\Status;
use App\Modules\Admin\User\Models\User;

class LeadService
{

    public function getLeads()
    {
        $leads = (new Lead())->getLeads(); // В этом методе будет прописаны условия по выборки лидов
//        return $leads;
        $statuses = Status::all();

        $resultLeads = [];

        $statuses->each(function($item, $key) use(&$resultLeads,$leads) { // вернём лиды которые сгруппированы по статусу; $item - элемент коллекций $statuses, $key - ключ данного элемента; в use() указываем переменные, которые нужны нам в callback функций
            $collection = $leads->where('status_id', $item->id);
            $resultLeads[$item->title] = $collection->map(function($elem) { // map() - вернёт нам коллекцию тех элементов которые попали в $collection;
                return $elem; // Будем получать все лиды по статусу(new: ; process: ; done:), содержимое будет то что попало в $collection
            });
        });

        return $resultLeads;
    }

    public function store($request, User $user)
    {
        $lead = new Lead();
        $lead->fill($request->only($lead->getFillable())); // тут мы заполняем модель данными; в save() мы будем сохранять их БД

        $status = Status::where('title','new')->first();

        $lead->status()->associate($status);// ассоциируем лид со статусом

        $user->leads()->save($lead);// сохраняем новый лид в БД к текущему пользователю


        ///add comments
        $this->addStoreComments($lead, $request, $user, $status);

        $lead->statuses()->attach($status->id); // attach() - нужен для добавления информаций в связующею таблицу;

        return $lead;
    }

    private function addStoreComments($lead, $request, $user, $status)
    {
        $is_event = true; // Тут мы понимаем что лид был создан пользователем
        $tmpText = "Автор <strong>".$user->fullname.'</strong> создал лид со статусом '.$status->title_ru;
        LeadCommentService::saveComment($tmpText, $lead, $user, $status, null, $is_event);
        // fullname = User(getFullnameAttribute())

        if($request->text) {
            $is_event = false;
            $tmpText = "Пользователь <strong>".$user->fullname.'</strong> оставил комментарий '.$request->text;
            LeadCommentService::saveComment($tmpText, $lead, $user, $status, $request->text, $is_event);
        }

    }

    public function update($request, $user, $lead)
    {
        $tmp = clone $lead; // Копируем объект лида; Это нужно, для того чтобы проверить изменился ли статус лида или какой-либо параметр у лида;

        $lead->count_create++;

        $status = Status::where('title','new')->first();
        $lead->fill($request->only($lead->getFillable()));
        $lead->status()->associate($status); // привязываем к лиду текущий статус
        $lead->save();

        ///add comments
        $this->addUpdateComments($lead, $request, $user, $status, $tmp);


        return $lead;

    }

    private function addUpdateComments($lead, $request, $user, $status, $tmp)
    {
        if ($request->text) {
            $tmpText = "Пользователь " . $user->fullname . ' оставил комментарий ' .  $request->text ;
            LeadCommentService::saveComment($tmpText, $lead, $user, $status, $request->text);
        }

        if ($tmp->source_id != $lead->source_id) {
            $is_event = true;
            $tmpText = "Пользователь " . $user->fullname . ' изменил источник на ' . $lead->source->title;
            LeadCommentService::saveComment($tmpText, $lead, $user, $status,null,$is_event);
        }

        if ($tmp->unit_id != $lead->unit_id) {
            $is_event = true;
            $tmpText = "Пользователь " . $user->fullname . ' изменил подразделение на ' . $lead->unit->title;
            LeadCommentService::saveComment($tmpText, $lead, $user, $status,null,$is_event);
        }

        if ($tmp->status_id != $lead->status_id) {
            $is_event = true;
            $tmpText = "Пользователь " . $user->fullname . ' изменил статус на ' . $lead->status->title_ru;
            LeadCommentService::saveComment($tmpText, $lead, $user, $status,null,$is_event);
        }

        $is_event = true;
        /**Автор лида* создал лид *дата и время создания* со статусом *статус**/
        $tmpText = "Автор " . $user->fullname . ' создал лид  со статусом ' . $status->title_ru;
        LeadCommentService::saveComment($tmpText, $lead, $user, $status, $request->text, $is_event);

        $lead->statuses()->attach($status->id);
    }

    public function archive()
    {
        $leads = (new Lead())->getArchive();

        return $leads;
    }

    public function checkExist($request)
    {
        $queryBuilder = Lead::select('*');

        if($request->link) {
            $queryBuilder->where('link',$request->link);
        }
        elseif ($request->phone) {
            $queryBuilder->where('phone',$request->phone);
        }

        $queryBuilder->where('status_id','!=', Lead::DONE_STATUS); // те данные которые не завершенные

        return $queryBuilder->first();

    }

    public function updateQuality($request, $lead)
    {
        $lead->isQualityLead = true;
        $lead->save();

        return $lead;
    }

}
