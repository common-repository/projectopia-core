<?php
require_once('clients/clients.php');
if ( get_option('disable_invoices') != 1 ) {
	require_once( 'invoices/invoices.php' );
}
require_once('messages/messages.php');
require_once('projects/projects.php');
if ( get_option('enable_quotes') == 1 ) {
	require_once( 'quotes/quotes.php' );
	require_once( 'forms/forms.php' );
}
require_once('tasks/tasks.php');
require_once('teams/teams.php');
require_once('templates/templates.php');
require_once('terms/terms.php');
require_once('leads/leads.php');
require_once('leadforms/leadforms.php');
if ( get_option('cqpim_enable_faq') == 1 ) {
	require_once('faq/faqs.php');   
}