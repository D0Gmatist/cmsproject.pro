<?php

namespace Modules\errorTemplate;

interface ErrorTemplateInterface {
    public function displayError( $error, $error_num, $query );
}