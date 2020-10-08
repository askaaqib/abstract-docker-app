<?php
declare(strict_types=1);
namespace App\Admin\Controllers\Test;

use App\Admin\Controllers\AbstractAdminController;
use App\Common\Exception\AppControllerException;
use App\Common\Exception\AppException;
use App\Common\Database\Primary\Test as TestTable;
use App\Common\Test;
use App\Common\Validator;
use Comely\Database\Schema;
use Comely\Utils\Security\Passwords;

/**
 * Class Add
 * @package App\Admin\Controllers\Test
 */
class Add extends AbstractAdminController {

    public function adminCallback(): void
    {
        $db = $this->app->db()->primary();
        Schema::Bind($db, 'App\Common\Database\Primary\Test');
//        Schema::Bind($db, 'App\Common\Database\Primary\Users');
    }

    public function get()
    {
        $this->page()->title('Create Test')->index(610, 20)
            ->prop("icon", "mdi mdi-account-plus-outline");

        $this->breadcrumbs("Test Control", null, "ion ion-ios-people");
        $template = $this->template("/test/add.knit");
        //   ->assign("form", $form->array());
        $this->body($template);
        // Response
        /*   $this->response()->set("status", true);
           $this->response()->set("disabled", true);
           $this->messages()->success('You have been logged in!');
           $this->messages()->info('Please wait...');
           $this->response()->set("redirect", $this->request()->url()->root($authToken->base16()->hexits() . "/dashboard"));
           return;*/
    }
}