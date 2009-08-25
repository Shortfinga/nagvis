<?php

/*****************************************************************************
 *
 * Copyright (C) 2009 NagVis Project
 *
 * License:
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 *****************************************************************************/

class Viewpoint
{
	public $label;
	public $center;
	public $zoom;

	public function __construct($label = "", $center = "", $zoom = 0)
	{
		if ($label == '')
			throw new Exception('Attempt to create an invalid object of Viewpoint class');

		$this->label = $label;
		$this->center = $center;
		$this->zoom = $zoom;
	}
}

?>
