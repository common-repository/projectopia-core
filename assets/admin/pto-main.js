/**
 * This is main script file for projectopia.
 *
 * @file   This files build the all scripts together.
 * @author Projectopia.
 *
 * @since 5.0.0
 *
 * @package projectopia
 */

import './scss/pto-header.scss';
import './scss/pto-dashboard.scss';
import './scss/pto-main.scss';

import ptoAdmin from './js/pto-admin.js';

jQuery( ()=> {
	ptoAdmin.init();
} );
