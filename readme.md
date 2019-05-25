## KosmosX Support
![](https://img.shields.io/badge/version-1.0.0-green.svg)
![](https://img.shields.io/badge/laravel->=5.7-blue.svg)
![](https://img.shields.io/badge/lumen->=5.7-blue.svg)
    
    composer require kosmosx/support

**Register service poviders**

    /Kosmosx/Support/SupportServiceProvider::class

    php artisan kosmosx:publish:support
    
**Use it**

    $support = app('factory.support');

    $statusSuccess = $support->success(200, $var, 'message...'); //return object StatusService 
    $statusFail = $support->fail(400, $var, 'message...');       //return object StatusService
    
    
    $statusFail->isSuccess() //return false
    $statusFail->isFail()    //return true
    $statusFail->toArray()
    //result
    [
        "success"=>false,
        "data"=>[...],
        "message"=>"message...",
        "statusCode"=>200
    ]
    
    $api = $support->api()
    $api->collection($data, $transformer, $includesData = null, $serializer = null) //Create collection with data Transformer
    
**Route API auto discovery**
    
    \ApiService::apiDiscovery();
    
