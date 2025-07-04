function loadProducts() {
    fetch('get_products.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const tableBody = document.getElementById('productsTableBody');
            if (!tableBody) {
                return;
            }
            tableBody.innerHTML = '';
            if (data.error) {
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
            const tableBody = document.getElementById('productsTableBody');
            if (tableBody) {
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center">Kļūda ielādējot produktus</td></tr>';
            }
        });
}
function validateProductCode(code) {
    const errors = [];
    const trimmedCode = code.trim();
    if (!trimmedCode) {
        errors.push('Produkta kods ir obligāts');
    } else if (trimmedCode.length < 3 || trimmedCode.length > 20) {
        errors.push('Produkta kodam jābūt no 3 līdz 20 simboliem');
    } else if (!/^[A-Z0-9_-]+$/i.test(trimmedCode)) {
        errors.push('Produkta kods drīkst saturēt tikai burtus, ciparus, zemsvītras un domuzīmes');
    }
    return errors;
}
function validateProductName(name) {
    const errors = [];
    const trimmedName = name.trim();
    if (!trimmedName) {
        errors.push('Produkta nosaukums ir obligāts');
    } else if (trimmedName.length < 2 || trimmedName.length > 100) {
        errors.push('Produkta nosaukumam jābūt no 2 līdz 100 simboliem');
    } else if (!/^[\p{L}\p{N}\s\-\.,&()]+$/u.test(trimmedName)) {
        errors.push('Produkta nosaukums satur neatļautus simbolus');
    }
    return errors;
}
function validateCategory(category) {
    const errors = [];
    const trimmedCategory = category.trim();
    if (!trimmedCategory) {
        errors.push('Kategorija ir obligāta');
    } else if (trimmedCategory.length < 2 || trimmedCategory.length > 50) {
        errors.push('Kategorijas nosaukumam jābūt no 2 līdz 50 simboliem');
    }
    return errors;
}
function validateBarcode(barcode) {
    const errors = [];
    if (barcode && barcode.trim()) {
        const trimmedBarcode = barcode.trim();
        if (!/^[0-9]{8,13}$/.test(trimmedBarcode)) {
            errors.push('Svītrkods drīkst saturēt tikai ciparus un būt 8-13 simbolu garš');
        }
    }
    return errors;
}
function validateUnitPrice(price) {
    const errors = [];
    if (!price || price === '') {
        errors.push('Vienības cena ir obligāta');
    } else {
        const numPrice = parseFloat(price);
        if (isNaN(numPrice) || numPrice < 0) {
            errors.push('Vienības cenai jābūt pozitīvam skaitlim');
        } else if (numPrice > 999999.99) {
            errors.push('Vienības cena nedrīkst pārsniegt 999999.99 EUR');
        }
    }
    return errors;
}
function validateMinStock(stock) {
    const errors = [];
    if (!stock || stock === '') {
        errors.push('Minimālais krājuma līmenis ir obligāts');
    } else {
        const numStock = parseInt(stock);
        if (isNaN(numStock) || numStock < 0 || numStock != parseFloat(stock)) {
            errors.push('Minimālajam krājuma līmenim jābūt pozitīvam veselam skaitlim');
        } else if (numStock > 999999) {
            errors.push('Minimālais krājuma līmenis nedrīkst pārsniegt 999999');
        }
    }
    return errors;
}
function validateDescription(description) {
    const errors = [];
    if (description && description.trim().length > 500) {
        errors.push('Apraksts nedrīkst pārsniegt 500 simbolus');
    }
    return errors;
}
function validateProductForm(formData) {
    const allErrors = [];
    allErrors.push(...validateProductCode(formData.product_code));
    allErrors.push(...validateProductName(formData.product_name));
    allErrors.push(...validateCategory(formData.category));
    allErrors.push(...validateBarcode(formData.barcode));
    allErrors.push(...validateUnitPrice(formData.unit_price));
    allErrors.push(...validateMinStock(formData.min_stock_level));
    allErrors.push(...validateDescription(formData.description));
    return allErrors;
}
function displayValidationErrors(errors) {
    document.querySelectorAll('.validation-error').forEach(el => el.remove());
    if (errors.length > 0) {
        const errorContainer = document.createElement('div');
        errorContainer.className = 'validation-error';
        errorContainer.style.cssText = `
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
            font-size: 14px;
        `;
        const errorList = document.createElement('ul');
        errorList.style.cssText = 'margin: 0; padding-left: 20px;';
        errors.forEach(error => {
            const listItem = document.createElement('li');
            listItem.textContent = error;
            errorList.appendChild(listItem);
        });
        errorContainer.appendChild(errorList);
        const modalContent = document.querySelector('.modal-content');
        if (modalContent) {
            modalContent.insertBefore(errorContainer, modalContent.children[1]);
        }
    }
}
function showAddProductModal() {
    const modal = document.getElementById('addProductModal');
    modal.style.display = 'block';
}
function closeModal() {
    const modal = document.getElementById('addProductModal');
    modal.style.display = 'none';
    document.querySelectorAll('.validation-error').forEach(el => el.remove());
}
function searchProducts() {
    const searchInput = document.getElementById('productSearch');
    const searchTerm = searchInput.value.toLowerCase();
    const rows = document.querySelectorAll('#productsTableBody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}
document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    const searchInput = document.getElementById('productSearch');
    if (searchInput) {
        searchInput.addEventListener('input', searchProducts);
    }
    const closeButton = document.querySelector('.close');
    if (closeButton) {
        closeButton.addEventListener('click', closeModal);
    }
    setupAddProductForm();
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('addProductModal');
        if (e.target === modal) {
            closeModal();
        }
    });
});
function setupAddProductForm() {
    const addProductForm = document.getElementById('addProductForm');
    if (addProductForm) {
        addProductForm.removeEventListener('submit', handleAddProductSubmit);
        addProductForm.addEventListener('submit', handleAddProductSubmit);
    } else {
        setTimeout(setupAddProductForm, 100);
    }
}
function handleAddProductSubmit(e) {
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
    const validationErrors = validateProductForm(formData);
    if (validationErrors.length > 0) {
        displayValidationErrors(validationErrors);
        return;
    }
    const submitButton = document.querySelector('#addProductForm button[type="submit"]');
    const originalText = submitButton.textContent;
    submitButton.textContent = 'Saglabā...';
    submitButton.disabled = true;
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
            if (typeof showNotification !== 'undefined') {
                showNotification('Produkts veiksmīgi pievienots!', 'success');
            } else {
                alert('Produkts veiksmīgi pievienots!');
            }
            closeModal();
            document.getElementById('addProductForm').reset();
            loadProducts(); 
        } else if (data.validation_errors) {
            displayValidationErrors(data.validation_errors);
        } else {
            if (typeof showNotification !== 'undefined') {
                showNotification('Kļūda: ' + (data.error || 'Nezināma kļūda'), 'error');
            } else {
                alert('Kļūda: ' + (data.error || 'Nezināma kļūda'));
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof showNotification !== 'undefined') {
            showNotification('Kļūda pievienojot produktu', 'error');
        } else {
            alert('Kļūda pievienojot produktu');
        }
    })
    .finally(() => {
        submitButton.textContent = originalText;
        submitButton.disabled = false;
    });
}
function editProduct(productId) {
    fetch(`get_product.php?id=${productId}`)
        .then(response => response.json())
        .then(product => {
            if (product.error) {
                showNotification('Kļūda ielādējot produkta datus: ' + product.error, 'error');
                return;
            }
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
                            <label for="editProductCode">Produkta kods: *</label>
                            <input type="text" id="editProductCode" value="${product.product_code || ''}" required maxlength="20">
                        </div>
                        <div class="form-group">
                            <label for="editProductName">Nosaukums: *</label>
                            <input type="text" id="editProductName" value="${product.product_name || ''}" required maxlength="100">
                        </div>
                        <div class="form-group">
                            <label for="editCategory">Kategorija: *</label>
                            <input type="text" id="editCategory" value="${product.category || ''}" required maxlength="50">
                        </div>
                        <div class="form-group">
                            <label for="editBarcode">Svītrkods:</label>
                            <input type="text" id="editBarcode" value="${product.barcode || ''}" pattern="[0-9]{8,13}" title="8-13 cipari">
                        </div>
                        <div class="form-group">
                            <label for="editDescription">Apraksts:</label>
                            <textarea id="editDescription" maxlength="500">${product.description || ''}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="editUnitPrice">Vienības cena (€): *</label>
                            <input type="number" id="editUnitPrice" step="0.01" min="0" max="999999.99" value="${product.unit_price || ''}" required>
                        </div>
                        <div class="form-group">
                            <label for="editMinStock">Min. krājums: *</label>
                            <input type="number" id="editMinStock" min="0" max="999999" value="${product.min_stock_level || ''}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Saglabāt</button>
                    </form>
                </div>
            `;
            document.body.appendChild(modal);
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
                const validationErrors = validateProductForm(formData);
                if (validationErrors.length > 0) {
                    displayValidationErrors(validationErrors);
                    return;
                }
                const submitButton = document.querySelector('#editProductForm button[type="submit"]');
                const originalText = submitButton.textContent;
                submitButton.textContent = 'Saglabā...';
                submitButton.disabled = true;
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
                        showNotification('Produkts veiksmīgi atjaunināts!', 'success');
                        modal.remove();
                        loadProducts(); 
                    } else if (data.validation_errors) {
                        displayValidationErrors(data.validation_errors);
                    } else {
                        showNotification('Kļūda: ' + (data.error || 'Nezināma kļūda'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Kļūda saglabājot produktu', 'error');
                })
                .finally(() => {
                    submitButton.textContent = originalText;
                    submitButton.disabled = false;
                });
            });
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        })
        .catch(error => {
            console.error('Error fetching product:', error);
            showNotification('Kļūda ielādējot produkta datus', 'error');
        });
}
function deleteProduct(productId) {
    if (confirm('Vai tiešām vēlaties dzēst šo produktu? Šī darbība ir neatgriezeniska.')) {
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
                showNotification(data.message, 'success');
                loadProducts(); 
            } else {
                showNotification('Kļūda: ' + (data.error || 'Nezināma kļūda'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Kļūda dzēšot produktu', 'error');
        });
    }
}
window.loadProducts = loadProducts;
window.validateProductCode = validateProductCode;
window.validateProductName = validateProductName;
window.validateCategory = validateCategory;
window.validateBarcode = validateBarcode;
window.validateUnitPrice = validateUnitPrice;
window.validateMinStock = validateMinStock;
window.validateDescription = validateDescription;
window.validateProductForm = validateProductForm;
window.displayValidationErrors = displayValidationErrors;
window.showAddProductModal = showAddProductModal;
window.closeModal = closeModal;
window.searchProducts = searchProducts;
window.setupAddProductForm = setupAddProductForm;
window.handleAddProductSubmit = handleAddProductSubmit;
window.editProduct = editProduct;
window.deleteProduct = deleteProduct;
