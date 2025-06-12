// Global variables (set from PHP)
let currentUser = userName;
let currentRole = userRole;

// DOM Elements
const screens = document.querySelectorAll('.screen');

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    // Show appropriate dashboard based on PHP session
    showDashboard(currentRole);
    
    // Update user display
    updateUserDisplay(currentUser, currentRole);
    
    // Add keyboard navigation
    document.addEventListener('keydown', handleKeyboardNavigation);
    
    // Initialize specific features
    initializeShelfMap();
    initializeWarehouseOperations();
    startRealTimeUpdates();
}

// Login is now handled by PHP - this function is not needed

function showDashboard(role) {
    // Hide all screens
    screens.forEach(screen => screen.classList.remove('active'));
    
    // Show appropriate screen
    switch(role) {
        case 'admin':
            document.getElementById('admin-screen').classList.add('active');
            break;
        case 'warehouse':
            document.getElementById('warehouse-screen').classList.add('active');
            break;
        case 'shelf':
            document.getElementById('shelf-screen').classList.add('active');
            break;
        default:
            // Fallback to admin if role not recognized
            document.getElementById('admin-screen').classList.add('active');
    }
}

function updateUserDisplay(username, role) {
    const userElements = document.querySelectorAll('[id$="-username"]');
    userElements.forEach(element => {
        if (element.id.includes(role) || element.id === 'admin-username') {
            element.textContent = username;
        }
    });
}

// Logout is now handled by PHP - redirect to logout.php

