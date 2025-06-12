// Function to load products from the database
function loadProducts() {
    console.log('Loading products...');
    
    fetch('get_products.php')
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            const tableBody = document.getElementById('productsTableBody');
            if (!tableBody) {
                console.error('Products table body not found');
                return;
            }
            
            tableBody.innerHTML = '';

            if (data.error) {
                console.error('Error from server:', data.error);
                tableBody.innerHTML = `<tr><td colspan="8" class="text-center">Kļūda: ${data.error}</td></tr>`;
                return;
            }

            const products = data.products || [];
            
            if (products.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="8" class="text-center">Nav atrasts neviens produkts</td>';
                tableBody.appendChild(row);
                return;
            }

            products.forEach(product => {
                console.log('Processing product:', product);
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${product.product_code || ''}</td>
                    <td>${product.product_name || ''}</td>
                    <td>${product.category || ''}</td>
                    <td>${product.barcode || ''}</td>
                    <td>€${parseFloat(product.unit_price || 0).toFixed(2)}</td>
                    <td>${product.min_stock_level || 0}</td>
                    <td><span class="status ${product.min_stock_level > 0 ? 'active' : 'warning'}">${product.min_stock_level > 0 ? 'Pieejams' : 'Zems krājums'}</span></td>
                    <td>
                        <button class="btn-icon edit" onclick="editProduct(${product.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn-icon delete" onclick="deleteProduct(${product.id})"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error loading products:', error);
            const tableBody = document.getElementById('productsTableBody');
            if (tableBody) {
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center">Kļūda ielādējot produktus</td></tr>';
            }
        });
}

// Function to show the add product modal
function showAddProductModal() {
    const modal = document.getElementById('addProductModal');
    modal.style.display = 'block';
}

// Function to close the modal
function closeModal() {
    const modal = document.getElementById('addProductModal');
    modal.style.display = 'none';
}

// Function to handle product search
function searchProducts() {
    const searchInput = document.getElementById('productSearch');
    const searchTerm = searchInput.value.toLowerCase();
    const rows = document.querySelectorAll('#productsTableBody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

// Add event listeners when the document is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load products when the page loads
    loadProducts();

    // Add event listener for search input
    const searchInput = document.getElementById('productSearch');
    searchInput.addEventListener('input', searchProducts);

    // Add event listener for modal close button
    const closeButton = document.querySelector('.close');
    closeButton.addEventListener('click', closeModal);

    // Add event listener for add product form submission
    const addProductForm = document.getElementById('addProductForm');
    addProductForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            product_code: document.getElementById('productCode').value,
            product_name: document.getElementById('productName').value,
            category: document.getElementById('productCategory').value,
            barcode: document.getElementById('barcode').value,
            description: document.getElementById('description').value,
            unit_price: document.getElementById('unitPrice').value,
            min_stock_level: document.getElementById('minStock').value
        };

        fetch('add_product.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Produkts veiksmīgi pievienots!');
                closeModal();
                document.getElementById('addProductForm').reset();
                loadProducts(); // Reload the products table
            } else {
                alert('Kļūda: ' + (data.error || 'Nezināma kļūda'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kļūda pievienojot produktu');
        });
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('addProductModal');
        if (e.target === modal) {
            closeModal();
        }
    });
});

// Function to edit a product
function editProduct(productId) {
    // Fetch product data first
    fetch(`get_product.php?id=${productId}`)
        .then(response => response.json())
        .then(product => {
            if (product.error) {
                alert('Kļūda ielādējot produkta datus: ' + product.error);
                return;
            }
            
            // Create edit modal
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.style.display = 'block';
            modal.innerHTML = `
                <div class="modal-content">
                    <span class="close" onclick="this.parentElement.parentElement.remove()">&times;</span>
                    <h2>Rediģēt produktu</h2>
                    <form id="editProductForm">
                        <input type="hidden" id="editProductId" value="${product.id}">
                        <div class="form-group">
                            <label for="editProductCode">Produkta kods:</label>
                            <input type="text" id="editProductCode" value="${product.product_code || ''}" required>
                        </div>
                        <div class="form-group">
                            <label for="editProductName">Nosaukums:</label>
                            <input type="text" id="editProductName" value="${product.product_name || ''}" required>
                        </div>
                        <div class="form-group">
                            <label for="editCategory">Kategorija:</label>
                            <input type="text" id="editCategory" value="${product.category || ''}" required>
                        </div>
                        <div class="form-group">
                            <label for="editBarcode">Svītrkods:</label>
                            <input type="text" id="editBarcode" value="${product.barcode || ''}">
                        </div>
                        <div class="form-group">
                            <label for="editDescription">Apraksts:</label>
                            <textarea id="editDescription">${product.description || ''}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="editUnitPrice">Vienības cena (€):</label>
                            <input type="number" id="editUnitPrice" step="0.01" value="${product.unit_price || ''}" required>
                        </div>
                        <div class="form-group">
                            <label for="editMinStock">Min. krājums:</label>
                            <input type="number" id="editMinStock" value="${product.min_stock_level || ''}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Saglabāt</button>
                    </form>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Add form submit handler
            document.getElementById('editProductForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = {
                    id: document.getElementById('editProductId').value,
                    product_code: document.getElementById('editProductCode').value,
                    product_name: document.getElementById('editProductName').value,
                    category: document.getElementById('editCategory').value,
                    barcode: document.getElementById('editBarcode').value,
                    description: document.getElementById('editDescription').value,
                    unit_price: document.getElementById('editUnitPrice').value,
                    min_stock_level: document.getElementById('editMinStock').value
                };
                
                fetch('edit_product.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Produkts veiksmīgi atjaunināts!');
                        modal.remove();
                        loadProducts(); // Reload the products table
                    } else {
                        alert('Kļūda: ' + (data.error || 'Nezināma kļūda'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Kļūda saglabājot produktu');
                });
            });
            
            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        })
        .catch(error => {
            console.error('Error fetching product:', error);
            alert('Kļūda ielādējot produkta datus');
        });
}

// Function to delete a product
function deleteProduct(productId) {
    if (confirm('Vai tiešām vēlaties dzēst šo produktu?')) {
        fetch('delete_product.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: productId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                loadProducts(); // Reload the products table
            } else {
                alert('Kļūda: ' + (data.error || 'Nezināma kļūda'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kļūda dzēšot produktu');
        });
    }
} 