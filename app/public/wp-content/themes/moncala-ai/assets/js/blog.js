/**
 * Blog Functionality
 *
 * Handles TOC navigation, reading progress, and social sharing
 *
 * @package MonCala_AI
 */

document.addEventListener('DOMContentLoaded', function() {
	// Initialize reading progress tracker
	initReadingProgress();

	// Initialize table of contents navigation
	initTableOfContents();

	// Initialize social sharing copy-to-clipboard
	initSocialSharing();
});

/**
 * Initialize reading progress indicator
 */
function initReadingProgress() {
	const progressBar = document.getElementById('reading-progress');
	if (!progressBar) return;

	window.addEventListener('scroll', function() {
		// Calculate scroll percentage
		const windowHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
		const scrolled = (window.scrollY / windowHeight) * 100;
		progressBar.style.width = scrolled + '%';
	});
}

/**
 * Initialize table of contents navigation
 */
function initTableOfContents() {
	const tocToggle = document.querySelector('.toc__toggle');
	const tocNav = document.querySelector('.toc__nav');
	const tocLinks = document.querySelectorAll('.toc__link');

	if (!tocToggle || !tocNav) return;

	// Toggle TOC visibility
	tocToggle.addEventListener('click', function() {
		const isCollapsed = tocNav.classList.toggle('collapsed');
		tocToggle.setAttribute('aria-expanded', !isCollapsed);
	});

	// Handle TOC link clicks
	tocLinks.forEach(function(link) {
		link.addEventListener('click', function(e) {
			e.preventDefault();

			const targetId = this.getAttribute('href').substring(1);
			const targetElement = document.getElementById(targetId);

			if (targetElement) {
				// Scroll to target
				targetElement.scrollIntoView({ behavior: 'smooth' });

				// Update active state
				updateActiveTocLink();
			}
		});
	});

	// Update active TOC link on scroll
	window.addEventListener('scroll', updateActiveTocLink, { passive: true });

	/**
	 * Update active TOC link based on scroll position
	 */
	function updateActiveTocLink() {
		const headings = document.querySelectorAll('h2[id], h3[id]');
		let activeId = null;

		headings.forEach(function(heading) {
			const rect = heading.getBoundingClientRect();
			if (rect.top <= 100) {
				activeId = heading.id;
			}
		});

		// Remove active class from all links
		tocLinks.forEach(function(link) {
			link.classList.remove('active');
		});

		// Add active class to current link
		if (activeId) {
			const activeLink = document.querySelector(`.toc__link[href="#${activeId}"]`);
			if (activeLink) {
				activeLink.classList.add('active');
			}
		}
	}
}

/**
 * Initialize social sharing functionality
 */
function initSocialSharing() {
	const copyButtons = document.querySelectorAll('.social-share__button--copy');

	copyButtons.forEach(function(button) {
		button.addEventListener('click', function(e) {
			e.preventDefault();

			const url = this.getAttribute('data-copy-url');
			if (!url) return;

			// Copy to clipboard
			navigator.clipboard.writeText(url).then(function() {
				// Show success feedback
				const originalText = button.querySelector('.social-share__text');
				const originalHTML = originalText ? originalText.innerHTML : 'Copied!';

				if (originalText) {
					originalText.innerHTML = 'Copied!';
				} else {
					button.textContent = 'Copied!';
				}

				// Reset after 2 seconds
				setTimeout(function() {
					if (originalText) {
						originalText.innerHTML = originalHTML;
					} else {
						button.textContent = 'Copy';
					}
				}, 2000);
			}).catch(function(err) {
				console.error('Failed to copy URL:', err);
			});
		});
	});
}
