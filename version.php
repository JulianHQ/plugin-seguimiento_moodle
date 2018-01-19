<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines the version and other meta-info about the plugin
 *
 * Setting the $plugin->version to 0 prevents the plugin from being installed.
 * See https://docs.moodle.org/dev/version.php for more info.
 *
 * @package    mod_seguimiento
 * @copyright  2018 Jeyson Vega <jeysonvegaromero@gmail.com> - Julian Hernandez <juliher.094@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'mod_seguimiento'; //Tipo módulo - Nombre seguimiento
$plugin->version = 2018010100; //Versión en formato YYYYMMDD
$plugin->release = 'v1.0'; //Versión de lectura humana
$plugin->requires = 2014051200; //Moodle 2.7.0 mínimo
$plugin->maturity = MATURITY_STABLE; //Versión estable
//$plugin->cron = 0; //Limitaciones de ejecución inactivos
$plugin->dependencies = array(); //Dependencias opcionales
