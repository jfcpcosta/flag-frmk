<?php namespace Flag\Frmk\Mvc;

use Flag\Frmk\Http\Exception\InternalServerErrorException;
use Flag\Frmk\Http\FlashBag;

class View {

    public static function render(string $name, array $data = null, bool $layout = true): void {
        $path = "../views/$name.phtml";
        
        if (!file_exists($path)) {
            throw new InternalServerErrorException();
        }
    
        $data['bag'] = FlashBag::has() ? FlashBag::get() : [];
        
        if (!is_null($data)) {
            extract($data);
        }

    
        if ($layout) {
            include "../views/common/header.phtml";
            include $path;
            include "../views/common/footer.phtml";
        } else {
            include $path;
        }
    }
}