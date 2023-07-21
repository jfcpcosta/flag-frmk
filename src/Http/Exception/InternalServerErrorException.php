<?php namespace Flag\Frmk\Http\Exception;

class InternalServerErrorException extends HttpException {

    public function __construct(string $message = 'Internal Server Error') {
        parent::__construct($message, 500);
    }
}