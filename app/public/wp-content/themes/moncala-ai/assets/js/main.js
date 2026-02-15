/**
 * MonCala AI Theme - Main JavaScript
 *
 * Handles interactive components and theme functionality
 * - Mobile menu toggle
 * - Smooth scrolling
 * - Code block copy button (Phase 4+)
 *
 * @since 1.0.0
 */

(function () {
	'use strict';

	/**
	 * Initialize theme
	 */
	function init() {
		// Mobile menu toggle (Phase 3)
		setupMobileMenu();

		// Smooth scroll (Phase 3+)
		setupSmoothScroll();

		// Code copy button (Phase 4+)
		setupCodeCopyButton();
	}

	/**
	 * Mobile menu toggle functionality
	 */
	function setupMobileMenu() {
		const menuToggle = document.querySelector('.mobile-menu-toggle');
		const navMenu = document.querySelector('.main-navigation');

		if (!menuToggle || !navMenu) return;

		menuToggle.addEventListener('click', function () {
			navMenu.classList.toggle('is-active');
			menuToggle.setAttribute(
				'aria-expanded',
				menuToggle.getAttribute('aria-expanded') === 'true' ? 'false' : 'true'
			);
		});

		// Close menu on link click
		const menuLinks = navMenu.querySelectorAll('a');
		menuLinks.forEach((link) => {
			link.addEventListener('click', function () {
				navMenu.classList.remove('is-active');
				menuToggle.setAttribute('aria-expanded', 'false');
			});
		});
	}

	/**
	 * Smooth scroll behavior
	 */
	function setupSmoothScroll() {
		document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
			anchor.addEventListener('click', function (e) {
				const href = this.getAttribute('href');
				const target = document.querySelector(href);

				if (target) {
					e.preventDefault();
					target.scrollIntoView({ behavior: 'smooth' });
				}
			});
		});
	}

	/**
	 * Code block copy button
	 */
	function setupCodeCopyButton() {
		const codeBlocks = document.querySelectorAll('pre code');

		codeBlocks.forEach((codeBlock) => {
			const pre = codeBlock.parentNode;

			// Create copy button
			const copyButton = document.createElement('button');
			copyButton.className = 'code-copy-btn';
			copyButton.textContent = 'Copy';
			copyButton.type = 'button';
			copyButton.setAttribute('aria-label', 'Copy code');

			// Insert button before code block
			pre.style.position = 'relative';
			pre.insertBefore(copyButton, codeBlock);

			// Copy functionality
			copyButton.addEventListener('click', function () {
				const text = codeBlock.textContent;

				// Use modern clipboard API
				if (navigator.clipboard) {
					navigator.clipboard.writeText(text).then(() => {
						const originalText = copyButton.textContent;
						copyButton.textContent = 'Copied!';
						setTimeout(() => {
							copyButton.textContent = originalText;
						}, 2000);
					});
				} else {
					// Fallback for older browsers
					const textarea = document.createElement('textarea');
					textarea.value = text;
					document.body.appendChild(textarea);
					textarea.select();
					document.execCommand('copy');
					document.body.removeChild(textarea);

					copyButton.textContent = 'Copied!';
					setTimeout(() => {
						copyButton.textContent = 'Copy';
					}, 2000);
				}
			});
		});
	}

	// Initialize when DOM is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
