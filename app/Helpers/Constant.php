<?php

namespace App\Helpers;


class Constant
{
    const GENDER_MALE = [1, 'b', 'boy', 'Boy', 'BOY', 'm', 'male', 'Male', 'MALE'];
    const GENDER_FEMALE = [2, 'g', 'girl', 'Girl', 'GIRL', 'f', 'female', 'Female', 'FEMALE'];
    const YES = [1, 'yes', 'true', 'on', '1', 'y', 'Y', 'Yes', 'TRUE', 'ON', 'YES'];
    const NO = [0, 'no', 'false', 'off', '0', 'n', 'N', 'No', 'FALSE', 'OFF', 'NO'];

    const UPLOAD_TYPE = ['create', 'update', 'delete'];
    const NOT_STARTED = 0;
    const RATING_TAUGHT = 1;
    const RATING_ALMOST = 2;
    const RATING_MET = 3;
    const RATING_EXCEEDING= 4;
    const GRADES = [
        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12
    ];
    const CATEGORIES = ['Exam','Quiz'];
    const ROUNDS = [
        'September', 'February', 'May'
    ];
    const OTHER_ROUNDS = [
        'May', 'September', 'February'
    ];
    const ROUNDS_KEY = [
        0, 1, 2
    ];
    const OTHER_ROUNDS_KEY = [
        2, 0, 1
    ];
    const ROUNDS_PROGRESS = [
       [
              0,1
         ], [
              1, 2
         ], [
             0, 2
       ]
    ];
    const OTHER_ROUNDS_PROGRESS = [
        [
            2,0
        ], [
            0, 1
        ], [
            2, 1
        ]
    ];
    const UPLOAD_STATUS = ['New', 'Uploading', 'Completed', 'Failed'];
    const COUNTRY = ['uae', 'kwt', 'qatar', 'oman', 'ksa', 'egy', 'bahrain'];
    const CURRICULUM = ['MOE', 'British', 'American', 'Indian', 'Iranian', 'Philippine', 'International Baccalaureate', 'Australian', 'Pakistan', 'French', 'Canadian', 'German', 'Others'];
    const CONST_COMPLETED = ['key' => 'completed', 'name' => 'completed', 'color' => '#0CAF60'];
    const CONST_IN_PROGRESS = ['key' => 'in_progress', 'name' => 'in_progress', 'color' => '#FFC837'];
    const CONST_NOT_STARTED = ['key' => 'not_started', 'name' => 'not_started', 'color' => '#E03137'];
    const CONST_SUBMITTED = ['key' => 'submitted', 'name' => 'submitted', 'color' => '#2F78EE'];
    const CONST_NOT_SUBMITTED = ['key' => 'not_submitted', 'name' => 'not_submitted', 'color' => '#FFC837'];
    const CONST_REJECTED = ['key' => 'rejected', 'name' => 'rejected', 'color' => '#E03137'];
    const CONST_ACCEPTED = ['key' => 'accepted', 'name' => 'accepted', 'color' => '#0CAF60'];
    const CONST_PUBLISH = ['key' => 'publish', 'name' => 'publish', 'color' => '#0CAF60'];
    const CONST_DRAFT = ['key' => 'draft', 'name' => 'draft', 'color' => '#FFC837'];

    const SUBJECTS = [
        'Arabic', 'Islamic', 'SocialStudies',
    ];


