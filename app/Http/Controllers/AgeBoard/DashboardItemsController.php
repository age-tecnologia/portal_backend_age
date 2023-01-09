<?php

namespace App\Http\Controllers\AgeBoard;

use App\Http\Controllers\Controller;
use App\Models\AgeBoard\Item;
use Illuminate\Http\Request;

class DashboardItemsController extends Controller
{

    public function index(Request $request)
    {
        $items = Item::whereDashboardId($request->input('id'))->get(['id', 'item', 'iframe']);

        return $items;
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
