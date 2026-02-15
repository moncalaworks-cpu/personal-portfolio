/**
 * Task Manager Admin JavaScript
 *
 * Handles admin page interactions
 */

(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		// Date input enhancement
		const dateInput = document.getElementById('task_due_date');
		if (dateInput) {
			// Set min date to today
			const today = new Date();
			const year = today.getFullYear();
			const month = String(today.getMonth() + 1).padStart(2, '0');
			const day = String(today.getDate()).padStart(2, '0');
			dateInput.min = `${year}-${month}-${day}`;
		}

		// Form validation
		const taskForm = document.querySelector('form[method="post"]');
		if (taskForm) {
			taskForm.addEventListener('submit', function (e) {
				const title = document.getElementById('task_title');
				if (!title || !title.value.trim()) {
					e.preventDefault();
					alert('Task title is required');
					title.focus();
					return false;
				}
			});
		}

		// Delete confirmation already handled by onclick attribute in PHP
		// This is just for reference

		// Filter form auto-submit behavior (optional)
		const filterForm = document.querySelector('form.wp-clearfix');
		if (filterForm) {
			const selects = filterForm.querySelectorAll('select');
			selects.forEach((select) => {
				select.addEventListener('change', function () {
					// Optional: auto-submit on filter change
					// filterForm.submit();
				});
			});
		}
	});
})();
