<?php namespace Flag\Frmk\Http;

class Response {

    public static function redirect(string $url): void {
        header("Location: $url");
        exit(301);
    }

    public static function contentType(string $type): void {
        header('Content-Type: ' . $type);
    }

    public static function status(int $code, string $message): void {
        header("HTTP/1.1 $code $message");
    }

    public static function json(mixed $data): void {
        static::contentType('application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public static function created(): void {
        static::status(201, 'Created');
    }

    public static function noContent(): void {
        static::status(204, 'No Content');
    }

    public static function badRequest(): void {
        static::status(400, 'Bad Request');
    }

    public static function unauthorized(): void {
        static::status(401, 'Unauthorized');
    }

    public static function forbidden(): void {
        static::status(403, 'Forbidden');
    }

    public static function notFound(): void {
        static::status(404, 'Not Found');
    }

    public static function methodNotAllowed(): void {
        static::status(405, 'Method Not Allowed');
    }

    public static function internalServerError(): void {
        static::status(500, 'Internal Server Error');
    }
}