document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('invoice-items-wrapper');
    const prototypeHolder = document.getElementById('invoice-item-prototype');
    const addButton = document.getElementById('add-invoice-item');
    const grandTotalElement = document.getElementById('invoice-grand-total');
    const submitButton = document.getElementById('invoice-submit');

    if (!wrapper || !prototypeHolder || !addButton || !grandTotalElement || !submitButton) {
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

        wrapper.querySelectorAll('.invoice-item').forEach(function (item) {
            grandTotal += updateLineTotal(item);
        });

        grandTotalElement.textContent = grandTotal.toFixed(2) + ' €';
    }

    function updateSubmitButton() {
        const seller = document.querySelector('#invoice_seller');
        const customer = document.querySelector('#invoice_customer');

        let hasValidLine = false;

        wrapper.querySelectorAll('.invoice-item').forEach(function (item) {
            const product = item.querySelector('select');
            const quantity = item.querySelector('input[id*="_quantity"]');

            if (
                product &&
                product.value &&
                quantity &&
                toNumber(quantity.value) > 0
            ) {
                hasValidLine = true;
            }
        });

        /*
            Si on est ADMIN :
            le champ vendeur existe, donc il doit être rempli.

            Si on est VENDEUR :
            le champ vendeur n’existe pas dans le formulaire,
            donc on considère que le vendeur est valide automatiquement.
        */
        const sellerIsValid = !seller || seller.value;

        submitButton.disabled = !(
            sellerIsValid &&
            customer &&
            customer.value &&
            hasValidLine
        );
    }

    function refreshInvoice() {
        updateGrandTotal();
        updateSubmitButton();
    }

    function bindCalculationEvents(item) {
        const select = item.querySelector('select');
        const priceInput = item.querySelector('input[id*="_price"]');
        const quantityInput = item.querySelector('input[id*="_quantity"]');
        const removeButton = item.querySelector('.remove-item');

        if (select) {
            select.addEventListener('change', function () {
                updateFromProduct(item);
                refreshInvoice();
            });
        }

        [priceInput, quantityInput].forEach(function (input) {
            if (input) {
                input.addEventListener('input', function () {
                    refreshInvoice();
                });
            }
        });

        if (removeButton) {
            removeButton.addEventListener('click', function () {
                item.remove();
                refreshInvoice();
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
        refreshInvoice();

        index++;
    });

    const seller = document.querySelector('#invoice_seller');
    const customer = document.querySelector('#invoice_customer');

    if (seller) {
        seller.addEventListener('change', refreshInvoice);
    }

    if (customer) {
        customer.addEventListener('change', refreshInvoice);
    }

    wrapper.querySelectorAll('.invoice-item').forEach(function (item) {
        bindCalculationEvents(item);
    });

    refreshInvoice();
});