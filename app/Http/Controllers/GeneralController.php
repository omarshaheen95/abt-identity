<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Models\Level;
use App\Models\Term;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function levelsByYear(Request $request,$id)
    {
        $rows = Level::query()->where('year_id',$id)->get();
        if ((bool)request()->get('multiple', false)) {
            $html = '';
        } else {
            $html = '<option><option/>';
        }
        foreach ($rows as $row) {
            $html .= '<option value="' . $row->id . '">' . $row->name . '</option>';
        }
        return Response::respondSuccess(Response::SUCCESS, $html);
    }
    public function termsByLevel(Request $request,$id)
    {
        $rows = Term::query()->where('level_id',$id)->get();
        if ((bool)request()->get('multiple', false)) {
            $html = '';
        } else {
            $html = '<option><option/>';
        }
        foreach ($rows as $row) {
            $html .= '<option value="' . $row->id . '">' . $row->name . '</option>';
        }
        return Response::respondSuccess(Response::SUCCESS, $html);
    }
}
