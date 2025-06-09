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
        // TODO: Implement form submission to add new product
        closeModal();
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
    // TODO: Implement edit functionality
    console.log('Edit product:', productId);
}

// Function to delete a product
function deleteProduct(productId) {
    if (confirm('Vai tiešām vēlaties dzēst šo produktu?')) {
        // TODO: Implement delete functionality
        console.log('Delete product:', productId);
    }
} 