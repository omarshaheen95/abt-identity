<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Services;

use App\Models\Term;
use Illuminate\Support\Collection;

class QuestionStandardService
{
    private $arabStandards;
    private $nonArabStandards;
    private $year;
    public function __construct($year)
    {
        $this->year = $year;
        $this->arabStandards = $this->initializeArabStandards();
        $this->nonArabStandards = $this->initializeNonArabStandards();
    }

    public function setQuestionsStandards(bool $arab)
    {
        foreach (range(1, 12) as $grade) {
            $this->setQuestionsStandard($grade, $arab);
        }
    }

    public function setQuestionsStandard(int $grade, bool $arab)
    {
        $assessment = Term::query()
            ->with(['question', 'level'])
            ->whereHas('level', function ($query) use ($grade, $arab) {
                $query->where('grade', $grade)
                    ->where('arab', $arab)
                    ->where('year_id', $this->year);
            })
            ->first();

        if (!$assessment) {
            return;
        }

        $standards = $arab ? $this->arabStandards : $this->nonArabStandards;



        foreach ($assessment->question->groupBy('subject_id') as $kill => $questions) {
            $skillStandards = $standards->where('skill', $kill);
            foreach ($questions as $key => $question) {
                $standard = $skillStandards->where('num', $key + 1)->first();
                if ($question->type == 'matching')
                {
                    break;
                }
                if ($standard) {
                    $question->question_standard()->updateOrCreate(
                        [
                            'standard' => $standard['standard'],
                            'mark' => $question->mark
                        ]
                    );
                }
            }
        }
    }

