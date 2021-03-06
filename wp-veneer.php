<?php
/**
 * Plugin Name: Veneer CMS
 * Plugin URI: http://usabilitydynamics.com/
 * Description: Application managing must-use plugins and obfuscation rewrites.
 * Version: 0.1.5
 * Author: Usability Dynamics
 * Author URI: http://usabilitydynamics.com/
 * License: GPLv2 or later
 *
 * The Loder class is self-initializing.
 *
 * @example
 *
 *      // Get Settings Object
 *      Veneer::get_instance()->state->settings;
 *      Veneer::get_instance()->get()
 *
 * @namespace Veneer
 * @module Veneer
 */

// Include bootstrap.
include_once( __DIR__ . '/lib/class-bootstrap.php' );

// Initialize.
new UsabilityDynamics\Veneer\Bootstrap();