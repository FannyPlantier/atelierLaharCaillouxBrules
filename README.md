# Project : Atelier Lahar Cailloux Brûlés
Showcase website project built with WordPress

## Context and objectives
### Project overwiew
This project consists in designing and deploying a showcase website for Atelier Lahar – Cailloux Brûlés, a French artisan ceramist.
The website aims to present the artist’s universe, his work, and his approach, while offering clear and elegant communication channels.

The project covers the entire lifecycle of the website: conception, design, development, configuration, and deployment using WordPress and podman.

### Business Objectives
- Establish a professional and artistic online presence for the ceramist
- Promote the artistic identity, craftsmanship, and values of the atelier
- Showcase ceramic pieces and past creations
- Facilitate contact with potential clients, galleries, and partners

### Awareness and Communication Objectives
- Highlight the ceramist’s profile, background, and creative process  
- Strengthen the brand image of Atelier Lahar  
- Improve online visibility and accessibility

### Technical Objectives
- Provide a user-friendly and secure website
- Allow the administrator to easily manage content without technical knowledge
- Ensure responsive design and good performance on all devices

### Project Scope
Included :
- Showcase website (Front-Office)
- WordPress administration interface (Back-Office)
- Content management (pages, images, texts)
- Multilingual support (French, English, Spanish)
- Contact and communication features

Excluded :
- Online sales and payment systems (for now)
- Shopping cart and order management
- Physical or virtual stock management
- Advanced marketing automation tools

## Detailed Functional Specifications
### Front-Office (Visitor Experience)
|Feature| Actor | Detailed Description |
|:-----|:------|:---------------------|
Home page |	Visitor |Presentation of the atelier, artistic universe, and key visuals. Highlights recent creations or featured pieces.
Profile & biography | Visitor |	Dedicated page “The Artisan / The Studio” presenting the ceramist’s background, philosophy, inspirations, and photos of the studio and creative process.
Creations / Gallery | Visitor |	Visual showcase of ceramic pieces presented as a gallery or portfolio. Each item includes photos, title, short description, dimensions, materials, and status (e.g. Unique Piece, Sold, Exhibition Piece).
Creation detail page | Visitor | Detailed page for a specific piece with high-resolution images, description, techniques, materials, and optional availability information (without purchase).
Static pages | Visitor | Informational pages such as “About”, “Approach”, “Exhibitions”, or “News” managed via WordPress pages.
Contact form | Visitor | Contact form allowing visitors to send a message directly to the ceramist. Required fields: Name, Email, Subject, Message.
Social media links | Visitor | Clickable icons linking to social networks (Instagram, Facebook, etc.). Displayed in the header and/or footer. Links open in a new tab.


### Back-Office (Administration Interface)
|Feature| Actor | Detailed Description |
|:-----|:------|:---------------------|
Content management | Administrator| Creation, modification, and deletion of pages and posts via WordPress.
Gallery management | Administrator | Upload and management of images for ceramic pieces (media library, galleries, featured images).
Creation entries | Administrator | Management of ceramic pieces as custom posts or structured content (title, description, materials, dimensions, status).
Multilingual content | Administrator | Management of translations for all pages and creations through a multilingual plugin.
Contact messages | Administrator | Reception of messages sent via the contact form, forwarded by email.

## Multilingual Management
### Supported Languages :
- French (FR)
- English (EN)
- Spanish (ES)

### Language Selection :  
A language selector (dropdown or flags) available in the header or footer
The selected language persists during navigation.

### Content Translation :  
All editable content must be translatable:
- Pages  
- Creation descriptions
- Biography and static texts
Interface elements (menus, buttons, system messages) must be available in all supported languages

### Back-Office Language Management
The administrator can manage and edit translations directly from the WordPress administration panel

## Non-functional specifications
### Design and User Experience
- Design: The design must be clean, highlight the photos of the pieces, and reflect the artisanal and high-end identity of the Atelier Lahar.
- Responsive Design: The site must be fully functional and aesthetically pleasing on desktops, tablets, and mobiles.

### Security
- HTTPS protocol (SSL certificate)
- Protection against common web vulnerabilities
- Secure handling of contact form data
- GDPR compliance (privacy policy, consent for contact form)

### Performance and Scalability
- Fast page loading (target < 2 seconds)  
- Image optimization for galleries
- Hosting suitable for moderate traffic with scalability options

## Visual identity guidelines
### Fonts :  
Titles : Payrus  
Main text : Poor Richard

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