    const MAIN_SUBJECTS = [
        [
            'name' => [
                'en' => 'Arabic For Arabs', 'ar' => 'اللغة العربية للناطقين بها',
            ],
            'slug' => 'Arabic-For-Arabs',
            'arab' => true,
            'example_file' => 'Examples Sheets/Arabic For Arabs Example.xlsx',
            'type' => 'Arabic',
            'total_ranges' => [
                'below' => [0,49],
                'inline' => [50,69],
                'above' => [70,100],
            ],
            'total_ranges_10' => [
                'below' => [0,59],
                'inline' => [60,69],
                'above' => [70,100],
            ],
        ],
        [
            'name' => [
                'en' => 'Arabic For Non-Arabs', 'ar' => 'اللغة العربية للناطقين بغيرها',
            ],
            'slug' => 'Arabic-For-Non-Arabs',
            'arab' => false,
            'example_file' => 'Examples Sheets/Arabic For Non-Arabs Example.xlsx',
            'type' => 'Arabic',
            'total_ranges' => [
                'below' => [0,49],
                'inline' => [50,69],
                'above' => [70,100],
            ],
            'total_ranges_10' => [
                'below' => [0,59],
                'inline' => [60,69],
                'above' => [70,100],
            ],
        ],
        [
            'name' => [
                'en' => 'Islamic Studies For Arabs', 'ar' => 'التربية الإسلامية للطلاب العرب',
            ],
            'slug' => 'Islamic-Studies-For-Arabs',
            'arab' => true,
            'example_file' => 'Examples Sheets/Islamic Studies For Arabs Example.xlsx',
            'type' => 'Islamic',
            'total_ranges' => [
                'below' => [0,49],
                'inline' => [50,69],
                'above' => [70,100],
            ],
            'total_ranges_10' => [
                'below' => [0,59],
                'inline' => [60,69],
                'above' => [70,100],
            ],
        ],
        [
            'name' => [
                'en' => 'Islamic Studies For Non-Arabs', 'ar' => 'التربية الإسلامية للطلاب غير العرب',
            ],
            'slug' => 'Islamic-Studies-For-Non-Arabs',
            'arab' => false,
            'example_file' => 'Examples Sheets/Islamic Studies For Non-Arabs Example.xlsx',
            'type' => 'Islamic',
            'total_ranges' => [
                'below' => [0,49],
                'inline' => [50,69],
                'above' => [70,100],
            ],
            'total_ranges_10' => [
                'below' => [0,59],
                'inline' => [60,69],
                'above' => [70,100],
            ],
        ],
        [
            'name' => [
                'en' => 'Social Studies For Arabs', 'ar' => 'الدراسات الاجتماعية للطلاب العرب',
            ],
            'slug' => 'Social-Studies-For-Arabs',
            'arab' => true,
            'example_file' => 'Examples Sheets/Social Studies For Arabs Example.xlsx',
            'type' => 'SocialStudies',
            'total_ranges' => [
                'below' => [0,49],
                'inline' => [50,69],
                'above' => [70,100],
            ],
            'total_ranges_10' => [
                'below' => [0,59],
                'inline' => [60,69],
                'above' => [70,100],
            ],
        ],
        [
            'name' => [
                'en' => 'Social Studies For Non-Arabs', 'ar' => 'الدراسات الاجتماعية للطلاب غير العرب',
            ],
            'slug' => 'Social-Studies-For-Non-Arabs',
            'arab' => false,
            'example_file' => 'Examples Sheets/Social Studies For Non-Arabs Example.xlsx',
            'type' => 'SocialStudies',
            'total_ranges' => [
                'below' => [0,49],
                'inline' => [50,69],
                'above' => [70,100],
            ],
            'total_ranges_10' => [
                'below' => [0,59],
                'inline' => [60,69],
                'above' => [70,100],
            ],
        ],

    ];

    const ARABIC_SKILLS = [
        [
            'ar' => 'القراءة', 'en' => 'Reading',
        ],
        [
            'ar' => 'الاستماع', 'en' => 'Listening',
        ],
        [
            'ar' => 'الكتابة', 'en' => 'Writing',
        ],
        [
            'ar' => 'التحدث', 'en' => 'Speaking',
        ],
    ];

    const ISLAMIC_SKILLS = [
        [
            'en' => 'The Holy Qur’an and Hadeeth', 'ar' => 'القرآن الكريم والحديث الشريف',
        ],
        [
            'en' => 'Islamic values and Principles', 'ar' => 'قيم الإسلام و آدابه',
        ],
        [
            'en' => 'Islamic law and Etiquettes', 'ar' => 'أحكام الإسلام و مقاصدها',
        ],
        [
            'en' => 'Seerah and Islamic figures', 'ar' => 'السيرة والشخصيات الإسلامية',
        ],
        [
            'en' => 'Faith', 'ar' => 'العقيدة',
        ],
        [
            'en' => 'Identity and Belonging', 'ar' => 'الهوية والإنتماء',
        ],
    ];

    const SOCIAL_ARABS_SKILLS = [
        ['en' => 'Geography', 'ar' => 'الجغرافيا'],
        ['en' => 'History', 'ar' => 'التاريخ'],
        ['en' => 'National Education', 'ar' => 'التربية الوطنية'],
        ['en' => 'Economy', 'ar' => 'الإقتصاد'],
    ];

