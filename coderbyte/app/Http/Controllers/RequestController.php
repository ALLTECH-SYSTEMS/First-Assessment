<?php

namespace App\Http\Controllers;

use App\Http\Resources\RequestCollection;
use App\Http\Resources\RequestResource;
use App\Models\Request as ModelsRequest;
use App\Models\User;
use App\Notifications\ApprovedNotification;
use App\Notifications\AssignedNotification;
use App\Notifications\NewRequestNotification;
use App\Notifications\UploadedNotification;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Notification;

class RequestController extends Controller
{
    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = ModelsRequest::all();
        return new RequestCollection($request);
    }
    
    /**
     * All Product owner request.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexProductOwner($id)
    {
        $request = ModelsRequest::where('owner_id', $id)->get();
        return new RequestCollection($request);
    }
    
    /**
     * All Photographer assignment.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPhotographer($id)
    {
        $request = ModelsRequest::where('photographer_id', $id)->get();
        return new RequestCollection($request);
    }

    /**
     * Creating new request.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $attr = request()->validate([
            'owner_id' => 'required',
            'product' => 'required',
            'location' => 'required'
        ]);

        $request = ModelsRequest::create([
            'owner_id' => $attr['owner_id'],
            'product' => $attr['product'],
            'location' => $attr['location']
        ]);

        //send mail to admin
        $form = new RequestResource($request);
        $user = User::where('category', 1)->get();
        foreach ($user as $value) {
            Notification::route('mail', $value->email)->notify(new NewRequestNotification());
        }

        return $this->success($form, 'Created successful.');
    }

    /**
     * Assign request to photographer by admin.
     *
     * @param  int  $id
     */
    public function assign($id)
    {
        if (request()->user()->category == 1) {
            $attr = request()->validate([
                'photographer_id' => 'required',
            ]);
            $request = ModelsRequest::find($id);
            $request->update([
                'photographer_id' => $attr['photographer_id'],
                'status' => 1,
            ]);
            
            //send mail to photographer
            $user = User::where('id', $attr['photographer_id'])->first();
            Notification::route('mail', $user->email)->notify(new AssignedNotification());

            return $this->success($request, 'Assigned successful.');
        }else {
            return $this->error('Only Admin can perform this operation.', 401);
        }
    }

    /**
     * Uploading pictures url.
     *
     * @param  int  $id
     */
    public function upload($id)
    {
        if (request()->user()->category == 3) {
            $attr = request()->validate([
                'lqt' => 'required',
                'hri' => 'required',
            ]);
            $request = ModelsRequest::find($id);
            if ($request->photographer_id != request()->user()->id) {
                return $this->error("This request wasn't assigined to this photographer.", 401);
            }
            $request->update([
                'LQT' => $attr['lqt'],
                'HRI' => $attr['hri'],
            ]);
            
            //send mail to owner
            $user = User::where('id', $request->owner_id)->first();
            Notification::route('mail', $user->email)->notify(new UploadedNotification());

            return $this->success($request, 'Uploaded successful.');
        }else {
            return $this->error('Only Photographer can perform this operation.', 401);
        }
    }

    /**
     * Approving picture taken by photographer.
     *
     * @param  int  $id
     */
    public function approve($id)
    {
        if (request()->user()->category == 2) {
            $request = ModelsRequest::find($id);
            if($request->LQT == null){
                return $this->error("Request can't be approve because picture hasn't been uploaded.", 401);
            }
            $request->update([
                'approve' => 1,
            ]);
            
            //send mail to photographer
            $user = User::where('id', $request->photographer_id)->first();
            Notification::route('mail', $user->email)->notify(new ApprovedNotification('Approved'));

            return $this->success($request, 'Approved successful.');
        }else {
            return $this->error('Only Product Owner can perform this operation.', 401);
        }
    }

    /**
     * Rejecting picture taken by photographer.
     *
     * @param  int  $id
     */
    public function reject($id)
    {
        if (request()->user()->category == 2) {
            $request = ModelsRequest::find($id);
            if($request->LQT == null){
                return $this->error("Request can't be rejected because picture hasn't been uploaded.", 401);
            }
            $request->update([
                'approve' => 2,
            ]);
            
            //send mail to photographer
            $user = User::where('id', $request->photographer_id)->first();
            Notification::route('mail', $user->email)->notify(new ApprovedNotification('Rejected'));

            return $this->success($request, 'Rejected successful.');
        }else {
            return $this->error('Only Product Owner can perform this operation.', 401);
        }
    }

    /**
     * Editing a specified request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editRequest($id)
    {
        if (request()->user()->category == 2) {
            $attr = request()->validate([
                'product' => 'required',
                'location' => 'required'
            ]);
            $request = ModelsRequest::find($id);
            $request->update([
                'product' => $attr['product'],
                'location' => $attr['location']
            ]);
            
            return $this->success($request, 'Edited successful.');
        }else {
            return $this->error('Only Product Owner can perform this operation.', 401);
        }
    }

    /**
     * Editing a specified request picture.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editImages($id)
    {
        if (request()->user()->category == 3) {
            $attr = request()->validate([
                'lqt' => 'required',
                'hri' => 'required',
            ]);
            $request = ModelsRequest::find($id);
            $request->update([
                'LQT' => $attr['lqt'],
                'HRI' => $attr['hri'],
            ]);
            
            return $this->success($request, 'Edited successful.');
        }else {
            return $this->error('Only Photographer can perform this operation.', 401);
        }
    }

    /**
     * Removing a specified request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (request()->user()->category == 2) {
            $request = ModelsRequest::find($id);

            if(!$request){
                return $this->error("Couldn't find a related request to this id.", 401);
            }
            $request->delete();
            return $this->success('Request deleted successfully.');
        }else {
            return $this->error('Only Product Owner can perform this operation.', 401);
        }
    }

}
