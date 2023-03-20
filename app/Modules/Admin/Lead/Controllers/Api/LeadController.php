<?php

namespace App\Modules\Admin\Lead\Controllers\Api;

use App\Modules\Admin\Lead\Models\Lead;
use App\Modules\Admin\Lead\Requests\LeadCreateRequest;
use App\Modules\Admin\Lead\Services\LeadService;
use App\Modules\Admin\Status\Models\Status;
use App\Services\Response\ResponseServise;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{

    private $service;

    /**
     * LeadController constructor.
     * @param $service
     */
    public function __construct(LeadService $service)
    {
        $this->service = $service;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', Lead::class);

        $result = $this->service->getLeads();

        return ResponseServise::sendJsonResponse(true, 200, [],[
            'items' => $result
        ]);
    }

    /**
     * Create of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeadCreateRequest $request)
    {
        //
        $this->authorize('create', Lead::class);

        $lead = $this->service->store($request, Auth::user());

        return ResponseServise::sendJsonResponse(true, 200, [],[
            'item' => $lead
        ]);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Modules\Admin\Lead\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function show(Lead $lead)
    { // Тут в качестве лида придет его ид из запроса
        $this->authorize('view', Lead::class);
        return ResponseServise::sendJsonResponse(true, 200, [],[
            'item' => $lead
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Modules\Admin\Lead\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function edit(Lead $lead)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Modules\Admin\Lead\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function update(LeadCreateRequest $request, Lead $lead)
    {
        $this->authorize('edit', Lead::class);

        $lead = $this->service->update($request, Auth::user(), $lead);

        return ResponseServise::sendJsonResponse(true, 200, [],[
            'item' => $lead
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Modules\Admin\Lead\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead)
    {
        //
    }

    public function archive() { // Возвращаем список лидов которые находятся в архиве;
        // Архивные лиды это те у которых статус = 3(завершен) больше 24 часов назад
        $this->authorize('view', Lead::class);

        $leads = $this->service->archive(); // Получаем коллекцию архивированных лидов

        return ResponseServise::sendJsonResponse(true, 200, [],[
            'items' => $leads
        ]);
    }

    public function checkExist(Request $request) { // Данный метод будет проверять существование определённого лида по каким-то данным

        $this->authorize('create', Lead::class);

        $lead = $this->service->checkExist($request);

        if($lead) {
            return ResponseServise::sendJsonResponse(true, 200, [],[
                'item' => $lead,
                'exist' => true
            ]);
        }

        return ResponseServise::success();

    }

    public function updateQuality(Request $request, Lead $lead) { // изменяем quality у лида

        $this->authorize('edit', Lead::class);

        $lead = $this->service->updateQuality($request, $lead);

        return ResponseServise::sendJsonResponse(true, 200, [],[
            'item' => $lead
        ]);

    }

}
