<?php

namespace App\Http\Controllers\Inspection;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inspection\InspectionPasswordRequest;
use App\Http\Requests\Inspection\InspectionProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Traits\UpdatesPassword;

class InspectionController extends Controller
{
    use UpdatesPassword;

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
        $reason = $this->passwordChangeReason('inspection');
        $forced = $reason !== null;
        return view('inspection.inspection.password', compact('title', 'forced', 'reason'));
    }
    public function updatePassword(InspectionPasswordRequest $request)
    {
        return $this->applyPasswordUpdate($request, 'inspection');
    }
}
