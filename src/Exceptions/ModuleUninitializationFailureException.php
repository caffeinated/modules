<?php

namespace Caffeinated\Modules\Exceptions;


class ModuleUninitializationFailureException extends \Exception {

	public function __construct( $slug, $message = null ) {
		parent::__construct('Module with slug name [' . $slug . '] failed to uninitialize.' . (isset($message)?' Inner message: '.$message:''));
	}
}