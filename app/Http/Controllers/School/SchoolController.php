<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\School\SchoolPasswordRequest;
use App\Http\Requests\School\SchoolProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SchoolController extends Controller
{
    public function viewUpdateProfile()
    {
        $title = t('Profile');
        $school = Auth::guard('school')->user();
        return view('school.school.profile', compact('title','school'));
    }

    public function viewUpdatePassword()
    {
        $title = t('Password');
        return view('school.school.password', compact('title'));
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
        $data = $request->validated();
        $school = Auth::guard('school')->user();
        if (Hash::check($request->get('old_password'), $school->password)) {
            $data['password'] = bcrypt($request->get('password'));
            $school->update($data);
            return redirect()->back()->with('message', t('Successfully Updated'))->with('m-class', 'success');
        } else {
            return redirect()->back()->withErrors([t('Current Password Invalid')])->with('message', t('Current Password Invalid'))->with('m-class', 'error');
        }
    }
}
