<?php namespace Flag\Frmk\Http\Exception;

class BadRequestException extends HttpException {

    public function __construct(string $message = 'Bad Request') {
        parent::__construct($message, 400);
    }
}