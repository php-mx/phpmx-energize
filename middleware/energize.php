<?php

use Controller\Error\E400;
use Controller\Error\E401;
use Controller\Error\E403;
use Controller\Error\E404;
use Controller\Error\E405;
use Controller\Error\E500;
use Controller\Error\E501;
use Controller\Error\E503;
use Energize\Front;
use PhpMx\Log;
use PhpMx\Response;

return new class {

    function __invoke(Closure $next)
    {
        try {
            $this->page($next());
        } catch (Throwable $e) {
            if ($e->getCode() == STS_REDIRECT) $this->redirect($e);
            if (IS_GET) $this->errorPage($e);
            $this->catch($e);
        }
    }

    function page($content)
    {
        if (is_httpStatus($content))
            throw new Exception(env("STM_$content"), $content);

        $content = Front::renderize($content);

        if (is_array($content)) {
            $status = Response::getStatus() ?? STS_OK;
            $content = [
                'info' => [
                    'mx' => true,
                    'status' => $status,
                    'error' => is_httpStatusError($status),
                    'message' => env("STM_$status", null),
                    'alert' => Front::getAlerts(),
                ],
                'data' => $content
            ];
            if (env('DEV'))
                $response['log'] = Log::getArray();
        } else {
            if (env('DEV'))
                $content = prepare("$content\n<!--[#]-->", Log::getString());
        }

        Response::content($content);
        Response::send();
    }

    function errorPage(Throwable $e)
    {
        try {
            list($status, $message, $file, $line) = [
                $e->getCode(),
                $e->getMessage(),
                path($e->getFile()),
                $e->getLine()
            ];

            if (!is_httpStatus($status))
                $status = STS_INTERNAL_SERVER_ERROR;

            Response::header('Error-Message', remove_accents($message));
            Response::header('Error-Status', $status);

            if (env('DEV')) {
                Response::header('Error-File', $file);
                Response::header('Error-Line', $line);
            }

            $content = match ($status) {
                STS_BAD_REQUEST => (new E400)->default($e),
                STS_UNAUTHORIZED => (new E401)->default($e),
                STS_FORBIDDEN => (new E403)->default($e),
                STS_NOT_FOUND => (new E404)->default($e),
                STS_METHOD_NOT_ALLOWED => (new E405)->default($e),
                STS_INTERNAL_SERVER_ERROR => (new E500)->default($e),
                STS_NOT_IMPLEMENTED => (new E501)->default($e),
                STS_SERVICE_UNAVAILABLE => (new E503)->default($e),
            };

            $this->page($content);
        } catch (Throwable) {
            return;
        }
    }

    function catch(Throwable $e)
    {
        $status = $e->getCode();

        if (!is_httpStatus($status))
            $status = !is_class($e, Error::class) ? STS_BAD_REQUEST : STS_INTERNAL_SERVER_ERROR;

        $message = $e->getMessage();

        if (empty($message))
            $message = env("STM_$status");

        if (is_json($message))
            $message = json_decode($message, true);

        if (is_array($message) && isset($message['message']))
            $message = $message['message'];

        $response = [
            'info' => [
                'mx' => true,
                'status' => $status,
                'error' => $status > 399,
                'message' => $message,
                'alert' => Front::getAlerts(),
            ],
            'data' => null
        ];

        $headerMessageError = is_array($message) ? implode('|', $message) : $message;
        Response::header('Error-Message', remove_accents($headerMessageError));
        Response::header('Error-Status', $response['info']['status']);

        if (env('DEV') && $response['info']['error']) {
            $response['info']['file'] = $e->getFile();
            $response['info']['line'] = $e->getLine();
            Response::header('Error-File', $response['info']['file']);
            Response::header('Error-Line', $response['info']['line']);
        }

        Response::status($status);
        Response::cache(false);
        Response::content($response);
        Response::send();
    }

    function redirect(Throwable $e)
    {
        if (IS_PARTIAL) {
            $url = !empty($e->getMessage()) ? url($e->getMessage()) : url('.');
            $scheme = [
                'info' => [
                    'mx' => true,
                    'status' => STS_REDIRECT,
                    'error' => false,
                    'location' => $url,
                    'alert' => Front::getAlerts(),
                ],
                'data' => null
            ];
            Response::header('Mx-Location', $url);
            Response::content($scheme);
            Response::status(STS_OK);
            Response::send();
        }

        Response::header('location', $e->getMessage());
        Response::status(STS_REDIRECT);
        Response::send();
    }
};