    const SOCIAL_NON_ARABS_SKILLS = [
        ['en' => 'Moral Education', 'ar' => 'التربية الأخلاقية'],
        ['en' => 'Social Studies', 'ar' => 'الدراسات الإجتماعية'],
        ['en' => 'Cultural Education', 'ar' => 'الثقافة'],
    ];

    const SKILLS = [
        'Reading', 'Listening', 'Writing', 'Speaking', 'The Holy Qur’an and Hadeeth', 'Islamic values and Principles', 'Islamic law and Etiquettes',
        'Seerah and Islamic figures', 'Faith', 'Identity and Belonging', 'Geography',
        'History', 'National Education', 'Economy', 'Moral Education', 'Social Studies', 'Cultural Education',
    ];
    const SKILLS_MARKS = [
        'Reading' => [
            'total' => 25,
            'ranges' => [
                'below' => [0,12],
                'inline' => [13,19],
                'above' => [20,25],
            ],
            'ranges_10' => [
                'below' => [0,14],
                'inline' => [15,19],
                'above' => [20,25],
            ],
        ],
        'Listening'=> [
            'total' => 25,
            'ranges' => [
                'below' => [0,12],
                'inline' => [13,19],
                'above' => [20,25],
            ],
            'ranges_10' => [
                'below' => [0,14],
                'inline' => [15,19],
                'above' => [20,25],
            ],
        ],
        'Writing'=> [
            'total' => 25,
            'ranges' => [
                'below' => [0,12],
                'inline' => [13,19],
                'above' => [20,25],
            ],
            'ranges_10' => [
                'below' => [0,14],
                'inline' => [15,19],
                'above' => [20,25],
            ],
        ],
        'Speaking'=> [
            'total' => 25,
            'ranges' => [
                'below' => [0,12],
                'inline' => [13,19],
                'above' => [20,25],
            ],
            'ranges_10' => [
                'below' => [0,14],
                'inline' => [15,19],
                'above' => [20,25],
            ],
        ],
        'The Holy Qur’an and Hadeeth'=> [
            'total' => 30,
            'ranges' => [
                'below' => [0,15],
                'inline' => [16,21],
                'above' => [22,30],
            ],
            'ranges_10' => [
                'below' => [0,17],
                'inline' => [18,23],
                'above' => [24,30],
            ],
        ],
        'Islamic values and Principles'=> [
            'total' => 10,
            'ranges' => [
                'below' => [0,5],
                'inline' => [6,7],
                'above' => [8,10],
            ],
            'ranges_10' => [
                'below' => [0,6],
                'inline' => [7,8],
                'above' => [9,10],
            ],
        ],
        'Islamic law and Etiquettes'=> [
            'total' => 10,
            'ranges' => [
                'below' => [0,5],
                'inline' => [6,7],
                'above' => [8,10],
            ],
            'ranges_10' => [
                'below' => [0,6],
                'inline' => [7,8],
                'above' => [9,10],
            ],
        ],
        'Seerah and Islamic figures'=> [
            'total' => 20,
            'ranges' => [
                'below' => [0,10],
                'inline' => [11,14],
                'above' => [15,20],
            ],
            'ranges_10' => [
                'below' => [0,12],
                'inline' => [13,16],
                'above' => [17,20],
            ],
        ],
        'Faith'=> [
            'total' => 20,
            'ranges' => [
                'below' => [0,10],
                'inline' => [11,14],
                'above' => [15,20],
            ],
            'ranges_10' => [
                'below' => [0,12],
                'inline' => [13,16],
                'above' => [17,20],
            ],
        ],
        'Identity and Belonging'=> [
            'total' => 10,
            'ranges' => [
                'below' => [0,5],
                'inline' => [6,7],
                'above' => [8,10],
            ],
            'ranges_10' => [
                'below' => [0,6],
                'inline' => [7,8],
                'above' => [9,10],
            ],
        ],
        'Geography'=> [
            'total' => 25,
            'ranges' => [
                'below' => [0,12],
                'inline' => [13,18],
                'above' => [19,25],
            ],
            'ranges_10' => [
                'below' => [0,12],
                'inline' => [13,18],
                'above' => [19,25],
            ],
        ],
        'History'=> [
            'total' => 25,
            'ranges' => [
                'below' => [0,12],
                'inline' => [13,18],
                'above' => [19,25],
            ],
            'ranges_10' => [
                'below' => [0,12],
                'inline' => [13,18],
                'above' => [19,25],
            ],
        ],
        'National Education'=> [
            'total' => 25,
            'ranges' => [
                'below' => [0,12],
                'inline' => [13,18],
                'above' => [19,25],
            ],
            'ranges_10' => [
                'below' => [0,12],
                'inline' => [13,18],
                'above' => [19,25],
            ],
        ],
        'Economy'=> [
            'total' => 25,
            'ranges' => [
                'below' => [0,12],
                'inline' => [13,18],
                'above' => [19,25],
            ],
            'ranges_10' => [
                'below' => [0,12],
                'inline' => [13,18],
                'above' => [19,25],
            ],
        ],
        'Moral Education'=> [
            'total' => 33,
            'ranges' => [
                'below' => [0,16],
                'inline' => [17,23],
                'above' => [24,33],
            ],
            'ranges_10' => [
                'below' => [0,16],
                'inline' => [17,23],
                'above' => [24,33],
            ],
        ],
        'Social Studies'=> [
            'total' => 33,
            'ranges' => [
                'below' => [0,16],
                'inline' => [17,23],
                'above' => [24,33],
            ],
            'ranges_10' => [
                'below' => [0,16],
                'inline' => [17,23],
                'above' => [24,33],
            ],
        ],
        'Cultural Education'=> [
            'total' => 34,
            'ranges' => [
                'below' => [0,16],
                'inline' => [17,23],
                'above' => [24,34],
            ],
            'ranges_10' => [
                'below' => [0,16],
                'inline' => [17,23],
                'above' => [24,34],
            ],
        ],

    ];

