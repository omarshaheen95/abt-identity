<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>A.B.T Identity Benchmark Test</title>
</head>
<body style="margin:0px; background: #f8f8f8; ">
<div width="100%"
     style="background: #f8f8f8; padding: 0px 0px; font-family:arial; line-height:28px; height:100%;  width: 100%; color: #514d6a;">
    <div style="max-width: 700px; padding:50px 0;  margin: 0px auto; font-size: 14px">
        <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 20px">
            <tbody>
            <tr>
                <td style="vertical-align: top; padding-bottom:30px;" align="center">
                    <a href="{{ url('/') }}" target="_blank">
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
        <div style="padding: 40px; background: #fff; direction: ltr;     font-size: 16px;">
            <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                <tbody>
                <tr>
                    <td>
                        <b>A.B.T Identity Benchmark Test</b>
                        <p>Dear , {{$markingRequest->school->name}}</p>

                        <p>Kindly note that the Status of the Marking Request of ABT assessments have been <span style="font-weight:bolder;color:@if($markingRequest->status == 'Accepted') #00FF00 @elseif($markingRequest->status == 'In Progress') #e37200 @elseif($markingRequest->status == 'Completed') #0000FF  @elseif($markingRequest->status == 'Rejected') #FF0000 @else #000 @endif">
                                {{$markingRequest->status == 'Pending' ? 'Submitted and pending for approval':$markingRequest->status }}</span>
                        </p>
                        @if(!is_null($markingRequest->notes))
                            <p>Notes: {{$markingRequest->notes}}</p>
                        @endif

                        <br/>
                        @if(isset($data) && count($data) > 0)
                            <!-- Extra Content Section -->
                            <p><b>Year:</b> {{$data['year']}}</p>
                            <p><b>Round: </b>{{$data['round']}}
                            </p>
                            <p><b>Grades:</b>
                                Grade {{$data['grades']}} </p>
                            <p><b>Section:</b> {{$data['section']}}</p>
                            <!-- End Extra Content Section -->
                            <br />
                            <!-- New Paragraph -->
                            <p style="color: blue;">The results will be available within 7 working days - Then you can feel free to download it.</p>
                            <!-- End New Paragraph -->
                            <br />
                            <!-- New Table Section -->
                            <b>Overall Student Status</b>
                            <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; margin-top: 20px; text-align: center;">
                                <thead>
                                <tr>
                                    <th>Total Students</th>
                                    <th>Started</th>
                                    <th>Not Started Yet</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{$data['total']}}</td>
                                    <td style="color: green;">{{$data['started']}}  ({{$data['started_per']}}%)</td>
                                    <td style="color: red;">{{$data['not_started']}}  ({{$data['not_started_per']}}%)</td>
                                </tr>
                                </tbody>
                            </table>
                            <!-- End New Table Section -->

                        @endif
                        @if($markingRequest->status == 'Completed')
                            <br />
                            <p style="color: blue;">The results are ready now - please feel free to download from your school control panel</p>
                            <br />
                        @endif
                        <b>Thanks .</b>
                        <center>
                            <b>A.B.T Identity Benchmark Test</b>
                        </center>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px">
{{--            <img src="{{asset('images/email_footer.jpg')}}" width="100%" />--}}
        </div>
    </div>
</div>
</body>
</html>

