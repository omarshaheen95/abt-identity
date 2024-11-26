@extends('student.layout.container')
@section('title')
    {{t('Home')}}
@endsection

@section('content')
    <section class="mt-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    @if(session('term-message'))
                        <div class="alert alert-info alert-dismissible fade show mt-2" role="alert">
                            {{session('term-message')}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="header-card">
                        <div class="info">
                            <div class="logo">
                                <img class="img-fluid" alt="Logo" src="{{!settingCache('logo')? asset('logo.svg'):asset(settingCache('logo'))}}" />
                            </div>

                            <div class="content">
                                <ul class="nav justify-content-center">
                                    <li class="nav-item">  {{t('Username')}} :<span class="ms-2"> {{$student->name}} </span> </li>
                                    <li class="nav-item">  {{t('Level')}} :<span class="ms-2">{{$student->level->name}}</span> </li>
                                    <li class="nav-item">  {{t('Student Id')}} :<span class="ms-2"> {{$student->id}} </span> </li>
                                    <li class="nav-item">  {{t('Completed Assessments')}} :<span class="ms-2"> {{count($completed_terms)}} </span> </li>
                                    <li class="nav-item">  {{t('Available Assessments')}} :<span class="ms-2"> {{count($available_terms)}} </span> </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab">
                            <ul class="nav nav-tab justify-content-center">
                                <li class="nav-item">
                                    <a href="#available" data-bs-toggle="tab" class="nav-link {{session('term-message')?'':'active'}}"> {{t('Available Assessments')}}</a>
                                </li>

                                <li class="nav-item">
                                    <a href="#complete" data-bs-toggle="tab" class="nav-link {{session('term-message')?'active':''}} "> {{t('Completed Assessments')}}  </a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-lg-12">
                    <div class="tab-content my-md-5 my-4">


                        <div class="tab-pane fade {{session('term-message')?'':'show active'}}" id="available">
                            <div class="row">
                                @if(count($available_terms)>0)
                                    @foreach($available_terms as $term)
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <div class="exam-card">
                                                <div class="icon">
                                                    <svg id="vuesax_twotone_document-text" data-name="vuesax/twotone/document-text" xmlns="http://www.w3.org/2000/svg" width="72.747" height="72.747" viewBox="0 0 72.747 72.747">
                                                        <g id="document-text">
                                                            <path id="Vector" d="M54.56,15.156V45.467c0,9.093-4.547,15.156-15.156,15.156H15.156C4.547,60.622,0,54.56,0,45.467V15.156C0,6.062,4.547,0,15.156,0H39.4C50.013,0,54.56,6.062,54.56,15.156Z" transform="translate(9.093 6.062)" fill="none" stroke="#662d91" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/>
                                                            <path id="Vector-2" data-name="Vector" d="M0,0V6.062a6.08,6.08,0,0,0,6.062,6.062h6.062" transform="translate(43.951 13.64)" fill="none" stroke="#662d91" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" opacity="0.4"/>
                                                            <path id="Vector-3" data-name="Vector" d="M0,0H12.124" transform="translate(24.249 39.405)" fill="none" stroke="#662d91" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" opacity="0.4"/>
                                                            <path id="Vector-4" data-name="Vector" d="M0,0H24.249" transform="translate(24.249 51.529)" fill="none" stroke="#662d91" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" opacity="0.4"/>
                                                            <path id="Vector-5" data-name="Vector" d="M0,0H72.747V72.747H0Z" fill="none" opacity="0"/>
                                                        </g>
                                                    </svg>
                                                </div>
                                                <div class="content">
                                                    <h2 class="title">{{$term->name}} </h2>
                                                    <p class="question-count"> {{t('Number of questions')}}: <span class="count">{{count($term->question)}} </span></p>
                                                    <button type="button" class="btn" data-exam-id="{{$term->id}}">{{t('Start Now')}} </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <h6 class="w-100 text-center">{{t('No assessments are available')}}</h6>
                                @endif

                            </div>
                        </div>


                        <div class="tab-pane fade {{session('term-message')?'show active':''}}" id="complete">
                            <div class="row">
                                @if(count($completed_terms)>0)
                                    @foreach($completed_terms as $c_term)
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <div class="exam-card">
                                                <div class="icon">
                                                    <svg id="vuesax_twotone_document-text" data-name="vuesax/twotone/document-text" xmlns="http://www.w3.org/2000/svg" width="72.747" height="72.747" viewBox="0 0 72.747 72.747">
                                                        <g id="document-text">
                                                            <path id="Vector" d="M54.56,15.156V45.467c0,9.093-4.547,15.156-15.156,15.156H15.156C4.547,60.622,0,54.56,0,45.467V15.156C0,6.062,4.547,0,15.156,0H39.4C50.013,0,54.56,6.062,54.56,15.156Z" transform="translate(9.093 6.062)" fill="none" stroke="#662d91" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/>
                                                            <path id="Vector-2" data-name="Vector" d="M0,0V6.062a6.08,6.08,0,0,0,6.062,6.062h6.062" transform="translate(43.951 13.64)" fill="none" stroke="#662d91" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" opacity="0.4"/>
                                                            <path id="Vector-3" data-name="Vector" d="M0,0H12.124" transform="translate(24.249 39.405)" fill="none" stroke="#662d91" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" opacity="0.4"/>
                                                            <path id="Vector-4" data-name="Vector" d="M0,0H24.249" transform="translate(24.249 51.529)" fill="none" stroke="#662d91" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" opacity="0.4"/>
                                                            <path id="Vector-5" data-name="Vector" d="M0,0H72.747V72.747H0Z" fill="none" opacity="0"/>
                                                        </g>
                                                    </svg>
                                                </div>
                                                <div class="content">
                                                    <h2 class="title">{{$c_term->term->name}} </h2>
                                                    <p class="question-count"> {{t('Number of questions')}}: <span class="count"> {{count($c_term->term->question)}} </span></p>
                                                    <p class="question-count"> {{t('Completed at')}} : <span class="count">{{$c_term->created_at->format('Y-m-d')}}</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <h6 class="w-100 text-center">{{t('No completed assessments are available')}}</h6>
                                @endif
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </section>

   @include('student.term.parts.start-term-modal')

@endsection

@section('script')
    <script>
        $(document).on("click",".exam-card .btn", function(){
            let examID = $(this).data("exam-id"),
                examURL = "{{route('student.term',['id'=>':id'])}}".replace(":id", examID);
            $("#start-term .btn-exam-view").attr("href", examURL);
            $("#start-term").modal("show");
        });
    </script>
@endsection