    public function initializeArabStandards()
    {
        return collect(
            [
                [
                    "standard" => "يدرك ويصف عدة تقاليد وطنية بدون تردد أو مساعدة مثل الطعام والضيافة.",
                    "num" => 1,
                    "skill" => 1
                ],
                [
                    "standard" => "يصف بدقة كيف تغيرت العائلة والمجتمع عبر الزمن.",
                    "num" => 2,
                    "skill" => 1
                ],
                [
                    "standard" => "يحدد ويشرح العناصر الثقافية التي ساهمت في التراث الوطني لدولة الإمارات.",
                    "num" => 3,
                    "skill" => 1
                ],
                [
                    "standard" => "يصف ويشرح الطرق المختلفة التي يعتمد الناس على الطبيعة وتفاعلهم معها.",
                    "num" => 4,
                    "skill" => 1
                ],
                [
                    "standard" => "يشارك بنشاط في مناقشات جماعية حول قضايا تتعلق بالدراسات الاجتماعية مع المعلم والأقران.",
                    "num" => 5,
                    "skill" => 1
                ],
                [
                    "standard" => "يحدد بثبات مواقع المعلومات ويجمع المعلومات المعروضة لغرض محدد.",
                    "num" => 6,
                    "skill" => 1
                ],
                [
                    "standard" => "يصف بدقة عمل ومساهمة الجماعات المتنوعة في الاقتصاد المحلي.",
                    "num" => 7,
                    "skill" => 1
                ],
                [
                    "standard" => "يُظهر معرفة وفهم العادات والتقاليد الإماراتية.",
                    "num" => 8,
                    "skill" => 1
                ],
                [
                    "standard" => "يشارك بفعالية في الفعاليات الثقافية والتقاليد الوطنية.",
                    "num" => 9,
                    "skill" => 1
                ],
                [
                    "standard" => "يصف الأنشطة الفنية والموسيقية التقليدية في الإمارات.",
                    "num" => 10,
                    "skill" => 1
                ],
                [
                    "standard" => "يحدد القيم الأساسية التي تشكل الهوية الإماراتية.",
                    "num" => 1,
                    "skill" => 2
                ],
                [
                    "standard" => "يُظهر فهمًا لمكونات الهوية الوطنية وكيف تتفاعل مع الثقافة المحلية.",
                    "num" => 2,
                    "skill" => 2
                ],
                [
                    "standard" => "يشرح دور اللغة العربية في تعزيز الهوية الإماراتية.",
                    "num" => 3,
                    "skill" => 2
                ],
                [
                    "standard" => "يجمع معلومات عن الشخصيات التاريخية التي ساهمت في تشكيل الهوية الإماراتية.",
                    "num" => 4,
                    "skill" => 2
                ],
                [
                    "standard" => "يصف الأعياد الوطنية وكيف تعكس الهوية الإماراتية.",
                    "num" => 5,
                    "skill" => 2
                ],
                [
                    "standard" => "يحدد ويشرح الفنون والحرف التقليدية التي تعبر عن الهوية الإماراتية.",
                    "num" => 6,
                    "skill" => 2
                ],
                [
                    "standard" => "يشارك في مناقشات حول أهمية الحفاظ على الهوية الإماراتية في ظل العولمة.",
                    "num" => 7,
                    "skill" => 2
                ],
                [
                    "standard" => "يشرح كيفية تأثير التعليم على تعزيز الهوية الإماراتية.",
                    "num" => 8,
                    "skill" => 2
                ],
                [
                    "standard" => "يصف العوامل التاريخية التي ساهمت في تشكيل الهوية الإماراتية.",
                    "num" => 9,
                    "skill" => 2
                ],
                [
                    "standard" => "يُظهر وعيًا بالقيم والمبادئ التي تعزز الهوية الوطنية.",
                    "num" => 10,
                    "skill" => 2
                ],
                [
                    "standard" => "يشرح الحقوق والواجبات المنصوص عليها في دستور الإمارات.",
                    "num" => 1,
                    "skill" => 3
                ],
                [
                    "standard" => "يحدد القيم الاجتماعية التي تعزز المواطنة الفعالة.",
                    "num" => 2,
                    "skill" => 3
                ],
                [
                    "standard" => "يُظهر إلمامًا بالمبادئ الأساسية للمواطنة في دولة الإمارات.",
                    "num" => 3,
                    "skill" => 3
                ],
                [
                    "standard" => "يشارك بنشاط في الأنشطة المجتمعية التي تعزز روح المواطنة.",
                    "num" => 4,
                    "skill" => 3
                ],
                [
                    "standard" => "يُظهر احترامًا للقوانين والأنظمة التي تحكم المجتمع.",
                    "num" => 5,
                    "skill" => 3
                ],
                [
                    "standard" => "يحدد كيف يمكن للأفراد المساهمة في تنمية المجتمع.",
                    "num" => 6,
                    "skill" => 3
                ],
                [
                    "standard" => "يشارك في مناقشات حول قضايا تتعلق بالمواطنة.",
                    "num" => 7,
                    "skill" => 3
                ],
                [
                    "standard" => "يصف دور الشباب في تعزيز المواطنة الفعالة.",
                    "num" => 8,
                    "skill" => 3
                ],
                [
                    "standard" => "يُظهر فهمًا للمسؤوليات الاجتماعية المرتبطة بالمواطنة.",
                    "num" => 9,
                    "skill" => 3
                ],
                [
                    "standard" => "يحدد القيم الأساسية التي يجب على المواطن الإماراتي الالتزام بها.",
                    "num" => 10,
                    "skill" => 3
                ],
                [
                    "standard" => "يُظهر قدرة على العمل الجماعي والمشاركة في المشاريع المجتمعية.",
                    "num" => 11,
                    "skill" => 3
                ],
                [
                    "standard" => "يشارك بفعالية في الانتخابات والعمليات السياسية.",
                    "num" => 12,
                    "skill" => 3
                ],
                [
                    "standard" => "يصف كيف تؤثر الثقافة الإماراتية على القيم الوطنية.",
                    "num" => 13,
                    "skill" => 3
                ],
                [
                    "standard" => "يُظهر وعيًا بأهمية التنوع الثقافي في تعزيز الوحدة الوطنية.",
                    "num" => 14,
                    "skill" => 3
                ],
                [
                    "standard" => "يحدد كيفية تعزيز قيم التسامح والتعايش السلمي في المجتمع.",
                    "num" => 15,
                    "skill" => 3
                ],
                [
                    "standard" => "يشارك في مبادرات التطوع لخدمة المجتمع.",
                    "num" => 16,
                    "skill" => 3
                ],
                [
                    "standard" => "يُظهر قدرة على التفكير النقدي في قضايا المواطنة.",
                    "num" => 17,
                    "skill" => 3
                ],
                [
                    "standard" => "يحدد أهمية التعليم في تعزيز القيم والمواطنة.",
                    "num" => 18,
                    "skill" => 3
                ],
                [
                    "standard" => "يشارك في الفعاليات الوطنية التي تعزز الهوية والمواطنة.",
                    "num" => 19,
                    "skill" => 3
                ],
                [
                    "standard" => "يصف كيف يمكن استخدام التكنولوجيا في تعزيز المواطنة الفعالة.",
                    "num" => 20,
                    "skill" => 3
                ]
            ]
        );
    }

