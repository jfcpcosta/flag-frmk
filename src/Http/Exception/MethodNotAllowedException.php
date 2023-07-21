<?php namespace Flag\Frmk\Http\Exception;

class MethodNotAllowedException extends HttpException {

    public function __construct(string $message = 'Method Not Allowed') {
        parent::__construct($message, 405);
    }
}