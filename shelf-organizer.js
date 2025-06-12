// Shelf Organizer JavaScript Functions

// Organization Functions
function startOrganizing(shelfId) {
    document.getElementById('organizingShelf').textContent = shelfId;
    document.querySelector('.current-tasks').style.display = 'none';
    document.getElementById('organizeForm').style.display = 'block';
    
    // Add some context based on shelf
    const productCount = document.getElementById('productCount');
    const comment = document.getElementById('organizingComment');
    
    switch(shelfId) {
        case 'A4':
            productCount.value = 15;
            comment.placeholder = 'Plaukts nepieciešams pārkārtot - produkti nav pareizā secībā...';
            break;
        case 'C1':
            productCount.value = 5;
            comment.placeholder = 'Nepieciešams papildināt krājumu līdz minimālajam līmenim...';
            break;
        case 'B3':
            productCount.value = 0;
            comment.placeholder = 'Plaukts nepieciešama apkope - bojāta konstrukcija...';
            break;
    }
}

function completeOrganizing() {
    const shelf = document.getElementById('organizingShelf').textContent;
    const count = document.getElementById('productCount').value;
    const comment = document.getElementById('organizingComment').value;
    
    // Simulate API call
    setTimeout(() => {
        alert(`Plaukta ${shelf} kārtošana pabeigta!\nProduktus: ${count}\nKomentārs: ${comment || 'Nav komentāra'}`);
        cancelOrganizing();
        
        // Update shelf status in the map
        updateShelfStatus(shelf, 'organized');
    }, 500);
}

function cancelOrganizing() {
    document.querySelector('.current-tasks').style.display = 'block';
    document.getElementById('organizeForm').style.display = 'none';
    document.getElementById('productCount').value = '';
    document.getElementById('organizingComment').value = '';
}

// Restock Functions
function restockItem(product, shelf) {
    document.getElementById('restockProduct').value = product;
    document.getElementById('restockShelf').value = shelf;
    document.getElementById('restockModal').style.display = 'block';
    
    // Set suggested amount based on product
    const amountField = document.getElementById('restockAmount');
    switch(product) {
        case 'Mīti':
            amountField.value = 20;
            break;
        case 'Ūdens':
            amountField.value = 25;
            break;
        case 'Tabletes':
            amountField.value = 24;
            break;
    }
}

function closeRestockModal() {
    document.getElementById('restockModal').style.display = 'none';
    document.getElementById('restockForm').reset();
}

// Handle restock form submission
document.addEventListener('DOMContentLoaded', function() {
    const restockForm = document.getElementById('restockForm');
    if (restockForm) {
        restockForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const product = document.getElementById('restockProduct').value;
            const shelf = document.getElementById('restockShelf').value;
            const amount = document.getElementById('restockAmount').value;
            const comment = document.getElementById('restockComment').value;
            
            // Simulate API call
            setTimeout(() => {
                alert(`Papildināšana pabeigta!\nProdukt: ${product}\nPlaukts: ${shelf}\nDaudzums: ${amount}\nKomentārs: ${comment || 'Nav komentāra'}`);
                closeRestockModal();
                
                // Remove the row from restock table
                const row = event.target.closest('tr');
                if (row) {
                    row.style.opacity = '0.5';
                    row.style.textDecoration = 'line-through';
                    setTimeout(() => row.remove(), 1000);
                }
            }, 500);
        });
    }
});

// Check Functions
function startCheck(section) {
    const sections = {
        'A': ['A1', 'A2', 'A3', 'A4'],
        'B': ['B1', 'B2', 'B3', 'B4'],
        'C': ['C1', 'C2', 'C3', 'C4']
    };
    
    const shelves = sections[section];
    let currentShelf = 0;
    
    function checkNextShelf() {
        if (currentShelf < shelves.length) {
            const shelf = shelves[currentShelf];
            const result = confirm(`Pārbaudīt plauktu ${shelf}?\n\nNospied OK, ja plaukts ir kārtībā\nNospied Cancel, ja ir problēmas`);
            
            // Add to recent checks
            addRecentCheck(shelf, result ? 'ok' : 'issue');
            
            currentShelf++;
            setTimeout(checkNextShelf, 500);
        } else {
            alert(`Sekcijas ${section} pārbaude pabeigta!`);
            markSectionCompleted(section);
        }
    }
    
    checkNextShelf();
}

