<?php

namespace App\Http\Resources;

use App\Http\Controllers\AppController;
use App\Http\Controllers\RequestFormController;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestFormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //get user
        $authenticatedUser = $request->user();
        $canEdit = $this->editable && $this->user->id == $authenticatedUser->id;
        $canDelete = $this->editable && $this->user->id == $authenticatedUser->id && ($this->approvedBy->isEmpty()) && $this->deniedBy == null;
        $canDiscard = $this->editable && $this->user->id == $authenticatedUser->id && (!($this->approvedBy->isEmpty()) || $this->deniedBy != null);
        $canInitiate = $this->approvalStatus == 1 && $authenticatedUser->hasRole('accountant');
        $canReconcile = $this->approvalStatus == 3 && $authenticatedUser->hasRole('accountant');

        //next to approve
        if ($this->resource->stagesApprovalStatus == 0) { // Use $this->resource for model properties
            // Ensure $authenticatedUser->position is loaded and not null
            $nextApprove = $authenticatedUser->position && $this->resource->stagesApprovalPosition == $authenticatedUser->position->id;
        } else {
            $nextApprove = $authenticatedUser->hasRole('management');
        }

        $canApproveOrDeny = $this->user->id != $authenticatedUser->id && $this->approvalBy == null && $this->approvalStatus != 2 && $nextApprove;


        switch ($this->type){

            case "MATERIALS":
            case "CASH":
                return [
                    'id'                                  =>  $this->id,
                    'code'                                =>  $this->code,
                    'type'                                =>  $this->type,
                    'personCollectingAdvance'             =>  $this->personCollectingAdvance,
                    'project'                             =>  new ProjectResource($this->project),
                    'information'                         =>  json_decode($this->information),
                    'total'                               =>  $this->total,
                    'requestedBy'                         =>  new UserResource($this->user),
                    'dateRequested'                       =>  $this->dateRequested,
                    'nextPositionToApprove'              =>  $this->approvalPosition($this->stagesApprovalPosition),
                    'stagesApprovalStatus'                =>  $this->stagesApprovalStatus,
                    'currentStage'                        =>  $this->currentStage,
                    'totalStages'                         =>  $this->totalStages,
                    'stages'                              =>  json_decode($this->stages),
                    'approvalStatus'                      =>  intval($this->approvalStatus),
                    'approvedDate'                        =>  $this->approvedDate,
                    'dateInitiated'                       =>  $this->dateInitiated,
                    'dateReconciled'                      =>  $this->dateReconciled,
                    'status'                              =>  $this->getApprovalStatus($this->approvalStatus),
                    'statusMessage'                       =>  $this->resolveStatusMessage(),
                    'approvedBy'                          =>  new UserResource($this->whenLoaded('approvalBy')),
                    'deniedBy'                            =>  new UserResource($this->whenLoaded('deniedBy')),
                    'editable'                            =>  $this->editable,
                    'remarks'                             =>  json_decode($this->remarks),
                    'quotes'                              =>  json_decode($this->quotes),
                    'receipts'                            =>  json_decode($this->receipts),
                    'canEdit'                             =>  $canEdit,
                    'canDelete'                           =>  $canDelete,
                    'canDiscard'                          =>  $canDiscard,
                    'canApproveOrDeny'                    =>  $canApproveOrDeny,
                    'canInitiate'                         =>  $canInitiate,
                    'canReconcile'                        =>  $canReconcile,
                ];

            case "VEHICLE_MAINTENANCE":
                return [
                    'id'                                  =>  $this->id,
                    'code'                                =>  $this->code,
                    'type'                                =>  $this->type,
                    'assessedBy'                          =>  $this->assessedBy,
                    'vehicle'                             =>  new VehicleResource($this->vehicle),
                    'information'                         =>  json_decode($this->information),
                    'total'                               =>  $this->total,
                    'requestedBy'                         =>  new UserResource($this->user),
                    'dateRequested'                       =>  $this->dateRequested,
                    'nextPositionToApprove'              =>  $this->approvalPosition($this->stagesApprovalPosition),
                    'stagesApprovalStatus'                =>  $this->stagesApprovalStatus,
                    'currentStage'                        =>  $this->currentStage,
                    'totalStages'                         =>  $this->totalStages,
                    'stages'                              =>  json_decode($this->stages),
                    'approvalStatus'                      =>  intval($this->approvalStatus),
                    'approvedDate'                        =>  $this->approvedDate,
                    'dateInitiated'                       =>  $this->dateInitiated,
                    'dateReconciled'                      =>  $this->dateReconciled,
                    'status'                              =>  $this->getApprovalStatus($this->approvalStatus),
                    'statusMessage'                       =>  $this->resolveStatusMessage(),
                    'approvedBy'                          =>  new UserResource($this->whenLoaded('approvalBy')),
                    'deniedBy'                            =>  new UserResource($this->whenLoaded('deniedBy')),
                    'editable'                            =>  $this->editable,
                    'remarks'                             =>  json_decode($this->remarks),
                    'quotes'                              =>  json_decode($this->quotes),
                    'receipts'                            =>  json_decode($this->receipts),
                    'canEdit'                             =>  $canEdit,
                    'canDelete'                           =>  $canDelete,
                    'canDiscard'                          =>  $canDiscard,
                    'canApproveOrDeny'                    =>  $canApproveOrDeny,
                    'canInitiate'                         =>  $canInitiate,
                    'canReconcile'                        =>  $canReconcile,
                ];

            case "FUEL":
                return [
                    'id'                                  =>  $this->id,
                    'type'                                =>  $this->type,
                    'code'                                =>  $this->code,
                    'driverName'                          =>  $this->driverName,
                    'fuelRequestedLitres'                 =>  $this->fuelRequestedLitres,
                    'fuelRequestedMoney'                  =>  $this->fuelRequestedMoney,
                    'purpose'                             =>  $this->purpose,
                    'vehicle'                             =>  new VehicleResource($this->vehicle),
                    'mileage'                             =>  $this->mileage,
                    'project'                             =>  new ProjectResource($this->project),
                    'lastRefillDate'                      =>  $this->lastRefillDate,
                    'lastRefillFuelReceived'              =>  $this->lastRefillFuelReceived,
                    'lastRefillMileageCovered'            =>  $this->lastRefillMileageCovered,
                    'requestedBy'                         =>  new UserResource($this->user),
                    'dateRequested'                       =>  $this->dateRequested,
                    'nextPositionToApprove'              =>  $this->approvalPosition($this->stagesApprovalPosition),
                    'stagesApprovalStatus'                =>  $this->stagesApprovalStatus,
                    'currentStage'                        =>  $this->currentStage,
                    'totalStages'                         =>  $this->totalStages,
                    'stages'                              =>  json_decode($this->stages),
                    'approvalStatus'                      =>  intval($this->approvalStatus),
                    'approvedDate'                        =>  $this->approvedDate,
                    'dateInitiated'                       =>  $this->dateInitiated,
                    'dateReconciled'                      =>  $this->dateReconciled,
                    'status'                              =>  $this->getApprovalStatus($this->approvalStatus),
                    'statusMessage'                       =>  $this->resolveStatusMessage(),
                    'approvedBy'                          =>  new UserResource($this->whenLoaded('approvalBy')),
                    'deniedBy'                            =>  new UserResource($this->whenLoaded('deniedBy')),
                    'editable'                            =>  $this->editable,
                    'remarks'                             =>  json_decode($this->remarks),
                    'quotes'                              =>  json_decode($this->quotes),
                    'receipts'                            =>  json_decode($this->receipts),
                    'canEdit'                             =>  $canEdit,
                    'canDelete'                           =>  $canDelete,
                    'canDiscard'                          =>  $canDiscard,
                    'canApproveOrDeny'                    =>  $canApproveOrDeny,
                    'canInitiate'                         =>  $canInitiate,
                    'canReconcile'                        =>  $canReconcile,
                ];

            default:
                return [];
        }
    }

    public function approvalPosition($positionId)
    {
        if (!$positionId) {
            // Fallback if stagesApprovalPosition is null (e.g. fully approved or management directly)
            // This matches the old logic's else case implicitly.
            // However, the original returned an array of titles, let's be consistent if possible
            // or decide if a single string or null is better.
            // For now, returning a default string if no specific title can be found.
            // Consider if ['Managing Director','Contracts Manager'] was a meaningful default or placeholder.
            // If $this->stagesApprovalStatus is true (meaning it went to direct management/MD approval),
            // then specific stage logic doesn't apply.
            if ($this->resource->stagesApprovalStatus) {
                 return "Management/MD"; // Or a more appropriate generic term
            }
            return "N/A"; // Or null, or an empty array
        }

        $stages = json_decode($this->resource->stages);
        if (is_array($stages)) {
            foreach ($stages as $stage) {
                if (isset($stage->position) && $stage->position == $positionId && isset($stage->positionTitle)) {
                    return $stage->positionTitle; // Return single title string
                }
            }
        }

        // Fallback if not found in stages JSON or if stages is not as expected
        // This indicates a potential data consistency issue or a case not covered by stages JSON.
        // Log this situation if possible in a real application.
        // $position = Position::find($positionId); // Original problematic call
        // if (is_object($position)) {
        // return $position->title;
        // }
        // Fallback to a generic or error indicator if absolutely necessary,
        // but ideally, data should be in stages or pre-loaded.
        // For now, returning a placeholder to indicate it needs attention.
        // The original returned an array like ['Managing Director','Contracts Manager'] as a fallback.
        // This seems like a list of possible approvers rather than THE specific one.
        // Let's return a single string for consistency with the successful lookup.
        return "Position Title Not Found"; // Or log error and return null
    }

    private function getApprovedBy($id){
        $user=User::find($id);
        if(is_object($user))
            return new UserResource($user);
        else
            return null;
    }

    private function getApprovalStatus($status){
        switch ($status){
            case 0:
                return "Pending";
            case 1:
                return "Approved";
            case 2:
                return "Denied";
            case 3:
                return "Initiated";
            case 4:
                return "Reconciled";
            case 5:
                return "Discarded";
            default:
                return "Unknown";
        }
    }

    private function resolveStatusMessage()
    {
        $requestFormModel = $this->resource; // Access the underlying model

        switch ($requestFormModel->approvalStatus) {
            case 0:
                if ($requestFormModel->stagesApprovalStatus) {
                    return "Pending : Manager to approve";
                } else {
                    // Try to get position title from stages JSON
                    $stages = json_decode($requestFormModel->stages, true);
                    $positionTitle = "N/A"; // Default
                    if (is_array($stages)) {
                        foreach ($stages as $stage) {
                            if (isset($stage['position']) && $stage['position'] == $requestFormModel->stagesApprovalPosition && isset($stage['positionTitle'])) {
                                $positionTitle = $stage['positionTitle'];
                                break;
                            }
                        }
                    }
                    // Fallback if not in stages (should ideally not happen if data is consistent)
                    if ($positionTitle === "N/A" && $requestFormModel->stagesApprovalPosition) {
                         // $position = Position::find($requestFormModel->stagesApprovalPosition); // Avoid this
                         // If you absolutely must, and cannot ensure data in stages JSON:
                         // error_log("Position title for ID {$requestFormModel->stagesApprovalPosition} not found in stages JSON for RequestForm ID {$requestFormModel->id}. Querying DB.");
                         // $position = Position::find($requestFormModel->stagesApprovalPosition);
                         // if ($position) $positionTitle = $position->title; else $positionTitle = "Unknown Position";
                         $positionTitle = "Position ID: " . $requestFormModel->stagesApprovalPosition; // Placeholder
                    }
                    return "Pending : " . $positionTitle . " to approve";
                }
            case 1:
                return "Approved: Accountant to initiate";
            case 2:
                // Ensure deniedBy is loaded, which it should be from controller
                if ($this->whenLoaded('deniedBy') && $requestFormModel->deniedBy) {
                    return "Denied: By " . $requestFormModel->deniedBy->firstName . " " . $requestFormModel->deniedBy->middleName . " " . $requestFormModel->deniedBy->lastName;
                }
                return "Denied"; // Fallback if deniedBy is not available
            case 3:
                return "Accountant to reconcile";
            case 4:
                return "Reconciled";
            case 5:
                return "Discarded";
            default:
                return "Unknown";
        }
    }
}
