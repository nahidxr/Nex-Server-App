<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Scorm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
//use Illuminate\View\View;
use Illuminate\Support\Facades\View;
//use Illuminate\Http\Requests;
use App\Http\Requests\ScormRequest;
use Peopleaps\Scorm\Exception\InvalidScormArchiveException;
use Peopleaps\Scorm\Exception\StorageNotFoundException;
//use Peopleaps\Scorm\Manager\ScormManager;
use Peopleaps\Scorm\Model\ScormModel;
use Peopleaps\Scorm\Manager\ScormManager;
use Peopleaps\Scorm\Model\ScormScoTrackingModel;

class ScormController extends Controller {


    private $scormManager;


    public function __construct(ScormManager $scormManager) {
        $this->scormManager = $scormManager;
    }


    public function index() {
        $products = \App\Models\Scorm::latest()->paginate(5);


        return view('backend.pages.scorm.index', compact('products'))
                        ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create() {
        return view('backend.pages.scorm.create');
    }


    public function store(ScormRequest $request) {
        try {
            $scorm = $this->scormManager->uploadScormArchive($request->file('file'));
//            dd($scorm);
            // handle scorm runtime error msg
        } catch (InvalidScormArchiveException | StorageNotFoundException $ex) {
            return $this->respondCouldNotCreateResource(trans('scorm.' . $ex->getMessage()));
        }

        // response helper function from base controller reponse json.
        //return $this->respond(ScormModel::with('scos')->whereUuid($scorm['uuid'])->first());
        return redirect('/admin/scorm')->with('success', 'Scorm Archived Successfully');
    }


    public function scormData(Request $request)
    {
        $sco_id = $request->sco_id;
        $user_id = $request->user_id;


        $request->validate([
            'sco_id' => 'required|string',
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $scorm = ScormScoTrackingModel::where(['sco_id' => $sco_id, 'user_id' => $user_id])->first();


        if ($scorm) {

            return response()->json([
                'success' => true,
                'data' => $scorm->details
            ]);
        } else {

            return response()->json([
                'success' => false,
                'message' => 'SCORM data not found for the given user and sco_id'
            ],404);
        }
    }

    public function show($id) {

        $scorm = ScormModel::with('scos')->where('id',$id)->first();


        return view('backend.pages.scorm.show', compact('scorm'));

    }

    public function userReport($id) {

        $scorm = ScormModel::with(['scostrack.user'])->where('id', $id)->first();

     //   echo '<pre>';print_r($scorm->toArray());echo '</pre>';exit();

        return view('backend.pages.scorm.userReport', compact('scorm'));

    }

    public function saveProgress(Request $request)
    {
        try {
            $userId = $request->user_id;
            $uuid = $request->uuid;
          //  $uuid = '48df0b08-e177-4cb2-95db-821a9172a673';
            $data = $request->data ?? [];


            // Validate inputs
            if (!$userId || !$uuid) {
                throw new \Exception('User ID or UUID is missing.');
            }

            // Update SCORM tracking
            $updatedTracking = $this->scormManager->updateScoTracking($uuid, $userId, $data);

            //dd($updatedTracking);

            if (!$updatedTracking) {
                throw new \Exception('Failed to update SCORM tracking.');
            }

            // Return the tracking data or a success response
            return response()->json([
                'message' => 'Progress saved successfully',
                'tracking' => $updatedTracking
            ]);

        } catch (\Exception $e) {
            // Log the error with detailed message and stack trace
            \Log::error('Error saving progress: ' . $e->getMessage(), ['exception' => $e]);

            // Return a generic error message
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function edit($id) {
        //
    }


    public function update(Request $request, $id) {
        //
    }



    public function commitData(Request $request)
    {
        // Perform any final actions if needed, such as marking a session complete
        return response()->json(['message' => 'Data committed successfully']);
    }
}
