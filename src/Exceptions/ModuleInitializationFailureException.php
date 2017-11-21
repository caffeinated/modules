<?php

namespace Caffeinated\Modules\Exceptions;


class ModuleInitializationFailureException extends \Exception {

	public function __construct( $slug, $message = null ) {
		parent::__construct('Module with slug name [' . $slug . '] failed to initialize.' . (isset($message)?' Inner message: '.$message:''));
	}
}