<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;
use OpenApi\Attributes as OA;

class AuditLogController extends Controller
{
    use ApiResponse;

    #[OA\Get(
        path: "/api/audit-logs",
        summary: "لیست تغییرات (Audit Logs)",
        tags: ["Audit Logs"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "auditable_type", in: "query", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "auditable_id", in: "query", required: false, schema: new OA\Schema(type: "integer")),
        ],
        responses: [new OA\Response(response: 200, description: "لیست تغییرات")]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Audit::with('user')
            ->whereIn('auditable_type', [
                'App\Models\Task',
                'App\Models\Project',
                'App\Models\Version',
            ])
            ->orderBy('created_at', 'desc');

        if ($request->has('auditable_type')) {
            $query->where('auditable_type', $request->auditable_type);
        }
        if ($request->has('auditable_id')) {
            $query->where('auditable_id', $request->auditable_id);
        }

        return $this->successResponse($query->paginate(20), 'لیست تغییرات');
    }
}
