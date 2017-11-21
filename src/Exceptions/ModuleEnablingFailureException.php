<?php

namespace Caffeinated\Modules\Exceptions;


class ModuleEnablingFailureException extends \Exception {

	public function __construct( $slug, $message = null ) {
		parent::__construct('Module with slug name [' . $slug . '] failed to enable.' . (isset($message)?' Inner message: '.$message:''));
	}
}