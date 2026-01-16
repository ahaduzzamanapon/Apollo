<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientReport;
use App\Models\Patient;
use App\Models\PatientTest;
use App\Models\Doctor;
use App\Models\Test;

class TestEntryForm extends Controller
{
    public function index()
    {
        $patients = PatientReport::with('tests','patient','referenceDoctor')->get();
        // dd($patients[1]->tests);
        return view('admin.patients.test_entry.patient_test_list', compact('patients'));
    }

    public function patientTestEntry($id)
    {
        $data = $this->getPatientTestData($id);
        return view('admin.patients.test_entry.patient_test_entry', $data);
    }

    public function print($id)
    {
        $data = $this->getPatientTestData($id);
        return view('admin.patients.test_entry.test_result_print', $data);
    }

    public function exportPdf($id)
    {
        $data = $this->getPatientTestData($id);
        $html = view('admin.patients.test_entry.test_result_pdf', $data)->render();

        // Debugging: Return HTML directly to check if data is populated
        // return $html; 

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'default_font' => 'FreeSerif',
            'fontDir' => array_merge($fontDirs, [
                public_path('fonts/'),
            ]),
            'fontdata' => $fontData + [
                'kalpurush' => [
                    'R' => 'kalpurush.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);

        $mpdf->WriteHTML($html);

        $patientName = $data['patients']->first()->name ?? 'Patient';
        return response($mpdf->Output('test_result_'.$patientName.'.pdf', 'S'))
             ->header('Content-Type', 'application/pdf')
             ->header('Content-Disposition', 'attachment; filename="test_result_'.$patientName.'.pdf"');
    }

    private function getPatientTestData($id)
    {
        $patients = Patient::join('patient_reports', 'patients.id', '=', 'patient_reports.patient_id')
            ->join('patient_tests', 'patient_reports.id', '=', 'patient_tests.patient_report_id')
            ->leftJoin('report_categories', 'report_categories.id', '=', 'patient_tests.report_category_id')
            ->leftJoin('test_categorys', 'test_categorys.id', '=', 'report_categories.category_name')
            ->leftJoin('test_fields', 'test_fields.test_id', '=', 'report_categories.id')
            ->where('patient_reports.id', $id)
            ->select(
                'patients.*',
                'patient_tests.id as test_id',
                'report_categories.test_name',
                'test_categorys.category_name as test_category_name',
                'test_fields.perameter',
                'test_fields.unit',
                'test_fields.ref_val',
                'test_fields.id as field_id'
            )
            ->get();

        $testIds = $patients->pluck('test_id')->unique();
        $savedResults = \App\Models\PatientTestResult::whereIn('test_id', $testIds)
            ->get()
            ->keyBy('test_id');

        $reportId = $id;
        return compact('patients', 'savedResults', 'reportId');
    }

    public function store(Request $request)
    {
        // $request->results is array: [patient_test_id => [field_id => value]]
        $results = $request->input('results', []);
        $patient_id = $request->input('patient_id');

        foreach ($results as $patientTestId => $fields) {
            // Check if all fields are empty? Maybe store anyway.
            // fields is array like [50 => "12", 51 => "100"]
            
            \App\Models\PatientTestResult::updateOrCreate(
                [
                    'patien_id' => $patient_id, // Note typo in DB column
                    'test_id' => $patientTestId, // storing patient_test_id here
                ],
                [
                    'resilt' => json_encode($fields) // Note typo in DB column
                ]
            );
        }

        return redirect()->back()->with('success', 'Test results saved successfully.');
    }
}