    const SUBJECTS_TYPES = [
        'Arabs', 'Non-Arabs',
    ];

    const LESSONS_INPUTS = [
        ['label' => 'Delivery Platform', 'name' => 'delivery_platform', 'type' => 'text', 'section' => null],
        ['label' => 'Support Staff', 'name' => 'support_staff', 'type' => 'text', 'section' => null],
        ['label' => 'Number', 'name' => 'number', 'type' => 'number', 'section' => null],
        ['label' => 'Girls', 'name' => 'boy', 'type' => 'number', 'section' => null],
        ['label' => 'Boys', 'name' => 'girl', 'type' => 'number', 'section' => null],
        ['label' => 'Emirati', 'name' => 'emirati', 'type' => 'number', 'section' => null],
        ['label' => 'SoD', 'name' => 'SoD', 'type' => 'number', 'section' => null],
        ['label' => 'EAL', 'name' => 'EAL', 'type' => 'number', 'section' => null],
        ['label' => 'G&T', 'name' => 'G&T', 'type' => 'number', 'section' => null],
        ['label' => 'Date', 'name' => 'date', 'type' => 'date', 'section' => null],
        ['label' => 'Key Vocabulary', 'name' => 'key_vocabulary', 'type' => 'text', 'section' => null],
        ['label' => 'Screen Time', 'name' => 'screen_time', 'type' => 'text', 'section' => null],

        ['label' => 'By the end of the lesson, students should', 'name' => 'lesson_benefits', 'type' => 'textarea', 'section' => null],
        ['label' => 'Reference to scheme of work/ previous learning / Context', 'name' => 'context', 'type' => 'textarea', 'section' => null],

        ['label' => 'Time', 'name' => 'time', 'type' => 'text', 'section' => 'section_1','section_inputs'=> [
            ['label' => 'Live Learning and Teaching Activity', 'name' => 'learning_teaching_activity', 'type' => 'textarea'],
        ]],

        ['label' => 'Time', 'name' => 'time', 'type' => 'text', 'section' => 'section_2','section_inputs'=> [
            ['label' => 'Independent/Offline Learning', 'name' => 'independent_offline_learning', 'type' => 'textarea'],
            ['label' => 'Live Support Strategies Available', 'name' => 'live_support_strategies', 'type' => 'textarea'],
            ['label' => 'Differentiation', 'name' => 'differentiation', 'type' => 'textarea'],
            ['label' => 'E-Safety Measures', 'name' => 'e_safety', 'type' => 'textarea'],
        ]],


        ['label' => 'Time', 'name' => 'time', 'type' => 'text', 'section' => 'section_3', 'section_inputs'=>[
            ['label' => 'Plenary/Assessment Opportunities', 'name' => 'plenary_assessment_opportunities', 'type' => 'textarea'],
        ]],

    ];

