<?php

namespace App\Http\Controllers\V1\IssueNote;

use PDF;
use Carbon\Carbon;
use App\Models\IssueNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Interfaces\IssueNoteServiceInterface;

class IssueNoteController extends Controller
{
    protected $issueNoteService;

    public function __construct(IssueNoteServiceInterface $issueNoteService)
    {
        $this->issueNoteService = $issueNoteService;
    }

    public function createView()
    {
        $lastIssueNoteNo = IssueNote::max('issue_note_number');

        $lastIssueNoteNum = intval(substr($lastIssueNoteNo, 3));
        $newIssueNoteNum = str_pad($lastIssueNoteNum + 1, 6, '0', STR_PAD_LEFT);
        $newIssueNoteNo = 'IN-' . $newIssueNoteNum;

        $response = $this->issueNoteService->createView();

        if ($response['status'] === 200) {

            return view('issueNote.create', [
                'newIssueNoteNo' => $newIssueNoteNo,
                'products' => $response['data']['products'],
            ]);
        } else {
            return view('error')->with('message', $response['message']);
        }
    }

    public function getProductData($productId)
    {

        $response = $this->issueNoteService->getProductData($productId);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }



    public function createIssueNote(Request $attributes)
    {

        $validator = Validator::make($attributes->all(), [
            'issue_note_number' => 'required|string',
            'issue_note_date' => 'required|string',
            'lorry_number' => 'nullable|string',
            'driver_name' => 'nullable|string',
            'remarks' => 'nullable|string',
            'issue_products' => 'required|array|min:1',
            'issue_products.*.productId' => 'required|integer|exists:products,id',
            'issue_products.*.issueQuantity' => 'required|numeric|min:0.01',
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => 400, 'message' => $validator->errors()->first(), 'data' => null], 400);
        }

        $response = $this->issueNoteService->createIssueNote($attributes);

        return response()->json(['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }


    public function getAll()
    {
        $response = $this->issueNoteService->getAll();

        // Check the status and extract data
        if ($response['status'] === 200) {
            $issue_notes = $response['data'];
            // dd($issue_notes);
        } else {
            $issue_notes = [];
            // You might want to handle the error differently, e.g., redirect with a message
            return redirect()->back()->withErrors(['message' => $response['message']]);
        }

        return view('issueNote.issueNoteList', compact('issue_notes'));
    }

    public function deleteIssueNote($issue_note_id)
    {
        $response = $this->issueNoteService->deleteIssueNote($issue_note_id);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }

    public function issueNoteProductsView($issue_note_id)
    {
        $response = $this->issueNoteService->issueNoteProductsView($issue_note_id);

        return (['status' => $response['status'], 'message' => $response['message'], 'data' => $response['data']]);
    }

    public function genaratePdf($issue_note_id)
    {

        $response = $this->issueNoteService->genaratePdf($issue_note_id);

        if ($response['status'] === 200) {
            $pdfData = $response['data'];

            $date = $pdfData->current_date;

            $pdf = PDF::loadView('issueNote.pdf', compact('pdfData'));

            // Return the PDF as a download
            return $pdf->download('Issue-Note_' . $date . '.pdf');
        } else {
            // Handle error response
            return response()->json(['status' => $response['status'], 'message' => $response['message']], $response['status']);
        }
    }
}
