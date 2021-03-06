<?php
/* 
 * 检查私人资源访问权限
 */

namespace IdxLib\Middleware\SlimRestful;

use Exception;
use IdxLib\Standard\HttpResponse\HandlerSetIDXResponseErr;
use IdxLib\Middleware\SlimRestful\Util\SlimRestfulCache;

class PrivateAuthCheck
{
    private $container;
    private $UIDArgumentName;

    /**
     * reveive the container object when class object build
     * @param \Slim\Container $container
     * @param string $UIDArgumentName
     */
    public function __construct(\Slim\Container $container, string $UIDArgumentName = 'uid')
    {
        $this->container = $container;
        $this->UIDArgumentName = $UIDArgumentName;
    }

    /**
     * restful Middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface $response PSR7 response
     * @param  callable $next Next Middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        $tokenCollection = SlimRestfulCache::getDefaultCache('tokenCollection');
        if($tokenCollection != false){
            if($request->getAttribute('route')->getArgument($this->UIDArgumentName) != $tokenCollection->first()->uid){
                HandlerSetIDXResponseErr::setStatus403();
            } else {
                $response = $next($request, $response);
            }
        }else{
            HandlerSetIDXResponseErr::setStatus500();
        }
        return $response;
    }
}