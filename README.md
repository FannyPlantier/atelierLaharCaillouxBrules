# Project : Atelier Lahar Cailloux Brûlés
End-to-end E-commerce website project, from conception to deployment using prestashop and podman.

## Context and objectives
### Project obejctives
This project aims to build an e-commerce website "Atelier Lahar Cailloux Brûlés," for a French artisan ceramist. 
- Business Goals: Sell unique or limited-edition artisan ceramic pieces online. Establish a professional and artistic online presence.
- Awareness Objectives: Highlight the ceramist's profile and artistic approach.
- Technical Objectives: Provide a secure and user-friendly platform for both the customer and the administrator.

### Project Scope
- Included: Showcase/e-commerce website (Front-Office), Administration Interface (Back-Office), User, Catalog, and Order Management.
- Excluded: Management of physical stock (the site will manage virtual/logical stock), advanced marketing automation tools (for now).
  

## Detailed functional specifications
### Front-Office (Customer Experience)
| Feature | Actor  | Detailed Description |
| :-------| :----- | :------------------- |
| Profile and biography | Visitor | Display a dedicated "The Artisan/The Studio" page presenting the ceramist's background, philosophy, and photos of their work and studio. |
| Account management | Visitor/Customer | Creation: Registration via email/password. Login/Logout: Secure system. Management: Customers can update their personal information (name, email) and addresses. |
| Catalog and product page | Visitor | Catalog: Display of pieces in a grid or list (photos, name, price). Product Page: Must include high-resolution photo(s), detailed description, dimensions, materials, price, stock status ("Available," "Sold," "Unique Piece"), and an "Add to Cart" button.|
| Shopping cart | Visitor / Customer | Customers can add/remove pieces. Display of content summary, quantities, and subtotal. |
| Checkout process | Customer | Tunnel: Entry/Selection of shipping and billing addresses. Shipping Choice: Display of shipping options and associated fees. |
| Payment | Customer | Secure payment with multiple methods (e.g., Credit Card, PayPal). Integration with a third-party payment gateway is required. |
| Order confirmation | System / Customer | Email: Automatic sending of an order confirmation email summarizing the order. Display: Post-payment thank you page. |
| Order tracking and history | Customer | Client Area: Viewing a list of past orders. Detail: Display of the order status (e.g., "Awaiting Preparation," "Shipped"). |
| Contact Form | Visitor / Customer | Sending a direct message to the ceramist's email address (or via a secure interface). Required fields: Name, Email, Subject, Message. |
| Social media links | Visitor | Addition of clickable icons (logo) for the ceramist's social media (Instagram, Facebook...). Placement in the Footer and/or Header. Links must open in a new tab. |

### Back-Office (Administration Interface)
| Feature | Actor  | Detailed Description |
| :-------| :----- | :------------------- |
| Catalog Management | Administrator | CRUD operations on products: Create, Read, Update, Delete. Fields: Name, Description, Price, Stock Quantity, Photos (multiple), Category (if applicable), Status (Available, Sold, Unique Piece),  Images, Weight/Dimensions (for shipping fee calculation). |
| Order Management | Administrator | Viewing: Dashboard listing all orders (filterable by status, date). Status Update: Ability to update the status (e.g., from "Paid" to "In Preparation," then to "Shipped"). Details: Access to order details (products, customer, addresses, shipping method). |
| Customer Management | Administrator | Viewing a list of customer accounts (name, email, registration date). Possibility to edit or deactivate an account if necessary. |

### Multilingual management
| Feature | Actor  | Detailed Description |
| :-------| :----- | :------------------- |
| Supported languages | System | The site must support French (FR), English (EN) and Spanish (ES). |
| Language selection | Visitor | A language selector (dropdown or flags) must be available in the header or footer, allowing users to switch languages at any time. The selected language should persist throughout the browsing session. |
| Content translation | System / Administrator | The platform must allow for the full translation of all editable content (product descriptions, static pages, biography) and user interface texts (buttons, error messages, menus). |
| Back-office management | Administrator | The administrator must be able to enter and manage the different language versions of the content within the administration interface. |

## Non-functional specifications
### Design and User Experience
- Design: The design must be clean, highlight the photos of the pieces, and reflect the artisanal and high-end identity of the Atelier Lahar.
- Responsive Design: The site must be fully functional and aesthetically pleasing on desktops, tablets, and mobiles.

### Security
- Protocol: Use of HTTPS (SSL Certificate).
- Payment: Secure integration with a payment gateway compliant with standards (e.g., PCI DSS).
- Customer Data: Protection of personal data in accordance with the GDPR.

### Performance and Scalability
- Loading: Pages, especially the catalog and product pages (with photos), must load quickly (target < 2 seconds).
- Hosting: Choose reliable hosting suitable for moderate to growing traffic volume.

## Integrations and Interfaces
### Payment
Gateway: Select and integrate a payment solution (e.g., Stripe, PayPal, or a local banking solution).

### Analytics
Analysis: Integration of Google Analytics or an equivalent analysis tool to track visits, user behavior, and conversions.

### Communication
Emailing: Use of a reliable transactional email service (for order confirmations, status tracking, etc.).

## Visual identity guidelines
### Background colors :  
zinc-950 (main background) : rgb(9, 9, 11)  
zinc-900 (other sections) : rgb(24, 24, 27)  
zinc-800 (borders) : rgb(39, 39, 42)..

### Text colors :
white (main titles) : rgb(255, 255, 255)  
gray-300 (main text) : rgb(209, 213, 219)  
gray-400 (descriptive text) : rgb(156, 163, 175)  
gray-500 (labels) : rgb(107, 114, 128)  
gray-600 (placeholders) : rgb(75, 85, 99)  

### Contrast colors :
orange-600 (borders, buttons) : rgb(234, 88, 12)  
orange-500 (sections, hover) : rgb(249, 115, 22)  

### Colors with opacity :
orange-600/10 : rgba(234, 88, 12, 0.1)  
orange-600/30 : rgba(234, 88, 12, 0.3)  
black/70 : rgba(0, 0, 0, 0.7)  
black/50 : rgba(0, 0, 0, 0.5)