<?php

namespace Modules\mysql\template;

interface TemplateInterface {
    public function displayError( $error, $error_num, $query );
}