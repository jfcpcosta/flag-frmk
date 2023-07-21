<?php namespace Flag\Frmk\Mvc;

use Flag\Frmk\Http\Request;

abstract class SecuredController extends Controller {

    protected $loggedUser;

    public function __construct() {
        $this->loggedUser = Request::session('user');

        if (is_null($this->loggedUser)) {
            $this->redirect('/login');
        }
    }
}