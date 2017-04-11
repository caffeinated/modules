<?php
/**
 * Created by Pankit Gami
 * Date: 11/4/17
 * Time: 5:27 PM
 */

namespace Caffeinated\Modules\Exceptions;


class ModuleNotFoundException extends \Exception {

	/**
	 * ModuleNotFoundException constructor.
	 */
	public function __construct( $slug ) {
		parent::__construct('Module with slug name [' . $slug . '] not found');
	}
}