<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\School\SchoolPasswordRequest;
use App\Http\Requests\School\SchoolProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Traits\UpdatesPassword;

class SchoolController extends Controller
{
    use UpdatesPassword;

    public function viewUpdateProfile()
    {
        $title = t('Profile');
        $school = Auth::guard('school')->user();
        return view('school.school.profile', compact('title','school'));
    }

    public function viewUpdatePassword()
    {
        $title = t('Password');
        $reason = $this->passwordChangeReason('school');
        $forced = $reason !== null;
        return view('school.school.password', compact('title', 'forced', 'reason'));
    }
    public function updateProfile(SchoolProfileRequest $request)
    {
        $data = $request->validated();
        $school = Auth::guard('school')->user();
        if ($request->hasFile('logo')) {
            $logo = uploadFile($request->file('logo'), 'schools');
            $data['logo'] = $logo['path'];
        }
        $school->update($data);
        return redirect()->back()->with('message', t('Successfully Updated'))->with('m-class', 'success');
    }

    public function updatePassword(SchoolPasswordRequest $request)
    {
        return $this->applyPasswordUpdate($request, 'school');
    }
}
