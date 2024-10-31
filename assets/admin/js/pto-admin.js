/**
 * This is admin common scripts file for projectopia.
 *
 * @file   This files defines the some events for admin pages.
 * @author Projectopia.
 *
 * @since 5.0.0
 *
 * @package projectopia
 */

'use strict';
import 'select2';
import 'select2/dist/css/select2.css';
import HighCharts from 'highcharts';
import Exporting from 'highcharts/modules/exporting';
Exporting( HighCharts );

/**
 * Admin class.
 *
 * @type  {Object}
 *
 * @since 5.0.0
 */
const ptoAdmin = {

	/**
	 * Initialize.
	 *
	 * @return {void}
	 */
	init() {
		this.setProps();
	},

	/**
	 * Set properties, events and selectors.
	 *
	 * @return {void}
	 */
	setProps() {

		//Current class object.
		var self = this;

		//Initiate element with select2 after load.
		jQuery( ()=> {

			jQuery( '.dropdownInner' ).show();
			jQuery( '.selectDropdown-init' ).select2();
			jQuery( '.select2Multiple' ).select2( {
				containerCssClass: 'selectMultiple-TeamMember',
				dropdownCssClass: 'selectMultipleTeamMember-dropdown'
			} );

		} );

		//Called function to show the income/expense graph chart ion dashboard.
		this.incomeExpenseChart();

		//Bind click event to delete the task from open task widget in dashboard page.
		jQuery( '.pto-dashboard-task-delete-button' ).on( 'click', function( e ) {
			e.preventDefault();

			const answer = confirm( 'Are you sure, You want to delete the task.' );
			if ( ! answer ) {
				return;
			}

			const taskId = jQuery( this ).data( 'task-id' );
			if ( 1 > taskId.length  ) {
				alert( 'Task Id is missing.' );
			}

			self.callAjax( this, {
				'action': 'pto_delete_task_page',
				'pto_nonce': localisation.global_nonce,
				'task_id': taskId
			} );
		} );

		//Bind event to show the delete button.
		jQuery( '#pto-my-open-task-widget' ).on( 'click', '.pto-open-task', () => {

			let checkboxes = jQuery( '#pto-my-open-task-widget' ).find( '.pto-open-task:checked' );
			if ( checkboxes.length ) {
				jQuery( '#pto-tasks-delete' ).removeClass( 'd-none' );
			} else {
				jQuery( '#pto-tasks-delete' ).addClass( 'd-none' );
			}

		} );

		//Bind click to delete the all selected tasks.
		jQuery( '#pto-my-open-task-widget' ).on( 'click', '#pto-tasks-delete', () => {
			let checkboxes = jQuery( '#pto-my-open-task-widget' ).find( '.pto-open-task:checked' );
			let taskIds = [];
			checkboxes.each( function( ) {
				taskIds.push( jQuery( this ).val() );
			} );

			//Check any task is selected or not
			if ( 0 === taskIds.length ) {
				alert( 'No tasks selected' );
				return;
			}

			//Ask for delete confirmation.
			const answer = confirm( 'Are you sure, You want to delete the tasks.' );
			if ( ! answer ) {
				return;
			}

			//Call ajax to delete the all selected tasks.
			self.callAjax( this, {
				'action': 'pto_delete_selected_tasks',
				'ajax_nonce': pto_object.ajax_nonce,
				'task_ids': taskIds
			} );

		} );

		//Bind the project updates filter in dashboard.
		jQuery( '#pto_filter_project_updates' ).on( 'change', function() {

			//Call ajax to change the filter value.
			self.callAjax( this, {
				'action': 'pto_filter_project_updates',
				'ajax_nonce': pto_object.ajax_nonce,
				'days': jQuery( this ).val()
			} );

		} );

		//Bind the debounce function to do live search on dom.
		jQuery( '#pto-dashboard-task-search' ).on( 'keyup',
			self.debounce( self.searchOpenTaskFromWizard, 250 )
		);

		//Set table id selector to data table.
		if ( jQuery( '#pto-pending-quote-estimate-data' ).length ) {
			self.setDataTable( '#pto-pending-quote-estimate-data'  );
		}

		if ( jQuery( '#pto-my-open-support-data' ).length ) {
			self.setDataTable( '#pto-my-open-support-data' );
		}

		if ( jQuery( '#pto-dashboard-invoice-outstanding' ).length ) {
			self.setDataTable( '#pto-dashboard-invoice-outstanding' );
		}

		if ( jQuery( '#pto-my-work-page-table' ).length ) {
			self.setDataTable( '#pto-my-work-page-table' );
		}

		if ( jQuery( '#pto-all-tickets-page-table' ).length ) {
			self.setDataTable( '#pto-all-tickets-page-table' );
		}

	},

	/**
	 * Function to initiate the data tables.
	 *
	 * @param string selector
	 *
	 * @return void
	 */
	setDataTable( selector ) {

		jQuery( selector ).DataTable( {
			responsive: true,
			'pageLength': 10,
			destroy: true,
			language: {
				searchPlaceholder: 'Search . . .'
			},
			'columnDefs': [
				{ 'className': 'dt-v-center', 'targets': '_all' }
			],
			'oLanguage': {
				'lengthMenu': 'Show _MENU_ ',
				'sInfo': 'Showing _START_ to _END_ of _TOTAL_ Result',
				'sSearch': '<a class="btn searchBtn" id="searchBtn"><img src="' + pto_object.PTO_PLUGIN_URL + '/assets/admin/img/search-icon.png" alt="search icon"></a>'
			},
			'fnDrawCallback': function( oSettings ) {
				if ( -1  == oSettings._iDisplayLength || oSettings._iDisplayLength >= oSettings.fnRecordsDisplay() ) {
					jQuery( oSettings.nTableWrapper ).find( '.dataTables_paginate' ).hide();
				} else {
					jQuery( oSettings.nTableWrapper ).find( '.dataTables_paginate' ).show();
				}

				jQuery( '[id$="-circle"]' ).percircle( {
					'progressBarColor': '#6576ff'
				} );
			},
			"preDrawCallback": function(oSettings) {
				jQuery( '.dropdownInner' ).show();
				jQuery( '.selectDropdown-init' ).select2();
				jQuery( '.select2Multiple' ).select2( {
					containerCssClass: 'selectMultiple-TeamMember',
					dropdownCssClass: 'selectMultipleTeamMember-dropdown'
				} );
			},
		} );

	},

	/**
	 * Debounce function.
	 */
	debounce( func, wait, immediate ) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if ( ! immediate ) {
					func.apply( context, args );
				}
			};
			var callNow = immediate && ! timeout;
			clearTimeout( timeout );
			timeout = setTimeout( later, wait );
			if ( callNow ) {
				func.apply( context, args );
			}
		};
	},

	/**
	 * Function to call the ajax with given data.
	 *
	 * @since 5.0.0
	 *
	 * @param string selector  Event handler element.
	 * @param array  data      List of values.
	 */
	callAjax( selector, data ) {

		const spinner = jQuery( '#cqpim_overlay' );
		jQuery.ajax( {
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function()  {
				spinner.show();
				jQuery( selector ).prop( 'disabled', true );
			}
		} ).done( function( response ) {
			spinner.hide();
			jQuery( selector ).prop( 'disabled', false );

			if ( ( false == response.error ) || response.success ) {
				location.reload();
			}
		} );
	},

	/**
	 * Live search for open task widget in dashboard.
	 *
	 * @return void
	 */
	searchOpenTaskFromWizard() {
		let queryString      = this.value.toLocaleLowerCase();
		const parentSelector = jQuery( this ).parents( '#pto-my-open-task-widget' );
		const taskSelector   = parentSelector.find( 'ul.open-tasks-lists li' );
		let hasTask = false;

		//Match with each task title.
		taskSelector.each( function() {
			var taskTitle = jQuery( this ).find( '.task-title' ).text().toLocaleLowerCase();
			if ( taskTitle.includes( queryString ) ) {
				jQuery( this ).show();
				hasTask = true;
			} else {
				jQuery( this ).hide();
			}
		} );

		//If task not found then show message.
		if ( hasTask ) {
			parentSelector.find( '#task-not-found' ).addClass( 'd-none' );
		} else {
			parentSelector.find( '#task-not-found' ).removeClass( 'd-none' );
		}
	},

	/**
	 * Function to plot the income expense chart in dashboard.
	 *
	 * @return void
	 */
	incomeExpenseChart() {

		//if element not exist then return
		if ( ! jQuery( '#bar-chart-1' ).length ) {
			return;
		}

		let data = [];

		//Get income and convert to an array.
		const isIncomeActive = jQuery( '#bar-chart-1' ).attr( 'data-income-active' );
		let income = jQuery( '#bar-chart-1' ).attr( 'data-income' );
		if ( '1' == isIncomeActive ) {
			data.push( {
				name: 'Income',
				data: income.split( ',' ).map( i => Number( i ) )
 			} );
		}

		//Get expense and convert to an array.
		const isExpenseActive = jQuery( '#bar-chart-1' ).attr( 'data-expense-active' );
		let expense = jQuery( '#bar-chart-1' ).attr( 'data-expense' );
		if ( '1' == isExpenseActive ) {
			data.push( {
				name: 'Expenditure',
				data: expense.split( ',' ).map( i => Number( i ) )
			} );
		}

		//Set income and expense data and properties to chart.
		HighCharts.chart( 'bar-chart-1', {
			credits: {
				enabled: false
			},
			chart: {
				height: ( 576 < jQuery( window ).width()  ) ? 300 : 200,
				type: 'column'
			},
			title: {
				text: ''
			},
			legend: {
				layout: 'horizontal',
				backgroundColor: '#FFFFFF',
				align: 'center',
				verticalAlign: 'top',
				floating: false
			},
			navigation: {
				buttonOptions: {
					verticalAlign: 'top',
					y: 0
				}
			},
			xAxis: {
				categories: [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
					'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
				],
				labels: {
					style: {
						color: '#56606D',
						fontSize: '16px',
						fontFamily: 'Cabin'
					}
				},
				crosshair: true
			},
			yAxis: {
				gridLineDashStyle: 'longdash',
				min: 0,
				title: {
					text: ''
				},
				labels: {
					style: {
						color: '#56606D',
						fontSize: '16px',
						fontFamily: 'Cabin'
					}
				}
			},
			plotOptions: {
				column: {
					pointPadding: 0.35,
					borderWidth: 0
				},
				series: {
					borderRadius: 0,
					dashStyle: 'Dash'
				}
			},
			colors: [ '#00cd98', '#ff9939' ],
			series: data
		} );
	}
};

export default ptoAdmin;