function addRecentCheck(shelf, status) {
    const checksList = document.querySelector('.checks-list');
    if (checksList) {
        const now = new Date();
        const timeStr = `Šodien ${now.getHours()}:${now.getMinutes().toString().padStart(2, '0')}`;
        
        const checkItem = document.createElement('div');
        checkItem.className = 'check-item';
        checkItem.innerHTML = `
            <div class="check-info">
                <strong>Plaukts ${shelf}</strong>
                <span class="check-time">${timeStr}</span>
            </div>
            <span class="check-status ${status}">${status === 'ok' ? '✓ Kārtībā' : '⚠ Nepieciešama uzmanība'}</span>
        `;
        
        checksList.insertBefore(checkItem, checksList.firstChild);
        
        // Keep only last 5 checks
        while (checksList.children.length > 5) {
            checksList.removeChild(checksList.lastChild);
        }
    }
}

function markSectionCompleted(section) {
    const scheduleItems = document.querySelectorAll('.schedule-item');
    scheduleItems.forEach(item => {
        const title = item.querySelector('h4').textContent;
        if (title.includes(`Sekcija ${section}`)) {
            item.classList.remove('pending');
            item.classList.add('completed');
            
            const timeSpan = item.querySelector('.time');
            const now = new Date();
            timeSpan.textContent = `${now.getHours()}:${now.getMinutes().toString().padStart(2, '0')} ✓`;
            
            const button = item.querySelector('button');
            if (button) {
                button.outerHTML = '<span class="status-text">Pabeigts</span>';
            }
        }
    });
}

// Maintenance Functions
function startMaintenance(location, type) {
    const maintenanceTypes = {
        'urgent': 'steidzamu remontu',
        'cleaning': 'uzkopšanu',
        'inspection': 'pārbaudi'
    };
    
    const action = maintenanceTypes[type] || 'apkopi';
    
    if (confirm(`Sākt ${action} vietā ${location}?`)) {
        // Simulate maintenance process
        alert(`${action.charAt(0).toUpperCase() + action.slice(1)} uzsākta vietā ${location}`);
        
        // Mark task as in progress
        const taskItem = event.target.closest('.task-item');
        if (taskItem) {
            taskItem.style.opacity = '0.7';
            taskItem.style.border = '2px dashed #ffa726';
            
            const button = event.target;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesā...';
            button.disabled = true;
            
            // Simulate completion after 3 seconds
            setTimeout(() => {
                completeMaintenanceTask(taskItem, location, action);
            }, 3000);
        }
    }
}

function completeMaintenanceTask(taskItem, location, action) {
    // Add to history
    addMaintenanceHistory(location, action);
    
    // Remove from tasks
    taskItem.style.transition = 'all 0.5s ease';
    taskItem.style.transform = 'translateX(100%)';
    taskItem.style.opacity = '0';
    
    setTimeout(() => {
        taskItem.remove();
        alert(`${action.charAt(0).toUpperCase() + action.slice(1)} pabeigta vietā ${location}!`);
    }, 500);
}

function addMaintenanceHistory(location, action) {
    const historyList = document.querySelector('.history-list');
    if (historyList) {
        const now = new Date();
        const dateStr = `${now.getDate()}.${(now.getMonth() + 1).toString().padStart(2, '0')}.${now.getFullYear()}, ${now.getHours()}:${now.getMinutes().toString().padStart(2, '0')}`;
        
        const historyItem = document.createElement('div');
        historyItem.className = 'history-item completed';
        historyItem.innerHTML = `
            <div class="history-info">
                <strong>${location} - ${action}</strong>
                <span class="history-date">${dateStr}</span>
            </div>
            <span class="history-status success">✓ Pabeigts</span>
        `;
        
        historyList.insertBefore(historyItem, historyList.firstChild);
        
        // Keep only last 10 history items
        while (historyList.children.length > 10) {
            historyList.removeChild(historyList.lastChild);
        }
    }
}

