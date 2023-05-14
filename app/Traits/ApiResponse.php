<?php

namespace App\Traits;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

trait ApiResponse
{
    public string $message = "";

    public int $code = ResponseAlias::HTTP_OK;

    public bool $success = true;

    /**
     * @param array|Collection|Model $data
     * @param string $message
     * @return Application|ResponseFactory|Response
     */
    public function response(array|Collection|Model $data = [], string $message = ''): Response|Application|ResponseFactory
    {
        return response([
            'meta' => [
                'success' => $data['success'] ?? $this->success,
                'code' => $data['code'] ?? $this->code,
                'message' => ($message != '') ? $message : $this->message
            ],
            'response' => $data ?? []
        ], $this->code);
    }

    public function responseUnsuccess(string $message = ''): Response|Application|ResponseFactory
    {
        $this->success = false;
        $this->message = $message;

        return $this->response();
    }

    /**
     * @param string $message
     * @param int $code
     * @return Response|Application|ResponseFactory
     */
    public function responseError(
        string $message, int $code = ResponseAlias::HTTP_BAD_REQUEST
    ): Response|Application|ResponseFactory {
        $this->success = false;
        $this->code = $code;
        $this->message = $message;

        return $this->response();
    }

    /**
     * @param array $errors
     * @param string $message
     * @param int $code
     * @return Application|ResponseFactory|Response
     */
    public function responseValidationException(
        array $errors, string $message = "Validation error.", int $code = ResponseAlias::HTTP_NOT_FOUND
    ): Response|Application|ResponseFactory {
        $this->success = false;
        $this->code = $code;
        $this->message = $message;

        return $this->response($errors);
    }

    /**
     * @param LengthAwarePaginator $awarePaginator
     * @param JsonResource|null $resourceForMap
     * @return Application|Response|ResponseFactory
     */
    public function responsePaginate(LengthAwarePaginator $awarePaginator, JsonResource $resourceForMap = null): Application|ResponseFactory|Response
    {
        if (!is_null($resourceForMap)) {
            $items = $resourceForMap->collection($awarePaginator->items());
        } else {
            $items = $awarePaginator->items();
        }

        return $this->response([
            'pagination' => $this->getPagination($awarePaginator),
            'items'      => $items,
        ]);
    }

    public function getPagination(LengthAwarePaginator $item)
    {
        return [
            'total'        => $item->total(),
            'current_page' => $item->currentPage(),
            'last_page'    => $item->lastPage(),
            'from'         => $item->firstItem(),
            'to'           => $item->lastItem(),
        ];
    }
}
