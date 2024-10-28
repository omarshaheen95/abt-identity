<?php

namespace App\Observers;

use App\Models\School;
use App\Models\SchoolGrade;
use App\Models\Year;

class SchoolObserver
{
    /**
     * Handle the School "created" event.
     *
     * @param  \App\Models\School  $school
     * @return void
     */
    public function created(School $school)
    {

        //get default year
        $default_year = Year::query()->where('default',1)->first();
        $default_year?$year_id=$default_year->id:$year_id=Year::query()->latest()->first()->id;

        $school->update(['available_year_id'=>$year_id]);

        $school_grades = [];
        foreach (range(1,12) as $grade)
        {
            $school_grades[] = [
                'school_id'=>$school->id,
                'arab'=>1,
                'grade'=>$grade,
                'created_at'=>now(),
                'updated_at'=>now()
            ];
            $school_grades[] = [
                'school_id'=>$school->id,
                'arab'=>0,
                'grade'=>$grade,
                'created_at'=>now(),
                'updated_at'=>now()
            ];
        }


        SchoolGrade::insert($school_grades);
    }

    /**
     * Handle the School "updated" event.
     *
     * @param  \App\Models\School  $school
     * @return void
     */
    public function updated(School $school)
    {
        //
    }

    /**
     * Handle the School "deleted" event.
     *
     * @param  \App\Models\School  $school
     * @return void
     */
    public function deleted(School $school)
    {
        //
    }

    /**
     * Handle the School "restored" event.
     *
     * @param  \App\Models\School  $school
     * @return void
     */
    public function restored(School $school)
    {
        //
    }

    /**
     * Handle the School "force deleted" event.
     *
     * @param  \App\Models\School  $school
     * @return void
     */
    public function forceDeleted(School $school)
    {
        //
    }
}
