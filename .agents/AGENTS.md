# Rules

## Browser Subagent Usage
DO NOT run the browser subagent (`browser_subagent`) automatically without explicit permission from the user. Browser subagent testing is token-intensive and should only be run when strictly necessary or explicitly requested by the user. Do NOT auto-capture screenshots to verify your UI changes unless the user asks for it.

## UI/UX Guidelines & Color Themes (Pro Max)
This project follows a "UI/UX Pro Max" guideline with a clean, modern, and professional aesthetic tailored for PKTJ.

### Color Palette
- **Deep Navy (Primary/Navbar/Footer)**: #0b2545 - Used for main navigation, footers, and solid background elements to convey trust and professionalism.
- **Accent Yellow (Highlight/Borders)**: #ffca28 - Used for top borders in navbars/footers and accentuating important headings or lines.
- **Light Blue-Purple (Active/Hover states)**: #e6ecff (bg) and #4361ee (text/border) - Used for active pagination pills, active nav links, and interactive highlights.
- **Light Gray (App Background)**: #f8f9fa - Used as the global body background for a clean, spacious feel.
- **Card/Container Backgrounds**: #ffffff (White) with subtle shadows (ox-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) or Bootstrap's .shadow-sm).
- **Text Colors**: 
  - Headings/Primary text: #333333 or #212529 (Dark)
  - Secondary/Muted text: #6c757d (Bootstrap text-muted)

### Typography & Icons
- **Font Family**: 'Inter', sans-serif. Use w-bold (700) or w-semibold (600) for headings and important data, w-medium (500) for labels.
- **Icons**: Bootstrap Icons (i-*). Avoid using icons redundantly on top of text in standard menus unless it adds clear value (keep it minimalist).

### Component Styling
- **Cards & Containers**: Always use rounded corners (.rounded-4 or .rounded-3) and remove default borders (.border-0) in favor of soft shadows (.shadow-sm).
- **Buttons & Pagers**: Prefer rounded-pill (.rounded-pill) shapes for pagination and action buttons to give a modern, seamless look.
- **Forms & Inputs**: Inputs should have subtle borders (order-secondary-subtle), rounded corners, and clear labels. Focus states should use a soft blue/purple glow instead of harsh default outlines.
- **Micro-interactions**: Use subtle transitions (	ransition: all 0.2s ease) on hovers, such as lifting cards (	ransform: translateY(-2px)) or slightly fading buttons (opacity: 0.85).

When creating new features or modifying existing ones, STRICTLY adhere to this color palette and component styling to maintain consistency.
