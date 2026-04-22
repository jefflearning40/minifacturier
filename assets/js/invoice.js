document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('invoice-items-wrapper');
    const prototypeHolder = document.getElementById('invoice-item-prototype');
    const addButton = document.getElementById('add-invoice-item');
    const grandTotalElement = document.getElementById('invoice-grand-total');

    // Le script ne fonctionne que sur la page facture
    if (!wrapper || !prototypeHolder || !addButton || !grandTotalElement) {
        return;
    }

    let index = wrapper.querySelectorAll('.invoice-item').length;

    function toNumber(value) {
        if (!value) {
            return 0;
        }

        return parseFloat(String(value).replace(',', '.').replace(' €', '')) || 0;
    }

    function updateFromProduct(item) {
        const select = item.querySelector('select');
        const priceInput = item.querySelector('input[id*="_price"]');

        if (!select || !priceInput || select.selectedIndex < 0) {
            return;
        }

        const selectedText = select.options[select.selectedIndex]?.text || '';

        if (!selectedText || selectedText.includes('Choisissez un produit')) {
            priceInput.value = '';
            return;
        }

        const parts = selectedText.split(' - ');

        if (parts.length >= 3) {
            const price = parts[2].replace(' €', '').trim();
            priceInput.value = price;
        }
    }

    function updateLineTotal(item) {
        const priceInput = item.querySelector('input[id*="_price"]');
        const quantityInput = item.querySelector('input[id*="_quantity"]');
        const totalDisplay = item.querySelector('.line-total-display');

        if (!priceInput || !quantityInput || !totalDisplay) {
            return 0;
        }

        const price = toNumber(priceInput.value);
        const quantity = toNumber(quantityInput.value);
        const total = price * quantity;

        totalDisplay.textContent = total.toFixed(2) + ' €';

        return total;
    }

    function updateGrandTotal() {
        let grandTotal = 0;

        wrapper.querySelectorAll('.invoice-item').forEach(item => {
            grandTotal += updateLineTotal(item);
        });

        grandTotalElement.textContent = grandTotal.toFixed(2) + ' €';
    }

    function bindCalculationEvents(item) {
        const select = item.querySelector('select');
        const priceInput = item.querySelector('input[id*="_price"]');
        const quantityInput = item.querySelector('input[id*="_quantity"]');
        const removeButton = item.querySelector('.remove-item');

        if (select) {
            select.addEventListener('change', function () {
                updateFromProduct(item);
                updateGrandTotal();
            });
        }

        [priceInput, quantityInput].forEach(input => {
            if (input) {
                input.addEventListener('input', function () {
                    updateGrandTotal();
                });
            }
        });

        if (removeButton) {
            removeButton.addEventListener('click', function () {
                item.remove();
                updateGrandTotal();
            });
        }

        updateFromProduct(item);
        updateLineTotal(item);
    }

    addButton.addEventListener('click', function () {
        let prototype = prototypeHolder.dataset.prototype;
        prototype = prototype.replace(/__name__/g, index);

        const temp = document.createElement('tbody');
        temp.innerHTML = prototype.trim();

        const newItem = temp.firstElementChild;

        if (!newItem) {
            return;
        }

        wrapper.appendChild(newItem);
        bindCalculationEvents(newItem);
        updateGrandTotal();

        index++;
    });

    wrapper.querySelectorAll('.invoice-item').forEach(item => {
        bindCalculationEvents(item);
    });

    updateGrandTotal();
});