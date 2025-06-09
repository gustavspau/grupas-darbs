// Global variables
let currentUser = null;
let currentRole = null;

// DOM Elements
const loginForm = document.getElementById('loginForm');
const screens = document.querySelectorAll('.screen');

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    // Add login form event listener
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
    
    // Add keyboard navigation
    document.addEventListener('keydown', handleKeyboardNavigation);
    
    // Check for existing session
    checkExistingSession();
}

function handleLogin(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const role = document.getElementById('role').value;
    
    // Simple validation (in real app, this would be server-side)
    if (username && password && role) {
        currentUser = username;
        currentRole = role;
        
        // Store session
        sessionStorage.setItem('currentUser', username);
        sessionStorage.setItem('currentRole', role);
        
        // Show appropriate dashboard
        showDashboard(role);
        
        // Update user display
        updateUserDisplay(username, role);
    } else {
        alert('Lūdzu aizpildiet visus laukus');
    }
}

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

function logout() {
    // Clear session
    sessionStorage.removeItem('currentUser');
    sessionStorage.removeItem('currentRole');
    
    // Reset form
    loginForm.reset();
    
    // Show login screen
    screens.forEach(screen => screen.classList.remove('active'));
    document.getElementById('login-screen').classList.add('active');
    
    // Reset variables
    currentUser = null;
    currentRole = null;
}

function checkExistingSession() {
    const storedUser = sessionStorage.getItem('currentUser');
    const storedRole = sessionStorage.getItem('currentRole');
    
    if (storedUser && storedRole) {
        currentUser = storedUser;
        currentRole = storedRole;
        showDashboard(storedRole);
        updateUserDisplay(storedUser, storedRole);
    }
}

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
            logout();
        }
    }
    
    // Enter key in login form
    if (e.key === 'Enter' && document.getElementById('login-screen').classList.contains('active')) {
        const loginBtn = document.querySelector('.login-btn');
        if (loginBtn) {
            loginBtn.click();
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
    const barcodeInputs = document.querySelectorAll('input[placeholder*="svītrkod"]');
    barcodeInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                simulateBarcodeScan(this.value);
                this.value = '';
            }
        });
    });
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
    const receivedItems = document.querySelector('.received-items');
    if (receivedItems) {
        const itemElement = document.createElement('div');
        itemElement.className = 'activity-item';
        itemElement.innerHTML = `
            <i class="fas fa-box"></i>
            <div class="activity-details">
                <p><strong>${product.name}</strong> - Daudzums: ${product.quantity}</p>
                <span class="location">${product.location}</span>
            </div>
            <button class="btn btn-sm" onclick="confirmReceive(this)">Apstiprināt</button>
        `;
        receivedItems.appendChild(itemElement);
    }
}

function confirmReceive(button) {
    const item = button.closest('.activity-item');
    item.style.backgroundColor = '#d4edda';
    button.textContent = 'Apstiprināts';
    button.disabled = true;
    
    // Update statistics
    updateStats('received');
}

// Statistics Update
function updateStats(action) {
    // This would update the dashboard statistics
    console.log(`Statistika atjaunināta: ${action}`);
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

// Export functions for global use
window.showAdminSection = showAdminSection;
window.showWarehouseSection = showWarehouseSection;
window.showShelfSection = showShelfSection;
window.logout = logout;
window.confirmReceive = confirmReceive; 