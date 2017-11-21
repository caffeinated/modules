<?php

namespace Caffeinated\Modules\Exceptions;


class ModuleDisablingFailureException extends \Exception {

	public function __construct( $slug, $message = null ) {
		parent::__construct('Module with slug name [' . $slug . '] failed to disable.' . (isset($message)?' Inner message: '.$message:''));
	}
}