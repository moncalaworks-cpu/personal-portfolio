# ADA Accessibility Development Checklist

**CRITICAL: Apply these checks to EVERY code change**

## 🖼️ Images & Media
- [ ] All images have descriptive alt text
- [ ] Use pattern: `alt="Descriptive text for [context]"`
- [ ] Icon-only elements have `aria-label`
- [ ] Decorative elements marked `aria-hidden="true"`

## ⌨️ Keyboard Navigation
- [ ] All interactive elements keyboard accessible (TAB, ENTER, ESC)
- [ ] Visible focus states (3px outline with outline-offset)
- [ ] Logical tab order maintained
- [ ] Use `focus-visible` for keyboard-only focus

## 🎨 Color & Contrast
- [ ] Text contrast ≥4.5:1 (WCAG AA minimum)
- [ ] Large text (18px+) contrast ≥3:1
- [ ] Don't rely on color alone
- [ ] Test with WebAIM or axe DevTools

## 📝 Forms & Input
- [ ] Every `<input>` has associated `<label for="id">`
- [ ] Error messages clearly identified
- [ ] Focus styles visible on all form fields
- [ ] Required fields marked appropriately

## 📱 Touch Targets
- [ ] Buttons/clickable elements: 44x44px minimum
- [ ] Spacing between targets: 8px minimum
- [ ] All accessible from keyboard

## 🔊 Screen Readers
- [ ] Semantic HTML: `<main>`, `<nav>`, `<section>`, `<article>`
- [ ] Correct heading hierarchy (h1 → h2 → h3)
- [ ] ARIA landmarks present
- [ ] Action confirmations in `aria-live` regions
- [ ] Links have descriptive text

## 🧭 Content Structure
- [ ] Heading hierarchy maintained
- [ ] Lists use proper `<ul>/<ol>` tags
- [ ] Tables have `<thead>`, `<tbody>`, headers
- [ ] Skip link implemented: `<a href="#main">`

## ✨ Dynamic Content
- [ ] Status updates in `aria-live="polite"` regions
- [ ] Modal dialogs trap focus
- [ ] Expand/collapse uses `aria-expanded`
- [ ] Loading states announced

## Code Examples

### Alt Text Pattern
```php
the_post_thumbnail( 'size', array(
    'alt' => sprintf(
        esc_attr__( 'Featured image for %s', 'moncala-ai' ),
        esc_attr( get_the_title() )
    ),
) );
```

### Focus State Pattern
```css
a:focus-visible, button:focus-visible {
    outline: 3px solid var(--color-primary);
    outline-offset: 2px;
}
```

### ARIA Label Pattern
```html
<div aria-label="Description">
    <span aria-hidden="true">🎯</span>
</div>
```

### Screen Reader Announcement
```html
<div aria-live="polite" aria-atomic="true" class="sr-only">
    Status update text here
</div>
```

## Testing Before Commit

1. **Keyboard Navigation**: Tab through entire page - all interactive elements reachable
2. **Screen Reader**: Enable VoiceOver (Mac) or NVDA (Windows) - page readable
3. **Contrast**: Use Lighthouse or WebAIM to verify ratios
4. **Mobile**: Test with mobile browser - touch targets adequate
5. **Lighthouse**: Run Chrome DevTools audit - target 90+ score

## Current Compliance
- ✅ WCAG 2.1 Level A: 95%
- ✅ WCAG 2.1 Level AA: 90%
- ⭐ WCAG 2.1 Level AAA: 70%

## The Golden Rule
**"Can this be used by someone without a mouse, without seeing the screen, or with limited hearing?"**

If the answer is NO - it needs accessibility work before deployment.

---

Last Updated: February 15, 2026
Status: All future changes must maintain these standards
