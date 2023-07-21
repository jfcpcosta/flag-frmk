<?php namespace Flag\Frmk\Mvc;

use Flag\Frmk\Http\Request;
use Flag\Frmk\Http\Response;

abstract class Controller {

    protected function redirect(string $url): void {
        Response::redirect($url);
    }
    
    protected function render(string $name, array $data = null, bool $layout = true): void {
        View::render($name, $data, $layout);
    }
    
    protected function isPost() {
        return Request::isPost();
    }

    protected function json(mixed $data): void {
        Response::json($data);
    }
}