// Shelf Map Enhancement
function selectShelf(shelfId) {
    // Remove previous selection
    document.querySelectorAll('.shelf').forEach(shelf => {
        shelf.classList.remove('selected');
    });
    
    // Select current shelf
    const shelf = document.querySelector(`[data-shelf="${shelfId}"]`);
    if (shelf) {
        shelf.classList.add('selected');
        
        // Show shelf details if panel exists
        const detailsPanel = document.getElementById('shelfDetails');
        if (detailsPanel) {
            detailsPanel.style.display = 'block';
            document.getElementById('selectedShelfName').textContent = shelfId;
            
            // Set shelf info based on current status
            const status = shelf.classList.contains('low-stock') ? 'Zems krājums' :
                         shelf.classList.contains('needs-organize') ? 'Nepieciešama kārtošana' :
                         shelf.classList.contains('maintenance') ? 'Nepieciešama apkope' : 'Normāls';
            
            document.getElementById('shelfStatus').textContent = status;
            document.getElementById('shelfProductCount').textContent = Math.floor(Math.random() * 50) + 10;
            document.getElementById('shelfLastUpdate').textContent = 'Šodien, 14:30';
        }
    }
}

function organizeShelf() {
    const shelfId = document.getElementById('selectedShelfName').textContent;
    startOrganizing(shelfId);
}

function updateShelfStatus() {
    const shelfId = document.getElementById('selectedShelfName').textContent;
    const newStatus = prompt('Ievadiet jauno statusu:\n1 - Normāls\n2 - Zems krājums\n3 - Nepieciešama kārtošana\n4 - Nepieciešama apkope', '1');
    
    if (newStatus && newStatus >= 1 && newStatus <= 4) {
        updateShelfStatus(shelfId, ['normal', 'low-stock', 'needs-organize', 'maintenance'][newStatus - 1]);
    }
}

function updateShelfStatus(shelfId, newStatus) {
    const shelf = document.querySelector(`[data-shelf="${shelfId}"]`);
    if (shelf) {
        // Remove all status classes
        shelf.classList.remove('normal', 'low-stock', 'needs-organize', 'maintenance');
        
        // Add new status
        shelf.classList.add(newStatus);
        
        // Update details panel if visible
        const detailsPanel = document.getElementById('shelfDetails');
        if (detailsPanel && detailsPanel.style.display !== 'none') {
            const statusText = {
                'normal': 'Normāls',
                'low-stock': 'Zems krājums',
                'needs-organize': 'Nepieciešama kārtošana',
                'maintenance': 'Nepieciešama apkope'
            };
            document.getElementById('shelfStatus').textContent = statusText[newStatus] || 'Nezināms';
        }
    }
}

// Close modals when clicking outside
window.addEventListener('click', function(e) {
    const restockModal = document.getElementById('restockModal');
    if (restockModal && e.target === restockModal) {
        closeRestockModal();
    }
});

// Initialize shelf organizer functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers to shelf elements
    document.querySelectorAll('.shelf').forEach(shelf => {
        if (!shelf.hasAttribute('onclick')) {
            const shelfId = shelf.getAttribute('data-shelf');
            shelf.setAttribute('onclick', `selectShelf('${shelfId}')`);
            shelf.style.cursor = 'pointer';
        }
    });
    
    // Add selected class styling
    const style = document.createElement('style');
    style.textContent = `
        .shelf.selected {
            border: 2px solid #6c5ce7 !important;
            box-shadow: 0 0 10px rgba(108, 92, 231, 0.3) !important;
            transform: scale(1.05);
        }
    `;
    document.head.appendChild(style);
});

// Simple Shelf Organizer JavaScript Functions

function showShelfSection(section) {
    // Hide all sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(s => s.classList.remove('active'));
    
    // Remove active from nav links
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => link.classList.remove('active'));
    
    // Show selected section
    const targetSection = document.getElementById(`shelf-${section}`);
    if (targetSection) {
        targetSection.classList.add('active');
    }
    
    // Add active to clicked nav link
    if (event && event.target) {
        event.target.closest('.nav-link').classList.add('active');
    }
}

// Simple placeholder functions for the new sections
function showInventoryList() {
    alert('Produktu saraksts ielādēts');
}

function startOrganizing() {
    alert('Kārtošana uzsākta - varat sākt organizēt preces plauktos');
}

function showShelfMap() {
    alert('Plauktu karte atvērta - redzamas visas noliktavas sekcijas');
}

function generateReport(type) {
    alert(`${type} atskaite ģenerēta un gatava apskatei`);
}

function saveShelfData() {
    alert('Dati saglabāti sistēmā');
} 