    public function initializeNonArabStandards()
    {
        return collect(
            [
                [
                    "num" => 1,
                    "standard" => "Recognizes and describes several national traditions without hesitation or assistance such as food and hospitality.",
                    "skill" => 1
                ],
                [
                    "num" => 2,
                    "standard" => "Can accurately describe how family and society have changed over time.",
                    "skill" => 1
                ],
                [
                    "num" => 3,
                    "standard" => "Identifies and explains the cultural elements that have contributed to the national heritage of the UAE.",
                    "skill" => 1
                ],
                [
                    "num" => 4,
                    "standard" => "Can describe and explain the different ways in which people rely on nature and their interaction with it.",
                    "skill" => 1
                ],
                [
                    "num" => 5,
                    "standard" => "Actively participates in group discussions on issues related to social studies with the teacher and peers.",
                    "skill" => 1
                ],
                [
                    "num" => 6,
                    "standard" => "Consistently locates information and collects information displayed for a specific purpose.",
                    "skill" => 1
                ],
                [
                    "num" => 7,
                    "standard" => "Accurately describes the work and contribution of diverse groups to the local economy.",
                    "skill" => 1
                ],
                [
                    "num" => 8,
                    "standard" => "Demonstrates knowledge and understanding of Emirati customs and traditions.",
                    "skill" => 1
                ],
                [
                    "num" => 9,
                    "standard" => "Actively participates in cultural events and national traditions.",
                    "skill" => 1
                ],
                [
                    "num" => 10,
                    "standard" => "It describes traditional artistic and musical activities in the UAE.",
                    "skill" => 1
                ],
                [
                    "num" => 1,
                    "standard" => "Identifies the core values that make up the Emirati identity.",
                    "skill" => 2
                ],
                [
                    "num" => 2,
                    "standard" => "Demonstrates an understanding of the components of national identity and how it interacts with the local culture.",
                    "skill" => 2
                ],
                [
                    "num" => 3,
                    "standard" => "Explains the role of the Arabic language in promoting Emirati identity.",
                    "skill" => 2
                ],
                [
                    "num" => 4,
                    "standard" => "Collects information on historical figures who have contributed to the formation of Emirati identity.",
                    "skill" => 2
                ],
                [
                    "num" => 5,
                    "standard" => "Describes national holidays and how they reflect Emirati identity.",
                    "skill" => 2
                ],
                [
                    "num" => 6,
                    "standard" => "Identifies and explains traditional arts and crafts that express Emirati identity.",
                    "skill" => 2
                ],
                [
                    "num" => 7,
                    "standard" => "Participates in discussions on the importance of preserving Emirati identity in a globalized environment.",
                    "skill" => 2
                ],
                [
                    "num" => 8,
                    "standard" => "Explains how education has impacted the strengthening of Emirati identity.",
                    "skill" => 2
                ],
                [
                    "num" => 9,
                    "standard" => "Describes the historical factors that contributed to the formation of Emirati identity.",
                    "skill" => 2
                ],
                [
                    "num" => 10,
                    "standard" => "Demonstrates awareness of the values and principles that strengthen national identity.",
                    "skill" => 2
                ],
                [
                    "num" => 1,
                    "standard" => "Explains the rights and duties enshrined in the UAE Constitution.",
                    "skill" => 3
                ],
                [
                    "num" => 2,
                    "standard" => "Identifies social values that promote active citizenship.",
                    "skill" => 3
                ],
                [
                    "num" => 3,
                    "standard" => "Demonstrate familiarity with the basic principles of citizenship in the UAE.",
                    "skill" => 3
                ],
                [
                    "num" => 4,
                    "standard" => "Actively participates in community activities that promote the spirit of citizenship.",
                    "skill" => 3
                ],
                [
                    "num" => 5,
                    "standard" => "Shows respect for the laws and regulations that govern society.",
                    "skill" => 3
                ],
                [
                    "num" => 6,
                    "standard" => "Defines how individuals can contribute to the development of society.",
                    "skill" => 3
                ],
                [
                    "num" => 7,
                    "standard" => "Participates in discussions on issues related to citizenship.",
                    "skill" => 3
                ],
                [
                    "num" => 8,
                    "standard" => "Describes the role of youth in promoting active citizenship.",
                    "skill" => 3
                ],
                [
                    "num" => 9,
                    "standard" => "Demonstrates an understanding of the social responsibilities associated with citizenship.",
                    "skill" => 3
                ],
                [
                    "num" => 10,
                    "standard" => "Demonstrates an understanding of the social responsibilities associated with citizenship.",
                    "skill" => 3
                ],
                [
                    "num" => 11,
                    "standard" => "Demonstrates an ability to work together and participate in community projects.",
                    "skill" => 3
                ],
                [
                    "num" => 12,
                    "standard" => "Actively participates in elections and political processes.",
                    "skill" => 3
                ],
                [
                    "num" => 13,
                    "standard" => "Describes how Emirati culture influences national values.",
                    "skill" => 3
                ],
                [
                    "num" => 14,
                    "standard" => "Demonstrate awareness of the importance of cultural diversity in promoting national unity.",
                    "skill" => 3
                ],
                [
                    "num" => 15,
                    "standard" => "Determines how to promote the values of tolerance and peaceful coexistence in society.",
                    "skill" => 3
                ],
                [
                    "num" => 16,
                    "standard" => "Determines how to promote the values of tolerance and peaceful coexistence in society.",
                    "skill" => 3
                ],
                [
                    "num" => 17,
                    "standard" => "Participates in volunteer initiatives to serve the community.",
                    "skill" => 3
                ],
                [
                    "num" => 18,
                    "standard" => "Identifies the importance of education in promoting values and citizenship.",
                    "skill" => 3
                ],
                [
                    "num" => 19,
                    "standard" => "Participates in national events that promote identity and citizenship.",
                    "skill" => 3
                ],
                [
                    "num" => 20,
                    "standard" => "Describes how technology can be used to promote active citizenship.",
                    "skill" => 3
                ]
            ]
        );
    }
}
