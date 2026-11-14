<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class NotificationController extends Controller
{
    use ApiResponse;

    #[OA\Get(
        path: "/api/notifications",
        summary: "لیست اعلان های کاربر",
        tags: ["Notifications"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "filter", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["unread", "read"]))
        ],
        responses: [
            new OA\Response(response: 200, description: "لیست اعلان ها")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->notifications();

        if ($request->query('filter') === 'unread') {
            $query->whereNull('read_at');
        } elseif ($request->query('filter') === 'read') {
            $query->whereNotNull('read_at');
        }

        return $this->successResponse($query->paginate(10), 'لیست اعلان ها');
    }

    #[OA\Post(
        path: "/api/notifications/{id}/read",
        summary: "علامت گذاری یک اعلان به عنوان خوانده شده",
        tags: ["Notifications"],
        security: [["bearerAuth" => []]],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))],
        responses: [new OA\Response(response: 200, description: "اعلان خوانده شد")]
    )]
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return $this->successResponse(null, 'اعلان خوانده شد');
    }

    #[OA\Post(
        path: "/api/notifications/read-all",
        summary: "علامت گذاری همه اعلان ها به عنوان خوانده شده",
        tags: ["Notifications"],
        security: [["bearerAuth" => []]],
        responses: [new OA\Response(response: 200, description: "همه اعلان ها خوانده شدند")]
    )]
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();
        return $this->successResponse(null, 'همه اعلان ها خوانده شدند');
    }

    #[OA\Delete(
        path: "/api/notifications/{id}",
        summary: "حذف یک اعلان",
        tags: ["Notifications"],
        security: [["bearerAuth" => []]],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string"))],
        responses: [new OA\Response(response: 200, description: "اعلان حذف شد")]
    )]
    public function destroy(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->delete();
        return $this->successResponse(null, 'اعلان حذف شد');
    }
}
