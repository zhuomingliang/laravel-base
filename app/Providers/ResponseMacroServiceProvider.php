<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use App\Library;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //错误返回
        Response::macro('e_back', function($code = 9999, $msg = '失败', $data = null){
                if(empty($data))
                {
                    $data = [];
                }
                $response_data = [];
                $response_data['data'] = !empty($data)?$data:(is_array($data)?[]:'');//业务数据包
                $response_data['Version'] = '1.0.0';
                $response_data['RetCode'] = (int)$code;
                $response_data['Msg'] = $msg;
                //$response_data['Sign'] = '';//Library\Sign::mksign($response_data);

                app('log')->error(sprintf(' params [%s] response [%s]',
                    json_encode(request()->all(), JSON_UNESCAPED_UNICODE),
                    json_encode($response_data, JSON_UNESCAPED_UNICODE)
                ));
            //$response_data = mb_convert_encoding( $response_data, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5' );

                return Response::json($response_data)->setEncodingOptions(320);
        });
	
        //成功返回
        Response::macro('s_back', function ($code = 100,  $msg = '成功',$data = null){
            if(empty($data))
            {
                $data = [];
            }
            $response_data = [];
            $response_data['data'] = !empty($data)?$data:(is_array($data)?[]:'');//业务数据包
            $response_data['Version'] = '1.0.0';
            $response_data['RetCode'] = (int)$code;
            $response_data['Msg'] = $msg;
            //$response_data['Sign'] = '';//Library\Sign::mksign($response_data);

            app('log')->debug(sprintf(' params [%s] response [%s]',
                json_encode(request()->all(), JSON_UNESCAPED_UNICODE),
                json_encode($response_data, JSON_UNESCAPED_UNICODE)
            ));
            //$response_data = mb_convert_encoding( $response_data, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5' );
            return Response::json($response_data)->setEncodingOptions(320);
        });


    }
}