    const TEXT = 'text';
    const TEXTAREA = 'textarea';
    const RADIO_BOX = 'radioBox';
    const PASSWORD = 'password';
    const FILE = 'file';
    const NUMBER = 'number';
    const COLOR = 'color';
    const TIME = 'time';
    const HIDDEN = 'hidden';
    const ARRAY = 'array';
    const OBJECT = 'object';

    const NULL = 'null';
    const NOT_NULL = 'not_null';
    const SEARCH = 'search';
    const TYPE = 'type';
    const BETWEEN = 'between';
    const IMAGE = 'image';
    const VIDEO = 'video';
    const DOCUMENT = 'document';
    const OTHER = 'other';
    const MAIN_IMAGE = 'main_image';
    const COVER_IMAGE = 'cover_image';

    const CP_GUARD = 'admin';
    const API_GUARD = 'api';


    const OUR_LANGUAGES = ['ar', 'en'];
    const STATUS = ['Draft', 'Published', 'Archived'];
    const STATUS_DRAFT = ['key' => 'Draft', 'value' => 'Draft', 'color' => '#FFC107'];
    const STATUS_PUBLISHED = ['key' => 'Published', 'value' => 'Published', 'color' => '#4CAF50'];
    const STATUS_ARCHIVED = ['key' => 'Archived', 'value' => 'Archived', 'color' => '#9E9E9E'];

    // date time response format
    const RESPONSE_DATETIME_FORMAT = 'd-m-Y h:i a';
    const RESPONSE_DATE_FORMAT = 'd-m-Y';
    const RESPONSE_TIME_FORMAT = 'h:i a';

    // date time validations
    const DATETIME_FORMAT = 'Y-m-d H:i:s';
    const DATE_FORMAT = 'Y-m-d';
    const TIME_FORMAT = 'H:i:s';
    const PERIOD_FORMAT = '%H:%I:%S';

    const STRING_REGEX = '/^[a-zA-Z0-9_.\s-]*$/u';
    const NORMAL_STRING_REGEX = '/^[{Arabic}a-zA-Z0-9_.\s-]*$/u';
    const ARABIC_STRING_REGEX = "/[\p{Arabic}\s\-_.,!?;:]+/u";
    const ENGLISH_STRING_REGEX = "/[a-zA-Z0-9\s\-_.,!?;:]+/u";
    const PHONE_REGEX = '/^([0-9\s\-\+\(\)]*)$/u';
    const USERNAME_EMAIL_REGEX = '/^(?=.{4,}$)[a-zA-Z0-9._@-]+(?<![_.@])$/u';


    const Comment = ['approved', 'rejected'];
    const APPROVED = ['key' => 'approved', 'value' => 'approved', 'color' => '#4c87af'];
    const WAITING = ['key' => 'waiting', 'value' => 'waiting', 'color' => '#FFC107'];
    const REJECTED = ['key' => 'rejected', 'value' => 'rejected', 'color' => '#9E9E9E'];

    const EXIST_SUBSCRIBERS_STATUS = [
        'Skip', 'Overwrite', 'Merge',
    ];

    const DRAFT = 'Draft';
    const SENT = 'Sent';
    const STOPPED = 'Stopped';
    const FAILED = 'Failed';
    const PUBLISHED = 'Published';
    const STATUS_ACCEPTED = ['key' => 'accepted', 'value' => 'accepted', 'color' => '#FFC107'];
    const STATUS_SUBMITTED = ['key' => 'submitted', 'value' => 'submitted', 'color' => '#4CAF50'];
    const STATUS_REJECTED = ['key' => 'rejected', 'value' => 'rejected', 'color' => '#9E9E9E'];

    const QUESTIONS_TYPES = ['true_false','multiple_choice','matching','sorting','writing','speaking','fill_blank'];
    const MEDIA_TYPES = ['image', 'audio', 'video', 'file'];
    const EXAM_SUBMISSION_TYPES = ['all', 'twice', 'single'];

    const REPORT_PERMISSIONS = [
        'students reports',
        'attainment reports',
        'combined reports',
        'progress reports',
        'general reports',
    ];


}
