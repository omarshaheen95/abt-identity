# Report Generation Inputs Documentation

This document outlines the required input fields for generating different types of reports in the system.

---

## **General Required Inputs**

| Field Name              | Input Type             | Options / Description                                                                               |
|-------------------------|------------------------|-----------------------------------------------------------------------------------------------------|
| **Report Type** (`generated_report_type`) | Dropdown | - **Attainment Report** → `"attainment"`<br>- **Combined Report** → `"combined"`                    |
| **School** (`school_id`) | Dropdown / Hidden | - **Multi-select** if guard is `manager` or `inspection`<br>- **Hidden input** if guard is `school` |
| **Student Type** (`student_type`) | Dropdown | - **All Students** → `"2"`<br>- **Arabs** → `"1"`<br>- **Non-Arabs** → `"0"`                        |
| **Grades** (`grades[]`) | Checkbox (1–12) | Select one or multiple grades                                                                       |

---

## **Attainment Report & Progress Report – Additional Inputs**

| Field Name               | Input Type   | Description |
|--------------------------|--------------|-------------|
| **Year** (`year_id`)     | Dropdown     | Select the academic year |
| **Grades Names** (`grades_names[]`) | Dropdown (array) | List of grade names depending on the selected school and year |
| **Include SEN** (`include_sen`)     | Radio        | Option to include Special Educational Needs students |
| **Include G&T** (`include_g_t`)     | Radio        | Option to include Gifted & Talented students |

---

## **Year To Year Progress Report & Trends Over Time Report – Additional Inputs**

| Field Name  | Input Type  | Description |
|-------------|-------------|-------------|
| **Years** (`years[]`) | Checkbox | Select 2 or 3 years depending on the report type |
| **Round** (`round`)   | Dropdown / Input | Specify the assessment round |

---
## **Student Report**

| Field Name                    | Input Type  | Description |
|-------------------------------|-------------|-------------|
| **Student Id** (`student_id`) | Checkbox | Select 2 or 3 years depending on the report type |
| **School** (`school_id`)      | Dropdown / Input | Specify the assessment round |

---
