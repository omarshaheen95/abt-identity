<?php

namespace App\Http\Controllers\Inspection;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inspection\InspectionPasswordRequest;
use App\Http\Requests\Inspection\InspectionProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InspectionController extends Controller
{
    public function viewUpdateProfile()
    {
        $title = t('Update Profile');
        $inspection =  Auth::guard('inspection')->user();
        return view('inspection.inspection.profile', compact('title','inspection'));
    }
    public function updateProfile(InspectionProfileRequest $request)
    {
        $data = $request->validated();
        $inspection = Auth::guard('inspection')->user();
        if ($request->hasFile('image')) {
            $image = uploadFile($request->file('image'), 'image');
            $data['image'] = $image['path'];
        }
        $inspection->update($data);
        return redirect()->back()->with('message', t('Successfully Updated'));
    }
    public function viewUpdatePassword()
    {
        $title = t('Update Password');
        return view('inspection.inspection.password', compact('title'));
    }
    public function updatePassword(InspectionPasswordRequest $request)
    {
        $data = $request->validated();
        $inspection = Auth::guard('inspection')->user();
        if (Hash::check($request->get('old_password'), $inspection->password)) {
            $data['password'] = bcrypt($request->get('password'));
            $inspection->update($data);
            return redirect()->back()->with('message', t('Successfully Updated'))->with('m-class', 'success');
        } else {
            return redirect()->back()->withErrors([t('Current Password Invalid')])->with('message', t('Current Password Invalid'))->with('m-class', 'error');
        }
    }
}
