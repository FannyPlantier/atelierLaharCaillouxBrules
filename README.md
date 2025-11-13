# Project : Atelier Lahar Cailloux Brûlés
End-to-end E-commerce website project, from conception to deployment and using podman.

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

### Back-Office (Administration Interface)
| Feature | Actor  | Detailed Description |
| :-------| :----- | :------------------- |
| Catalog Management | Administrator | CRUD operations on products: Create, Read, Update, Delete. Fields: Name, Description, Price, Stock Quantity, Photos (multiple), Category (if applicable), Status (Available, Sold, Unique Piece),  Images, Weight/Dimensions (for shipping fee calculation). |
| Order Management | Administrator | Viewing: Dashboard listing all orders (filterable by status, date). Status Update: Ability to update the status (e.g., from "Paid" to "In Preparation," then to "Shipped"). Details: Access to order details (products, customer, addresses, shipping method). |
| Customer Management | Administrator | Viewing a list of customer accounts (name, email, registration date). Possibility to edit or deactivate an account if necessary. |