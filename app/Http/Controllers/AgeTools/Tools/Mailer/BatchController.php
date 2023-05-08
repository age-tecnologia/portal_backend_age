<?php

namespace App\Http\Controllers\AgeTools\Tools\Mailer;

use App\Http\Controllers\Controller;
use App\Models\AgeTools\Tools\Mailer\Batch;
use App\Models\AgeTools\Tools\Mailer\EmailSending;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function getBatchAndEmailsSendings($mailerId)
    {

        $batch = Batch::whereMailerId($mailerId)->get('id');

        $emails = EmailSending::whereIn('lote_id', $batch)->count();

        return [
            'batch' => count($batch),
            'emails' => $emails
        ];

    }

    public function getBatchAndEmailsSendingsTemplate($templateId)
    {

        $batch = Batch::whereTemplateId($templateId)->get('id');

        $emails = EmailSending::whereIn('lote_id', $batch)->count();

        return [
            'batch' => count($batch),
            'emails' => $emails
        ];

    }


    public function getCountEmailReached($mailerId) : int
    {
        $date = Carbon::now()->subHours(24);

        $batchs = Batch::whereMailerId($mailerId)->where('created_at', '>=', $date)->get('id');

        $countEmails = 0;


        foreach ($batchs as $key => $batch) {
            $emails = EmailSending::whereLoteId($batch->id)->count();

            $countEmails += $emails;
        }

        return $countEmails;

    }

    /**
     * Show the form for creating a new resource.
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