// Admin Dashboard Functions
function showAdminSection(sectionName) {
    // Hide all admin sections
    document.querySelectorAll('#admin-screen .content-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Show selected section
    document.getElementById(`admin-${sectionName}`).classList.add('active');
    
    // Update navigation
    updateNavigation('#admin-screen', sectionName);
}

// Warehouse Dashboard Functions
function showWarehouseSection(sectionName) {
    // Hide all warehouse sections
    document.querySelectorAll('#warehouse-screen .content-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Show selected section
    document.getElementById(`warehouse-${sectionName}`).classList.add('active');
    
    // Update navigation
    updateNavigation('#warehouse-screen', sectionName);
}

// Shelf Dashboard Functions
function showShelfSection(sectionName) {
    // Hide all shelf sections
    document.querySelectorAll('#shelf-screen .content-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Show selected section
    document.getElementById(`shelf-${sectionName}`).classList.add('active');
    
    // Update navigation
    updateNavigation('#shelf-screen', sectionName);
}

function updateNavigation(screenSelector, activeSectionName) {
    // Remove active class from all nav links in current screen
    document.querySelectorAll(`${screenSelector} .nav-link`).forEach(link => {
        link.classList.remove('active');
    });
    
    // Add active class to current nav link
    const activeLink = document.querySelector(`${screenSelector} .nav-link[onclick*="${activeSectionName}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
}

// Keyboard Navigation
function handleKeyboardNavigation(e) {
    // ESC key to logout
    if (e.key === 'Escape' && currentUser) {
        if (confirm('Vai tiešām vēlaties izlogoties?')) {
            window.location.href = 'logout.php';
        }
    }
    
    // Enter key in login form
    if (e.key === 'Enter') {
        const loginScreen = document.getElementById('login-screen');
        if (loginScreen && loginScreen.classList.contains('active')) {
            const loginBtn = document.querySelector('.login-btn');
            if (loginBtn) {
                loginBtn.click();
            }
        }
    }
}

// Shelf Management Functions
function initializeShelfMap() {
    const shelves = document.querySelectorAll('.shelf');
    shelves.forEach(shelf => {
        shelf.addEventListener('click', function() {
            const shelfId = this.dataset.shelf;
            showShelfDetails(shelfId);
        });
    });
}

function showShelfDetails(shelfId) {
    // This would show detailed information about the selected shelf
    alert(`Plaukta ${shelfId} detalizēta informācija`);
}

// Warehouse Operations
function initializeWarehouseOperations() {
    // Initialize barcode scanning simulation
    const barcodeInput = document.getElementById('productScan');
    if (barcodeInput) {
        barcodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                simulateBarcodeScan(this.value);
                this.value = '';
            }
        });
    }

    // Load inventory items when the warehouse inventory section is displayed
    const warehouseInventorySection = document.getElementById('warehouse-inventory');
    if (warehouseInventorySection) {
        // Observe when the warehouse-inventory section becomes active
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class' && warehouseInventorySection.classList.contains('active')) {
                    loadInventoryItems();
                }
            });
        });
        observer.observe(warehouseInventorySection, { attributes: true });
    }
}

function simulateBarcodeScan(barcode) {
    if (barcode) {
        // Simulate product lookup
        const mockProduct = {
            name: `Produkts ${barcode}`,
            quantity: Math.floor(Math.random() * 100) + 1,
            location: `Sekcija ${String.fromCharCode(65 + Math.floor(Math.random() * 3))}, Plaukts ${Math.floor(Math.random() * 20) + 1}`
        };
        
        addScannedItem(mockProduct);
    }
}

function addScannedItem(product) {
    const receivedItemsList = document.getElementById('receivedItemsList');
    if (receivedItemsList) {
        const uniqueId = `${product.name.replace(/\s/g, '-_')}-${Date.now()}`;
        const itemElement = document.createElement('div');
        itemElement.className = 'activity-item';
        itemElement.innerHTML = `
            <i class="fas fa-box"></i>
            <div class="activity-details">
                <p><strong>${product.name}</strong></p>
                <div class="quantity-input-group">
                    <label for="quantity-${uniqueId}">Daudzums:</label>
                    <input type="number" id="quantity-${uniqueId}" value="${product.quantity}" min="1" class="item-quantity-input">
                </div>
                <span class="location">${product.location}</span>
            </div>
            <button class="btn btn-sm btn-primary" onclick="moveToInventory(this, '${product.name}', '${uniqueId}')">Pārvietot uz inventāru</button>
        `;
        receivedItemsList.appendChild(itemElement);
    }
}

async function moveToInventory(button, productName, uniqueId) {
    const item = button.closest('.activity-item');
    const quantityInput = item.querySelector(`#quantity-${uniqueId}`);
    const quantity = parseInt(quantityInput.value);

    if (isNaN(quantity) || quantity <= 0) {
        showNotification('Lūdzu ievadiet derīgu daudzumu.', 'error');
        return;
    }

    // Disable button and input while processing
    button.disabled = true;
    quantityInput.disabled = true;
    button.textContent = 'Pārvietoju...';

    try {
        const response = await fetch('move_to_inventory.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ product_name: productName, quantity: quantity })
        });

        const data = await response.json();
        if (data.success) {
            showNotification(`Produkts "${productName}" (${quantity} gab.) veiksmīgi pārvietots uz inventāru.`, 'success');
            item.remove(); // Remove item from received list
            updateStats('moved_to_inventory'); // Update statistics
            // Refresh the inventory list
            const inventoryTableBody = document.getElementById('inventoryTableBody');
            if (inventoryTableBody) {
                // Assuming you have a function to load inventory items
                loadInventoryItems();
            }
        } else {
            showNotification(`Kļūda pārvietojot produktu "${productName}" uz inventāru: ${data.message || 'Nezināma kļūda'}`, 'error');
            button.disabled = false;
            quantityInput.disabled = false;
            button.textContent = 'Pārvietot uz inventāru';
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification(`Kļūda komunikācijā ar serveri: ${error.message}`, 'error');
        button.disabled = false;
        quantityInput.disabled = false;
        button.textContent = 'Pārvietot uz inventāru';
    }
}

// Statistics Update
function updateStats(action) {
    // Update the dashboard statistics based on the action
}

// Real-time Updates Simulation
function startRealTimeUpdates() {
    setInterval(() => {
        // Simulate random updates to statistics
        if (currentRole === 'admin') {
            updateRandomStats();
        }
    }, 30000); // Update every 30 seconds
}

function updateRandomStats() {
    const statCards = document.querySelectorAll('.stat-card h3');
    statCards.forEach(stat => {
        const currentValue = parseInt(stat.textContent.replace(/[€,]/g, ''));
        if (!isNaN(currentValue)) {
            const change = Math.floor(Math.random() * 10) - 5; // Random change between -5 and +5
            const newValue = Math.max(0, currentValue + change);
            
            if (stat.textContent.includes('€')) {
                stat.textContent = `€${newValue.toLocaleString()}`;
            } else {
                stat.textContent = newValue.toString();
            }
        }
    });
}

// Mobile responsiveness
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('open');
    }
}

// Initialize additional features when page loads
window.addEventListener('load', function() {
    initializeShelfMap();
    initializeWarehouseOperations();
    startRealTimeUpdates();
    
    // Add mobile menu button if needed
    if (window.innerWidth <= 768) {
        addMobileMenuButton();
    }
});

function addMobileMenuButton() {
    const headers = document.querySelectorAll('.main-header');
    headers.forEach(header => {
        const menuButton = document.createElement('button');
        menuButton.innerHTML = '<i class="fas fa-bars"></i>';
        menuButton.className = 'mobile-menu-btn';
        menuButton.onclick = toggleSidebar;
        header.querySelector('.header-left').prepend(menuButton);
    });
}

// Utility Functions
function formatDate(date) {
    return new Intl.DateTimeFormat('lv-LV', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Function to confirm receive action
function confirmReceive(productCode) {
    if (confirm('Vai tiešām vēlaties apstiprināt šo produktu saņemšanu?')) {
        // Handle the confirm receive logic here
        console.log('Confirming receive for product:', productCode);
        showNotification('Produkts apstiprināts veiksmīgi!', 'success');
    }
}

// Export functions for global use
window.showAdminSection = showAdminSection;
window.showWarehouseSection = showWarehouseSection;
window.showShelfSection = showShelfSection;
window.confirmReceive = confirmReceive;

// Settings functions
function applyTheme(theme) {
    if (theme === 'dark') {
        document.body.classList.add('dark-theme');
    } else {
        document.body.classList.remove('dark-theme');
    }
}

// User registration validation (Reverted to basic checks)
function validateUserForm(formData) {
    const errors = {};
    // Minimal validation to ensure fields are not empty if they are required by backend (e.g. for hashing password).
    if (!formData.get('firstName')) {
        errors.firstName = 'Vārds nevar būt tukšs.';
    }
    if (!formData.get('lastName')) {
        errors.lastName = 'Uzvārds nevar būt tukšs.';
    }
    if (!formData.get('email')) {
        errors.email = 'E-pasts nevar būt tukšs.';
    }
    if (!formData.get('password')) {
        errors.password = 'Parole nevar būt tukša.';
    }
    if (!formData.get('role')) {
        errors.role = 'Loma nevar būt tukša.';
    }

    return errors;
}

// Validation popup functions
function createValidationPopup() {
    const popup = document.createElement('div');
    popup.className = 'validation-popup';
    popup.innerHTML = `
        <div class="validation-popup-header">
            <i class="fas fa-exclamation-circle"></i>
            <h3 class="validation-popup-title">Validācijas kļūdas</h3>
        </div>
        <div class="validation-popup-content">
            <ul class="validation-list"></ul>
        </div>
        <div class="validation-popup-footer">
            <button class="validation-popup-close">Aizvērt</button>
        </div>
    `;
    document.body.appendChild(popup);
    return popup;
}

function showValidationPopup(errors) {
    let popup = document.querySelector('.validation-popup');
    if (!popup) {
        popup = createValidationPopup();
    }

    const header = popup.querySelector('.validation-popup-header');
    const title = popup.querySelector('.validation-popup-title');
    const list = popup.querySelector('.validation-list');
    const closeBtn = popup.querySelector('.validation-popup-close');

    // Clear previous content
    list.innerHTML = '';

    // Set header style and icon
    header.className = 'validation-popup-header error';
    header.querySelector('i').className = 'fas fa-exclamation-circle';
    title.textContent = 'Validācijas kļūdas';

    // Add error messages to list
    Object.entries(errors).forEach(([field, message]) => {
        const li = document.createElement('li');
        li.className = 'error';
        li.innerHTML = `
            <i class="fas fa-times-circle"></i>
            <span>${message}</span>
        `;
        list.appendChild(li);
    });

    // Show popup
    popup.classList.add('show');

    // Add close button functionality
    closeBtn.onclick = () => {
        popup.classList.remove('show');
    };

    // Auto-hide after 5 seconds
    setTimeout(() => {
        popup.classList.remove('show');
    }, 5000);
}

function showSuccessPopup(message) {
    let popup = document.querySelector('.validation-popup');
    if (!popup) {
        popup = createValidationPopup();
    }

    const header = popup.querySelector('.validation-popup-header');
    const title = popup.querySelector('.validation-popup-title');
    const list = popup.querySelector('.validation-list');
    const closeBtn = popup.querySelector('.validation-popup-close');

    // Clear previous content
    list.innerHTML = '';

    // Set header style and icon
    header.className = 'validation-popup-header success';
    header.querySelector('i').className = 'fas fa-check-circle';
    title.textContent = 'Veiksmīgi';

    // Add success message
    const li = document.createElement('li');
    li.className = 'success';
    li.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>${message}</span>
    `;
    list.appendChild(li);

    // Show popup
    popup.classList.add('show');

    // Add close button functionality
    closeBtn.onclick = () => {
        popup.classList.remove('show');
    };

    // Auto-hide after 3 seconds
    setTimeout(() => {
        popup.classList.remove('show');
    }, 3000);
}

// Helper to show inline error messages
function showInlineError(inputElement, message) {
    // Remove any existing error messages for this input
    let errorElement = inputElement.nextElementSibling;
    if (errorElement && errorElement.classList.contains('inline-error-message')) {
        errorElement.remove();
    }

    if (message) {
        errorElement = document.createElement('div');
        errorElement.className = 'inline-error-message text-danger mt-1';
        errorElement.textContent = message;
        inputElement.parentNode.insertBefore(errorElement, inputElement.nextSibling);
        inputElement.classList.add('is-invalid'); // Add Bootstrap's invalid class for styling
    } else {
        inputElement.classList.remove('is-invalid');
    }
}

// Debounce function
function debounce(func, delay) {
    let timeout;
    return function(...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), delay);
    };
}

// Placeholder for availability checks (backend files removed)
async function checkEmailAvailability(email) {
    return { available: true }; 
}

async function checkUsernameAvailability(username) {
    return { available: true }; 
}

// Add user form submission handler (reverted to basic)
document.addEventListener('DOMContentLoaded', function() {
    const addUserForm = document.getElementById('addUserForm');
    const emailInput = document.getElementById('email');
    // Revert email input event listeners if they are still present
    if (emailInput) {
        emailInput.removeEventListener('input', debounce(async (e) => {}, 500)); // Remove previous listener
    }

    if (addUserForm) {
        addUserForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Clear previous error messages and highlighting
            document.querySelectorAll('.inline-error-message').forEach(el => el.remove());
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            
            // Basic validation for required fields (can be removed if backend handles everything)
            const formData = new FormData(this);
            const errors = validateUserForm(formData);

            if (Object.keys(errors).length > 0) {
                showValidationPopup(errors);
                Object.entries(errors).forEach(([field, message]) => {
                    const input = document.getElementById(field);
                    if (input) {
                        showInlineError(input, message);
                    }
                });
                return;
            }

            const userData = {
                first_name: formData.get('firstName'),
                last_name: formData.get('lastName'),
                email: formData.get('email'),
                password: formData.get('password'),
                role: formData.get('role')
            };
            
            // console.log('Sending user data:', userData); // Removed debug log

            fetch('add_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(userData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessPopup('Lietotājs veiksmīgi pievienots!');
                    addUserForm.reset(); 
                } else {
                    let errorMessage = 'Kļūda pievienojot lietotāju.';
                    if (data.error) {
                        errorMessage = data.error;
                        if (data.details) {
                            Object.entries(data.details).forEach(([field, message]) => {
                                const input = document.getElementById(field);
                                if (input) {
                                    showInlineError(input, message);
                                }
                            });
                        }
                    }
                    showValidationPopup({ general: errorMessage });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showValidationPopup({ general: 'Tīkla kļūda vai servera atbilde nav apstrādājama.' });
            });
        });
    }
});

// Function to load inventory items from the database
async function loadInventoryItems() {
    console.log('Loading inventory items...');
    const inventoryTableBody = document.getElementById('inventoryTableBody');
    if (!inventoryTableBody) {
        console.error('Inventory table body not found');
        return;
    }

    inventoryTableBody.innerHTML = ''; // Clear existing content

    try {
        const response = await fetch('get_inventory.php');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();

        if (data.success && data.inventory.length > 0) {
            data.inventory.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.product_code || ''}</td>
                    <td>${item.product_name || ''}</td>
                    <td>${item.category || ''}</td>
                    <td>${item.quantity || 0}</td>
                    <td>€${parseFloat(item.unit_price || 0).toFixed(2)}</td>
                    <td><span class="status active">Pieejams</span></td>
                    <td>
                        <button class="btn-icon edit" onclick="editProduct(${item.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn-icon delete" onclick="deleteProduct(${item.id})"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                inventoryTableBody.appendChild(row);
            });
        } else if (data.success && data.inventory.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="7" class="text-center">Inventārā nav neviena produkta</td>';
            inventoryTableBody.appendChild(row);
        } else {
            console.error('Error fetching inventory:', data.message || 'Unknown error');
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="7" class="text-center">Kļūda ielādējot inventāru: ${data.message || 'Nezināma kļūda'}</td>`;
            inventoryTableBody.appendChild(row);
        }
    } catch (error) {
        console.error('Error loading inventory items:', error);
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="7" class="text-center">Kļūda komunikācijā ar serveri: ${error.message}</td>`;
        inventoryTableBody.appendChild(row);
    }
}

// Shelf Data Entry Form Validation
function validateShelfForm(event) {
    event.preventDefault(); // Prevent default form submission

    const form = document.getElementById('shelfDataForm');
    const shelfCode = form.elements['shelf_code'].value;
    const productQuantity = form.elements['product_quantity'].value;
    const shelfStatus = form.elements['shelf_status'].value;
    const shelfComment = form.elements['shelf_comment'].value;

    let isValid = true;

    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

    // Validate Shelf Code
    if (shelfCode === '') {
        showInlineError(document.getElementById('shelfCode'), 'Lūdzu izvēlieties plauktu.');
        isValid = false;
    }

    // Validate Product Quantity
    const quantityNum = parseInt(productQuantity);
    if (productQuantity === '' || isNaN(quantityNum) || quantityNum <= 0) {
        showInlineError(document.getElementById('productQuantity'), 'Lūdzu ievadiet derīgu produktu skaitu (jābūt lielākam par 0).');
        isValid = false;
    }

    // Validate Shelf Status
    if (shelfStatus === '') {
        showInlineError(document.getElementById('shelfStatus'), 'Lūdzu izvēlieties stāvokli.');
        isValid = false;
    }

    // Validate Shelf Comment (optional, but check length if provided)
    if (shelfComment.length > 255) {
        showInlineError(document.getElementById('shelfComment'), 'Komentārs nedrīkst pārsniegt 255 rakstzīmes.');
        isValid = false;
    }

    if (isValid) {
        // If validation passes, send data to backend
        const formData = {
            shelf_code: shelfCode,
            product_quantity: quantityNum,
            shelf_status: shelfStatus,
            shelf_comment: shelfComment
        };

        fetch('save_shelf_data.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Dati veiksmīgi saglabāti!', 'success');
                form.reset(); // Reset form after successful submission
            } else {
                showNotification(data.message || 'Kļūda saglabājot datus.', 'error');
                // Display specific errors if provided by backend
                if (data.errors) {
                    for (const field in data.errors) {
                        const inputElement = document.getElementById(field);
                        if (inputElement) {
                            showInlineError(inputElement, data.errors[field]);
                        }
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Servera kļūda. Lūdzu mēģiniet vēlreiz.', 'error');
        });
    }
} 