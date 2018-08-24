<?php
namespace App\ResponseTrait;


use Dingo\Api\Routing\Helpers;

trait Responder
{
    use Helpers;

    public function responseCollection(Collection $collection, TransformerAbstract $transformer)
    {
        return $this->response->collection($collection, $transformer, [], function ($resource, Manager $fractal) {
            $fractal->setSerializer(new CustomSerializer());
        });
    }

    public function responseItem($item, TransformerAbstract $transformer)
    {
        return $this->response->item($item, $transformer, [], function ($resource, Manager $fractal) {
            $fractal->setSerializer(new CustomSerializer());
        });
    }

    public function responsePaginate(Paginator $paginator, TransformerAbstract $transformer)
    {
        return $this->response->paginator($paginator, $transformer, [], function ($resource, Manager $fractal) {
            $fractal->setSerializer(new CustomSerializer());
        });
    }

    public function responseData(array $data)
    {

        return response()->json([
            'message' => '操作成功',
            'status_code_api' => 200,
            'data' => $data
        ], 200);
    }

    public function responseSuccess($message='操作成功')
    {
        return response()->json([
            'message' => $message,
            'status_code_api' => 200
        ], 200);
    }

    public function responseFailed($message='操作失败')
    {
        return response()->json([
            'message' => $message,
            'status_code_api' => 400
        ], 400);
    }

    public function responseError($message='未知错误')
    {
        return response()->json([
            'message' => $message,
            'status_code_api' => 500
        ], 500);
    }
}
