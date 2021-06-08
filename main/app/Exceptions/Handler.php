<?php

namespace App\Exceptions;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class Handler.
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        BusinessException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Throwable $e
     *
     * @throws Throwable
     *
     * @return void
     */
    public function report(Throwable $e): void
    {
        try {
            $this->reportByBot($e);
        } finally {
            parent::report($e);
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request   $request
     * @param Throwable $e
     *
     * @throws Throwable
     *
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof BusinessException) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return parent::render($request, $e);
    }

    /**
     * Send To admin log entirely.
     *
     * @param Throwable $exception
     *
     * @throws Throwable
     *
     * @return void
     */
    protected function reportByBot(Throwable $exception): void
    {
        $botToken = config('logging.telegram_bot_token');
        $sendToId = config('logging.telegram_id');

        if (
            !$botToken
            || !$sendToId
            || $this->shouldntReport($exception)
        ) {
            return;
        }

        $date = Carbon::now()->locale('ru_RU');

        $callbackData = collect(compact('sendToId', 'botToken'));
        $context = [
            'timestamp' => $date->toJSON(),
            'data'      => $exception->getCode(),
            'content'   => $exception->getMessage(),
            'line'      => $exception->getLine(),
            'file'      => $exception->getFile(),
            'trace'     => $exception->getTrace(),
        ];

        /** @noinspection JsonEncodingApiUsageInspection */
        $contextPrepare = json_encode(
            $context,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

//        /** @var StreamInterface $contextPrepare */
        //$stream = Utils::streamFor($contextPrepare);

        $client = new Client(['http_errors' => false]);

        $fileName = Str::afterLast($exception->getFile(), '/');

        $client->post("https://api.telegram.org/bot{$callbackData->get('botToken')}/sendDocument", [

            //https://core.telegram.org/bots/api#senddocument
            'multipart' => [
                [
                    'name'     => 'chat_id',
                    'contents' => $callbackData->get('sendToId'),
                ],
                [
                    'name'     => 'parse_mode',
                    'contents' => 'HTML',
                ],
                [
                    'name'      => 'document',
                    'type'      => 'document',
                    'contents'  => $contextPrepare,
                    'filename'  => 'logerror-'.$fileName.'.log',
                    'media'     => 'attach://<loge1rror-'.$fileName.'.log>',
                ],
            ],
        ]);
    }
}
